<?php
/*
 * Prediction League JSON API.
 * Every request is api/index.php?r=<route>. GET reads, POST writes.
 * Requests must come from the site's own pages (see guard_request()) and,
 * apart from ?r=session, carry the X-CSRF-Token header it returns.
 */

declare(strict_types=1);
define('PL_APP', true);
require __DIR__ . '/lib.php';
require __DIR__ . '/mailer.php';
require __DIR__ . '/features.php';
require __DIR__ . '/autopredict.php';
require __DIR__ . '/botguard.php';
require __DIR__ . '/leagues.php';
require __DIR__ . '/pages.php';
require __DIR__ . '/seasons.php';
require __DIR__ . '/alerts.php';
require __DIR__ . '/standings.php';

try {
  date_default_timezone_set((string)cfg('timezone'));
  guard_request();
  start_session();

  $route = (string)($_GET['r'] ?? '');
  $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
  $routes = routes() + feature_routes() + botguard_routes() + league_routes() + page_routes() + season_routes() + alert_routes() + standings_routes();
  if (!isset($routes[$route])) {
    fail(404, 'Unknown API route.');
  }
  [$allowed, $handler] = $routes[$route];
  if ($method !== $allowed) {
    header("Allow: $allowed");
    fail(405, "Use $allowed for this route.");
  }
  refuse_if_banned();
  if ($route !== 'session') {
    check_token();
  }
  // Fill in automatic predictions for matches whose predictions have closed.
  if ($method === 'GET' && in_array($route, ['session', 'standings', 'fixtures', 'match', 'player', 'matchday', 'leagues', 'table'], true)) {
    run_auto_predictions();
  }
  // The daily list of blocked addresses, if one is due (sent after this reply).
  schedule_block_digest();
  respond($handler());
} catch (ApiError $e) {
  respond(['error' => $e->getMessage(), 'code' => $e->errorCode], $e->status);
} catch (Throwable $e) {
  error_log('Prediction League API: ' . $e);
  $message = cfg('debug') ? $e->getMessage() : 'Something went wrong on the server. Check the PHP error log for details.';
  respond(['error' => $message], 500);
}

function routes(): array {
  return [
    'session'          => ['GET',  'route_session'],
    'login'            => ['POST', 'route_login'],
    'logout'           => ['POST', 'route_logout'],
    'register'         => ['POST', 'route_register'],
    'account'          => ['POST', 'route_account'],
    'standings'        => ['GET',  'route_standings'],
    'fixtures'         => ['GET',  'route_fixtures'],
    'predictions'      => ['POST', 'route_save_predictions'],
    'match'            => ['GET',  'route_match'],
    'player'           => ['GET',  'route_player'],
    'admin.fixture'    => ['POST', 'route_admin_fixture'],
    'admin.fixtures'   => ['POST', 'route_admin_fixtures_bulk'],
    'admin.delete'     => ['POST', 'route_admin_delete_fixture'],
    'admin.deletemany' => ['POST', 'route_admin_delete_fixtures'],
    'admin.result'     => ['POST', 'route_admin_result'],
    'admin.users'      => ['GET',  'route_admin_users'],
    'admin.user'       => ['POST', 'route_admin_user'],
    'admin.deleteuser' => ['POST', 'route_admin_delete_user'],
    'admin.settings'   => ['GET',  'route_admin_settings'],
    'admin.savesettings' => ['POST', 'route_admin_save_settings'],
    'admin.recalculate'  => ['POST', 'route_admin_recalculate'],
  ];
}

// ---------------------------------------------------------------------
// Session and accounts
// ---------------------------------------------------------------------

function route_session(): array {
  $user = current_user(true);
  if ($user) {
    $user['prefs'] = user_prefs($user['id']);
    $user['follows'] = user_follows($user['id']);
  }
  return [
    'user' => $user,
    'csrf' => $_SESSION['csrf'],
    'settings' => public_settings(),
    'first_user_becomes_admin' => cfg('first_user_becomes_admin') && admin_count() === 0,
    'server_time' => now_str(),
  ];
}

function route_login(): array {
  $b = body();
  bot_check('login', $b, 'That username and password don\'t match. Check them and try again.');
  $username = str_in($b, 'username', 32);
  $password = (string)($b['password'] ?? '');

  // Ten failed attempts from one connection in 15 minutes pauses logins from it.
  if (recent_events('login_failed', 900) >= 10) {
    fail(429, 'Too many failed log-in attempts from your connection. Wait 15 minutes and try again, or reset your password.');
  }

  $row = q('SELECT userid, username, password FROM ' . t('users') . ' WHERE lid = ? AND username = ?',
    [lid(), $username])->fetch();
  if (!$row || !verify_password($password, (string)$row['password'])) {
    log_event('login_failed', 'Wrong password for "' . mb_substr($username, 0, 32) . '"', ['id' => $row ? (int)$row['userid'] : null, 'username' => $row ? clean_text($row['username']) : null]);
    usleep(400000);
    fail(401, 'That username and password don\'t match. Check them and try again.');
  }

  if (password_needs_upgrade((string)$row['password'])) {
    q('UPDATE ' . t('users') . ' SET password = ? WHERE lid = ? AND userid = ?',
      [password_hash($password, PASSWORD_DEFAULT), lid(), (int)$row['userid']]);
  }

  session_regenerate_id(true);
  $_SESSION['uid'] = (int)$row['userid'];
  log_event('login', 'Logged in', ['id' => (int)$row['userid'], 'username' => clean_text($row['username'])]);
  return route_session();
}

function route_logout(): array {
  if (current_user()) log_event('logout', 'Logged out');
  $_SESSION = [];
  session_regenerate_id(true);
  $_SESSION['csrf'] = bin2hex(random_bytes(32));
  return route_session();
}

function route_register(): array {
  $b = body();
  $viewer = current_user();
  $creatingAsAdmin = $viewer && $viewer['is_admin'];
  if (!$creatingAsAdmin && setting_bool('LOCKEDGAME')) {
    fail(403, 'Registration is closed. Ask the league admin to add you.');
  }
  if (!$creatingAsAdmin) {
    bot_check('register', $b, 'Your sign-up couldn\'t be completed. Try again later.');
  }

  $username = str_in($b, 'username', 32);
  if (!preg_match('/\A[\p{L}\p{N} ._\-]+\z/u', $username)) {
    fail(422, 'Usernames can use letters, numbers, spaces, dots, dashes and underscores.');
  }
  $email = str_in($b, 'email', 60);
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fail(422, 'Enter a valid email address.');
  }
  $password = (string)($b['password'] ?? '');
  if (mb_strlen($password) < 8) {
    fail(422, 'Passwords must be at least 8 characters.');
  }

  if (q('SELECT 1 FROM ' . t('users') . ' WHERE lid = ? AND username = ?', [lid(), $username])->fetch()) {
    fail(409, 'That username is taken. Choose another.');
  }
  if (!setting_bool('MULT_USERS') &&
      q('SELECT 1 FROM ' . t('users') . ' WHERE lid = ? AND email = ?', [lid(), $email])->fetch()) {
    fail(409, 'That email address already has an account.');
  }

  $makeAdmin = cfg('first_user_becomes_admin') && admin_count() === 0;
  q('INSERT INTO ' . t('users') . ' (lid, username, password, email, icon, lang, usertype, dflths, dfltas, since, auto)
     VALUES (?, ?, ?, ?, ?, ?, ?, 0, 0, ?, ?)',
    [lid(), $username, make_password_hash($password), $email, setting('DEF_ICON') ?: 'default.gif',
     'english', $makeAdmin ? USER_ADMIN : USER_NORMAL, date('Y-m-d'), 'N']);
  $newId = (int)db()->lastInsertId();

  if ($creatingAsAdmin) {
    log_event('player_add', "Added player $username");
    return ['created' => ['id' => $newId, 'username' => $username]];
  }
  session_regenerate_id(true);
  $_SESSION['uid'] = $newId;
  log_event('register', 'Joined the league' . ($makeAdmin ? ' as the first admin' : ''), ['id' => $newId, 'username' => $username]);
  return route_session();
}

function route_account(): array {
  $user = require_user();
  $b = body();
  $row = q('SELECT password FROM ' . t('users') . ' WHERE lid = ? AND userid = ?', [lid(), $user['id']])->fetch();
  if (!verify_password((string)($b['current_password'] ?? ''), (string)$row['password'])) {
    fail(422, 'Your current password is wrong.');
  }

  $email = str_in($b, 'email', 60);
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fail(422, 'Enter a valid email address.');
  }
  if (!setting_bool('MULT_USERS') &&
      q('SELECT 1 FROM ' . t('users') . ' WHERE lid = ? AND email = ? AND userid <> ?', [lid(), $email, $user['id']])->fetch()) {
    fail(409, 'That email address already has an account.');
  }
  q('UPDATE ' . t('users') . ' SET email = ? WHERE lid = ? AND userid = ?', [$email, lid(), $user['id']]);

  $new = (string)($b['new_password'] ?? '');
  if ($new !== '') {
    if (mb_strlen($new) < 8) fail(422, 'Passwords must be at least 8 characters.');
    q('UPDATE ' . t('users') . ' SET password = ? WHERE lid = ? AND userid = ?',
      [make_password_hash($new), lid(), $user['id']]);
  }
  log_event('account', $new !== '' ? 'Changed email and password' : 'Updated account details');
  return ['saved' => true, 'user' => current_user(true)];
}

// ---------------------------------------------------------------------
// Public game data
// ---------------------------------------------------------------------

function route_standings(): array {
  $next = null;
  foreach (q('SELECT * FROM ' . t('matches') . ' WHERE lid = ? AND matchdate > ? ORDER BY matchdate LIMIT 20', [lid(), now_str()]) as $cand) {
    if (!is_postponed((int)$cand['matchid'])) { $next = $cand; break; }
  }
  $season = requested_season();
  return [
    'table' => $season ? compute_table(season_bounds($season)) : compute_standings(),
    'season' => season_brief($season),
    'next_match' => $next ? match_out($next) : null,
  ];
}

function route_fixtures(): array {
  $user = current_user();
  $order = setting_bool('REV_USER_PREDS') ? 'DESC' : 'ASC';
  $matches = q('SELECT * FROM ' . t('matches') . " WHERE lid = ? ORDER BY matchdate $order, matchid $order", [lid()])->fetchAll();

  $mine = [];
  if ($user) {
    foreach (q('SELECT matchid, homescore, awayscore, isauto FROM ' . t('predictions') . ' WHERE lid = ? AND userid = ?',
               [lid(), $user['id']]) as $p) {
      if ($p['homescore'] !== null && $p['awayscore'] !== null) {
        $mine[(int)$p['matchid']] = ['home' => (int)$p['homescore'], 'away' => (int)$p['awayscore'], 'auto' => $p['isauto'] === 'Y'];
      }
    }
  }
  $counts = [];
  foreach (q('SELECT matchid, COUNT(*) AS n FROM ' . t('predictions') . ' WHERE lid = ? GROUP BY matchid', [lid()]) as $c) {
    $counts[(int)$c['matchid']] = (int)$c['n'];
  }

  $out = [];
  foreach ($matches as $m) {
    $row = match_out($m);
    $row['prediction_count'] = $counts[$row['id']] ?? 0;
    if ($user) {
      $pred = $mine[$row['id']] ?? null;
      $row['my_prediction'] = $pred;
      $hasScore = $pred && $row['result'];
      $row['my_points'] = $hasScore ? points_for($pred['home'], $pred['away'], $row['result']['home'], $row['result']['away']) : null;
      $row['my_outcome'] = $hasScore ? outcome_for($pred['home'], $pred['away'], $row['result']['home'], $row['result']['away']) : null;
    }
    $out[] = $row;
  }
  return ['fixtures' => $out];
}

function route_save_predictions(): array {
  $user = require_user();
  $items = body()['predictions'] ?? null;
  if (!is_array($items) || !$items) {
    fail(422, 'There are no predictions to save.');
  }

  $saved = 0;
  $skipped = [];
  $pdo = db();
  $pdo->beginTransaction();
  try {
    foreach ($items as $item) {
      $matchId = (int)($item['match_id'] ?? 0);
      $home = score_in($item['home'] ?? null);
      $away = score_in($item['away'] ?? null);
      if (($home === null) !== ($away === null)) {
        fail(422, 'Enter both scores for each match, or leave both empty.');
      }
      $m = q('SELECT matchid, matchdate FROM ' . t('matches') . ' WHERE lid = ? AND matchid = ?', [lid(), $matchId])->fetch();
      if (!$m) continue;
      if (predictions_closed((string)$m['matchdate']) || is_postponed($matchId)) {
        $skipped[] = $matchId;  // predictions closed (or the match was postponed) while the page was open
        continue;
      }
      $key = [lid(), $user['id'], $matchId];
      if ($home === null) {
        q('DELETE FROM ' . t('predictions') . ' WHERE lid = ? AND userid = ? AND matchid = ?', $key);
      } elseif (q('SELECT 1 FROM ' . t('predictions') . ' WHERE lid = ? AND userid = ? AND matchid = ?', $key)->fetch()) {
        q('UPDATE ' . t('predictions') . ' SET homescore = ?, awayscore = ?, predtime = ?, isauto = ?
           WHERE lid = ? AND userid = ? AND matchid = ?', array_merge([$home, $away, now_str(), 'N'], $key));
      } else {
        q('INSERT INTO ' . t('predictions') . ' (lid, userid, matchid, predtime, homescore, awayscore, isauto)
           VALUES (?, ?, ?, ?, ?, ?, ?)', [lid(), $user['id'], $matchId, now_str(), $home, $away, 'N']);
      }
      $saved++;
    }
    $pdo->commit();
  } catch (Throwable $e) {
    $pdo->rollBack();
    throw $e;
  }
  return ['saved' => $saved, 'locked' => $skipped];
}

function route_match(): array {
  $m = get_match((int)($_GET['id'] ?? 0));
  $match = match_out($m);
  $rows = q('SELECT p.userid, u.username, u.icon, p.homescore, p.awayscore, p.isauto
             FROM ' . t('predictions') . ' p
             INNER JOIN ' . t('users') . ' u ON u.userid = p.userid AND u.lid = p.lid
             WHERE p.lid = ? AND p.matchid = ? ORDER BY u.username', [lid(), $match['id']])->fetchAll();

  $predictions = [];
  $hidden = 0;
  foreach ($rows as $r) {
    if ($r['homescore'] === null || $r['awayscore'] === null) continue;
    if (!can_see_prediction($m, (int)$r['userid'])) {
      $hidden++;
      continue;
    }
    [$ph, $pa] = [(int)$r['homescore'], (int)$r['awayscore']];
    $predictions[] = [
      'user_id' => (int)$r['userid'],
      'username' => clean_text($r['username']),
      'icon' => icon_url($r['icon']),
      'home' => $ph,
      'away' => $pa,
      'auto' => $r['isauto'] === 'Y',
      'points' => $match['result'] ? points_for($ph, $pa, $match['result']['home'], $match['result']['away']) : null,
      'outcome' => $match['result'] ? outcome_for($ph, $pa, $match['result']['home'], $match['result']['away']) : null,
    ];
  }
  return ['match' => $match, 'predictions' => $predictions, 'hidden_count' => $hidden];
}

function route_player(): array {
  $id = (int)($_GET['id'] ?? 0);
  $u = q('SELECT userid, username, since, icon FROM ' . t('users') . ' WHERE lid = ? AND userid = ?', [lid(), $id])->fetch();
  if (!$u) fail(404, 'That player doesn\'t exist.');

  $rows = q('SELECT m.*, p.homescore AS phs, p.awayscore AS pas, p.isauto AS pauto
             FROM ' . t('predictions') . ' p
             INNER JOIN ' . t('matches') . ' m ON m.matchid = p.matchid AND m.lid = p.lid
             WHERE p.lid = ? AND p.userid = ? ORDER BY m.matchdate DESC', [lid(), $id])->fetchAll();

  $predictions = [];
  $hidden = 0;
  foreach ($rows as $r) {
    if ($r['phs'] === null || $r['pas'] === null) continue;
    if (!can_see_prediction($r, $id)) {
      $hidden++;
      continue;
    }
    $match = match_out($r);
    [$ph, $pa] = [(int)$r['phs'], (int)$r['pas']];
    $match['prediction'] = ['home' => $ph, 'away' => $pa, 'auto' => $r['pauto'] === 'Y'];
    $match['points'] = $match['result'] ? points_for($ph, $pa, $match['result']['home'], $match['result']['away']) : null;
    $match['outcome'] = $match['result'] ? outcome_for($ph, $pa, $match['result']['home'], $match['result']['away']) : null;
    $predictions[] = $match;
  }

  $standing = null;
  foreach (compute_standings() as $row) {
    if ($row['user_id'] === $id) $standing = $row;
  }
  return [
    'player' => ['id' => (int)$u['userid'], 'username' => clean_text($u['username']), 'since' => $u['since'], 'icon' => icon_url($u['icon'])],
    'standing' => $standing,
    'predictions' => $predictions,
    'hidden_count' => $hidden,
  ];
}

// ---------------------------------------------------------------------
// Admin
// ---------------------------------------------------------------------

function fixture_fields(array $src): array {
  $home = str_in($src, 'home_team', 30);
  $away = str_in($src, 'away_team', 30);
  if (mb_strtolower($home) === mb_strtolower($away)) {
    fail(422, 'The home and away teams must be different.');
  }
  $kickoff = parse_kickoff((string)($src['kickoff'] ?? ''));
  $compId = competition_id_in($src['competition_id'] ?? null);
  [$home, $away] = check_fixture_teams($home, $away, $kickoff, $compId);
  return [$kickoff, $home, $away, $compId];
}

function route_admin_fixture(): array {
  require_admin();
  $b = body();
  [$kickoff, $home, $away, $compId] = fixture_fields($b);
  competition_map();  // make sure the table exists
  $id = (int)($b['id'] ?? 0);
  if ($id) {
    $old = get_match($id);
    q('UPDATE ' . t('matches') . ' SET matchdate = ?, hometeam = ?, awayteam = ? WHERE lid = ? AND matchid = ?',
      [$kickoff, $home, $away, lid(), $id]);
    // A postponed match with a new kick-off time is back on: predictions reopen.
    $rearranged = is_postponed($id) && substr((string)$old['matchdate'], 0, 16) !== substr($kickoff, 0, 16);
    if ($rearranged) set_postponed($id, null);
    log_event('fixture_edit', ($rearranged ? 'Rearranged postponed fixture' : 'Edited fixture') . ": $home v $away, " . substr($kickoff, 0, 16));
  } else {
    q('INSERT INTO ' . t('matches') . ' (lid, matchdate, hometeam, awayteam) VALUES (?, ?, ?, ?)',
      [lid(), $kickoff, $home, $away]);
    $id = (int)db()->lastInsertId();
    log_event('fixture_add', "Added fixture: $home v $away, " . substr($kickoff, 0, 16));
  }
  set_match_competition($id, $compId);
  register_teams([$home, $away]);
  competition_map(true);
  save_standings(compute_standings());
  return ['fixture' => match_out(get_match($id))];
}

function route_admin_fixtures_bulk(): array {
  require_admin();
  $rows = body()['fixtures'] ?? null;
  if (!is_array($rows) || !$rows) fail(422, 'Add at least one fixture.');
  if (count($rows) > 100) fail(422, 'Add 100 fixtures or fewer at a time.');

  $clean = [];
  foreach ($rows as $i => $row) {
    try {
      $clean[] = fixture_fields(is_array($row) ? $row : []);
    } catch (ApiError $e) {
      fail(422, 'Row ' . ($i + 1) . ': ' . $e->getMessage());
    }
  }
  competition_map();  // create the tables before the transaction
  teams_map();
  $pdo = db();
  $pdo->beginTransaction();
  foreach ($clean as [$kickoff, $home, $away, $compId]) {
    q('INSERT INTO ' . t('matches') . ' (lid, matchdate, hometeam, awayteam) VALUES (?, ?, ?, ?)',
      [lid(), $kickoff, $home, $away]);
    set_match_competition((int)$pdo->lastInsertId(), $compId);
    register_teams([$home, $away]);
  }
  $pdo->commit();
  log_event('fixture_add', 'Added ' . count($clean) . ' fixtures: ' . mb_substr(implode('; ', array_map(fn($c) => "$c[1] v $c[2]", $clean)), 0, 200));
  return ['added' => count($clean)];
}

function route_admin_delete_fixture(): array {
  require_admin();
  $id = (int)(body()['id'] ?? 0);
  $gone = get_match($id);
  $pdo = db();
  $pdo->beginTransaction();
  q('DELETE FROM ' . t('predictions') . ' WHERE lid = ? AND matchid = ?', [lid(), $id]);
  q('DELETE FROM ' . t('matches') . ' WHERE lid = ? AND matchid = ?', [lid(), $id]);
  $pdo->commit();
  set_postponed($id, null);
  with_table('matchcomp', fn() => q('DELETE FROM ' . xt('matchcomp') . ' WHERE lid = ? AND matchid = ?', [lid(), $id]));
  save_standings(compute_standings());
  log_event('fixture_delete', 'Deleted fixture: ' . clean_text($gone['hometeam']) . ' v ' . clean_text($gone['awayteam']) . ', ' . substr((string)$gone['matchdate'], 0, 16));
  return ['deleted' => $id];
}

/** Remove several fixtures at once, with their predictions. */
function route_admin_delete_fixtures(): array {
  require_admin();
  $ids = body()['ids'] ?? null;
  if (!is_array($ids)) fail(422, 'Choose the fixtures to remove.');
  $ids = array_values(array_unique(array_filter(array_map('intval', $ids), fn($i) => $i > 0)));
  if (!$ids) fail(422, 'Choose the fixtures to remove.');
  if (count($ids) > 1000) fail(422, 'Remove up to 1,000 fixtures at a time.');
  $in = implode(',', array_fill(0, count($ids), '?'));
  $found = q('SELECT matchid, hometeam, awayteam, matchdate FROM ' . t('matches') . " WHERE lid = ? AND matchid IN ($in)", array_merge([lid()], $ids))->fetchAll();
  if (!$found) fail(404, 'Those fixtures no longer exist.');
  $ids = array_map(fn($r) => (int)$r['matchid'], $found);
  $in = implode(',', array_fill(0, count($ids), '?'));
  competition_map();  // make sure the link table exists before the transaction
  $pdo = db();
  $pdo->beginTransaction();
  $preds = (int)q('SELECT COUNT(*) FROM ' . t('predictions') . " WHERE lid = ? AND matchid IN ($in)", array_merge([lid()], $ids))->fetchColumn();
  q('DELETE FROM ' . t('predictions') . " WHERE lid = ? AND matchid IN ($in)", array_merge([lid()], $ids));
  q('DELETE FROM ' . t('matches') . " WHERE lid = ? AND matchid IN ($in)", array_merge([lid()], $ids));
  q('DELETE FROM ' . xt('matchcomp') . " WHERE lid = ? AND matchid IN ($in)", array_merge([lid()], $ids));
  $pdo->commit();
  with_table('postponed', fn() => q('DELETE FROM ' . xt('postponed') . " WHERE lid = ? AND matchid IN ($in)", array_merge([lid()], $ids)));
  postponed_map(true);
  competition_map(true);
  save_standings(compute_standings());
  $names = array_map(fn($r) => clean_text($r['hometeam']) . ' v ' . clean_text($r['awayteam']) . ' ' . substr((string)$r['matchdate'], 0, 10), $found);
  log_event('fixture_delete', mb_substr('Removed ' . count($found) . ' fixtures (' . $preds . ' predictions): ' . implode('; ', $names), 0, 255));
  return ['deleted' => count($found), 'predictions' => $preds];
}

function route_admin_result(): array {
  require_admin();
  $b = body();
  $id = (int)($b['id'] ?? 0);
  $m = get_match($id);
  if (is_p_score($b['home'] ?? null) || is_p_score($b['away'] ?? null)) {
    postpone_from_results($m);
    save_standings(compute_standings());
    log_event('fixture_edit', 'Postponed ' . clean_text($m['hometeam']) . ' v ' . clean_text($m['awayteam']) . ' (P entered as the result)');
    return ['fixture' => match_out(get_match($id)), 'postponed' => true];
  }
  $home = score_in($b['home'] ?? null);
  $away = score_in($b['away'] ?? null);
  if (($home === null) !== ($away === null)) {
    fail(422, 'Enter both scores, or leave both empty to clear the result.');
  }
  if ($home !== null && (string)$m['matchdate'] > now_str()) {
    fail(422, 'This match hasn\'t kicked off yet. Change the kick-off time first if it\'s wrong.');
  }
  if ($home !== null && is_postponed($id)) {
    fail(422, 'This match is postponed. Give it its new kick-off time first.');
  }
  q('UPDATE ' . t('matches') . ' SET homescore = ?, awayscore = ? WHERE lid = ? AND matchid = ?',
    [$home, $away, lid(), $id]);
  save_standings(compute_standings());
  log_event('result', clean_text($m['hometeam']) . ' v ' . clean_text($m['awayteam']) . ': ' . ($home === null ? 'result cleared' : "$home-$away"));
  return ['fixture' => match_out(get_match($id))];
}

function route_admin_users(): array {
  require_admin();
  $table = [];
  foreach (compute_standings() as $r) $table[$r['user_id']] = $r;
  $users = [];
  foreach (q('SELECT userid, username, email, usertype, since, icon FROM ' . t('users') . ' WHERE lid = ? ORDER BY username', [lid()]) as $u) {
    $pu = public_user($u);
    $pu['points'] = $table[$pu['id']]['points'] ?? 0;
    $users[] = $pu;
  }
  return ['users' => $users, 'max_admins' => (int)setting('MAX_ADMIN_USERS')];
}

function route_admin_user(): array {
  $me = require_admin();
  $b = body();
  $id = (int)($b['id'] ?? 0);
  $target = q('SELECT userid, username, usertype FROM ' . t('users') . ' WHERE lid = ? AND userid = ?', [lid(), $id])->fetch();
  if (!$target) fail(404, 'That player doesn\'t exist.');
  $makeAdmin = !empty($b['is_admin']);
  $isAdmin = (int)$target['usertype'] >= USER_ADMIN;

  if ($makeAdmin && !$isAdmin && admin_count() >= max(1, (int)setting('MAX_ADMIN_USERS'))) {
    fail(422, 'You already have the maximum number of admins. Raise "MAX_ADMIN_USERS" in Settings first.');
  }
  if (!$makeAdmin && $isAdmin && $id === $me['id']) {
    fail(422, 'You can\'t remove your own admin rights.');
  }
  if ($makeAdmin !== $isAdmin) {
    q('UPDATE ' . t('users') . ' SET usertype = ? WHERE lid = ? AND userid = ?',
      [$makeAdmin ? USER_ADMIN : USER_NORMAL, lid(), $id]);
    log_event('player_admin', ($makeAdmin ? 'Made ' : 'Removed admin rights from ') . clean_text($target['username']) . ($makeAdmin ? ' an admin' : ''));
  }
  return route_admin_users();
}

function route_admin_delete_user(): array {
  $me = require_admin();
  $id = (int)(body()['id'] ?? 0);
  if ($id === $me['id']) fail(422, 'You can\'t delete your own account.');
  $target = q('SELECT username FROM ' . t('users') . ' WHERE lid = ? AND userid = ?', [lid(), $id])->fetch();
  if (!$target) fail(404, 'That player doesn\'t exist.');
  delete_user_icon($id);
  $pdo = db();
  $pdo->beginTransaction();
  q('DELETE FROM ' . t('predictions') . ' WHERE lid = ? AND userid = ?', [lid(), $id]);
  q('DELETE FROM ' . t('standings') . ' WHERE lid = ? AND userid = ?', [lid(), $id]);
  q('DELETE FROM ' . t('users') . ' WHERE lid = ? AND userid = ?', [lid(), $id]);
  $pdo->commit();
  with_table('prefs', fn() => q('DELETE FROM ' . xt('prefs') . ' WHERE lid = ? AND userid = ?', [lid(), $id]));
  remove_player_from_leagues($id);
  save_standings(compute_standings());
  log_event('player_delete', 'Deleted player ' . clean_text($target['username']) . ' and their predictions');
  return route_admin_users();
}

function route_admin_settings(): array {
  require_admin();
  $values = settings();
  $out = [];
  foreach (default_settings() as $key => $def) {
    [$grp, $default, $descr, $type] = $def;
    $value = $values[$key] ?? $default;
    $row = ['key' => $key, 'group' => $grp, 'value' => $type === 'secret' ? '' : $value, 'description' => $descr, 'type' => $type];
    if ($type === 'secret') $row['is_set'] = $value !== '';
    if ($type === 'select') $row['options'] = $def[4];
    $out[] = $row;
  }
  return ['settings' => $out];
}

function route_admin_save_settings(): array {
  require_admin();
  $values = body()['values'] ?? null;
  if (!is_array($values)) fail(422, 'No settings were sent.');
  $defs = default_settings();
  $current = settings();
  $changed = [];
  foreach ($values as $key => $value) {
    if (!isset($defs[$key])) continue;
    $type = $defs[$key][3];
    if ($type === 'secret') {
      if ((string)$value === '') continue;  // empty means keep the saved one
      $value = (string)$value;
    } else {
      $value = trim((string)$value);
    }
    if ($type === 'select' && !isset($defs[$key][4][$value])) fail(422, "Choose a value for $key from the list.");
    if ($type === 'bool') $value = strtoupper($value) === 'TRUE' ? 'TRUE' : 'FALSE';
    if ($type === 'number' && !is_numeric($value)) fail(422, "$key must be a number.");
    if ($type === 'email' && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) fail(422, "$key must be an email address.");
    if ($type === 'url' && $value !== '' && !preg_match('#\Ahttps?://#i', $value)) fail(422, "$key must start with http:// or https://.");
    if (mb_strlen($value) > 90) fail(422, "$key must be 90 characters or fewer.");
    if (($current[$key] ?? null) === $value) continue;
    q('UPDATE ' . t('config') . ' SET value = ? WHERE lid = ? AND param = ?', [$value, lid(), $key]);
    $changed[] = $type === 'secret' ? "$key (changed)" : "$key = " . ($value === '' ? '(empty)' : $value);
  }
  if ($changed) log_event('settings', mb_substr('Changed ' . implode(', ', $changed), 0, 255));
  settings(true);
  save_standings(compute_standings());
  return route_admin_settings() + ['public' => public_settings()];
}

function route_admin_recalculate(): array {
  require_admin();
  $table = compute_standings();
  save_standings($table);
  log_event('recalculate', 'Recalculated the table');
  return ['rows' => count($table)];
}
