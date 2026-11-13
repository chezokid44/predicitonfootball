<?php
/*
 * Prediction League - daily email of newly blocked addresses.
 *
 * At most one email a day, listing every address blocked since the last one
 * (bot traps, filled honeypot fields, repeated failed bot checks). It's sent
 * through its own SMTP account (Settings > Block alerts), never the league's
 * normal email account, so alerts and players' emails stay apart.
 *
 * There's no cron job: after the first visit to the site past BLOCK_ALERT_HOUR,
 * the list is sent once the visitor's page has been delivered.
 */

declare(strict_types=1);

if (!defined('PL_APP')) {
  http_response_code(403);
  exit;
}

const ALERT_RETRY_MINUTES = 60;  // after a failed send, wait this long before trying again

function alert_routes(): array {
  return [
    'admin.alerttest' => ['POST', 'route_admin_alerttest'],
    'admin.alertsend' => ['POST', 'route_admin_alertsend'],
  ];
}

/** The separate alerts email account (always SMTP). */
function mail_account_alerts(): array {
  return [
    'from' => setting('ALERT_FROM'), 'from_name' => (setting('PRED_LEAGUE_TITLE') ?: 'Prediction League') . ' alerts',
    'transport' => 'smtp',
    'host' => setting('ALERT_SMTP_HOST'), 'port' => (int)setting('ALERT_SMTP_PORT') ?: 587, 'secure' => setting('ALERT_SMTP_SECURE'),
    'user' => setting('ALERT_SMTP_USER'), 'pass' => setting('ALERT_SMTP_PASS'),
    'where' => 'Admin > Settings > Block alerts',
  ];
}

/** Who gets the list: BLOCK_ALERT_TO, or every admin with an email address. */
function alert_recipients(): array {
  $to = trim(setting('BLOCK_ALERT_TO'));
  if ($to !== '') return [$to];
  $rows = q('SELECT email FROM ' . t('users') . ' WHERE lid = ? AND usertype >= 4 AND email IS NOT NULL AND email <> ?', [lid(), ''])->fetchAll(PDO::FETCH_COLUMN);
  return array_values(array_unique(array_filter(array_map('trim', $rows), fn($e) => (bool)filter_var($e, FILTER_VALIDATE_EMAIL))));
}

// Sending state lives in two hidden config rows.
function alert_state(string $key): ?string {
  $v = q('SELECT value FROM ' . t('config') . ' WHERE lid = ? AND param = ?', [lid(), $key])->fetchColumn();
  return $v === false ? null : (string)$v;
}

function set_alert_state(string $key, string $value): void {
  if (alert_state($key) === null) {
    try {
      q('INSERT INTO ' . t('config') . ' (lid, grp, param, value, descr, ro) VALUES (?, ?, ?, ?, ?, ?)',
        [lid(), 99, $key, $value, 'Used by the daily block alert email.', 'Y']);
      return;
    } catch (Throwable $e) {
      // Another request added it first: fall through and update.
    }
  }
  q('UPDATE ' . t('config') . ' SET value = ? WHERE lid = ? AND param = ?', [$value, lid(), $key]);
}

/** Blocks made after $since (and up to $until). */
function blocks_since(string $since, string $until): array {
  ensure_block_columns();
  return with_table('blocks', fn() => q('SELECT ip, reason, created, expires, strikes, permanent FROM ' . xt('blocks') . ' WHERE lid = ? AND created > ? AND created <= ? ORDER BY created',
    [lid(), $since, $until])->fetchAll());
}

function block_digest_text(array $blocks, string $since): string {
  $title = setting('PRED_LEAGUE_TITLE') ?: 'your prediction league';
  $now = now_str();
  $lines = [
    count($blocks) === 1 ? "1 address was blocked on $title" : count($blocks) . " addresses were blocked on $title",
    'between ' . date('D j M Y H:i', strtotime($since)) . ' and ' . date('D j M Y H:i', strtotime($now)) . '.',
    '',
  ];
  foreach ($blocks as $b) {
    $state = (int)$b['permanent'] ? 'BANNED PERMANENTLY'
      : ($b['expires'] > $now ? 'blocked until ' . date('D j M H:i', strtotime((string)$b['expires'])) : 'block has ended');
    if ((int)$b['strikes'] > 1) $state .= ', strike ' . (int)$b['strikes'] . (ban_strikes() ? ' of ' . ban_strikes() : '');
    $lines[] = '- ' . $b['ip'];
    $lines[] = '  ' . date('D j M H:i', strtotime((string)$b['created'])) . ': ' . $b['reason'] . " ($state)";
  }
  $lines[] = '';
  $lines[] = 'Blocks only stop log-in, sign-up and password resets from those addresses.';
  $lines[] = 'See them, or lift a block, in Admin > Activity: ' . rtrim(site_url(), '/') . '/#/admin/activity';
  $lines[] = '';
  $lines[] = 'You get this because the daily block alert is on (Admin > Settings > Block alerts).';
  return implode("\n", $lines);
}

/**
 * Send the list if one is due. With $force (the admin's "Send now"), send
 * whatever is new regardless of the time of day. Returns what happened.
 */
function send_block_digest(bool $force = false): array {
  if (!setting_bool('BLOCK_ALERT_ON') && !$force) return ['status' => 'off'];
  $today = date('Y-m-d');
  $last = alert_state('BLOCK_ALERT_LAST');
  if (!$force) {
    if ((int)date('G') < max(0, min(23, (int)setting('BLOCK_ALERT_HOUR')))) return ['status' => 'not_yet'];
    if ($last !== null && substr($last, 0, 10) === $today) return ['status' => 'done_today'];
    $tried = alert_state('BLOCK_ALERT_TRIED');
    if ($tried !== null && $tried !== '' && strtotime($tried) > time() - ALERT_RETRY_MINUTES * 60) return ['status' => 'waiting_to_retry'];
  }
  $now = now_str();
  // First time: start from a day ago rather than listing every block ever made.
  $since = $last ?? date('Y-m-d H:i:s', time() - 86400);

  // Claim this run so two visitors at the same moment can't both send it.
  if ($last === null) {
    set_alert_state('BLOCK_ALERT_LAST', $since);
    $last = $since;
  }
  $claim = q('UPDATE ' . t('config') . ' SET value = ? WHERE lid = ? AND param = ? AND value = ?', [$now, lid(), 'BLOCK_ALERT_LAST', $last]);
  if ($claim->rowCount() !== 1) return ['status' => 'done_today'];

  $blocks = blocks_since($since, $now);
  if (!$blocks) return ['status' => 'nothing_new'];

  $to = alert_recipients();
  $account = mail_account_alerts();
  $useDiscord = function_exists('discord_webhook') && discord_webhook() !== null && setting_bool('DISCORD_BLOCKS');
  $useEmail = mail_account_ok($account) || !$useDiscord;  // without Discord, a missing email setup is an error
  $sentTo = [];
  $errors = [];
  // Discord first: if the email then fails, the error is posted after the list.
  if ($useDiscord) {
    $err = discord_block_digest($blocks, $since);
    if ($err === null) $sentTo[] = 'Discord';
    else {
      $errors[] = "Discord: $err";
      log_event('discord_failed', mb_substr("Block alert: $err", 0, 255), ['id' => null, 'username' => null]);
    }
  }
  if ($useEmail) {
    try {
      if (!$to) throw new MailError('There\'s no one to send it to. Set BLOCK_ALERT_TO, or give an admin an email address.');
      if (!mail_account_ok($account)) throw new MailError('The alerts email account isn\'t set up (ALERT_FROM and ALERT_SMTP_HOST).');
      $subject = '[' . (setting('PRED_LEAGUE_TITLE') ?: 'Prediction League') . '] ' . (count($blocks) === 1 ? '1 address blocked' : count($blocks) . ' addresses blocked');
      $text = block_digest_text($blocks, $since);
      foreach ($to as $addr) send_mail($addr, $subject, $text, $account);
      $sentTo = array_merge($sentTo, $to);
    } catch (MailError $e) {
      $errors[] = $e->getMessage();
      log_event('mail_failed', mb_substr('Block alert email: ' . $e->getMessage(), 0, 255), ['id' => null, 'username' => null]);
    }
  }
  if (!$sentTo) {
    // Nothing got through: put the start back so the same blocks go in the next attempt, an hour from now.
    q('UPDATE ' . t('config') . ' SET value = ? WHERE lid = ? AND param = ? AND value = ?', [$since, lid(), 'BLOCK_ALERT_LAST', $now]);
    set_alert_state('BLOCK_ALERT_TRIED', $now);
    return ['status' => 'failed', 'error' => implode(' ', $errors), 'count' => count($blocks)];
  }
  set_alert_state('BLOCK_ALERT_TRIED', '');
  log_event('block_alert', 'Sent the list of ' . count($blocks) . ' newly blocked address' . (count($blocks) === 1 ? '' : 'es') . ' to ' . implode(', ', $sentTo)
    . ($errors ? ' (' . mb_substr(implode(' ', $errors), 0, 120) . ')' : ''), ['id' => null, 'username' => null]);
  return ['status' => 'sent', 'count' => count($blocks), 'to' => $sentTo, 'errors' => $errors];
}

/** Called on every API request: if a list is due, send it after the reply has gone. */
function schedule_block_digest(): void {
  try {
    if (!setting_bool('BLOCK_ALERT_ON')) return;
    if ((int)date('G') < max(0, min(23, (int)setting('BLOCK_ALERT_HOUR')))) return;
    $last = alert_state('BLOCK_ALERT_LAST');
    if ($last !== null && substr($last, 0, 10) === date('Y-m-d')) return;
  } catch (Throwable $e) {
    return;
  }
  register_shutdown_function(function () {
    if (function_exists('fastcgi_finish_request')) @fastcgi_finish_request();  // don't keep the visitor waiting
    try {
      send_block_digest();
    } catch (Throwable $e) {
      error_log('Block alert email: ' . $e->getMessage());
    }
  });
}

function route_admin_alerttest(): array {
  $u = require_admin();
  $to = alert_recipients() ?: [$u['email']];
  try {
    foreach ($to as $addr) {
      send_mail((string)$addr, 'Test block alert from ' . (setting('PRED_LEAGUE_TITLE') ?: 'your prediction league'),
        "This is a test from the block alerts email account.\n\nIf you're reading this, the daily list of blocked addresses can be sent.\n",
        mail_account_alerts());
    }
  } catch (MailError $e) {
    log_event('mail_failed', mb_substr('Test block alert: ' . $e->getMessage(), 0, 255));
    fail(502, $e->getMessage());
  }
  log_event('mail_test', 'Sent a test block alert to ' . implode(', ', $to));
  return ['sent_to' => $to];
}

function route_admin_alertsend(): array {
  require_admin();
  $r = send_block_digest(true);
  if ($r['status'] === 'failed') fail(502, $r['error']);
  return $r + ['last' => alert_state('BLOCK_ALERT_LAST')];
}
