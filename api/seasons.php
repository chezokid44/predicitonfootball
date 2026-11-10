<?php
/*
 * Prediction League - seasons and teams.
 *
 * A season is a date range (e.g. 2026/27: 1 Aug 2026 to 31 Jul 2027). A
 * fixture belongs to the season its kick-off falls in, so nothing extra is
 * stored on fixtures. Tables can be shown for one season or for all time.
 *
 * Teams are a list of clubs and countries with an optional badge. For each
 * season, admins say which teams are in each competition (Leeds in the
 * Championship one season, the Premier League the next). Fixtures still store
 * team names, as in the old script; when a team list exists for a
 * competition and season, new fixtures must use teams from it.
 */

declare(strict_types=1);

if (!defined('PL_APP')) {
  http_response_code(403);
  exit;
}

const BADGE_DIR = 'uploads/badges';
const BADGE_SIZE = 128;

function season_routes(): array {
  return [
    'admin.seasons'          => ['GET',  'route_admin_seasons'],
    'admin.season.save'      => ['POST', 'route_admin_season_save'],
    'admin.season.delete'    => ['POST', 'route_admin_season_delete'],
    'admin.teams'            => ['GET',  'route_admin_teams'],
    'admin.team.save'        => ['POST', 'route_admin_team_save'],
    'admin.team.delete'      => ['POST', 'route_admin_team_delete'],
    'admin.team.badge'       => ['POST', 'route_admin_team_badge'],
    'admin.team.unbadge'     => ['POST', 'route_admin_team_unbadge'],
    'admin.compteams.save'   => ['POST', 'route_admin_compteams_save'],
    'admin.teams.import'     => ['POST', 'route_admin_teams_import'],
    'admin.teamlists'        => ['GET',  'route_admin_teamlists'],
  ];
}

// ---------------------------------------------------------------------
// Seasons
// ---------------------------------------------------------------------

/** All seasons, newest first. */
function seasons(bool $reload = false): array {
  static $rows = null;
  if ($rows === null || $reload) {
    $rows = array_map(fn($r) => [
      'id' => (int)$r['seasonid'], 'name' => clean_text($r['name']),
      'start' => $r['start_date'], 'end' => $r['end_date'], 'is_current' => (bool)(int)$r['is_current'],
    ], with_table('seasons', fn() => q('SELECT * FROM ' . xt('seasons') . ' WHERE lid = ? ORDER BY start_date DESC', [lid()])->fetchAll()));
  }
  return $rows;
}

function season_by_id(int $id): ?array {
  foreach (seasons() as $s) if ($s['id'] === $id) return $s;
  return null;
}

/** The season a kick-off ('Y-m-d H:i:s') falls in. */
function season_for_date(string $when): ?array {
  $day = substr($when, 0, 10);
  foreach (seasons() as $s) if ($s['start'] <= $day && $day <= $s['end']) return $s;
  return null;
}

/** The one marked current; otherwise the one we're in; otherwise the latest that has started. */
function current_season(): ?array {
  foreach (seasons() as $s) if ($s['is_current']) return $s;
  $today = date('Y-m-d');
  if ($s = season_for_date($today . ' 00:00:00')) return $s;
  foreach (seasons() as $s) if ($s['start'] <= $today) return $s;
  $all = seasons();
  return $all ? end($all) : null;
}

/** Datetime bounds for compute_table(). */
function season_bounds(?array $s): array {
  return $s ? ['from' => $s['start'] . ' 00:00:00', 'to' => $s['end'] . ' 23:59:59'] : ['from' => null, 'to' => null];
}

/** ?season= on a request: '' = current season, 'all' = all time, or an id. */
function requested_season(): ?array {
  $v = (string)($_GET['season'] ?? '');
  if ($v === 'all') return null;
  if ($v !== '' && ctype_digit($v)) {
    $s = season_by_id((int)$v);
    if (!$s) fail(404, 'That season doesn\'t exist any more.');
    return $s;
  }
  return current_season();
}

function season_brief(?array $s): ?array {
  return $s ? ['id' => $s['id'], 'name' => $s['name'], 'start' => $s['start'], 'end' => $s['end']] : null;
}

function public_seasons(): array {
  $cur = current_season();
  return ['seasons' => array_map('season_brief', seasons()), 'current_season' => $cur ? $cur['id'] : null];
}

function route_admin_seasons(): array {
  require_admin();
  $counts = [];
  foreach (q('SELECT matchdate FROM ' . t('matches') . ' WHERE lid = ?', [lid()])->fetchAll(PDO::FETCH_COLUMN) as $d) {
    $s = season_for_date((string)$d);
    $key = $s ? $s['id'] : 0;
    $counts[$key] = ($counts[$key] ?? 0) + 1;
  }
  $cur = current_season();
  return [
    'seasons' => array_map(fn($s) => $s + ['fixtures' => $counts[$s['id']] ?? 0, 'current' => $cur && $cur['id'] === $s['id']], seasons()),
    'outside' => $counts[0] ?? 0,
  ];
}

function valid_day(string $v, string $what): string {
  $d = DateTime::createFromFormat('!Y-m-d', $v);
  if (!$d || $d->format('Y-m-d') !== $v) fail(422, "Choose the date the season $what.");
  return $v;
}

function route_admin_season_save(): array {
  require_admin();
  $b = body();
  $id = (int)($b['id'] ?? 0);
  $name = trim(preg_replace('/\s+/u', ' ', str_in($b, 'name', 30)));
  if ($name === '') fail(422, 'Give the season a name, such as 2026/27.');
  $start = valid_day((string)($b['start'] ?? ''), 'starts');
  $end = valid_day((string)($b['end'] ?? ''), 'ends');
  if ($end < $start) fail(422, 'The season has to end after it starts.');
  seasons();
  if ($id && !season_by_id($id)) fail(404, 'That season doesn\'t exist any more.');
  foreach (seasons() as $s) {
    if ($s['id'] === $id) continue;
    if (mb_strtolower($s['name']) === mb_strtolower($name)) fail(409, "There's already a season called $name.");
    if ($start <= $s['end'] && $s['start'] <= $end) {
      fail(422, "Those dates overlap {$s['name']} ({$s['start']} to {$s['end']}). Each fixture can only be in one season.");
    }
  }
  $current = !empty($b['is_current']);
  if ($current) q('UPDATE ' . xt('seasons') . ' SET is_current = 0 WHERE lid = ?', [lid()]);
  if ($id) {
    q('UPDATE ' . xt('seasons') . ' SET name = ?, start_date = ?, end_date = ?, is_current = ? WHERE lid = ? AND seasonid = ?',
      [$name, $start, $end, $current ? 1 : 0, lid(), $id]);
  } else {
    q('INSERT INTO ' . xt('seasons') . ' (lid, name, start_date, end_date, is_current) VALUES (?, ?, ?, ?, ?)',
      [lid(), $name, $start, $end, $current ? 1 : 0]);
  }
  seasons(true);
  log_event('season', ($id ? 'Changed' : 'Added') . " the season $name ($start to $end)");
  return route_admin_seasons();
}

function route_admin_season_delete(): array {
  require_admin();
  $s = season_by_id((int)(body()['id'] ?? 0));
  if (!$s) fail(404, 'That season doesn\'t exist any more.');
  q('DELETE FROM ' . xt('compteams') . ' WHERE seasonid = ?', [$s['id']]);
  q('DELETE FROM ' . xt('seasons') . ' WHERE lid = ? AND seasonid = ?', [lid(), $s['id']]);
  seasons(true);
  log_event('season', "Deleted the season {$s['name']} (fixtures were kept)");
  return route_admin_seasons();
}

// ---------------------------------------------------------------------
// Teams
// ---------------------------------------------------------------------

/** lower-case name => team. */
function teams_map(bool $reload = false): array {
  static $map = null;
  if ($map === null || $reload) {
    $map = [];
    foreach (with_table('teams', fn() => q('SELECT * FROM ' . xt('teams') . ' WHERE lid = ? ORDER BY name', [lid()])->fetchAll()) as $r) {
      $map[mb_strtolower($r['name'])] = ['id' => (int)$r['teamid'], 'name' => clean_text($r['name']),
                                         'short' => clean_text((string)$r['short_name']), 'badge' => badge_url($r['badge'])];
    }
  }
  return $map;
}

function team_by_id(int $id): ?array {
  foreach (teams_map() as $t) if ($t['id'] === $id) return $t;
  return null;
}

function team_named(string $name): ?array {
  return teams_map()[mb_strtolower(trim($name))] ?? null;
}

function badge_url(?string $file): ?string {
  $file = (string)$file;
  return preg_match('#\Auploads/badges/t\d+-[a-f0-9]{12}\.png\z#', $file) ? $file : null;
}

function badge_for(string $teamName): ?string {
  $t = team_named($teamName);
  return $t ? $t['badge'] : null;
}

/** Add teams that aren't in the list yet (fixtures added with new names). */
function register_teams(array $names): void {
  foreach (array_unique(array_map('trim', $names)) as $name) {
    if ($name === '' || team_named($name)) continue;
    q('INSERT INTO ' . xt('teams') . ' (lid, name, short_name) VALUES (?, ?, ?)', [lid(), mb_substr($name, 0, 30), team_short_default($name)]);
    teams_map(true);
  }
}

/** "Arsenal" -> ARS; when that's taken, "Man United" -> MUN (first letter, then the next word). */
function team_short_default(string $name): string {
  $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name) ?: $name;
  $words = array_values(array_filter(preg_split('/[^A-Za-z]+/', $ascii)));
  if (!$words) return '';
  $taken = array_column(teams_map(), 'short');
  $options = [strtoupper(substr(implode('', $words), 0, 3))];
  if (count($words) > 1) {
    $options[] = strtoupper($words[0][0] . substr($words[count($words) - 1], 0, 2));
    $options[] = strtoupper(substr($words[0], 0, 2) . $words[count($words) - 1][0]);
  }
  foreach ($options as $o) if (!in_array($o, $taken, true)) return $o;
  return $options[0];
}

/** Team ids in a competition for a season. */
function comp_team_ids(int $seasonId, int $compId): array {
  return array_map('intval', with_table('compteams', fn() => q('SELECT teamid FROM ' . xt('compteams') . ' WHERE seasonid = ? AND compid = ?', [$seasonId, $compId])->fetchAll(PDO::FETCH_COLUMN)));
}

/**
 * For a fixture being added or changed: use each team's saved spelling, and,
 * when the competition has a team list for that season, insist on it.
 * Returns [home, away].
 */
function check_fixture_teams(string $home, string $away, string $kickoff, ?int $compId): array {
  $out = [];
  foreach ([$home, $away] as $name) {
    $t = team_named($name);
    $out[] = $t ? $t['name'] : $name;
  }
  if ($compId) {
    $season = season_for_date($kickoff);
    $ids = $season ? comp_team_ids($season['id'], $compId) : [];
    if ($ids) {
      $comp = competition_by_id($compId);
      foreach ($out as $name) {
        $t = team_named($name);
        if (!$t || !in_array($t['id'], $ids, true)) {
          fail(422, "$name isn't in {$comp['name']} for {$season['name']}. Check the spelling, or add the team in Admin > Teams.");
        }
      }
    }
  }
  return $out;
}

function route_admin_teams(): array {
  require_admin();
  $season = isset($_GET['season']) && ctype_digit((string)$_GET['season']) ? season_by_id((int)$_GET['season']) : current_season();
  $uses = [];
  foreach (q('SELECT hometeam, awayteam FROM ' . t('matches') . ' WHERE lid = ?', [lid()]) as $m) {
    foreach (['hometeam', 'awayteam'] as $k) {
      $key = mb_strtolower(trim((string)$m[$k]));
      $uses[$key] = ($uses[$key] ?? 0) + 1;
    }
  }
  $teams = array_values(array_map(fn($t) => $t + ['fixtures' => $uses[mb_strtolower($t['name'])] ?? 0], teams_map()));
  $known = array_flip(array_keys(teams_map()));
  $missing = count(array_diff_key($uses, $known));
  $assign = [];
  if ($season) {
    foreach (competitions() as $c) $assign[] = ['competition' => ['id' => $c['id'], 'name' => $c['name']], 'team_ids' => comp_team_ids($season['id'], $c['id'])];
  }
  return ['teams' => $teams, 'season' => season_brief($season), 'assign' => $assign,
          'unlisted_fixture_teams' => $missing, 'badges' => function_exists('imagecreatefromstring')];
}

function route_admin_team_save(): array {
  require_admin();
  $b = body();
  $id = (int)($b['id'] ?? 0);
  $name = trim(preg_replace('/\s+/u', ' ', str_in($b, 'name', 30)));
  if ($name === '') fail(422, 'Give the team a name.');
  $short = mb_strtoupper(mb_substr(trim(preg_replace('/[^\p{L}\p{N}]/u', '', (string)($b['short_name'] ?? ''))), 0, 5));
  if ($short === '') $short = team_short_default($name);
  teams_map();
  $other = team_named($name);
  if ($other && $other['id'] !== $id) fail(409, "There's already a team called {$other['name']}.");
  if ($id) {
    $old = team_by_id($id);
    if (!$old) fail(404, 'That team doesn\'t exist any more.');
    $pdo = db();
    $pdo->beginTransaction();
    q('UPDATE ' . xt('teams') . ' SET name = ?, short_name = ? WHERE lid = ? AND teamid = ?', [$name, $short, lid(), $id]);
    // Renaming a team renames it in every fixture too, so form and tables still match up.
    if ($old['name'] !== $name) {
      q('UPDATE ' . t('matches') . ' SET hometeam = ? WHERE lid = ? AND LOWER(hometeam) = ?', [$name, lid(), mb_strtolower($old['name'])]);
      q('UPDATE ' . t('matches') . ' SET awayteam = ? WHERE lid = ? AND LOWER(awayteam) = ?', [$name, lid(), mb_strtolower($old['name'])]);
    }
    $pdo->commit();
    log_event('team', $old['name'] === $name ? "Changed the team $name" : "Renamed the team {$old['name']} to $name (and its fixtures)");
  } else {
    q('INSERT INTO ' . xt('teams') . ' (lid, name, short_name) VALUES (?, ?, ?)', [lid(), $name, $short]);
    $id = (int)db()->lastInsertId();
    log_event('team', "Added the team $name");
    // Adding from a competition's list puts the team straight into it.
    $seasonId = (int)($b['season_id'] ?? 0);
    $compId = (int)($b['competition_id'] ?? 0);
    if ($seasonId && $compId && season_by_id($seasonId) && competition_by_id($compId)) {
      q('INSERT INTO ' . xt('compteams') . ' (seasonid, compid, teamid) VALUES (?, ?, ?)', [$seasonId, $compId, $id]);
    }
  }
  teams_map(true);
  $_GET['season'] = (string)($b['season_id'] ?? '');
  return route_admin_teams() + ['saved_id' => $id];
}

function route_admin_team_delete(): array {
  require_admin();
  $t = team_by_id((int)(body()['id'] ?? 0));
  if (!$t) fail(404, 'That team doesn\'t exist any more.');
  delete_badge_file($t['badge']);
  q('DELETE FROM ' . xt('compteams') . ' WHERE teamid = ?', [$t['id']]);
  q('DELETE FROM ' . xt('teams') . ' WHERE lid = ? AND teamid = ?', [lid(), $t['id']]);
  teams_map(true);
  log_event('team', "Deleted the team {$t['name']} (its fixtures were kept)");
  $_GET['season'] = (string)(body()['season_id'] ?? '');
  return route_admin_teams();
}

function route_admin_compteams_save(): array {
  require_admin();
  $b = body();
  $season = season_by_id((int)($b['season_id'] ?? 0));
  $comp = competition_by_id((int)($b['competition_id'] ?? 0));
  if (!$season || !$comp) fail(404, 'That season or competition doesn\'t exist any more.');
  $valid = array_column(teams_map(), 'id');
  $ids = array_values(array_intersect(array_unique(array_map('intval', is_array($b['team_ids'] ?? null) ? $b['team_ids'] : [])), $valid));
  $pdo = db();
  with_table('compteams', fn() => q('SELECT 1 FROM ' . xt('compteams') . ' WHERE 1 = 0'));
  $pdo->beginTransaction();
  q('DELETE FROM ' . xt('compteams') . ' WHERE seasonid = ? AND compid = ?', [$season['id'], $comp['id']]);
  foreach ($ids as $tid) q('INSERT INTO ' . xt('compteams') . ' (seasonid, compid, teamid) VALUES (?, ?, ?)', [$season['id'], $comp['id'], $tid]);
  $pdo->commit();
  log_event('team', "Set the teams in {$comp['name']} for {$season['name']}: " . count($ids) . ' teams');
  $_GET['season'] = (string)$season['id'];
  return route_admin_teams();
}

/**
 * Add every team name used in fixtures to the team list, and (with a season)
 * put teams into each competition they played in that season.
 */
function route_admin_teams_import(): array {
  require_admin();
  $b = body();
  teams_map();
  with_table('compteams', fn() => q('SELECT 1 FROM ' . xt('compteams') . ' WHERE 1 = 0'));
  $matches = q('SELECT matchid, matchdate, hometeam, awayteam FROM ' . t('matches') . ' WHERE lid = ?', [lid()])->fetchAll();
  $before = count(teams_map());
  $pdo = db();
  $pdo->beginTransaction();
  register_teams(array_merge(array_column($matches, 'hometeam'), array_column($matches, 'awayteam')));
  $added = count(teams_map()) - $before;
  $placed = 0;
  $season = season_by_id((int)($b['season_id'] ?? 0));
  if ($season) {
    $map = competition_map();
    $want = [];
    foreach ($matches as $m) {
      $cid = $map[(int)$m['matchid']] ?? null;
      $s = season_for_date((string)$m['matchdate']);
      if (!$cid || !$s || $s['id'] !== $season['id']) continue;
      foreach (['hometeam', 'awayteam'] as $k) $want[$cid][team_named((string)$m[$k])['id']] = true;
    }
    foreach ($want as $cid => $tids) {
      $have = comp_team_ids($season['id'], $cid);
      foreach (array_keys($tids) as $tid) {
        if (in_array($tid, $have, true)) continue;
        q('INSERT INTO ' . xt('compteams') . ' (seasonid, compid, teamid) VALUES (?, ?, ?)', [$season['id'], $cid, $tid]);
        $placed++;
      }
    }
  }
  $pdo->commit();
  log_event('team', "Added $added teams from fixtures" . ($season ? " and placed $placed in competitions for {$season['name']}" : ''));
  $_GET['season'] = $season ? (string)$season['id'] : '';
  return route_admin_teams() + ['added' => $added, 'placed' => $placed];
}

// ---------------------------------------------------------------------
// Badges
// ---------------------------------------------------------------------

function delete_badge_file(?string $url): void {
  if ($url && badge_url($url)) @unlink(dirname(__DIR__) . '/' . $url);
}

function route_admin_team_badge(): array {
  require_admin();
  $t = team_by_id((int)($_POST['id'] ?? 0));
  if (!$t) fail(404, 'That team doesn\'t exist any more.');
  if (!function_exists('imagecreatefromstring')) fail(500, 'The server can\'t process pictures (the PHP GD extension is missing).');
  $f = uploaded_file('badge', 5 * 1048576, 'badge');
  $info = @getimagesize($f['tmp_name']);
  $allowed = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_WEBP];
  if (!$info || !in_array($info[2], $allowed, true)) fail(422, 'Upload a PNG (best, for a see-through background), JPEG, GIF or WebP.');
  [$w, $h] = $info;
  if ($w < 16 || $h < 16) fail(422, 'That badge is too small. Use one at least 16 pixels wide.');
  if ($w * $h > 25000000) fail(422, 'That picture is too large to process.');
  $src = @imagecreatefromstring((string)file_get_contents($f['tmp_name']));
  if (!$src) fail(422, 'That picture couldn\'t be read. Try saving it as a PNG.');
  // Fit the whole badge in a square, keeping its shape and transparency.
  $scale = BADGE_SIZE / max($w, $h);
  $nw = max(1, (int)round($w * $scale));
  $nh = max(1, (int)round($h * $scale));
  $dst = imagecreatetruecolor(BADGE_SIZE, BADGE_SIZE);
  imagealphablending($dst, false);
  imagesavealpha($dst, true);
  imagefill($dst, 0, 0, imagecolorallocatealpha($dst, 0, 0, 0, 127));
  imagealphablending($dst, true);
  imagecopyresampled($dst, $src, intdiv(BADGE_SIZE - $nw, 2), intdiv(BADGE_SIZE - $nh, 2), 0, 0, $nw, $nh, $w, $h);
  imagedestroy($src);
  imagealphablending($dst, false);
  $dir = dirname(__DIR__) . '/' . BADGE_DIR;
  if (!is_dir($dir)) @mkdir($dir, 0755, true);
  if (!is_dir($dir) || !is_writable($dir)) {
    imagedestroy($dst);
    fail(500, 'The server can\'t save badges yet. Make the uploads/badges folder writable.');
  }
  $file = BADGE_DIR . '/t' . $t['id'] . '-' . bin2hex(random_bytes(6)) . '.png';
  $ok = imagepng($dst, dirname(__DIR__) . '/' . $file, 6);
  imagedestroy($dst);
  if (!$ok) fail(500, 'The badge couldn\'t be saved. Try again.');
  delete_badge_file($t['badge']);
  q('UPDATE ' . xt('teams') . ' SET badge = ? WHERE lid = ? AND teamid = ?', [$file, lid(), $t['id']]);
  teams_map(true);
  log_event('team', "Uploaded a badge for {$t['name']}");
  return ['team' => team_by_id($t['id'])];
}

function route_admin_team_unbadge(): array {
  require_admin();
  $t = team_by_id((int)(body()['id'] ?? 0));
  if (!$t) fail(404, 'That team doesn\'t exist any more.');
  delete_badge_file($t['badge']);
  q('UPDATE ' . xt('teams') . ' SET badge = NULL WHERE lid = ? AND teamid = ?', [lid(), $t['id']]);
  teams_map(true);
  return ['team' => team_by_id($t['id'])];
}

/**
 * For the fixture form: every team name, the seasons, and the team names in
 * each competition each season, so the home/away suggestions can be narrowed.
 */
function route_admin_teamlists(): array {
  require_admin();
  $names = [];
  foreach (teams_map() as $t) $names[$t['id']] = $t['name'];
  $lists = [];
  $sids = array_column(seasons(), 'id');
  if ($sids) {
    $rows = with_table('compteams', fn() => q('SELECT seasonid, compid, teamid FROM ' . xt('compteams') . ' WHERE seasonid IN (' . implode(',', array_fill(0, count($sids), '?')) . ')', $sids)->fetchAll());
    foreach ($rows as $r) {
      if (isset($names[(int)$r['teamid']])) $lists[(int)$r['seasonid']][(int)$r['compid']][] = $names[(int)$r['teamid']];
    }
  }
  foreach ($lists as &$byComp) foreach ($byComp as &$l) sort($l, SORT_NATURAL | SORT_FLAG_CASE);
  unset($byComp, $l);
  return [
    'teams' => array_values($names),
    'seasons' => array_map(fn($s) => ['id' => $s['id'], 'start' => $s['start'], 'end' => $s['end']], seasons()),
    'lists' => (object)array_map(fn($b) => (object)$b, $lists),
  ];
}
