<?php
/*
 * Prediction League - the real league tables.
 *
 * For each competition marked as a league (not a cup), a table of the teams
 * built from the results entered for its fixtures: played, won, drawn, lost,
 * goals, goal difference, points (3 for a win, 1 for a draw) and recent form.
 * One table per season; teams in the competition's team list for that season
 * appear even before they've played.
 *
 * Points adjustments (e.g. a team starting the season on -4) are added to a
 * team's points for one competition and season, with a reason shown under
 * the table.
 */

declare(strict_types=1);

if (!defined('PL_APP')) {
  http_response_code(403);
  exit;
}

function standings_routes(): array {
  return [
    'teamtable' => ['GET', 'route_teamtable'],
    'admin.pointadj.add' => ['POST', 'route_admin_pointadj_add'],
    'admin.pointadj.remove' => ['POST', 'route_admin_pointadj_remove'],
  ];
}

/** Competitions that show a league table. */
function table_competitions(): array {
  return array_values(array_filter(competitions(), fn($c) => $c['has_table']));
}

/**
 * The table for one competition in one season (null = all fixtures ever).
 * Sorted by points, goal difference, goals scored, then name.
 */
function team_standings(int $compId, ?array $season): array {
  $map = competition_map();
  $b = season_bounds($season);
  $rows = [];
  $blank = fn(string $name) => ['team' => $name, 'played' => 0, 'won' => 0, 'drawn' => 0, 'lost' => 0,
                                'for' => 0, 'against' => 0, 'diff' => 0, 'points' => 0, 'form' => []];
  // Everyone in the season's team list, even before they've played.
  if ($season) {
    foreach (comp_team_ids($season['id'], $compId) as $tid) {
      $t = team_by_id($tid);
      if ($t) $rows[mb_strtolower($t['name'])] = $blank($t['name']);
    }
  }
  $matches = q('SELECT matchid, matchdate, hometeam, awayteam, homescore, awayscore FROM ' . t('matches') . '
                WHERE lid = ? AND homescore IS NOT NULL AND awayscore IS NOT NULL ORDER BY matchdate', [lid()])->fetchAll();
  foreach ($matches as $m) {
    if (($map[(int)$m['matchid']] ?? null) !== $compId) continue;
    $when = (string)$m['matchdate'];
    if ($b['from'] !== null && ($when < $b['from'] || $when > $b['to'])) continue;
    $hs = (int)$m['homescore'];
    $as = (int)$m['awayscore'];
    foreach ([[(string)$m['hometeam'], $hs, $as], [(string)$m['awayteam'], $as, $hs]] as [$name, $gf, $ga]) {
      $key = mb_strtolower(trim($name));
      if (!isset($rows[$key])) $rows[$key] = $blank(team_named($name)['name'] ?? trim($name));
      $r = &$rows[$key];
      $r['played']++;
      $r['for'] += $gf;
      $r['against'] += $ga;
      $res = $gf > $ga ? 'W' : ($gf === $ga ? 'D' : 'L');
      $r[$res === 'W' ? 'won' : ($res === 'D' ? 'drawn' : 'lost')]++;
      $r['points'] += $res === 'W' ? 3 : ($res === 'D' ? 1 : 0);
      $r['form'][] = $res;
      unset($r);
    }
  }
  // Points adjustments for this competition (and season; all seasons for "all fixtures").
  foreach (point_adjustments($compId, $season) as $a) {
    $key = mb_strtolower(trim($a['team']));
    if (!isset($rows[$key])) $rows[$key] = $blank($a['team']);
    $rows[$key]['adjust'] = ($rows[$key]['adjust'] ?? 0) + $a['points'];
    $rows[$key]['points'] += $a['points'];
  }
  $out = [];
  foreach ($rows as $r) {
    $r['adjust'] = $r['adjust'] ?? 0;
    $r['diff'] = $r['for'] - $r['against'];
    $r['form'] = array_slice($r['form'], -5);
    $t = team_named($r['team']);
    $r['badge'] = $t['badge'] ?? null;
    $r['short'] = $t['short'] ?? '';
    $out[] = $r;
  }
  usort($out, fn($a, $b) => [$b['points'], $b['diff'], $b['for'], mb_strtolower($a['team'])] <=> [$a['points'], $a['diff'], $a['for'], mb_strtolower($b['team'])]);
  // Teams level on points, goal difference and goals share a position.
  $pos = 0;
  $prev = null;
  foreach ($out as $i => &$r) {
    $key = [$r['points'], $r['diff'], $r['for']];
    if ($key !== $prev) $pos = $i + 1;
    $r['position'] = $pos;
    $prev = $key;
  }
  unset($r);
  return $out;
}

/**
 * Points adjustments for a competition: one season's ($season), or every
 * season's when $season is null. Seasonless ones (made before any season
 * existed) always count.
 */
function point_adjustments(int $compId, ?array $season): array {
  $sql = 'SELECT id, team, points, reason, seasonid, created FROM ' . xt('pointadj') . ' WHERE lid = ? AND compid = ?';
  $args = [lid(), $compId];
  if ($season) {
    $sql .= ' AND seasonid IN (0, ?)';
    $args[] = (int)$season['id'];
  }
  $rows = with_table('pointadj', fn() => q($sql . ' ORDER BY id', $args)->fetchAll());
  return array_map(fn($r) => ['id' => (int)$r['id'], 'team' => (string)$r['team'], 'points' => (int)$r['points'],
                               'reason' => (string)$r['reason'], 'season_id' => (int)$r['seasonid'] ?: null, 'created' => $r['created']], $rows);
}

function route_admin_pointadj_add(): array {
  require_admin();
  $in = body();
  $comp = competition_by_id((int)($in['competition_id'] ?? 0));
  if (!$comp) fail(422, 'Choose a competition.');
  $seasonId = 0;
  if (seasons()) {
    $season = season_by_id((int)($in['season_id'] ?? 0));
    if (!$season) fail(422, 'Choose the season the adjustment is for.');
    $seasonId = (int)$season['id'];
  }
  $team = trim(preg_replace('/\s+/u', ' ', (string)($in['team'] ?? '')));
  if ($team === '' || mb_strlen($team) > 60) fail(422, 'Choose a team.');
  $known = team_named($team)['name'] ?? null;
  if ($known === null) {
    // Use the spelling from the fixtures, so the note matches the table.
    $m = q('SELECT hometeam, awayteam FROM ' . t('matches') . ' WHERE lid = ? AND (LOWER(hometeam) = LOWER(?) OR LOWER(awayteam) = LOWER(?)) LIMIT 1', [lid(), $team, $team])->fetch();
    if ($m) $known = mb_strtolower(trim((string)$m['hometeam'])) === mb_strtolower($team) ? trim((string)$m['hometeam']) : trim((string)$m['awayteam']);
  }
  $team = $known ?? $team;
  $raw = trim((string)($in['points'] ?? ''));
  if (!preg_match('/^[+-]?\d{1,3}$/', $raw) || (int)$raw === 0) fail(422, 'Enter the points to add or take away, e.g. -4 (from -999 to 999, not 0).');
  $points = (int)$raw;
  $reason = mb_substr(trim(preg_replace('/[\x00-\x1F\x7F]/u', ' ', (string)($in['reason'] ?? ''))), 0, 100);
  with_table('pointadj', fn() => q('INSERT INTO ' . xt('pointadj') . ' (lid, compid, seasonid, team, points, reason, created) VALUES (?, ?, ?, ?, ?, ?, ?)',
    [lid(), $comp['id'], $seasonId, $team, $points, $reason, now_str()]));
  log_event('pointadj', sprintf('%s: %s%d points for %s%s', $comp['name'], $points > 0 ? '+' : '', $points, $team, $reason !== '' ? " ($reason)" : ''));
  return ['ok' => true];
}

function route_admin_pointadj_remove(): array {
  require_admin();
  $id = (int)(body()['id'] ?? 0);
  $r = with_table('pointadj', fn() => q('SELECT compid, team, points FROM ' . xt('pointadj') . ' WHERE lid = ? AND id = ?', [lid(), $id])->fetch());
  if (!$r) fail(404, 'That adjustment has already been removed.');
  q('DELETE FROM ' . xt('pointadj') . ' WHERE lid = ? AND id = ?', [lid(), $id]);
  $c = competition_by_id((int)$r['compid']);
  log_event('pointadj', sprintf('%s: removed the %s%d points for %s', $c['name'] ?? 'Competition', (int)$r['points'] > 0 ? '+' : '', (int)$r['points'], $r['team']));
  return ['ok' => true];
}

function route_teamtable(): array {
  $comps = table_competitions();
  $id = (int)($_GET['comp'] ?? 0);
  $comp = null;
  foreach ($comps as $c) if ($c['id'] === $id) $comp = $c;
  if (!$comp && $id) {
    $c = competition_by_id($id);
    if (!$c) fail(404, 'That competition doesn\'t exist any more.');
    fail(404, "{$c['name']} is a cup, so it has no league table.");
  }
  $comp = $comp ?? ($comps[0] ?? null);
  $season = requested_season();
  return [
    'competitions' => array_map(fn($c) => ['id' => $c['id'], 'name' => $c['name'], 'short' => $c['short']], $comps),
    'competition' => $comp ? ['id' => $comp['id'], 'name' => $comp['name'], 'short' => $comp['short'], 'places' => $comp['places']] : null,
    'season' => season_brief($season),
    'table' => $comp ? team_standings($comp['id'], $season) : [],
    'adjustments' => $comp ? point_adjustments($comp['id'], $season) : [],
  ];
}
