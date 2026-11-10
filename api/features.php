<?php
/*
 * Prediction League API - messaging, player icons, password reset,
 * activity log, email test, backup/restore and CSV import/export.
 * Loaded by api/index.php.
 */

declare(strict_types=1);

if (!defined('PL_APP')) {
  http_response_code(403);
  exit;
}

function feature_routes(): array {
  return [
    'players'           => ['GET',  'route_players'],
    'prefs.save'        => ['POST', 'route_prefs_save'],
    'matchday'          => ['GET',  'route_matchday'],
    'admin.results'     => ['POST', 'route_admin_results'],
    'admin.competitions'      => ['GET',  'route_admin_competitions'],
    'admin.competition'       => ['POST', 'route_admin_competition'],
    'admin.competitiondelete' => ['POST', 'route_admin_competition_delete'],
    'admin.competitionorder'  => ['POST', 'route_admin_competition_order'],
    'messages'          => ['GET',  'route_messages'],
    'messages.count'    => ['GET',  'route_messages_count'],
    'message'           => ['GET',  'route_message'],
    'message.send'      => ['POST', 'route_message_send'],
    'message.delete'    => ['POST', 'route_message_delete'],
    'icon.upload'       => ['POST', 'route_icon_upload'],
    'icon.remove'       => ['POST', 'route_icon_remove'],
    'reset.request'     => ['POST', 'route_reset_request'],
    'reset.check'       => ['GET',  'route_reset_check'],
    'reset.confirm'     => ['POST', 'route_reset_confirm'],
    'admin.resetlink'   => ['POST', 'route_admin_resetlink'],
    'admin.log'         => ['GET',  'route_admin_log'],
    'admin.testmail'    => ['POST', 'route_admin_testmail'],
    'admin.export'      => ['GET',  'route_admin_export'],
    'admin.exportcsv'   => ['GET',  'route_admin_exportcsv'],
    'admin.restore'     => ['POST', 'route_admin_restore'],
    'admin.importfixtures' => ['POST', 'route_admin_importfixtures'],
  ];
}

// ---------------------------------------------------------------------
// Shared helpers
// ---------------------------------------------------------------------

/** id => ['id','username','icon'] for the given user ids. */
function user_briefs(array $ids): array {
  $ids = array_values(array_unique(array_map('intval', $ids)));
  $out = [];
  if (!$ids) return $out;
  $in = implode(',', array_fill(0, count($ids), '?'));
  foreach (q('SELECT userid, username, icon FROM ' . t('users') . " WHERE lid = ? AND userid IN ($in)", array_merge([lid()], $ids)) as $u) {
    $out[(int)$u['userid']] = ['id' => (int)$u['userid'], 'username' => clean_text($u['username']), 'icon' => icon_url($u['icon'])];
  }
  return $out;
}

function brief_or_deleted(array $briefs, int $id): array {
  return $briefs[$id] ?? ['id' => null, 'username' => 'Deleted player', 'icon' => null];
}

/** The uploaded file in $field, or a clear error. */
function uploaded_file(string $field, int $maxBytes, string $what): array {
  $f = $_FILES[$field] ?? null;
  if (!$f || !is_array($f) || is_array($f['error'])) fail(422, "Choose a $what to upload.");
  switch ($f['error']) {
    case UPLOAD_ERR_OK: break;
    case UPLOAD_ERR_NO_FILE: fail(422, "Choose a $what to upload.");
    case UPLOAD_ERR_INI_SIZE:
    case UPLOAD_ERR_FORM_SIZE: fail(413, "That $what is bigger than the server allows (" . ini_get('upload_max_filesize') . ').');
    default: fail(500, "The server couldn't receive the $what. Try again.");
  }
  if (!is_uploaded_file($f['tmp_name'])) fail(422, "Choose a $what to upload.");
  if ($f['size'] > $maxBytes) fail(413, "That $what is too big. The limit is " . round($maxBytes / 1048576, 1) . ' MB.');
  return $f;
}

function route_players(): array {
  require_user();
  $players = [];
  foreach (q('SELECT userid, username, icon FROM ' . t('users') . ' WHERE lid = ? ORDER BY username', [lid()]) as $u) {
    $players[] = ['id' => (int)$u['userid'], 'username' => clean_text($u['username']), 'icon' => icon_url($u['icon'])];
  }
  $out = ['players' => $players];
  if (current_user()['is_admin']) {
    $out['email_optin'] = count(email_recipients('allow_email', array_column($players, 'id'), current_user()['id']));
  }
  return $out;
}

// ---------------------------------------------------------------------
// Messaging (plmsgdata, shared with the old script)
// ---------------------------------------------------------------------

function messaging_on(): void {
  if (!setting_bool('USE_MESSAGING')) fail(403, 'Messaging is turned off for this league.');
}

function unread_count(int $uid): int {
  return (int)q('SELECT COUNT(*) AS n FROM ' . t('messages') . " WHERE lid = ? AND receiver = ? AND status = 'new'", [lid(), $uid])->fetch()['n'];
}

function message_out(array $m, array $briefs, int $me, bool $withBody): array {
  $body = (string)($m['body'] ?? '');
  $out = [
    'id' => (int)$m['msgid'],
    'thread' => (int)($m['threadid'] ?: $m['msgid']),
    'subject' => clean_text($m['subject']) ?: '(no subject)',
    'preview' => mb_substr(trim(preg_replace('/\s+/u', ' ', clean_text($body))), 0, 110),
    'sent' => (string)$m['msgtime'],
    'unread' => (int)$m['receiver'] === $me && $m['status'] === 'new',
    'mine' => (int)$m['sender'] === $me,
    'from' => brief_or_deleted($briefs, (int)$m['sender']),
    'to' => brief_or_deleted($briefs, (int)$m['receiver']),
  ];
  if ($withBody) $out['body'] = clean_text($body);
  return $out;
}

/** A message the user may read: sent by them, or received and not deleted. */
function visible_message(int $id, int $me): array {
  $m = q('SELECT * FROM ' . t('messages') . ' WHERE lid = ? AND msgid = ?', [lid(), $id])->fetch();
  if (!$m || !(((int)$m['sender'] === $me) || ((int)$m['receiver'] === $me && $m['status'] !== 'deleted'))) {
    fail(404, 'That message doesn\'t exist or was deleted.');
  }
  return $m;
}

function route_messages_count(): array {
  $u = current_user();
  if (!$u || !setting_bool('USE_MESSAGING')) return ['unread' => 0];
  return ['unread' => unread_count($u['id'])];
}

function route_messages(): array {
  $u = require_user();
  messaging_on();
  $box = ($_GET['box'] ?? '') === 'sent' ? 'sent' : 'inbox';
  $rows = $box === 'sent'
    ? q('SELECT * FROM ' . t('messages') . ' WHERE lid = ? AND sender = ? ORDER BY msgtime DESC, msgid DESC LIMIT 300', [lid(), $u['id']])->fetchAll()
    : q('SELECT * FROM ' . t('messages') . " WHERE lid = ? AND receiver = ? AND (status IS NULL OR status <> 'deleted') ORDER BY msgtime DESC, msgid DESC LIMIT 300", [lid(), $u['id']])->fetchAll();
  $briefs = user_briefs(array_merge(array_column($rows, 'sender'), array_column($rows, 'receiver')));
  return [
    'box' => $box,
    'messages' => array_map(fn($m) => message_out($m, $briefs, $u['id'], false), $rows),
    'unread' => unread_count($u['id']),
  ];
}

function route_message(): array {
  $u = require_user();
  messaging_on();
  $m = visible_message((int)($_GET['id'] ?? 0), $u['id']);
  if ((int)$m['receiver'] === $u['id'] && $m['status'] === 'new') {
    q('UPDATE ' . t('messages') . " SET status = 'read' WHERE lid = ? AND msgid = ?", [lid(), (int)$m['msgid']]);
    $m['status'] = 'read';
  }
  $other = (int)$m['sender'] === $u['id'] ? (int)$m['receiver'] : (int)$m['sender'];
  $thread = [$m];
  if (!empty($m['threadid'])) {
    $thread = q('SELECT * FROM ' . t('messages') . ' WHERE lid = ? AND threadid = ?
                 AND ((sender = ? AND receiver = ?) OR (sender = ? AND receiver = ?)) ORDER BY msgtime, msgid',
      [lid(), (int)$m['threadid'], $u['id'], $other, $other, $u['id']])->fetchAll();
    $thread = array_values(array_filter($thread, fn($t) => (int)$t['sender'] === $u['id'] || $t['status'] !== 'deleted'));
    // Opening a conversation marks everything in it as read.
    q('UPDATE ' . t('messages') . " SET status = 'read' WHERE lid = ? AND threadid = ? AND receiver = ? AND status = 'new'",
      [lid(), (int)$m['threadid'], $u['id']]);
  }
  $briefs = user_briefs([$u['id'], $other]);
  return [
    'message' => message_out($m, $briefs, $u['id'], true),
    'thread' => array_map(fn($t) => message_out($t, $briefs, $u['id'], true), $thread),
    'other' => brief_or_deleted($briefs, $other),
    'unread' => unread_count($u['id']),
  ];
}

function route_message_send(): array {
  $u = require_user();
  messaging_on();
  $b = body();
  $recent = (int)q('SELECT COUNT(DISTINCT msgtime) AS n FROM ' . t('messages') . ' WHERE lid = ? AND sender = ? AND msgtime >= ?',
    [lid(), $u['id'], date('Y-m-d H:i:s', time() - 600)])->fetch()['n'];
  if ($recent >= 20) fail(429, 'You\'ve sent a lot of messages in the last few minutes. Wait a little and try again.');

  $text = trim((string)($b['body'] ?? ''));
  if ($text === '') fail(422, 'Write a message before sending.');
  if (mb_strlen($text) > 5000) fail(422, 'Messages must be 5,000 characters or fewer.');
  $subject = trim(preg_replace('/\s+/u', ' ', (string)($b['subject'] ?? '')));

  $threadId = null;
  $prev = null;
  $receivers = [];
  if (!empty($b['reply_to'])) {
    $orig = visible_message((int)$b['reply_to'], $u['id']);
    $receivers = [(int)$orig['sender'] === $u['id'] ? (int)$orig['receiver'] : (int)$orig['sender']];
    $threadId = (int)($orig['threadid'] ?: $orig['msgid']);
    $prev = (int)$orig['msgid'];
    if ($subject === '') $subject = 'Re: ' . preg_replace('/^(Re:\s*)+/i', '', clean_text($orig['subject']));
  } elseif (!empty($b['to_all'])) {
    if (!$u['is_admin']) fail(403, 'Only admins can message every player.');
    $receivers = array_map('intval', q('SELECT userid FROM ' . t('users') . ' WHERE lid = ? AND userid <> ?', [lid(), $u['id']])->fetchAll(PDO::FETCH_COLUMN));
    if (!$receivers) fail(422, 'There are no other players to message yet.');
  } else {
    $to = (int)($b['to'] ?? 0);
    if ($to === $u['id']) fail(422, 'You can\'t send a message to yourself.');
    if (!q('SELECT 1 FROM ' . t('users') . ' WHERE lid = ? AND userid = ?', [lid(), $to])->fetch()) fail(422, 'Choose who to send the message to.');
    $receivers = [$to];
  }
  if ($subject === '') fail(422, 'Add a subject.');
  if (mb_strlen($subject) > 120) fail(422, 'Subjects must be 120 characters or fewer.');
  foreach ($receivers as $r) {
    if (!q('SELECT 1 FROM ' . t('users') . ' WHERE lid = ? AND userid = ?', [lid(), $r])->fetch()) {
      fail(422, 'That player no longer exists.');
    }
  }

  $pdo = db();
  $pdo->beginTransaction();
  $first = null;
  $now = now_str();
  foreach ($receivers as $r) {
    q('INSERT INTO ' . t('messages') . ' (lid, threadid, prevmsg, sender, receiver, subject, body, status, msgtime) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
      [lid(), $threadId, $prev, $u['id'], $r, $subject, $text, 'new', $now]);
    $id = (int)$pdo->lastInsertId();
    if ($threadId === null || !empty($b['to_all'])) {
      q('UPDATE ' . t('messages') . ' SET threadid = ? WHERE lid = ? AND msgid = ?', [$id, lid(), $id]);
    }
    $first = $first ?? $id;
  }
  $pdo->commit();

  // Email: the full message to opted-in players when an admin asks, or a
  // short notification to players who want one for private messages.
  $emailed = 0;
  $title = setting('PRED_LEAGUE_TITLE') ?: 'Prediction League';
  if (mail_configured()) {
    if (!empty($b['to_all']) && !empty($b['email_too'])) {
      foreach (email_recipients('allow_email', $receivers, $u['id']) as $r) {
        $emailed += try_mail($r['email'], $subject, $text . "\n\n--\nSent by {$u['username']} to every player in $title.\n"
          . 'You get these because you allow emails from the league admin. To stop them, untick that on your account page: ' . site_url() . "#/account\n");
      }
    } elseif (empty($b['to_all'])) {
      foreach (email_recipients('notify_messages', $receivers, $u['id']) as $r) {
        $emailed += try_mail($r['email'], "New message from {$u['username']}: $subject",
          "{$u['username']} sent you a private message in $title:\n\n  $subject\n\nRead it and reply here:\n" . site_url() . "#/message/$first\n\n"
          . 'To stop these emails, untick "Email me when I get a message" on your account page: ' . site_url() . "#/account\n");
      }
    }
  }
  if (!empty($b['to_all'])) {
    log_event('message_all', 'Messaged ' . count($receivers) . ' players' . ($emailed ? " (emailed $emailed)" : '') . ': ' . mb_substr($subject, 0, 120));
  }
  return ['sent' => count($receivers), 'id' => $first, 'emailed' => $emailed];
}

function route_message_delete(): array {
  $u = require_user();
  messaging_on();
  $id = (int)(body()['id'] ?? 0);
  $m = visible_message($id, $u['id']);
  if ((int)$m['receiver'] !== $u['id']) fail(422, 'You can only delete messages you received.');
  q('UPDATE ' . t('messages') . " SET status = 'deleted' WHERE lid = ? AND msgid = ?", [lid(), $id]);
  return ['deleted' => $id, 'unread' => unread_count($u['id'])];
}

/** Players among $ids (not $exclude) who opted in to $pref and have a valid email. */
function email_recipients(string $pref, array $ids, int $exclude): array {
  if (!$ids) return [];
  $prefs = prefs_map($ids);
  $in = implode(',', array_fill(0, count($ids), '?'));
  $out = [];
  foreach (q('SELECT userid, email FROM ' . t('users') . " WHERE lid = ? AND userid IN ($in)", array_merge([lid()], array_map('intval', $ids))) as $row) {
    $id = (int)$row['userid'];
    if ($id !== $exclude && !empty($prefs[$id][$pref]) && filter_var((string)$row['email'], FILTER_VALIDATE_EMAIL)) {
      $out[] = ['id' => $id, 'email' => (string)$row['email']];
    }
  }
  return $out;
}

/** Send, logging (not throwing) failures. Returns 1 if sent. */
function try_mail(string $to, string $subject, string $text): int {
  try {
    send_mail($to, $subject, $text);
    return 1;
  } catch (MailError $e) {
    log_event('mail_failed', "To $to: " . mb_substr($e->getMessage(), 0, 180));
    return 0;
  }
}

function route_prefs_save(): array {
  $u = require_user();
  $b = body();
  $old = user_prefs($u['id']);
  $allow = array_key_exists('allow_email', $b) ? (!empty($b['allow_email']) ? 1 : 0) : (int)$old['allow_email'];
  $notify = array_key_exists('notify_messages', $b) ? (!empty($b['notify_messages']) ? 1 : 0) : (int)$old['notify_messages'];
  $auto = array_key_exists('auto_predict', $b) ? (!empty($b['auto_predict']) ? 1 : 0) : (int)$old['auto_predict'];
  if ($auto && !$old['auto_predict'] && !setting_bool('AUTOPRED_ON')) fail(403, 'Automatic predictions are turned off for this league.');
  // Only fixtures whose predictions close after switching on get automatic predictions.
  $since = $auto ? ($old['auto_predict'] ? $old['auto_since'] : now_str()) : null;
  with_table('prefs', function () use ($u, $allow, $notify, $auto, $since) {
    q('DELETE FROM ' . xt('prefs') . ' WHERE lid = ? AND userid = ?', [lid(), $u['id']]);
    q('INSERT INTO ' . xt('prefs') . ' (lid, userid, allow_email, notify_messages, auto_predict, auto_since) VALUES (?, ?, ?, ?, ?, ?)',
      [lid(), $u['id'], $allow, $notify, $auto, $since]);
  });
  log_event('prefs', 'Email from admin ' . ($allow ? 'on' : 'off') . ', message emails ' . ($notify ? 'on' : 'off')
    . ', automatic predictions ' . ($auto ? 'on' : 'off'));
  return ['prefs' => user_prefs($u['id'])];
}

// ---------------------------------------------------------------------
// Matchday: everyone's predictions for all of one day's matches
// ---------------------------------------------------------------------

function route_matchday(): array {
  $days = q('SELECT DISTINCT substr(matchdate, 1, 10) AS d FROM ' . t('matches') . ' WHERE lid = ? ORDER BY d', [lid()])->fetchAll(PDO::FETCH_COLUMN);
  if (!$days) return ['date' => null, 'days' => [], 'matches' => [], 'players' => []];
  $date = (string)($_GET['date'] ?? '');
  if (!in_array($date, $days, true)) {
    // Default: the latest day that has kicked off, otherwise the first upcoming day.
    $today = date('Y-m-d');
    $past = array_values(array_filter($days, fn($d) => $d <= $today));
    $date = $past ? end($past) : $days[0];
  }
  $matches = q('SELECT * FROM ' . t('matches') . ' WHERE lid = ? AND matchdate >= ? AND matchdate <= ? ORDER BY matchdate, matchid',
    [lid(), "$date 00:00:00", "$date 23:59:59"])->fetchAll();
  $byId = [];
  foreach ($matches as $m) $byId[(int)$m['matchid']] = $m;

  $preds = [];
  if ($byId) {
    $in = implode(',', array_fill(0, count($byId), '?'));
    foreach (q('SELECT userid, matchid, homescore, awayscore, isauto FROM ' . t('predictions') . " WHERE lid = ? AND matchid IN ($in)",
               array_merge([lid()], array_keys($byId))) as $p) {
      if ($p['homescore'] === null || $p['awayscore'] === null) continue;
      $preds[(int)$p['userid']][(int)$p['matchid']] = [(int)$p['homescore'], (int)$p['awayscore'], $p['isauto'] === 'Y'];
    }
  }

  $players = [];
  foreach (q('SELECT userid, username, icon FROM ' . t('users') . ' WHERE lid = ? ORDER BY username', [lid()]) as $u) {
    $uid = (int)$u['userid'];
    if (empty($preds[$uid])) continue;
    $cells = [];
    $total = 0;
    foreach ($byId as $mid => $m) {
      $p = $preds[$uid][$mid] ?? null;
      if (!$p) { $cells[$mid] = null; continue; }
      if (!can_see_prediction($m, $uid)) { $cells[$mid] = ['hidden' => true]; continue; }
      $cell = ['home' => $p[0], 'away' => $p[1], 'auto' => $p[2], 'points' => null, 'outcome' => null];
      if ($m['homescore'] !== null && $m['awayscore'] !== null) {
        $cell['points'] = points_for($p[0], $p[1], (int)$m['homescore'], (int)$m['awayscore']);
        $cell['outcome'] = outcome_for($p[0], $p[1], (int)$m['homescore'], (int)$m['awayscore']);
        $total += $cell['points'];
      }
      $cells[$mid] = $cell;
    }
    $players[] = ['id' => $uid, 'username' => clean_text($u['username']), 'icon' => icon_url($u['icon']),
                  'cells' => (object)$cells, 'points' => $total];
  }
  usort($players, fn($a, $b) => ($b['points'] <=> $a['points']) ?: strcasecmp($a['username'], $b['username']));

  $i = array_search($date, $days, true);
  return [
    'date' => $date,
    'prev' => $i > 0 ? $days[$i - 1] : null,
    'next' => $i < count($days) - 1 ? $days[$i + 1] : null,
    'matches' => array_map('match_out', array_values($matches)),
    'players' => $players,
  ];
}

// ---------------------------------------------------------------------
// Admin: several results at once
// ---------------------------------------------------------------------

function route_admin_results(): array {
  require_admin();
  $items = body()['results'] ?? null;
  if (!is_array($items) || !$items) fail(422, 'There are no results to save.');
  $clean = [];
  foreach ($items as $item) {
    $m = get_match((int)($item['id'] ?? 0));
    $label = clean_text($m['hometeam']) . ' v ' . clean_text($m['awayteam']);
    try {
      $home = score_in($item['home'] ?? null);
      $away = score_in($item['away'] ?? null);
    } catch (ApiError $e) {
      fail(422, "$label: " . $e->getMessage());
    }
    if (($home === null) !== ($away === null)) fail(422, "$label: enter both scores, or leave both empty to clear the result.");
    if ($home !== null && (string)$m['matchdate'] > now_str()) fail(422, "$label hasn't kicked off yet.");
    $clean[] = [(int)$m['matchid'], $home, $away, $label];
  }
  $pdo = db();
  $pdo->beginTransaction();
  foreach ($clean as [$id, $home, $away]) {
    q('UPDATE ' . t('matches') . ' SET homescore = ?, awayscore = ? WHERE lid = ? AND matchid = ?', [$home, $away, lid(), $id]);
  }
  $pdo->commit();
  save_standings(compute_standings());
  log_event('result', mb_substr('Saved ' . count($clean) . ' results: ' . implode('; ', array_map(
    fn($c) => $c[3] . ' ' . ($c[1] === null ? 'cleared' : "$c[1]-$c[2]"), $clean)), 0, 255));
  return ['saved' => count($clean)];
}

// ---------------------------------------------------------------------
// Player icons
// ---------------------------------------------------------------------

function delete_user_icon(int $uid): void {
  $row = q('SELECT icon FROM ' . t('users') . ' WHERE lid = ? AND userid = ?', [lid(), $uid])->fetch();
  $url = $row ? icon_url($row['icon']) : null;
  if ($url) {
    $file = icon_dir() . '/' . basename($url);
    if (is_file($file)) @unlink($file);
  }
}

function route_icon_upload(): array {
  $u = require_user();
  if (!setting_bool('UPLOADICONS') && !$u['is_admin']) fail(403, 'Picture uploads are turned off for this league.');
  if (!function_exists('imagecreatefromstring')) fail(500, 'The server can\'t process pictures (the PHP GD extension is missing).');
  $f = uploaded_file('icon', 5 * 1048576, 'picture');

  $info = @getimagesize($f['tmp_name']);
  $allowed = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_WEBP];
  if (!$info || !in_array($info[2], $allowed, true)) fail(422, 'Upload a JPEG, PNG, GIF or WebP picture.');
  [$w, $h] = $info;
  if ($w < 16 || $h < 16) fail(422, 'That picture is too small. Use one at least 16 pixels wide.');
  if ($w * $h > 40000000) fail(422, 'That picture is too large to process. Use one under 40 megapixels.');

  $src = @imagecreatefromstring((string)file_get_contents($f['tmp_name']));
  if (!$src) fail(422, 'That picture couldn\'t be read. Try saving it as a JPEG or PNG.');

  // Phone photos store their rotation separately.
  if ($info[2] === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
    $exif = @exif_read_data($f['tmp_name']);
    $rotate = [3 => 180, 6 => -90, 8 => 90][(int)($exif['Orientation'] ?? 1)] ?? 0;
    if ($rotate) {
      $rotated = imagerotate($src, $rotate, 0);
      if ($rotated) { imagedestroy($src); $src = $rotated; $w = imagesx($src); $h = imagesy($src); }
    }
  }

  // Centre-crop to a square and re-encode as a 160px PNG (drops anything hidden in the file).
  $side = min($w, $h);
  $size = 160;
  $dst = imagecreatetruecolor($size, $size);
  imagealphablending($dst, false);
  imagesavealpha($dst, true);
  imagefill($dst, 0, 0, imagecolorallocatealpha($dst, 0, 0, 0, 127));
  imagecopyresampled($dst, $src, 0, 0, (int)(($w - $side) / 2), (int)(($h - $side) / 2), $size, $size, $side, $side);
  imagedestroy($src);

  $dir = icon_dir();
  if (!is_dir($dir)) @mkdir($dir, 0755, true);
  if (!is_dir($dir) || !is_writable($dir)) {
    imagedestroy($dst);
    fail(500, 'The server can\'t save pictures yet. The site owner needs to make the uploads/icons folder writable.');
  }
  $name = 'u' . $u['id'] . '-' . bin2hex(random_bytes(8)) . '.png';
  if (!imagepng($dst, "$dir/$name", 6)) {
    imagedestroy($dst);
    fail(500, 'The picture couldn\'t be saved. Try again.');
  }
  imagedestroy($dst);
  @chmod("$dir/$name", 0644);

  delete_user_icon($u['id']);
  q('UPDATE ' . t('users') . ' SET icon = ? WHERE lid = ? AND userid = ?', [ICON_DIR . '/' . $name, lid(), $u['id']]);
  log_event('icon', 'Uploaded a new picture');
  return ['user' => current_user(true)];
}

function route_icon_remove(): array {
  $u = require_user();
  delete_user_icon($u['id']);
  q('UPDATE ' . t('users') . ' SET icon = ? WHERE lid = ? AND userid = ?', [setting('DEF_ICON') ?: 'default.gif', lid(), $u['id']]);
  return ['user' => current_user(true)];
}

// ---------------------------------------------------------------------
// Password reset
// ---------------------------------------------------------------------

function create_reset_token(int $uid, int $ttl): string {
  $token = bin2hex(random_bytes(32));
  with_table('resets', function () use ($uid, $ttl, $token) {
    q('DELETE FROM ' . xt('resets') . ' WHERE lid = ? AND expires < ?', [lid(), now_str()]);
    q('INSERT INTO ' . xt('resets') . ' (token_hash, lid, userid, created, expires, used) VALUES (?, ?, ?, ?, ?, 0)',
      [hash('sha256', $token), lid(), $uid, now_str(), date('Y-m-d H:i:s', time() + $ttl)]);
  });
  return $token;
}

function reset_link(string $token): string {
  return site_url() . '#/reset/' . $token;
}

function reset_email(string $username, string $link, string $validFor): string {
  $title = setting('PRED_LEAGUE_TITLE') ?: 'Prediction League';
  return "Hi $username,\n\n"
    . "Someone asked to reset your password for $title. To choose a new password, open this link within $validFor:\n\n"
    . "$link\n\n"
    . "If you didn't ask for this, you can ignore this email. Your password stays the same.\n";
}

/** The unused, unexpired reset row for a token, or null. */
function find_reset(string $token): ?array {
  if (!preg_match('/\A[a-f0-9]{64}\z/', $token)) return null;
  $row = with_table('resets', fn() => q('SELECT * FROM ' . xt('resets') . ' WHERE token_hash = ? AND lid = ? AND used = 0 AND expires >= ?',
    [hash('sha256', $token), lid(), now_str()])->fetch());
  return $row ?: null;
}

function route_reset_request(): array {
  $generic = ['message' => 'If that matches an account with an email address, we\'ve sent a link to reset the password. It works for 1 hour.'];
  if (!bot_check('reset', body(), null)) return $generic;
  $login = str_in(body(), 'login', 60);
  if (recent_events('reset_request', 3600) >= 5) {
    fail(429, 'Too many reset requests from your connection. Try again in an hour.');
  }
  if (!mail_configured()) {
    fail(503, 'Password reset by email isn\'t available for this league. Ask the league admin for a reset link.');
  }
  log_event('reset_request', 'Asked for a reset for "' . mb_substr($login, 0, 60) . '"');

  $users = q('SELECT userid, username, email FROM ' . t('users') . ' WHERE lid = ? AND (username = ? OR email = ?) LIMIT 5',
    [lid(), $login, $login])->fetchAll();
  foreach ($users as $u) {
    if (!filter_var((string)$u['email'], FILTER_VALIDATE_EMAIL)) continue;
    $recent = (int)with_table('resets', fn() => q('SELECT COUNT(*) AS n FROM ' . xt('resets') . ' WHERE lid = ? AND userid = ? AND created >= ?',
      [lid(), (int)$u['userid'], date('Y-m-d H:i:s', time() - 120)])->fetch()['n']);
    if ($recent > 0) continue;
    $token = create_reset_token((int)$u['userid'], 3600);
    try {
      send_mail((string)$u['email'], 'Reset your password', reset_email(clean_text($u['username']), reset_link($token), '1 hour'));
    } catch (MailError $e) {
      log_event('mail_failed', 'Reset email to player ' . (int)$u['userid'] . ': ' . mb_substr($e->getMessage(), 0, 180));
    }
  }
  // Same answer whether or not an account matched.
  return $generic;
}

function route_reset_check(): array {
  $row = find_reset((string)($_GET['token'] ?? ''));
  if (!$row) return ['valid' => false];
  $u = q('SELECT username FROM ' . t('users') . ' WHERE lid = ? AND userid = ?', [lid(), (int)$row['userid']])->fetch();
  return ['valid' => (bool)$u, 'username' => $u ? clean_text($u['username']) : null];
}

function route_reset_confirm(): array {
  $b = body();
  $row = find_reset((string)($b['token'] ?? ''));
  if (!$row) fail(410, 'This reset link has expired or was already used. Ask for a new one.');
  $password = (string)($b['password'] ?? '');
  if (mb_strlen($password) < 8) fail(422, 'Passwords must be at least 8 characters.');
  $uid = (int)$row['userid'];
  $user = q('SELECT userid, username FROM ' . t('users') . ' WHERE lid = ? AND userid = ?', [lid(), $uid])->fetch();
  if (!$user) fail(410, 'This reset link is for an account that no longer exists.');

  q('UPDATE ' . t('users') . ' SET password = ? WHERE lid = ? AND userid = ?', [make_password_hash($password), lid(), $uid]);
  with_table('resets', fn() => q('UPDATE ' . xt('resets') . ' SET used = 1 WHERE lid = ? AND userid = ?', [lid(), $uid]));
  session_regenerate_id(true);
  $_SESSION['uid'] = $uid;
  log_event('password_reset', 'Set a new password with a reset link', ['id' => $uid, 'username' => clean_text($user['username'])]);
  return route_session();
}

function route_admin_resetlink(): array {
  require_admin();
  $b = body();
  $id = (int)($b['id'] ?? 0);
  $u = q('SELECT userid, username, email FROM ' . t('users') . ' WHERE lid = ? AND userid = ?', [lid(), $id])->fetch();
  if (!$u) fail(404, 'That player doesn\'t exist.');
  $token = create_reset_token($id, 72 * 3600);
  $link = reset_link($token);
  $emailed = false;
  if (!empty($b['send'])) {
    if (!filter_var((string)$u['email'], FILTER_VALIDATE_EMAIL)) fail(422, 'This player has no valid email address. Copy the link and send it another way.');
    try {
      send_mail((string)$u['email'], 'Reset your password', reset_email(clean_text($u['username']), $link, '3 days'));
      $emailed = true;
    } catch (MailError $e) {
      fail(502, $e->getMessage());
    }
  }
  log_event('reset_link', ($emailed ? 'Emailed' : 'Created') . ' a reset link for ' . clean_text($u['username']));
  return ['link' => $link, 'expires' => date('Y-m-d H:i', time() + 72 * 3600), 'emailed' => $emailed, 'email' => (string)$u['email']];
}

// ---------------------------------------------------------------------
// Activity log and email test
// ---------------------------------------------------------------------

function route_admin_log(): array {
  require_admin();
  $page = max(1, (int)($_GET['page'] ?? 1));
  $per = 50;
  $where = 'lid = ?';
  $params = [lid()];
  $action = (string)($_GET['action'] ?? '');
  if ($action !== '' && preg_match('/\A[a-z_]{1,40}\z/', $action)) {
    $where .= ' AND action = ?';
    $params[] = $action;
  }
  $search = trim((string)($_GET['q'] ?? ''));
  if ($search !== '') {
    $like = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], mb_substr($search, 0, 60)) . '%';
    $where .= " AND (username LIKE ? ESCAPE '\\' OR ip LIKE ? ESCAPE '\\' OR detail LIKE ? ESCAPE '\\')";
    array_push($params, $like, $like, $like);
  }
  return with_table('log', function () use ($where, $params, $page, $per) {
    $total = (int)q('SELECT COUNT(*) AS n FROM ' . xt('log') . " WHERE $where", $params)->fetch()['n'];
    $rows = q('SELECT created, username, ip, action, detail FROM ' . xt('log') . " WHERE $where ORDER BY created DESC, id DESC LIMIT $per OFFSET " . (($page - 1) * $per), $params)->fetchAll();
    $actions = q('SELECT DISTINCT action FROM ' . xt('log') . ' WHERE lid = ? ORDER BY action', [lid()])->fetchAll(PDO::FETCH_COLUMN);
    return ['rows' => $rows, 'total' => $total, 'page' => $page, 'pages' => max(1, (int)ceil($total / $per)), 'actions' => $actions];
  });
}

function route_admin_testmail(): array {
  $u = require_admin();
  $to = trim((string)(body()['to'] ?? '')) ?: $u['email'];
  try {
    send_mail($to, 'Test email from ' . (setting('PRED_LEAGUE_TITLE') ?: 'your prediction league'),
      "This is a test email from your prediction league.\n\nIf you're reading this, email is set up correctly.\n");
  } catch (MailError $e) {
    log_event('mail_failed', 'Test email: ' . mb_substr($e->getMessage(), 0, 200));
    fail(502, $e->getMessage());
  }
  log_event('mail_test', "Sent a test email to $to");
  return ['sent_to' => $to];
}

// ---------------------------------------------------------------------
// Backup, restore and CSV
// ---------------------------------------------------------------------

function backup_columns(): array {
  return [
    'users' => ['userid', 'username', 'password', 'email', 'icon', 'lang', 'usertype', 'dflths', 'dfltas', 'since', 'auto'],
    'matches' => ['matchid', 'matchdate', 'hometeam', 'awayteam', 'homescore', 'awayscore'],
    'predictions' => ['userid', 'matchid', 'predtime', 'homescore', 'awayscore', 'isauto'],
    'config' => ['grp', 'param', 'value', 'descr', 'ro'],
    'messages' => ['msgid', 'threadid', 'prevmsg', 'sender', 'receiver', 'subject', 'body', 'status', 'msgtime'],
  ];
}

function route_admin_export(): array {
  require_admin();
  $tables = [];
  foreach (backup_columns() as $name => $cols) {
    $sel = implode(', ', $cols);
    try {
      $tables[$name] = q("SELECT $sel FROM " . t($name) . ' WHERE lid = ?', [lid()])->fetchAll();
    } catch (PDOException $e) {
      if ($name !== 'messages') throw $e;
      $tables[$name] = [];
    }
  }
  $tables['competitions'] = with_table('comps', fn() => q('SELECT compid, name, short_name, position FROM ' . xt('comps') . ' WHERE lid = ?', [lid()])->fetchAll());
  $tables['fixture_competitions'] = with_table('matchcomp', fn() => q('SELECT matchid, compid FROM ' . xt('matchcomp') . ' WHERE lid = ?', [lid()])->fetchAll());
  ensure_prefs_columns();
  $tables['prefs'] = with_table('prefs', fn() => q('SELECT userid, allow_email, notify_messages, auto_predict, auto_since FROM ' . xt('prefs') . ' WHERE lid = ?', [lid()])->fetchAll());
  ensure_league_tables();
  $tables['follows'] = q('SELECT userid, compid FROM ' . xt('follows') . ' WHERE lid = ?', [lid()])->fetchAll();
  $tables['leagues'] = q('SELECT leagueid, name, owner, code, all_comps, pts_exact, pts_result, start_at, created FROM ' . xt('leagues') . ' WHERE lid = ?', [lid()])->fetchAll();
  $inLid = ' WHERE leagueid IN (SELECT leagueid FROM ' . xt('leagues') . ' WHERE lid = ?)';
  $tables['league_competitions'] = q('SELECT leagueid, compid FROM ' . xt('leaguecomps') . $inLid, [lid()])->fetchAll();
  $tables['league_members'] = q('SELECT leagueid, userid, role, joined FROM ' . xt('members') . $inLid, [lid()])->fetchAll();
  $tables['seasons'] = with_table('seasons', fn() => q('SELECT seasonid, name, start_date, end_date, is_current FROM ' . xt('seasons') . ' WHERE lid = ?', [lid()])->fetchAll());
  $tables['teams'] = with_table('teams', fn() => q('SELECT teamid, name, short_name, badge FROM ' . xt('teams') . ' WHERE lid = ?', [lid()])->fetchAll());
  $tables['competition_teams'] = with_table('compteams', fn() => q('SELECT seasonid, compid, teamid FROM ' . xt('compteams') . ' WHERE seasonid IN (SELECT seasonid FROM ' . xt('seasons') . ' WHERE lid = ?)', [lid()])->fetchAll());
  $tables['pages'] = with_table('pages', fn() => q('SELECT pageid, slug, title, body, status, audience, is_home, updated FROM ' . xt('pages') . ' WHERE lid = ?', [lid()])->fetchAll());
  $tables['menu'] = with_table('menu', fn() => q('SELECT position, item FROM ' . xt('menu') . ' WHERE lid = ? ORDER BY position', [lid()])->fetchAll());
  log_event('backup', 'Downloaded a full backup');
  return [
    'filename' => 'prediction-league-backup-' . date('Y-m-d-Hi') . '.json',
    'backup' => [
      'app' => 'prediction-league',
      'format' => 1,
      'exported' => date(DATE_ATOM),
      'title' => setting('PRED_LEAGUE_TITLE'),
      'tables' => $tables,
    ],
  ];
}

function route_admin_restore(): array {
  $me = require_admin();
  if (($_POST['confirm'] ?? '') !== 'RESTORE') fail(422, 'Type RESTORE to confirm.');
  $f = uploaded_file('backup', 20 * 1048576, 'backup file');
  $data = json_decode((string)file_get_contents($f['tmp_name']), true);
  if (!is_array($data) || ($data['app'] ?? '') !== 'prediction-league' || !is_array($data['tables'] ?? null)) {
    fail(422, 'That isn\'t a Prediction League backup file. Use a file from Admin > Data > Download backup.');
  }
  $cols = backup_columns();
  $tables = [];
  foreach ($cols as $name => $want) {
    $rows = $data['tables'][$name] ?? [];
    if (!is_array($rows)) fail(422, "The backup's $name data is damaged.");
    foreach ($rows as $i => $row) {
      if (!is_array($row)) fail(422, "The backup's $name data is damaged (row " . ($i + 1) . ').');
      foreach ($want as $c) {
        if (!array_key_exists($c, $row)) fail(422, "The backup's $name data is missing \"$c\" (row " . ($i + 1) . ').');
        if (is_array($row[$c])) fail(422, "The backup's $name data is damaged (row " . ($i + 1) . ').');
      }
    }
    $tables[$name] = $rows;
  }
  if (!$tables['users']) fail(422, 'That backup has no players, so it can\'t be restored.');
  $hasAdmin = false;
  foreach ($tables['users'] as $u) if ((int)$u['usertype'] >= USER_ADMIN) $hasAdmin = true;
  if (!$hasAdmin) fail(422, 'That backup has no admin account. Restoring it would lock everyone out of the admin pages.');

  $prefs = [];
  foreach (is_array($data['tables']['prefs'] ?? null) ? $data['tables']['prefs'] : [] as $p) {
    if (is_array($p) && isset($p['userid'])) {
      $since = isset($p['auto_since']) && preg_match('/\A\d{4}-\d\d-\d\d \d\d:\d\d:\d\d\z/', (string)$p['auto_since']) ? (string)$p['auto_since'] : null;
      $prefs[] = [(int)$p['userid'], empty($p['allow_email']) ? 0 : 1, empty($p['notify_messages']) ? 0 : 1, empty($p['auto_predict']) ? 0 : 1, $since];
    }
  }
  $comps = [];
  foreach (is_array($data['tables']['competitions'] ?? null) ? $data['tables']['competitions'] : [] as $c) {
    if (is_array($c) && isset($c['compid'], $c['name'])) {
      $comps[] = [(int)$c['compid'], mb_substr((string)$c['name'], 0, 40), mb_substr((string)($c['short_name'] ?? ''), 0, 8), (int)($c['position'] ?? 0)];
    }
  }
  $links = [];
  foreach (is_array($data['tables']['fixture_competitions'] ?? null) ? $data['tables']['fixture_competitions'] : [] as $l) {
    if (is_array($l) && isset($l['matchid'], $l['compid'])) $links[] = [(int)$l['matchid'], (int)$l['compid']];
  }
  $bt = fn(string $k) => array_values(array_filter(is_array($data['tables'][$k] ?? null) ? $data['tables'][$k] : [], 'is_array'));
  $follows = array_map(fn($r) => [(int)($r['userid'] ?? 0), (int)($r['compid'] ?? 0)], $bt('follows'));
  $leagues = [];
  foreach ($bt('leagues') as $r) {
    if (!isset($r['leagueid'], $r['name'], $r['owner'], $r['code'])) continue;
    $code = clean_code((string)$r['code']);
    if (strlen($code) !== 8) continue;
    $leagues[] = [(int)$r['leagueid'], mb_substr((string)$r['name'], 0, 60), (int)$r['owner'], $code, empty($r['all_comps']) ? 0 : 1,
                  (int)($r['pts_exact'] ?? 3), (int)($r['pts_result'] ?? 1), $r['start_at'] ?? null, (string)($r['created'] ?? now_str())];
  }
  $leagueIds = array_column($leagues, 0);
  $lcomps = array_values(array_filter(array_map(fn($r) => [(int)($r['leagueid'] ?? 0), (int)($r['compid'] ?? 0)], $bt('league_competitions')), fn($r) => in_array($r[0], $leagueIds, true)));
  $members = array_values(array_filter(array_map(fn($r) => [(int)($r['leagueid'] ?? 0), (int)($r['userid'] ?? 0), ($r['role'] ?? '') === 'owner' ? 'owner' : 'member', (string)($r['joined'] ?? now_str())], $bt('league_members')), fn($r) => in_array($r[0], $leagueIds, true)));
  $pages = [];
  foreach ($bt('pages') as $r) {
    if (!isset($r['pageid'], $r['slug'], $r['title'])) continue;
    $pages[] = [(int)$r['pageid'], mb_substr((string)$r['slug'], 0, 60), mb_substr((string)$r['title'], 0, 100), (string)($r['body'] ?? ''),
                ($r['status'] ?? '') === 'draft' ? 'draft' : 'published', ($r['audience'] ?? '') === 'players' ? 'players' : 'all',
                empty($r['is_home']) ? 0 : 1, (string)($r['updated'] ?? now_str())];
  }
  $seasonRows = [];
  foreach ($bt('seasons') as $r) {
    if (!isset($r['seasonid'], $r['name'], $r['start_date'], $r['end_date'])) continue;
    $seasonRows[] = [(int)$r['seasonid'], mb_substr((string)$r['name'], 0, 30), substr((string)$r['start_date'], 0, 10), substr((string)$r['end_date'], 0, 10), empty($r['is_current']) ? 0 : 1];
  }
  $teamRows = [];
  foreach ($bt('teams') as $r) {
    if (!isset($r['teamid'], $r['name'])) continue;
    $teamRows[] = [(int)$r['teamid'], mb_substr((string)$r['name'], 0, 30), mb_substr((string)($r['short_name'] ?? ''), 0, 5), badge_url($r['badge'] ?? null)];
  }
  $sIds = array_column($seasonRows, 0);
  $ctRows = array_values(array_filter(array_map(fn($r) => [(int)($r['seasonid'] ?? 0), (int)($r['compid'] ?? 0), (int)($r['teamid'] ?? 0)], $bt('competition_teams')), fn($r) => in_array($r[0], $sIds, true)));
  $menuItems = array_values(array_filter(array_map(fn($r) => mb_substr((string)($r['item'] ?? ''), 0, 40), $bt('menu'))));
  // Create the tables before the transaction: MySQL commits implicitly on CREATE TABLE.
  ensure_league_tables();
  with_table('pages', fn() => q('SELECT 1 FROM ' . xt('pages') . ' WHERE 1 = 0'));
  foreach (['seasons', 'teams', 'compteams'] as $tb) with_table($tb, fn() => q('SELECT 1 FROM ' . xt($tb) . ' WHERE 1 = 0'));
  with_table('menu', fn() => q('SELECT 1 FROM ' . xt('menu') . ' WHERE 1 = 0'));
  with_table('prefs', fn() => q('SELECT COUNT(*) FROM ' . xt('prefs')));
  ensure_prefs_columns();
  with_table('comps', fn() => q('SELECT COUNT(*) FROM ' . xt('comps')));
  with_table('matchcomp', fn() => q('SELECT COUNT(*) FROM ' . xt('matchcomp')));

  $pdo = db();
  $pdo->beginTransaction();
  try {
    q('DELETE FROM ' . xt('matchcomp') . ' WHERE lid = ?', [lid()]);
    q('DELETE FROM ' . xt('comps') . ' WHERE lid = ?', [lid()]);
    foreach ($comps as [$cid, $cname, $cshort, $cpos]) {
      q('INSERT INTO ' . xt('comps') . ' (compid, lid, name, short_name, position) VALUES (?, ?, ?, ?, ?)', [$cid, lid(), $cname, $cshort, $cpos]);
    }
    foreach ($links as [$lm, $lc]) {
      q('INSERT INTO ' . xt('matchcomp') . ' (lid, matchid, compid) VALUES (?, ?, ?)', [lid(), $lm, $lc]);
    }
    $inLid = ' WHERE leagueid IN (SELECT leagueid FROM ' . xt('leagues') . ' WHERE lid = ?)';
    q('DELETE FROM ' . xt('members') . $inLid, [lid()]);
    q('DELETE FROM ' . xt('leaguecomps') . $inLid, [lid()]);
    q('DELETE FROM ' . xt('leagues') . ' WHERE lid = ?', [lid()]);
    q('DELETE FROM ' . xt('follows') . ' WHERE lid = ?', [lid()]);
    foreach ($leagues as $l) {
      q('INSERT INTO ' . xt('leagues') . ' (leagueid, lid, name, owner, code, all_comps, pts_exact, pts_result, start_at, created) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
        [$l[0], lid(), ...array_slice($l, 1)]);
    }
    foreach ($lcomps as $r) q('INSERT INTO ' . xt('leaguecomps') . ' (leagueid, compid) VALUES (?, ?)', $r);
    foreach ($members as $r) q('INSERT INTO ' . xt('members') . ' (leagueid, userid, role, joined) VALUES (?, ?, ?, ?)', $r);
    foreach ($follows as $r) q('INSERT INTO ' . xt('follows') . ' (lid, userid, compid) VALUES (?, ?, ?)', [lid(), $r[0], $r[1]]);
    q('DELETE FROM ' . xt('compteams') . ' WHERE seasonid IN (SELECT seasonid FROM ' . xt('seasons') . ' WHERE lid = ?)', [lid()]);
    q('DELETE FROM ' . xt('seasons') . ' WHERE lid = ?', [lid()]);
    q('DELETE FROM ' . xt('teams') . ' WHERE lid = ?', [lid()]);
    foreach ($seasonRows as $r) q('INSERT INTO ' . xt('seasons') . ' (seasonid, lid, name, start_date, end_date, is_current) VALUES (?, ?, ?, ?, ?, ?)', [$r[0], lid(), ...array_slice($r, 1)]);
    foreach ($teamRows as $r) q('INSERT INTO ' . xt('teams') . ' (teamid, lid, name, short_name, badge) VALUES (?, ?, ?, ?, ?)', [$r[0], lid(), ...array_slice($r, 1)]);
    foreach ($ctRows as $r) q('INSERT INTO ' . xt('compteams') . ' (seasonid, compid, teamid) VALUES (?, ?, ?)', $r);
    q('DELETE FROM ' . xt('pages') . ' WHERE lid = ?', [lid()]);
    foreach ($pages as $p) {
      q('INSERT INTO ' . xt('pages') . ' (pageid, lid, slug, title, body, status, audience, is_home, updated) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)', [$p[0], lid(), ...array_slice($p, 1)]);
    }
    q('DELETE FROM ' . xt('menu') . ' WHERE lid = ?', [lid()]);
    foreach ($menuItems as $i => $item) q('INSERT INTO ' . xt('menu') . ' (lid, position, item) VALUES (?, ?, ?)', [lid(), $i, $item]);
    q('DELETE FROM ' . xt('prefs') . ' WHERE lid = ?', [lid()]);
    foreach ($prefs as [$puid, $pa, $pn, $pauto, $psince]) {
      q('INSERT INTO ' . xt('prefs') . ' (lid, userid, allow_email, notify_messages, auto_predict, auto_since) VALUES (?, ?, ?, ?, ?, ?)',
        [lid(), $puid, $pa, $pn, $pauto && $psince ? 1 : 0, $pauto && $psince ? $psince : null]);
    }
    foreach (['predictions', 'messages', 'matches', 'users', 'config'] as $name) {
      q('DELETE FROM ' . t($name) . ' WHERE lid = ?', [lid()]);
    }
    q('DELETE FROM ' . t('standings') . ' WHERE lid = ?', [lid()]);
    foreach ($cols as $name => $want) {
      if (!$tables[$name]) continue;
      $sql = 'INSERT INTO ' . t($name) . ' (lid, ' . implode(', ', $want) . ') VALUES (?' . str_repeat(', ?', count($want)) . ')';
      $ins = $pdo->prepare($sql);
      foreach ($tables[$name] as $row) {
        $ins->execute(array_merge([lid()], array_map(fn($c) => $row[$c] === null ? null : (is_bool($row[$c]) ? (int)$row[$c] : $row[$c]), $want)));
      }
    }
    $pdo->commit();
  } catch (Throwable $e) {
    $pdo->rollBack();
    error_log('Prediction League restore: ' . $e);
    fail(422, 'The backup couldn\'t be restored, so nothing was changed. The database said: ' . $e->getMessage());
  }
  settings(true);
  competitions(true);
  competition_map(true);
  save_standings(compute_standings());

  $counts = array_map('count', $tables);
  log_event('restore', sprintf('Restored a backup from %s: %d players, %d fixtures, %d predictions',
    substr((string)($data['exported'] ?? '?'), 0, 10), $counts['users'], $counts['matches'], $counts['predictions']), $me);
  $stillHere = current_user(true);
  if (!$stillHere) {
    $_SESSION = ['csrf' => $_SESSION['csrf']];
  }
  return ['restored' => $counts, 'logged_out' => !$stillHere];
}

function csv_line(array $cells): string {
  $out = [];
  foreach ($cells as $c) {
    $c = (string)$c;
    // Stop spreadsheet programs treating a cell as a formula.
    if ($c !== '' && strpbrk($c[0], '=+-@') !== false && !is_numeric($c)) $c = "'" . $c;
    $out[] = preg_match('/[",\r\n]/', $c) ? '"' . str_replace('"', '""', $c) . '"' : $c;
  }
  return implode(',', $out) . "\r\n";
}

function route_admin_exportcsv(): array {
  require_admin();
  $what = (string)($_GET['what'] ?? '');
  $rows = [];
  switch ($what) {
    case 'table':
      $rows[] = ['Position', 'Player', 'Played', 'Exact scores', 'Right results', 'For', 'Against', 'Goal difference', 'Points'];
      foreach (compute_standings() as $r) {
        $rows[] = [$r['position'], $r['username'], $r['played'], $r['exact'], $r['result'], $r['for'], $r['against'], $r['diff'], $r['points']];
      }
      break;
    case 'fixtures':
      $rows[] = ['Date', 'Time', 'Competition', 'Home', 'Away', 'Home score', 'Away score'];
      foreach (q('SELECT * FROM ' . t('matches') . ' WHERE lid = ? ORDER BY matchdate, matchid', [lid()]) as $m) {
        $c = match_competition((int)$m['matchid']);
        $rows[] = [substr($m['matchdate'], 0, 10), substr($m['matchdate'], 11, 5), $c['name'] ?? '', clean_text($m['hometeam']), clean_text($m['awayteam']), $m['homescore'], $m['awayscore']];
      }
      break;
    case 'predictions':
      $rows[] = ['Date', 'Time', 'Competition', 'Home', 'Away', 'Player', 'Predicted home', 'Predicted away', 'Home score', 'Away score', 'Points', 'Automatic'];
      $res = q('SELECT m.matchid, m.matchdate, m.hometeam, m.awayteam, m.homescore AS mhs, m.awayscore AS mas, u.username, p.homescore AS phs, p.awayscore AS pas, p.isauto
                FROM ' . t('predictions') . ' p
                JOIN ' . t('matches') . ' m ON m.matchid = p.matchid AND m.lid = p.lid
                JOIN ' . t('users') . ' u ON u.userid = p.userid AND u.lid = p.lid
                WHERE p.lid = ? ORDER BY m.matchdate, m.matchid, u.username', [lid()]);
      foreach ($res as $r) {
        $scored = $r['phs'] !== null && $r['pas'] !== null && $r['mhs'] !== null && $r['mas'] !== null;
        $rows[] = [substr($r['matchdate'], 0, 10), substr($r['matchdate'], 11, 5), match_competition((int)$r['matchid'])['name'] ?? '',
                   clean_text($r['hometeam']), clean_text($r['awayteam']), clean_text($r['username']), $r['phs'], $r['pas'], $r['mhs'], $r['mas'],
                   $scored ? points_for((int)$r['phs'], (int)$r['pas'], (int)$r['mhs'], (int)$r['mas']) : '', $r['isauto'] === 'Y' ? 'Yes' : 'No'];
      }
      break;
    case 'players':
      $points = [];
      foreach (compute_standings() as $r) $points[$r['user_id']] = $r['points'];
      $prefs = prefs_map();
      $rows[] = ['Username', 'Email', 'Admin', 'Joined', 'Points', 'Allows email from admin', 'Message emails'];
      foreach (q('SELECT userid, username, email, usertype, since FROM ' . t('users') . ' WHERE lid = ? ORDER BY username', [lid()]) as $u) {
        $p = $prefs[(int)$u['userid']] ?? ['allow_email' => false, 'notify_messages' => false];
        $rows[] = [clean_text($u['username']), $u['email'], (int)$u['usertype'] >= USER_ADMIN ? 'Yes' : 'No', $u['since'], $points[(int)$u['userid']] ?? 0,
                   $p['allow_email'] ? 'Yes' : 'No', $p['notify_messages'] ? 'Yes' : 'No'];
      }
      break;
    default:
      fail(422, 'Choose what to download.');
  }
  $csv = "\xEF\xBB\xBF" . implode('', array_map('csv_line', $rows));
  return ['filename' => "prediction-league-$what-" . date('Y-m-d') . '.csv', 'content' => $csv, 'rows' => count($rows) - 1];
}

/** Parse a UK or ISO date and a time into "Y-m-d H:i". */
function csv_kickoff(string $date, string $time): ?string {
  $date = trim($date);
  $time = trim(str_replace('.', ':', $time)) ?: '15:00';
  foreach (['Y-m-d', 'd/m/Y', 'd/m/y', 'd-m-Y', 'd.m.Y', 'j/n/Y', 'j/n/y'] as $df) {
    $d = DateTime::createFromFormat('!' . $df, $date);
    if ($d && $d->format($df) === $date) {
      foreach (['H:i', 'G:i', 'H:i:s'] as $tf) {
        $t = DateTime::createFromFormat('!' . $tf, $time);
        if ($t && $t->format($tf) === $time) return $d->format('Y-m-d') . ' ' . $t->format('H:i');
      }
      return null;
    }
  }
  return null;
}

function route_admin_importfixtures(): array {
  require_admin();
  $dryRun = ($_POST['dry_run'] ?? '') === '1';
  $f = uploaded_file('csv', 1048576, 'CSV file');
  $text = (string)file_get_contents($f['tmp_name']);
  $text = preg_replace('/^\xEF\xBB\xBF/', '', $text);
  if (!mb_check_encoding($text, 'UTF-8')) $text = mb_convert_encoding($text, 'UTF-8', 'Windows-1252');
  $firstLine = strtok($text, "\r\n") ?: '';
  $delims = [',' => substr_count($firstLine, ','), ';' => substr_count($firstLine, ';'), "\t" => substr_count($firstLine, "\t")];
  arsort($delims);
  $delim = (string)array_key_first($delims);

  $fh = fopen('php://temp', 'r+');
  fwrite($fh, $text);
  rewind($fh);
  $header = fgetcsv($fh, 0, $delim, '"', '');
  if (!$header) fail(422, 'That file is empty.');
  $map = [];
  foreach ($header as $i => $h) {
    $h = strtolower(trim(preg_replace('/[^a-z ]/i', ' ', (string)$h)));
    $h = preg_replace('/\s+/', ' ', $h);
    if (in_array($h, ['date', 'match date'], true)) $map['date'] = $i;
    elseif (in_array($h, ['time', 'kick off time', 'ko'], true)) $map['time'] = $i;
    elseif (in_array($h, ['kickoff', 'kick off', 'datetime', 'date time'], true)) $map['kickoff'] = $i;
    elseif (in_array($h, ['home', 'home team', 'hometeam'], true)) $map['home'] = $i;
    elseif (in_array($h, ['away', 'away team', 'awayteam'], true)) $map['away'] = $i;
    elseif (in_array($h, ['competition', 'comp', 'tournament', 'cup', 'league'], true)) $map['competition'] = $i;
  }
  if (!isset($map['home'], $map['away']) || (!isset($map['date']) && !isset($map['kickoff']))) {
    fail(422, 'The first row must name the columns: Date, Time, Home, Away (or Kickoff, Home, Away).');
  }

  $compByName = [];
  foreach (competitions() as $c) {
    $compByName[mb_strtolower($c['name'])] = $c['id'];
    $compByName[mb_strtolower($c['short'])] = $compByName[mb_strtolower($c['short'])] ?? $c['id'];
  }
  $newComps = [];
  $existing = [];
  foreach (q('SELECT matchdate, hometeam, awayteam FROM ' . t('matches') . ' WHERE lid = ?', [lid()]) as $m) {
    $existing[substr($m['matchdate'], 0, 16) . '|' . mb_strtolower(clean_text($m['hometeam'])) . '|' . mb_strtolower(clean_text($m['awayteam']))] = true;
  }

  $rows = [];
  $line = 1;
  while (($r = fgetcsv($fh, 0, $delim, '"', '')) !== false) {
    $line++;
    if ($r === [null] || !array_filter($r, fn($c) => trim((string)$c) !== '')) continue;
    if (count($rows) >= 500) fail(422, 'Import 500 fixtures or fewer at a time.');
    $cell = fn($k) => isset($map[$k]) ? trim((string)($r[$map[$k]] ?? '')) : '';
    $home = $cell('home');
    $away = $cell('away');
    if (isset($map['kickoff'])) {
      $parts = preg_split('/[ T]+/', $cell('kickoff'), 2);
      $kickoff = csv_kickoff($parts[0] ?? '', $parts[1] ?? '');
    } else {
      $kickoff = csv_kickoff($cell('date'), $cell('time'));
    }
    $compName = mb_substr(trim(preg_replace('/\s+/u', ' ', $cell('competition'))), 0, 40);
    $row = ['line' => $line, 'kickoff' => $kickoff ? "$kickoff:00" : null, 'home_team' => $home, 'away_team' => $away,
            'competition' => $compName, 'status' => 'ok', 'note' => ''];
    if ($compName !== '' && !isset($compByName[mb_strtolower($compName)])) {
      $newComps[mb_strtolower($compName)] = $compName;
      $row['note'] = "New competition \"$compName\" will be added.";
    }
    try {
      if (!$kickoff) fail(422, 'Date or time not recognised. Use 26/12/2026 and 15:00.');
      $cid = $compName !== '' ? ($compByName[mb_strtolower($compName)] ?? null) : null;
      [, $home, $away] = fixture_fields(['kickoff' => $kickoff, 'home_team' => $home, 'away_team' => $away, 'competition_id' => $cid]);
      $row['home_team'] = $home;
      $row['away_team'] = $away;
      $key = $kickoff . '|' . mb_strtolower($home) . '|' . mb_strtolower($away);
      if (isset($existing[$key])) {
        $row['status'] = 'duplicate';
        $row['note'] = 'Already added, will be skipped.';
      }
      $existing[$key] = true;
    } catch (ApiError $e) {
      $row['status'] = 'error';
      $row['note'] = $e->getMessage();
    }
    $rows[] = $row;
  }
  fclose($fh);
  if (!$rows) fail(422, 'That file has no fixtures under the header row.');

  $ok = array_values(array_filter($rows, fn($r) => $r['status'] === 'ok'));
  $errors = array_values(array_filter($rows, fn($r) => $r['status'] === 'error'));
  if ($dryRun) {
    return ['rows' => $rows, 'ok' => count($ok), 'duplicates' => count($rows) - count($ok) - count($errors), 'errors' => count($errors),
            'new_competitions' => array_values($newComps)];
  }
  if ($errors) {
    fail(422, 'Fix the rows with problems first. Line ' . $errors[0]['line'] . ': ' . $errors[0]['note']);
  }
  competition_map();  // create the tables before the transaction
  $needed = [];
  foreach ($ok as $r) if ($r['competition'] !== '' && !isset($compByName[mb_strtolower($r['competition'])])) $needed[mb_strtolower($r['competition'])] = $r['competition'];
  foreach ($needed as $key => $name) {
    $compByName[$key] = add_competition($name, '');
  }
  teams_map();
  $pdo = db();
  $pdo->beginTransaction();
  foreach ($ok as $r) {
    q('INSERT INTO ' . t('matches') . ' (lid, matchdate, hometeam, awayteam) VALUES (?, ?, ?, ?)', [lid(), $r['kickoff'], $r['home_team'], $r['away_team']]);
    $cid = $r['competition'] !== '' ? $compByName[mb_strtolower($r['competition'])] : null;
    set_match_competition((int)$pdo->lastInsertId(), $cid);
    register_teams([$r['home_team'], $r['away_team']]);
  }
  $pdo->commit();
  log_event('fixtures_import', 'Imported ' . count($ok) . ' fixtures from ' . mb_substr(basename((string)$f['name']), 0, 80));
  return ['added' => count($ok), 'skipped' => count($rows) - count($ok)];
}

// ---------------------------------------------------------------------
// Admin: competitions
// ---------------------------------------------------------------------

/** A short tag from a name: "Premier League" -> "PL", "FA Cup" -> "FAC". */
function default_short_name(string $name): string {
  $words = preg_split('/\s+/u', trim($name));
  if (count($words) === 1) return mb_strtoupper(mb_substr($name, 0, 4));
  $tag = '';
  foreach ($words as $w) $tag .= mb_strtoupper(mb_substr($w, 0, 1)) . (preg_match('/^\p{Lu}{2,}$/u', $w) ? mb_substr($w, 1) : '');
  return mb_substr($tag, 0, 8);
}

/** Create a competition at the end of the list; returns its id. */
function add_competition(string $name, string $short): int {
  $pos = (int)with_table('comps', fn() => q('SELECT COALESCE(MAX(position), 0) AS p FROM ' . xt('comps') . ' WHERE lid = ?', [lid()])->fetch()['p']) + 1;
  q('INSERT INTO ' . xt('comps') . ' (lid, name, short_name, position) VALUES (?, ?, ?, ?)', [lid(), $name, $short ?: default_short_name($name), $pos]);
  $id = (int)db()->lastInsertId();
  competitions(true);
  log_event('competition', "Added competition $name");
  return $id;
}

function route_admin_competitions(): array {
  require_admin();
  $counts = array_count_values(array_values(competition_map()));
  $total = (int)q('SELECT COUNT(*) AS n FROM ' . t('matches') . ' WHERE lid = ?', [lid()])->fetch()['n'];
  return [
    'competitions' => array_map(fn($c) => $c + ['fixtures' => $counts[$c['id']] ?? 0], competitions(true)),
    'uncategorised' => $total - array_sum($counts),
    'public' => public_settings(),
  ];
}

function route_admin_competition(): array {
  require_admin();
  $b = body();
  $name = trim(preg_replace('/\s+/u', ' ', str_in($b, 'name', 40)));
  $short = trim(str_in($b, 'short_name', 8, false));
  $id = (int)($b['id'] ?? 0);
  foreach (competitions(true) as $c) {
    if ($c['id'] !== $id && mb_strtolower($c['name']) === mb_strtolower($name)) fail(409, 'There\'s already a competition with that name.');
  }
  if ($id) {
    $old = competition_by_id($id);
    if (!$old) fail(404, 'That competition no longer exists.');
    q('UPDATE ' . xt('comps') . ' SET name = ?, short_name = ? WHERE lid = ? AND compid = ?', [$name, $short ?: default_short_name($name), lid(), $id]);
    log_event('competition', $old['name'] === $name ? "Edited competition $name" : "Renamed competition {$old['name']} to $name");
  } else {
    add_competition($name, $short);
  }
  return route_admin_competitions();
}

function route_admin_competition_delete(): array {
  require_admin();
  $id = (int)(body()['id'] ?? 0);
  $c = competition_by_id($id);
  if (!$c) fail(404, 'That competition no longer exists.');
  competition_map();
  q('DELETE FROM ' . xt('matchcomp') . ' WHERE lid = ? AND compid = ?', [lid(), $id]);
  q('DELETE FROM ' . xt('comps') . ' WHERE lid = ? AND compid = ?', [lid(), $id]);
  remove_competition_from_leagues($id);
  with_table('compteams', fn() => q('DELETE FROM ' . xt('compteams') . ' WHERE compid = ? AND seasonid IN (SELECT seasonid FROM ' . xt('seasons') . ' WHERE lid = ?)', [$id, lid()]));
  competition_map(true);
  log_event('competition', "Deleted competition {$c['name']} (its fixtures were kept)");
  return route_admin_competitions();
}

function route_admin_competition_order(): array {
  require_admin();
  $ids = body()['ids'] ?? null;
  if (!is_array($ids)) fail(422, 'Send the competitions in their new order.');
  $known = array_column(competitions(true), 'id');
  $ids = array_values(array_unique(array_map('intval', $ids)));
  sort($known);
  $check = $ids; sort($check);
  if ($check !== $known) fail(422, 'The list of competitions changed. Reload the page and try again.');
  foreach ($ids as $i => $cid) {
    q('UPDATE ' . xt('comps') . ' SET position = ? WHERE lid = ? AND compid = ?', [$i + 1, lid(), $cid]);
  }
  return route_admin_competitions();
}
