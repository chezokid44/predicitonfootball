<?php
/*
 * Prediction League - the real league tables.
 *
 * For each competition marked as a league (not a cup), a table of the teams
 * built from the results entered for its fixtures: played, won, drawn, lost,
 * goals, goal difference, points (3 for a win, 1 for a draw) and recent form.
 * One table per season; teams in the competition's team list for that season
 * appear even before they've played.
 */

declare(strict_types=1);

if (!defined('PL_APP')) {
  http_response_code(403);
  exit;
}

function standings_routes(): array {
  return [
    'teamtable' => ['GET', 'route_teamtable'],
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
  $out = [];
  foreach ($rows as $r) {
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
    'competition' => $comp ? ['id' => $comp['id'], 'name' => $comp['name'], 'short' => $comp['short'], 'zones' => $comp['zones']] : null,
    'season' => season_brief($season),
    'table' => $comp ? team_standings($comp['id'], $season) : [],
  ];
}
