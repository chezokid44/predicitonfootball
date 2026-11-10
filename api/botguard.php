<?php
/*
 * Prediction League - keeping bots out of log-in, sign-up and password reset.
 *
 *  1. Honeypot field: a form field people never see. Bots that fill it in are
 *     given a normal-looking failure and their address is blocked.
 *  2. Trap pages: a hidden link (hp/), plus wp-login.php and xmlrpc.php, which
 *     only bots visit. Their address is blocked from the forms for a while.
 *  3. Proof of work: the browser solves a small puzzle in the background before
 *     the form is sent. Each puzzle works once, can't be answered in under a
 *     second, and gets harder for addresses with recent failed log-ins.
 *
 * Blocks only stop log-in, sign-up and password resets; the rest of the site
 * still works. Admins can lift blocks in Admin > Activity.
 *
 * Strikes: every block is a strike, and so is each attempt from an address
 * while it's blocked (a trap page, or a log-in, sign-up or reset form; at most
 * one every 10 minutes). At BAN_STRIKES strikes the address is banned
 * permanently, which (with BAN_WHOLE_SITE) shuts it out of the whole site.
 * Admins can also ban an address by hand. Strikes are forgotten once an
 * address has gone 30 days without a block.
 */

declare(strict_types=1);

if (!defined('PL_APP')) {
  http_response_code(403);
  exit;
}

const HONEYPOT_FIELD = 'website';
const POW_MIN_SECONDS = 1;      // fastest a person can fill in a form
const POW_MAX_AGE = 1800;       // puzzles expire after 30 minutes
const POW_PURPOSES = ['login', 'register', 'reset'];
const FOREVER = '9999-12-31 23:59:59';
const STRIKE_GAP_MINUTES = 10;  // attempts while blocked add at most one strike this often
const STRIKE_FORGET_DAYS = 30;  // strikes reset after this long without a block

function botguard_routes(): array {
  return [
    'challenge'     => ['GET',  'route_challenge'],
    'admin.blocks'  => ['GET',  'route_admin_blocks'],
    'admin.unblock' => ['POST', 'route_admin_unblock'],
    'admin.ban'     => ['POST', 'route_admin_ban'],
  ];
}

function bot_check_on(): bool {
  return setting_bool('BOT_CHECK');
}

// ---------------------------------------------------------------------
// Blocked addresses (plblocklist)
// ---------------------------------------------------------------------

/** Addresses in config 'never_block_ips' are never blocked (e.g. your own). */
function ip_allowlisted(string $ip): bool {
  return in_array($ip, (array)(cfg('never_block_ips') ?? []), true);
}

/** Older block lists have no strike columns: add them once. */
function ensure_block_columns(): void {
  static $checked = false;
  if ($checked) return;
  $checked = true;
  with_table('blocks', function () {
    try {
      q('SELECT strikes, permanent, last_strike FROM ' . xt('blocks') . ' WHERE 1 = 0');
    } catch (PDOException $e) {
      if (stripos($e->getMessage(), 'no such table') !== false || $e->getCode() === '42S02') throw $e;
      db()->exec('ALTER TABLE ' . xt('blocks') . ' ADD COLUMN strikes INT NOT NULL DEFAULT 1');
      db()->exec('ALTER TABLE ' . xt('blocks') . ' ADD COLUMN permanent ' . (is_mysql() ? 'TINYINT' : 'INT') . ' NOT NULL DEFAULT 0');
      db()->exec('ALTER TABLE ' . xt('blocks') . ' ADD COLUMN last_strike DATETIME DEFAULT NULL');
    }
  });
}

function block_row(string $ip): ?array {
  ensure_block_columns();
  $r = with_table('blocks', fn() => q('SELECT * FROM ' . xt('blocks') . ' WHERE lid = ? AND ip = ?', [lid(), $ip])->fetch());
  return $r ?: null;
}

function ban_strikes(): int {
  return max(0, min(100, (int)setting('BAN_STRIKES')));
}

/**
 * Block an address (one more strike). At BAN_STRIKES strikes, or with
 * $permanent, it's banned for good.
 */
function block_ip(string $reason, ?string $ip = null, bool $permanent = false): void {
  $ip = $ip ?? client_ip();
  if ($ip === '' || ip_allowlisted($ip)) return;
  $hours = max(0, min(720, (int)setting('BOT_BLOCK_HOURS')));
  $now = now_str();
  $row = block_row($ip);
  $strikes = 1;
  if ($row) {
    $recent = (string)$row['expires'] > date('Y-m-d H:i:s', time() - STRIKE_FORGET_DAYS * 86400);
    $strikes = $recent ? (int)$row['strikes'] + 1 : 1;
  }
  $wasBanned = $row && (int)$row['permanent'];
  $banned = $wasBanned || $permanent || (ban_strikes() > 0 && $strikes >= ban_strikes());
  if (!$banned && $hours === 0 && !$row) {
    // Blocking is off: only remember the strike.
    $expires = $now;
  } else {
    $expires = $banned ? FOREVER : date('Y-m-d H:i:s', time() + $hours * 3600);
    // A new strike never shortens a block that runs longer.
    if ($row && !$banned && (string)$row['expires'] > $expires) $expires = (string)$row['expires'];
  }
  $label = $reason;
  if ($banned && !$wasBanned) $label = $permanent ? $reason : "$reason (strike $strikes: banned permanently)";
  with_table('blocks', function () use ($ip, $label, $now, $expires, $strikes, $banned, $row) {
    if ($row) {
      q('UPDATE ' . xt('blocks') . ' SET reason = ?, created = ?, expires = ?, strikes = ?, permanent = ?, last_strike = ? WHERE lid = ? AND ip = ?',
        [mb_substr($label, 0, 100), $now, $expires, $strikes, $banned ? 1 : 0, $now, lid(), $ip]);
    } else {
      q('INSERT INTO ' . xt('blocks') . ' (lid, ip, reason, created, expires, strikes, permanent, last_strike) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
        [lid(), mb_substr($ip, 0, 45), mb_substr($label, 0, 100), $now, $expires, $strikes, $banned ? 1 : 0, $now]);
    }
  });
  if ($banned && !$wasBanned) {
    log_event('ban', $permanent ? "Banned $ip permanently: $reason" : "Banned $ip permanently after $strikes strikes", ['id' => null, 'username' => null]);
  }
}

function ip_blocked(?string $ip = null): bool {
  $ip = $ip ?? client_ip();
  if ($ip === '' || ip_allowlisted($ip)) return false;
  try {
    $row = block_row($ip);
    return $row !== null && (string)$row['expires'] > now_str();
  } catch (Throwable $e) {
    return false;
  }
}

function ip_banned(?string $ip = null): bool {
  $ip = $ip ?? client_ip();
  if ($ip === '' || ip_allowlisted($ip)) return false;
  try {
    $row = block_row($ip);
    return $row !== null && (int)$row['permanent'] === 1;
  } catch (Throwable $e) {
    return false;
  }
}

/** Another try from an address that's already blocked: one more strike (at most every 10 minutes). */
function strike_while_blocked(string $what, string $ip): void {
  $row = block_row($ip);
  if (!$row || (int)$row['permanent']) return;
  $last = (string)($row['last_strike'] ?? '');
  if ($last !== '' && strtotime($last) > time() - STRIKE_GAP_MINUTES * 60) return;
  block_ip("Tried $what while blocked", $ip);
}

/** For log-in, sign-up and reset. $strike: this is a real attempt (not just opening the form). */
function refuse_if_blocked(bool $strike = false): void {
  if (ip_blocked()) {
    if ($strike) strike_while_blocked('the ' . ($GLOBALS['PL_FORM'] ?? 'log-in') . ' form', client_ip());
    fail(403, 'Your connection has been blocked for a while after activity that looked automated. Try again later, or ask the league admin.', 'blocked');
  }
}

/** With BAN_WHOLE_SITE, banned addresses get nothing from the API at all. */
function refuse_if_banned(): void {
  if (setting_bool('BAN_WHOLE_SITE') && ip_banned()) {
    fail(403, 'This connection has been banned from the site. If you think that\'s a mistake, contact the league admin.', 'banned');
  }
}

// ---------------------------------------------------------------------
// Proof of work
// ---------------------------------------------------------------------

/** Puzzle size: BOT_POW_BITS, plus 1 bit per 2 recent failed log-ins (up to +4). */
function pow_bits(): int {
  $base = max(8, min(22, (int)setting('BOT_POW_BITS') ?: 16));
  $extra = min(4, intdiv(recent_events('login_failed', 900), 2));
  return min(24, $base + $extra);
}

function leading_zero_bits(string $binary): int {
  $bits = 0;
  foreach (str_split($binary) as $byte) {
    $v = ord($byte);
    if ($v === 0) { $bits += 8; continue; }
    while (($v & 0x80) === 0) { $bits++; $v <<= 1; }
    break;
  }
  return $bits;
}

function route_challenge(): array {
  $purpose = (string)($_GET['for'] ?? '');
  if (!in_array($purpose, POW_PURPOSES, true)) fail(422, 'Unknown form.');
  if (!bot_check_on()) return ['enabled' => false];
  refuse_if_blocked();
  $challenge = bin2hex(random_bytes(16));
  $bits = pow_bits();
  // Keep a few outstanding puzzles per form (several tabs, a page drawn twice).
  $open = is_array($_SESSION['pow'][$purpose] ?? null) ? $_SESSION['pow'][$purpose] : [];
  $open[$challenge] = ['bits' => $bits, 't' => microtime(true)];
  $_SESSION['pow'][$purpose] = array_slice($open, -5, null, true);
  return ['enabled' => true, 'challenge' => $challenge, 'bits' => $bits, 'min_ms' => POW_MIN_SECONDS * 1000];
}

/**
 * Check a submitted form from a visitor: blocked address, honeypot and proof
 * of work. A filled honeypot blocks the address and fails with $decoy, the
 * same message a person would get, so bots learn nothing; with a null $decoy
 * it returns false so the caller can show a normal-looking success.
 */
function bot_check(string $purpose, array $b, ?string $decoy): bool {
  $GLOBALS['PL_FORM'] = ['login' => 'log-in', 'register' => 'sign-up', 'reset' => 'password reset'][$purpose] ?? $purpose;
  refuse_if_blocked(true);
  if (!bot_check_on()) return true;

  if (trim((string)($b[HONEYPOT_FIELD] ?? '')) !== '') {
    log_event('bot_honeypot', "Filled the hidden field on the $purpose form", ['id' => null, 'username' => null]);
    block_ip("Filled the hidden $purpose field");
    usleep(400000);
    if ($decoy === null) return false;  // caller shows a normal-looking success
    fail(401, $decoy);
  }

  $pow = is_array($b['pow'] ?? null) ? $b['pow'] : [];
  $challenge = (string)($pow['challenge'] ?? '');
  $nonce = (string)($pow['nonce'] ?? '');
  $open = is_array($_SESSION['pow'][$purpose] ?? null) ? $_SESSION['pow'][$purpose] : [];
  $saved = ($challenge !== '' && isset($open[$challenge])) ? $open[$challenge] : null;
  unset($_SESSION['pow'][$purpose][$challenge]);  // every puzzle works once
  $age = $saved ? microtime(true) - (float)$saved['t'] : null;

  $reason = null;
  if (!$saved) $reason = 'missing or reused puzzle';
  elseif ($age > POW_MAX_AGE) $reason = 'puzzle expired';
  elseif ($age < POW_MIN_SECONDS) $reason = 'sent too fast';
  elseif (strlen($nonce) > 20 || !ctype_digit($nonce)) $reason = 'bad answer';
  elseif (leading_zero_bits(hash('sha256', "$challenge:$nonce", true)) < (int)$saved['bits']) $reason = 'wrong answer';

  if ($reason !== null) {
    log_event('bot_check_failed', ucfirst($purpose) . " form: $reason", ['id' => null, 'username' => null]);
    // Repeated failures from one address look automated.
    if (recent_events('bot_check_failed', 600) >= 10) block_ip('Failed the bot check 10 times');
    fail(400, 'We couldn\'t confirm you\'re not a bot. Try again.', 'bot_check');
  }
  return true;
}

// ---------------------------------------------------------------------
// Trap pages
// ---------------------------------------------------------------------

/** Called by hp/index.php, wp-login.php and xmlrpc.php: log, block, show nothing. */
function run_trap(string $where): void {
  try {
    date_default_timezone_set((string)cfg('timezone'));
    $ip = client_ip();
    if (!ip_blocked($ip)) {
      $ua = mb_substr(preg_replace('/[^\x20-\x7E]/', '', (string)($_SERVER['HTTP_USER_AGENT'] ?? '')), 0, 120);
      log_event('bot_trap', "Visited $where" . ($ua !== '' ? " ($ua)" : ''), ['id' => null, 'username' => null]);
      block_ip("Visited $where");
    } else {
      strike_while_blocked($where, $ip);
    }
  } catch (Throwable $e) {
    // Not installed yet, or the database is down: just show nothing.
  }
  http_response_code(404);
  header('Content-Type: text/html; charset=utf-8');
  header('Cache-Control: no-store, private');  // a cached trap would stop catching bots
  header('X-Robots-Tag: noindex, nofollow');
  echo "<!doctype html><title>Not found</title><p>Not found.</p>";
}

// ---------------------------------------------------------------------
// Admin: blocked addresses
// ---------------------------------------------------------------------

function route_admin_blocks(): array {
  require_admin();
  ensure_block_columns();
  $rows = with_table('blocks', fn() => q('SELECT ip, reason, created, expires, strikes, permanent FROM ' . xt('blocks') . ' WHERE lid = ? AND (expires > ? OR permanent = 1) ORDER BY permanent DESC, created DESC LIMIT 500',
    [lid(), now_str()])->fetchAll());
  $rows = array_map(fn($r) => ['ip' => $r['ip'], 'reason' => $r['reason'], 'created' => $r['created'], 'expires' => (int)$r['permanent'] ? null : $r['expires'],
                               'strikes' => (int)$r['strikes'], 'permanent' => (bool)(int)$r['permanent']], $rows);
  return ['blocks' => $rows, 'your_ip' => client_ip(), 'hours' => (int)setting('BOT_BLOCK_HOURS'),
          'ban_strikes' => ban_strikes(), 'whole_site' => setting_bool('BAN_WHOLE_SITE')];
}

function route_admin_unblock(): array {
  require_admin();
  $ip = trim((string)(body()['ip'] ?? ''));
  ensure_block_columns();
  if ($ip === 'all') {
    // Bans stay: they're only lifted one at a time.
    with_table('blocks', fn() => q('DELETE FROM ' . xt('blocks') . ' WHERE lid = ? AND permanent = 0', [lid()]));
    log_event('unblock', 'Lifted every temporary block');
  } else {
    if (!filter_var($ip, FILTER_VALIDATE_IP)) fail(422, 'That isn\'t an IP address.');
    $row = block_row($ip);
    with_table('blocks', fn() => q('DELETE FROM ' . xt('blocks') . ' WHERE lid = ? AND ip = ?', [lid(), $ip]));
    log_event('unblock', ($row && (int)$row['permanent'] ? 'Lifted the permanent ban' : 'Lifted the block') . " on $ip (strikes cleared)");
  }
  return route_admin_blocks();
}

function route_admin_ban(): array {
  require_admin();
  $b = body();
  $ip = trim((string)($b['ip'] ?? ''));
  if (!filter_var($ip, FILTER_VALIDATE_IP)) fail(422, 'Enter an IP address, like 203.0.113.7 or 2001:db8::1.');
  if ($ip === client_ip()) fail(422, 'That\'s your own address. Banning it would lock you out of the site.');
  if (ip_allowlisted($ip)) fail(422, 'That address is in never_block_ips in api/config.php, so it can\'t be banned.');
  $reason = trim(preg_replace('/\s+/u', ' ', mb_substr((string)($b['reason'] ?? ''), 0, 80)));
  block_ip('Banned by ' . current_user()['username'] . ($reason !== '' ? ": $reason" : ''), $ip, true);
  return route_admin_blocks();
}
