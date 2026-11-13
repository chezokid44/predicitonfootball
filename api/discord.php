<?php
/*
 * Prediction League - alerts in a Discord channel.
 *
 * A Discord webhook (Server settings > Integrations > Webhooks > New webhook
 * > Copy webhook URL) lets the site post into a channel. It's used as a
 * second route for alerts, in case email fails:
 *   - the daily list of newly blocked addresses (DISCORD_BLOCKS),
 *   - each address banned permanently, as it happens (DISCORD_BANS),
 *   - server errors and emails that couldn't be sent (DISCORD_ERRORS).
 *
 * Posts are sent after the visitor's page has gone. The same error is posted
 * at most once every 30 minutes, and at most 20 errors an hour.
 *
 * The webhook URL is longer than the settings table allows, so it's kept in
 * its own small table (plstore), which also remembers recent error posts.
 */

declare(strict_types=1);

if (!defined('PL_APP')) {
  http_response_code(403);
  exit;
}

const DISCORD_ERROR_REPEAT_MINUTES = 30;
const DISCORD_ERRORS_PER_HOUR = 20;
const DISCORD_COLOURS = ['info' => 0x2F6DB5, 'warn' => 0xC99A06, 'error' => 0xC4122F, 'ok' => 0x1D7A46];

function discord_routes(): array {
  return [
    'admin.discord'      => ['GET',  'route_admin_discord'],
    'admin.discord.save' => ['POST', 'route_admin_discord_save'],
    'admin.discord.test' => ['POST', 'route_admin_discord_test'],
  ];
}

// ---------------------------------------------------------------------
// A small key/value store for values that don't fit plconfigdata
// ---------------------------------------------------------------------

function store_get(string $name): ?string {
  try {
    $v = with_table('store', fn() => q('SELECT value FROM ' . xt('store') . ' WHERE lid = ? AND name = ?', [lid(), $name])->fetchColumn());
    return $v === false ? null : (string)$v;
  } catch (Throwable $e) {
    return null;
  }
}

function store_set(string $name, ?string $value): void {
  with_table('store', fn() => q('DELETE FROM ' . xt('store') . ' WHERE lid = ? AND name = ?', [lid(), $name]));
  if ($value !== null) q('INSERT INTO ' . xt('store') . ' (lid, name, value, updated) VALUES (?, ?, ?, ?)', [lid(), $name, $value, now_str()]);
}

// ---------------------------------------------------------------------
// The webhook
// ---------------------------------------------------------------------

/** A Discord webhook address (or, while debugging, a local test address). */
function valid_webhook(string $url): bool {
  if (preg_match('#\Ahttps://(?:(?:ptb|canary)\.)?discord(?:app)?\.com/api/webhooks/\d{5,25}/[\w-]{20,120}\z#', $url)) return true;
  return (bool)cfg('debug') && (bool)preg_match('#\Ahttps?://127\.0\.0\.1:\d+/[\w/-]*\z#', $url);
}

function discord_webhook(): ?string {
  $url = store_get('discord_webhook');
  return $url !== null && valid_webhook($url) ? $url : null;
}

/** Shown in place of the address: enough to recognise it, not enough to use it. */
function webhook_hint(?string $url): ?string {
  if ($url === null) return null;
  return preg_match('#/webhooks/(\d+)/#', $url, $m) ? 'Webhook ' . $m[1] . ' (address saved)' : 'Address saved';
}

/**
 * Post now. $payload is Discord's JSON (content, embeds). Returns null on
 * success or the reason it failed.
 */
function discord_send(array $payload, ?string $url = null): ?string {
  $url = $url ?? discord_webhook();
  if ($url === null) return 'No Discord webhook is set up.';
  $payload += ['username' => mb_substr((setting('PRED_LEAGUE_TITLE') ?: 'Prediction League'), 0, 70), 'allowed_mentions' => ['parse' => []]];
  $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
  $status = 0;
  $response = '';
  if (function_exists('curl_init')) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
      CURLOPT_POST => true, CURLOPT_POSTFIELDS => $body, CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => ['Content-Type: application/json'], CURLOPT_TIMEOUT => 6, CURLOPT_CONNECTTIMEOUT => 4,
    ]);
    $response = (string)curl_exec($ch);
    $status = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $err = curl_error($ch);
    curl_close($ch);
    if ($status === 0) return 'Couldn\'t reach Discord' . ($err !== '' ? ": $err" : '.');
  } else {
    $ctx = stream_context_create(['http' => ['method' => 'POST', 'header' => "Content-Type: application/json\r\n", 'content' => $body,
                                             'timeout' => 6, 'ignore_errors' => true]]);
    $response = (string)@file_get_contents($url, false, $ctx);
    foreach ($http_response_header ?? [] as $h) {
      if (preg_match('#^HTTP/\S+\s+(\d{3})#', $h, $m)) $status = (int)$m[1];
    }
    if ($status === 0) return 'Couldn\'t reach Discord. The server may block outgoing connections.';
  }
  if ($status >= 200 && $status < 300) return null;
  if ($status === 404 || $status === 401) return 'Discord says that webhook doesn\'t exist any more. Make a new one and save its address.';
  if ($status === 429) return 'Discord asked the site to slow down (too many posts). Try again in a minute.';
  $msg = json_decode($response, true)['message'] ?? '';
  return "Discord refused the post (HTTP $status" . ($msg !== '' ? ": $msg" : '') . ').';
}

/** Post after the visitor's page has been sent, so nobody waits for Discord. */
function discord_later(array $payload): void {
  static $queue = null;
  if (discord_webhook() === null) return;
  if ($queue === null) {
    $queue = [];
    register_shutdown_function(function () use (&$queue) {
      if (!$queue) return;
      if (function_exists('fastcgi_finish_request')) @fastcgi_finish_request();
      foreach ($queue as $p) {
        $err = discord_send($p);
        if ($err !== null) {
          error_log("Prediction League Discord: $err");
          log_event('discord_failed', mb_substr($err, 0, 255), ['id' => null, 'username' => null]);
          break;
        }
      }
    });
  }
  $queue[] = $payload;
}

function discord_embed(string $title, string $text, string $tone = 'info', ?string $link = null): array {
  $e = ['title' => mb_substr($title, 0, 250), 'description' => mb_substr($text, 0, 4000), 'color' => DISCORD_COLOURS[$tone] ?? DISCORD_COLOURS['info'],
        'timestamp' => gmdate('c')];
  if ($link) $e['url'] = $link;
  return ['embeds' => [$e]];
}

function admin_link(string $hash): ?string {
  $base = rtrim((string)site_url(), '/');
  return $base !== '' ? "$base/$hash" : null;
}

// ---------------------------------------------------------------------
// What gets posted
// ---------------------------------------------------------------------

/**
 * Post an error (server error, email that failed). The same error is posted
 * at most once every 30 minutes, and at most 20 an hour.
 */
function discord_error(string $title, string $detail): void {
  try {
    if (!setting_bool('DISCORD_ERRORS') || discord_webhook() === null) return;
    $now = time();
    $recent = json_decode((string)store_get('discord_recent'), true);
    $recent = is_array($recent) ? array_filter($recent, fn($t) => is_int($t) && $t > $now - 3600) : [];
    $key = substr(md5($title . '|' . preg_replace('/\d+/', '#', strtok($detail, "\n") ?: '')), 0, 12);
    if (isset($recent[$key]) && $recent[$key] > $now - DISCORD_ERROR_REPEAT_MINUTES * 60) return;
    if (count($recent) >= DISCORD_ERRORS_PER_HOUR) return;
    $recent[$key] = $now;
    store_set('discord_recent', json_encode($recent));
    discord_later(discord_embed($title, $detail, 'error', admin_link('#/admin/activity')));
  } catch (Throwable $e) {
    error_log('Prediction League Discord: ' . $e->getMessage());
  }
}

/** An address banned permanently, as it happens. */
function discord_ban(string $detail): void {
  try {
    if (!setting_bool('DISCORD_BANS') || discord_webhook() === null) return;
    discord_later(discord_embed('Address banned permanently', $detail, 'warn', admin_link('#/admin/activity')));
  } catch (Throwable $e) {
    error_log('Prediction League Discord: ' . $e->getMessage());
  }
}

/** The daily list of blocked addresses, now. Returns null or why it failed. */
function discord_block_digest(array $blocks, string $since): ?string {
  if (!setting_bool('DISCORD_BLOCKS')) return 'off';
  $now = now_str();
  $lines = [];
  foreach ($blocks as $b) {
    $state = (int)($b['permanent'] ?? 0) ? '**banned permanently**'
      : ((string)$b['expires'] > $now ? 'blocked until ' . date('D j M H:i', strtotime((string)$b['expires'])) : 'block has ended');
    if ((int)($b['strikes'] ?? 1) > 1) $state .= ', strike ' . (int)$b['strikes'];
    $lines[] = '`' . $b['ip'] . '` ' . date('D j M H:i', strtotime((string)$b['created'])) . ' - ' . $b['reason'] . " ($state)";
  }
  $text = implode("\n", $lines);
  if (mb_strlen($text) > 3800) $text = mb_substr($text, 0, 3800) . "\n... and more: see Admin > Activity.";
  $n = count($blocks);
  return discord_send(discord_embed(($n === 1 ? '1 address blocked' : "$n addresses blocked") . ' since ' . date('D j M H:i', strtotime($since)),
    $text, 'warn', admin_link('#/admin/activity')));
}

// ---------------------------------------------------------------------
// Admin
// ---------------------------------------------------------------------

function route_admin_discord(): array {
  require_admin();
  return ['webhook' => webhook_hint(discord_webhook()), 'curl' => function_exists('curl_init')];
}

function route_admin_discord_save(): array {
  require_admin();
  $url = trim((string)(body()['webhook'] ?? ''));
  if ($url === '') {
    store_set('discord_webhook', null);
    log_event('settings', 'Removed the Discord webhook');
    return route_admin_discord();
  }
  if (!valid_webhook($url)) {
    fail(422, 'That isn\'t a Discord webhook address. In Discord: Server settings > Integrations > Webhooks > New webhook > Copy webhook URL. It starts https://discord.com/api/webhooks/');
  }
  store_set('discord_webhook', $url);
  log_event('settings', 'Saved a Discord webhook');
  return route_admin_discord();
}

function route_admin_discord_test(): array {
  $u = require_admin();
  $err = discord_send(discord_embed('Test from ' . (setting('PRED_LEAGUE_TITLE') ?: 'your prediction league'),
    "This channel will get the alerts you've switched on: the daily list of blocked addresses, permanent bans, and server errors or emails that couldn't be sent.\n\nSent by " . $u['username'] . '.',
    'ok', admin_link('#/admin/settings')));
  if ($err !== null) {
    log_event('discord_failed', mb_substr("Test: $err", 0, 255));
    fail(502, $err);
  }
  log_event('discord_test', 'Sent a test message to Discord');
  return ['sent' => true];
}
