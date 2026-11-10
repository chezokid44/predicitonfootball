<?php
/*
 * Prediction League - leagues.
 *
 * One account plays everywhere. Fixtures belong to competitions (set up by
 * site admins). Every player chooses which competitions they play, and each
 * competition has an official table. Players can also create private
 * leagues: pick competitions, set their own scoring and start date, and
 * invite friends with a code. A prediction is made once per match and counts
 * in every table that includes that match.
 */

declare(strict_types=1);

if (!defined('PL_APP')) {
  http_response_code(403);
  exit;
}

const CODE_ALPHABET = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';  // no 0/O, 1/I/L

function league_routes(): array {
  return [
    'leagues'          => ['GET',  'route_leagues'],
    'table'            => ['GET',  'route_table'],
    'follows.save'     => ['POST', 'route_follows_save'],
    'league.create'    => ['POST', 'route_league_create'],
    'league.update'    => ['POST', 'route_league_update'],
    'league.preview'   => ['GET',  'route_league_preview'],
    'league.join'      => ['POST', 'route_league_join'],
    'league.leave'     => ['POST', 'route_league_leave'],
    'league.remove'    => ['POST', 'route_league_remove'],
    'league.transfer'  => ['POST', 'route_league_transfer'],
    'league.newcode'   => ['POST', 'route_league_newcode'],
    'league.delete'    => ['POST', 'route_league_delete'],
    'admin.leagues'    => ['GET',  'route_admin_leagues'],
  ];
}

function leagues_on(): bool {
  return setting_bool('LEAGUES_ON');
}

// ---------------------------------------------------------------------
// Competitions a player plays (plfollows). No rows = every competition.
// A row with compid 0 marks an explicit choice.
// ---------------------------------------------------------------------

/** userid => null (plays everything) or list of competition ids. */
function follows_map(): array {
  static $map = null;
  if ($map === null) {
    $map = [];
    foreach (with_table('follows', fn() => q('SELECT userid, compid FROM ' . xt('follows') . ' WHERE lid = ?', [lid()])->fetchAll()) as $r) {
      $uid = (int)$r['userid'];
      $map[$uid] = $map[$uid] ?? [];
      if ((int)$r['compid'] !== 0) $map[$uid][] = (int)$r['compid'];
    }
  }
  return $map;
}

function user_follows(int $uid): ?array {
  return follows_map()[$uid] ?? null;
}

function plays_competition(int $uid, int $compId): bool {
  $f = user_follows($uid);
  return $f === null || in_array($compId, $f, true);
}

/** Add competitions to what a player plays (when joining a league). */
function follow_competitions(int $uid, array $compIds): void {
  $current = user_follows($uid);
  if ($current === null) return;  // already plays everything
  $add = array_diff(array_map('intval', $compIds), $current);
  foreach ($add as $cid) {
    q('INSERT INTO ' . xt('follows') . ' (lid, userid, compid) VALUES (?, ?, ?)', [lid(), $uid, $cid]);
  }
}

function route_follows_save(): array {
  $u = require_user();
  $b = body();
  $all = !empty($b['all']);
  $ids = array_values(array_unique(array_map('intval', is_array($b['competition_ids'] ?? null) ? $b['competition_ids'] : [])));
  $known = array_column(competitions(), 'id');
  $ids = array_values(array_intersect($ids, $known));
  if (!$all && !$ids) fail(422, 'Choose at least one competition, or choose to play them all.');
  with_table('follows', function () use ($u, $all, $ids) {
    q('DELETE FROM ' . xt('follows') . ' WHERE lid = ? AND userid = ?', [lid(), $u['id']]);
    if (!$all) {
      foreach (array_merge([0], $ids) as $cid) {
        q('INSERT INTO ' . xt('follows') . ' (lid, userid, compid) VALUES (?, ?, ?)', [lid(), $u['id'], $cid]);
      }
    }
  });
  $names = array_map(fn($id) => competition_by_id($id)['name'], $ids);
  log_event('follows', $all ? 'Plays every competition' : mb_substr('Plays ' . implode(', ', $names), 0, 255));
  return ['follows' => $all ? null : $ids];
}

// ---------------------------------------------------------------------
// Private leagues
// ---------------------------------------------------------------------

function new_league_code(): string {
  for ($try = 0; $try < 20; $try++) {
    $code = '';
    for ($i = 0; $i < 8; $i++) $code .= CODE_ALPHABET[random_int(0, strlen(CODE_ALPHABET) - 1)];
    if (!q('SELECT 1 FROM ' . xt('leagues') . ' WHERE code = ?', [$code])->fetch()) return $code;
  }
  fail(500, 'Couldn\'t make an invite code. Try again.');
  return '';
}

/** Codes are shown as ABCD-EFGH; accept them typed any way. */
function clean_code(string $code): string {
  return strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $code));
}

function get_league(int $id): array {
  $l = with_table('leagues', fn() => q('SELECT * FROM ' . xt('leagues') . ' WHERE lid = ? AND leagueid = ?', [lid(), $id])->fetch());
  if (!$l) fail(404, 'That league doesn\'t exist any more.');
  return $l;
}

function league_comp_ids(int $leagueId): array {
  return array_map('intval', with_table('leaguecomps', fn() => q('SELECT compid FROM ' . xt('leaguecomps') . ' WHERE leagueid = ?', [$leagueId])->fetchAll(PDO::FETCH_COLUMN)));
}

function league_member_ids(int $leagueId): array {
  return array_map('intval', with_table('members', fn() => q('SELECT userid FROM ' . xt('members') . ' WHERE leagueid = ?', [$leagueId])->fetchAll(PDO::FETCH_COLUMN)));
}

function is_member(int $leagueId, int $uid): bool {
  return (bool)with_table('members', fn() => q('SELECT 1 FROM ' . xt('members') . ' WHERE leagueid = ? AND userid = ?', [$leagueId, $uid])->fetch());
}

/** The league if the current user owns it (or is a site admin). */
function managed_league(int $id): array {
  $u = require_user();
  $l = get_league($id);
  if ((int)$l['owner'] !== $u['id'] && !$u['is_admin']) fail(403, 'Only the league\'s creator can change it.');
  return $l;
}

function league_scoring(array $l): array {
  return ['exact' => (int)$l['pts_exact'], 'result' => (int)$l['pts_result']];
}

function league_out(array $l, ?array $viewer, bool $full = false): array {
  $comps = (int)$l['all_comps'] ? null : league_comp_ids((int)$l['leagueid']);
  $owner = user_briefs([(int)$l['owner']])[(int)$l['owner']] ?? ['id' => null, 'username' => 'Former player', 'icon' => null];
  $member = $viewer && is_member((int)$l['leagueid'], $viewer['id']);
  $out = [
    'id' => (int)$l['leagueid'],
    'name' => clean_text($l['name']),
    'owner' => $owner,
    'competitions' => $comps === null ? null : array_values(array_filter(array_map(function ($id) {
      $c = competition_by_id($id);
      return $c ? ['id' => $c['id'], 'name' => $c['name'], 'short' => $c['short']] : null;
    }, $comps))),
    'scoring' => league_scoring($l),
    'start_at' => $l['start_at'],
    'created' => $l['created'],
    'members' => count(league_member_ids((int)$l['leagueid'])),
    'is_member' => $member,
    'is_owner' => $viewer && (int)$l['owner'] === $viewer['id'],
  ];
  if ($member || ($viewer && $viewer['is_admin'])) {
    $out['code'] = $l['code'];
  }
  return $out;
}

/** A private league's table, optionally for one season (from the later of its start and the season's). */
function league_table(array $l, ?array $season = null): array {
  $comps = (int)$l['all_comps'] ? null : league_comp_ids((int)$l['leagueid']);
  $b = season_bounds($season);
  $from = $l['start_at'] ?: null;
  if ($b['from'] !== null && ($from === null || $b['from'] > $from)) $from = $b['from'];
  return compute_table([
    'users' => league_member_ids((int)$l['leagueid']),
    'comps' => $comps,
    'scoring' => league_scoring($l),
    'from' => $from,
    'to' => $b['to'],
    'show_empty' => true,
  ]);
}

/** Validate the create/edit form. */
function league_fields(array $b, bool $creating): array {
  $name = trim(preg_replace('/\s+/u', ' ', str_in($b, 'name', 60)));
  if (mb_strlen($name) < 3) fail(422, 'League names need at least 3 characters.');
  $all = !empty($b['all_competitions']);
  $ids = array_values(array_unique(array_map('intval', is_array($b['competition_ids'] ?? null) ? $b['competition_ids'] : [])));
  $ids = array_values(array_intersect($ids, array_column(competitions(), 'id')));
  if (!$all && !$ids) {
    fail(422, competitions() ? 'Choose at least one competition for the league, or all of them.' : 'There are no competitions yet, so choose "All competitions".');
  }
  $num = function (string $key, int $default) use ($b) {
    $v = $b[$key] ?? $default;
    if (!is_numeric($v) || (int)$v != $v || (int)$v < 0 || (int)$v > 20) fail(422, 'Points must be whole numbers from 0 to 20.');
    return (int)$v;
  };
  $exact = $num('pts_exact', (int)setting('CORR_SCORE'));
  $result = $num('pts_result', (int)setting('CORR_RESULT'));
  if ($exact === 0 && $result === 0) fail(422, 'Give at least some points for an exact score or a right result.');
  $start = (string)($b['start'] ?? 'now');
  if ($start === 'all') {
    $startAt = null;
  } elseif ($start === 'date') {
    $d = DateTime::createFromFormat('!Y-m-d', (string)($b['start_date'] ?? ''));
    if (!$d || $d->format('Y-m-d') !== (string)$b['start_date']) fail(422, 'Choose the date the league starts counting from.');
    $startAt = $d->format('Y-m-d') . ' 00:00:00';
  } elseif ($start === 'keep' && !$creating) {
    $startAt = false;  // leave unchanged
  } else {
    $startAt = now_str();
  }
  return [$name, $all, $ids, $exact, $result, $startAt];
}

function save_league_comps(int $leagueId, bool $all, array $ids): void {
  q('DELETE FROM ' . xt('leaguecomps') . ' WHERE leagueid = ?', [$leagueId]);
  if (!$all) {
    foreach ($ids as $cid) q('INSERT INTO ' . xt('leaguecomps') . ' (leagueid, compid) VALUES (?, ?)', [$leagueId, $cid]);
  }
}

function ensure_league_tables(): void {
  foreach (['leagues', 'leaguecomps', 'members', 'follows'] as $t) {
    with_table($t, fn() => q('SELECT 1 FROM ' . xt($t) . ' WHERE 1 = 0'));
  }
}

function route_league_create(): array {
  $u = require_user();
  if (!leagues_on()) fail(403, 'Private leagues are turned off on this site.');
  ensure_league_tables();
  $owned = (int)q('SELECT COUNT(*) AS n FROM ' . xt('leagues') . ' WHERE lid = ? AND owner = ?', [lid(), $u['id']])->fetch()['n'];
  $max = max(1, (int)setting('MAX_LEAGUES_PER_PLAYER'));
  if ($owned >= $max && !$u['is_admin']) fail(422, "You can create up to $max leagues. Delete one you no longer use first.");
  [$name, $all, $ids, $exact, $result, $startAt] = league_fields(body(), true);

  $pdo = db();
  $pdo->beginTransaction();
  q('INSERT INTO ' . xt('leagues') . ' (lid, name, owner, code, all_comps, pts_exact, pts_result, start_at, created) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
    [lid(), $name, $u['id'], new_league_code(), $all ? 1 : 0, $exact, $result, $startAt, now_str()]);
  $id = (int)$pdo->lastInsertId();
  save_league_comps($id, $all, $ids);
  q('INSERT INTO ' . xt('members') . ' (leagueid, userid, role, joined) VALUES (?, ?, ?, ?)', [$id, $u['id'], 'owner', now_str()]);
  if (!$all) follow_competitions($u['id'], $ids);
  $pdo->commit();
  log_event('league_create', "Created the private league $name");
  return ['league' => league_out(get_league($id), $u)];
}

function route_league_update(): array {
  $b = body();
  $l = managed_league((int)($b['id'] ?? 0));
  [$name, $all, $ids, $exact, $result, $startAt] = league_fields($b, false);
  $id = (int)$l['leagueid'];
  $pdo = db();
  $pdo->beginTransaction();
  q('UPDATE ' . xt('leagues') . ' SET name = ?, all_comps = ?, pts_exact = ?, pts_result = ?, start_at = ? WHERE leagueid = ?',
    [$name, $all ? 1 : 0, $exact, $result, $startAt === false ? $l['start_at'] : $startAt, $id]);
  save_league_comps($id, $all, $ids);
  $pdo->commit();
  log_event('league_edit', "Changed the private league $name");
  return ['league' => league_out(get_league($id), current_user())];
}

function league_by_code(string $code): array {
  $code = clean_code($code);
  $l = strlen($code) === 8
    ? with_table('leagues', fn() => q('SELECT * FROM ' . xt('leagues') . ' WHERE lid = ? AND code = ?', [lid(), $code])->fetch())
    : false;
  if (!$l) {
    log_event('league_code_bad', 'Tried an invite code that doesn\'t exist');
    fail(404, 'That invite code doesn\'t match a league. Check it with whoever invited you; codes look like ABCD-2345.');
  }
  return $l;
}

function check_code_attempts(): void {
  if (recent_events('league_code_bad', 600) >= 10) {
    fail(429, 'Too many wrong invite codes. Wait 10 minutes and try again.');
  }
}

function route_league_preview(): array {
  $u = require_user();
  check_code_attempts();
  $l = league_by_code((string)($_GET['code'] ?? ''));
  return ['league' => league_out($l, $u) + ['can_join' => !is_member((int)$l['leagueid'], $u['id'])]];
}

function route_league_join(): array {
  $u = require_user();
  if (!leagues_on()) fail(403, 'Private leagues are turned off on this site.');
  check_code_attempts();
  ensure_league_tables();
  $l = league_by_code((string)(body()['code'] ?? ''));
  $id = (int)$l['leagueid'];
  if (is_member($id, $u['id'])) return ['league' => league_out($l, $u), 'already' => true];
  $max = max(2, (int)setting('MAX_LEAGUE_MEMBERS'));
  if (count(league_member_ids($id)) >= $max) fail(422, "This league is full (up to $max players).");
  q('INSERT INTO ' . xt('members') . ' (leagueid, userid, role, joined) VALUES (?, ?, ?, ?)', [$id, $u['id'], 'member', now_str()]);
  if (!(int)$l['all_comps']) follow_competitions($u['id'], league_comp_ids($id));
  log_event('league_join', 'Joined the private league ' . clean_text($l['name']));
  return ['league' => league_out($l, $u), 'already' => false];
}

function route_league_leave(): array {
  $u = require_user();
  $l = get_league((int)(body()['id'] ?? 0));
  if ((int)$l['owner'] === $u['id']) {
    fail(422, 'You created this league, so you can\'t just leave it. Hand it to another member first, or delete it.');
  }
  q('DELETE FROM ' . xt('members') . ' WHERE leagueid = ? AND userid = ?', [(int)$l['leagueid'], $u['id']]);
  log_event('league_leave', 'Left the private league ' . clean_text($l['name']));
  return ['left' => (int)$l['leagueid']];
}

function route_league_remove(): array {
  $b = body();
  $l = managed_league((int)($b['id'] ?? 0));
  $target = (int)($b['user_id'] ?? 0);
  if ($target === (int)$l['owner']) fail(422, 'The league\'s creator can\'t be removed. Hand the league to someone else first.');
  if (!is_member((int)$l['leagueid'], $target)) fail(404, 'That player isn\'t in this league.');
  q('DELETE FROM ' . xt('members') . ' WHERE leagueid = ? AND userid = ?', [(int)$l['leagueid'], $target]);
  $name = user_briefs([$target])[$target]['username'] ?? 'a player';
  log_event('league_remove', "Removed $name from the private league " . clean_text($l['name']));
  return route_table_for_league((int)$l['leagueid']);
}

function route_league_transfer(): array {
  $b = body();
  $l = managed_league((int)($b['id'] ?? 0));
  $target = (int)($b['user_id'] ?? 0);
  if (!is_member((int)$l['leagueid'], $target)) fail(404, 'That player isn\'t in this league.');
  q('UPDATE ' . xt('members') . " SET role = 'member' WHERE leagueid = ?", [(int)$l['leagueid']]);
  q('UPDATE ' . xt('members') . " SET role = 'owner' WHERE leagueid = ? AND userid = ?", [(int)$l['leagueid'], $target]);
  q('UPDATE ' . xt('leagues') . ' SET owner = ? WHERE leagueid = ?', [$target, (int)$l['leagueid']]);
  $name = user_briefs([$target])[$target]['username'] ?? 'a player';
  log_event('league_transfer', 'Handed the private league ' . clean_text($l['name']) . " to $name");
  return route_table_for_league((int)$l['leagueid']);
}

function route_league_newcode(): array {
  $l = managed_league((int)(body()['id'] ?? 0));
  q('UPDATE ' . xt('leagues') . ' SET code = ? WHERE leagueid = ?', [new_league_code(), (int)$l['leagueid']]);
  log_event('league_code', 'Made a new invite code for ' . clean_text($l['name']));
  return ['league' => league_out(get_league((int)$l['leagueid']), current_user())];
}

/** Delete a league and everything that belongs to it. */
function delete_league(int $id): void {
  q('DELETE FROM ' . xt('members') . ' WHERE leagueid = ?', [$id]);
  q('DELETE FROM ' . xt('leaguecomps') . ' WHERE leagueid = ?', [$id]);
  q('DELETE FROM ' . xt('leagues') . ' WHERE leagueid = ?', [$id]);
}

function route_league_delete(): array {
  $l = managed_league((int)(body()['id'] ?? 0));
  delete_league((int)$l['leagueid']);
  log_event('league_delete', 'Deleted the private league ' . clean_text($l['name']));
  return ['deleted' => (int)$l['leagueid']];
}

/** When a player is deleted: leave their leagues, hand on (or delete) the ones they created. */
function remove_player_from_leagues(int $uid): void {
  ensure_league_tables();
  foreach (q('SELECT leagueid FROM ' . xt('leagues') . ' WHERE lid = ? AND owner = ?', [lid(), $uid])->fetchAll(PDO::FETCH_COLUMN) as $lgId) {
    $next = q('SELECT userid FROM ' . xt('members') . ' WHERE leagueid = ? AND userid <> ? ORDER BY joined LIMIT 1', [(int)$lgId, $uid])->fetchColumn();
    if ($next) {
      q('UPDATE ' . xt('leagues') . ' SET owner = ? WHERE leagueid = ?', [(int)$next, (int)$lgId]);
      q('UPDATE ' . xt('members') . " SET role = 'owner' WHERE leagueid = ? AND userid = ?", [(int)$lgId, (int)$next]);
    } else {
      delete_league((int)$lgId);
    }
  }
  q('DELETE FROM ' . xt('members') . ' WHERE userid = ? AND leagueid IN (SELECT leagueid FROM ' . xt('leagues') . ' WHERE lid = ?)', [$uid, lid()]);
  q('DELETE FROM ' . xt('follows') . ' WHERE lid = ? AND userid = ?', [lid(), $uid]);
}

/** When a competition is deleted. */
function remove_competition_from_leagues(int $compId): void {
  ensure_league_tables();
  q('DELETE FROM ' . xt('leaguecomps') . ' WHERE compid = ? AND leagueid IN (SELECT leagueid FROM ' . xt('leagues') . ' WHERE lid = ?)', [$compId, lid()]);
  q('DELETE FROM ' . xt('follows') . ' WHERE lid = ? AND compid = ?', [lid(), $compId]);
}

// ---------------------------------------------------------------------
// The leagues page and tables
// ---------------------------------------------------------------------

/** Players who play a competition (explicitly, or by playing everything). */
function competition_players(int $compId): array {
  $follows = follows_map();
  $out = [];
  foreach (q('SELECT userid FROM ' . t('users') . ' WHERE lid = ?', [lid()])->fetchAll(PDO::FETCH_COLUMN) as $uid) {
    $f = $follows[(int)$uid] ?? null;
    if ($f === null || in_array($compId, $f, true)) $out[] = (int)$uid;
  }
  return $out;
}

function position_of(array $table, int $uid): ?array {
  foreach ($table as $r) {
    if ($r['user_id'] === $uid) return ['position' => $r['position'], 'points' => $r['points'], 'of' => count($table)];
  }
  return null;
}

function route_leagues(): array {
  $u = current_user();
  $season = requested_season();
  $comps = [];
  foreach (competitions() as $c) {
    $table = compute_table(['users' => competition_players($c['id']), 'comps' => [$c['id']], 'show_empty' => false] + season_bounds($season));
    $comps[] = ['id' => $c['id'], 'name' => $c['name'], 'short' => $c['short'],
                'plays' => $u ? plays_competition($u['id'], $c['id']) : false,
                'players' => count($table), 'me' => $u ? position_of($table, $u['id']) : null];
  }
  $overall = compute_table(season_bounds($season));
  $mine = [];
  if ($u) {
    ensure_league_tables();
    $ids = with_table('members', fn() => q('SELECT m.leagueid FROM ' . xt('members') . ' m JOIN ' . xt('leagues') . ' l ON l.leagueid = m.leagueid
                                              WHERE l.lid = ? AND m.userid = ? ORDER BY l.name', [lid(), $u['id']])->fetchAll(PDO::FETCH_COLUMN));
    foreach ($ids as $id) {
      $l = get_league((int)$id);
      $mine[] = league_out($l, $u) + ['me' => position_of(league_table($l, $season), $u['id'])];
    }
  }
  return [
    'overall' => ['players' => count($overall), 'me' => $u ? position_of($overall, $u['id']) : null],
    'competitions' => $comps,
    'mine' => $mine,
    'follows' => $u ? user_follows($u['id']) : null,
    'season' => season_brief($season),
    'leagues_on' => leagues_on(),
    'max_create' => (int)setting('MAX_LEAGUES_PER_PLAYER'),
  ];
}

function route_table_for_league(int $id, ?array $season = null): array {
  $u = require_user();
  $l = get_league($id);
  if (!is_member($id, $u['id']) && !$u['is_admin']) {
    fail(403, 'This is a private league. Ask a member for the invite code to join.');
  }
  return [
    'kind' => 'league',
    'title' => clean_text($l['name']),
    'league' => league_out($l, $u, true),
    'scoring' => league_scoring($l),
    'from' => $l['start_at'],
    'table' => league_table($l, $season),
    'season' => season_brief($season),
  ];
}

function route_table(): array {
  $season = requested_season();
  $type = (string)($_GET['type'] ?? 'overall');
  $id = (int)($_GET['id'] ?? 0);
  if ($type === 'league') return route_table_for_league($id, $season);
  if ($type === 'comp') {
    $c = competition_by_id($id);
    if (!$c) fail(404, 'That competition doesn\'t exist any more.');
    $u = current_user();
    return [
      'kind' => 'comp',
      'title' => $c['name'],
      'competition' => ['id' => $c['id'], 'name' => $c['name'], 'short' => $c['short']],
      'plays' => $u ? plays_competition($u['id'], $c['id']) : null,
      'scoring' => site_scoring(),
      'from' => null,
      'table' => compute_table(['users' => competition_players($c['id']), 'comps' => [$c['id']], 'show_empty' => false] + season_bounds($season)),
      'season' => season_brief($season),
    ];
  }
  return ['kind' => 'overall', 'title' => 'Overall', 'scoring' => site_scoring(), 'from' => null, 'table' => compute_table(season_bounds($season)), 'season' => season_brief($season)];
}

function route_admin_leagues(): array {
  require_admin();
  ensure_league_tables();
  $rows = q('SELECT * FROM ' . xt('leagues') . ' WHERE lid = ? ORDER BY created DESC', [lid()])->fetchAll();
  return ['leagues' => array_map(fn($l) => league_out($l, current_user()), $rows)];
}
