<?php
/**
 * Fetch results automatically from a free source, for matches that have
 * already kicked off (today or earlier). Future fixtures are never touched.
 *
 * Sources (set per competition in Admin > Results):
 *  - fixturedownload.com: free, no sign-up. Paste the competition's JSON (or
 *    CSV) link. Team names match a fixture list imported from the same site.
 *  - football-data.org: free account and API key (Premier League,
 *    Championship and other top leagues). Team names are matched loosely
 *    ("Leeds United FC" = "Leeds United"); add name differences if needed.
 *
 * Only empty results are filled in. A result that differs from the source is
 * shown, but only changed if the admin ticks "also correct results that
 * differ". With AUTO_RESULTS on, the site checks by itself every 30 minutes
 * while there are matches waiting for a result (at least 2 hours after kick-
 * off), after answering a page, so nobody waits for it.
 */
declare(strict_types=1);

if (!defined('PL_APP')) {
  http_response_code(404);
  exit;
}

const AUTO_RESULTS_GAP_MINUTES = 30;   // automatic checks at most this often
const AUTO_RESULTS_AFTER_MINUTES = 110; // a match needs a result this long after kick-off
const RESULTS_FETCH_MAX_BYTES = 5242880;
const RESULTS_MATCH_DAYS = 45;         // a source match counts within this many days of ours

function autoresults_routes(): array {
  return [
    'admin.resultsources'      => ['GET',  'route_admin_resultsources'],
    'admin.resultsource.save'  => ['POST', 'route_admin_resultsource_save'],
    'admin.fdtoken.save'       => ['POST', 'route_admin_fdtoken_save'],
    'admin.fetchresults'       => ['POST', 'route_admin_fetchresults'],
  ];
}

// ---------------------------------------------------------------------
// Settings for each competition
// ---------------------------------------------------------------------

/** @return array{type:string,url:string,code:string,aliases:string} */
function result_source(int $compId): array {
  $raw = store_get('rsrc:' . $compId);
  $d = $raw ? json_decode($raw, true) : null;
  $d = is_array($d) ? $d : [];
  return [
    'type' => in_array($d['type'] ?? '', ['fixturedownload', 'footballdata'], true) ? $d['type'] : 'none',
    'url' => (string)($d['url'] ?? ''),
    'code' => (string)($d['code'] ?? ''),
    'aliases' => (string)($d['aliases'] ?? ''),
  ];
}

function fd_token(): ?string {
  $t = store_get('fd_token');
  return $t !== null && $t !== '' ? $t : null;
}

function football_data_base(): string {
  $b = cfg('football_data_base');
  return is_string($b) && $b !== '' ? rtrim($b, '/') : 'https://api.football-data.org';
}

function source_url_ok(string $url): bool {
  $p = parse_url($url);
  if (!$p || empty($p['host'])) return false;
  if (($p['scheme'] ?? '') === 'https') return true;
  return cfg('debug') && ($p['scheme'] ?? '') === 'http' && in_array($p['host'], ['127.0.0.1', 'localhost'], true);
}

function route_admin_resultsources(): array {
  require_admin();
  $out = [];
  foreach (competitions() as $c) {
    $s = result_source($c['id']);
    $out[] = ['competition_id' => $c['id'], 'name' => $c['name']] + $s;
  }
  $last = store_get('rsrc_last');
  return [
    'sources' => $out,
    'fd_token_set' => fd_token() !== null,
    'auto' => setting_bool('AUTO_RESULTS'),
    'last' => $last ? json_decode($last, true) : null,
    'waiting' => count(matches_needing_results(null, true)),
  ];
}

function route_admin_resultsource_save(): array {
  require_admin();
  $b = body();
  $comp = competition_by_id((int)($b['competition_id'] ?? 0));
  if (!$comp) fail(422, 'Choose a competition.');
  $type = (string)($b['type'] ?? 'none');
  if (!in_array($type, ['none', 'fixturedownload', 'footballdata'], true)) fail(422, 'Choose where results come from.');
  $url = trim((string)($b['url'] ?? ''));
  $code = strtoupper(trim((string)($b['code'] ?? '')));
  $aliases = mb_substr(trim(str_replace("\r", '', (string)($b['aliases'] ?? ''))), 0, 4000);
  if ($type === 'fixturedownload') {
    if (!source_url_ok($url) || mb_strlen($url) > 300) fail(422, 'Paste the fixturedownload.com link for this competition (starting https://).');
  } else {
    $url = '';
  }
  if ($type === 'footballdata') {
    if (!preg_match('/^[A-Z0-9]{2,5}$/', $code)) fail(422, 'Enter the football-data.org competition code, e.g. ELC for the Championship or PL for the Premier League.');
  } else {
    $code = '';
  }
  foreach (array_filter(explode("\n", $aliases), fn($l) => trim($l) !== '') as $line) {
    if (substr_count($line, '=') !== 1) fail(422, "Each name difference goes on its own line as: their name = our name. This line isn't: " . mb_substr($line, 0, 60));
  }
  store_set('rsrc:' . $comp['id'], $type === 'none' ? null : json_encode(['type' => $type, 'url' => $url, 'code' => $code, 'aliases' => $aliases], JSON_UNESCAPED_SLASHES));
  log_event('settings', $comp['name'] . ': results source ' . ($type === 'none' ? 'removed' : 'set to ' . ($type === 'footballdata' ? "football-data.org ($code)" : 'fixturedownload.com')));
  return route_admin_resultsources();
}

function route_admin_fdtoken_save(): array {
  require_admin();
  $t = trim((string)(body()['token'] ?? ''));
  if ($t !== '' && !preg_match('/^[A-Za-z0-9]{16,64}$/', $t)) fail(422, 'That doesn\'t look like a football-data.org API key (letters and numbers, from the email they send you).');
  store_set('fd_token', $t === '' ? null : $t);
  log_event('settings', $t === '' ? 'Removed the football-data.org API key' : 'Saved a football-data.org API key');
  return route_admin_resultsources();
}

// ---------------------------------------------------------------------
// Which of our matches need a result
// ---------------------------------------------------------------------

/**
 * Matches that have kicked off (today or before), aren't postponed, and
 * have no result. With $settled, only those at least AUTO_RESULTS_AFTER_MINUTES
 * after kick-off. With $withResults, matches that already have a result too.
 */
function matches_needing_results(?int $compId, bool $settled = false, bool $withResults = false): array {
  $cut = $settled ? date('Y-m-d H:i:s', time() - AUTO_RESULTS_AFTER_MINUTES * 60) : now_str();
  $sql = 'SELECT m.*, mc.compid FROM ' . t('matches') . ' m JOIN ' . xt('matchcomp') . ' mc ON mc.lid = m.lid AND mc.matchid = m.matchid'
       . ' WHERE m.lid = ? AND m.matchdate <= ?' . ($withResults ? '' : ' AND m.homescore IS NULL');
  $args = [lid(), $cut];
  if ($compId !== null) { $sql .= ' AND mc.compid = ?'; $args[] = $compId; }
  $rows = with_table('matchcomp', fn() => q($sql . ' ORDER BY m.matchdate, m.matchid', $args)->fetchAll());
  $pp = postponed_map();
  return array_values(array_filter($rows, fn($m) => !isset($pp[(int)$m['matchid']])));
}

// ---------------------------------------------------------------------
// Fetching
// ---------------------------------------------------------------------

/** GET a URL. Returns [status, body] or throws ApiError. */
function http_fetch(string $url, array $headers = []): array {
  $headers[] = 'User-Agent: PredictionLeague/2 (results)';
  if (function_exists('curl_init')) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => true, CURLOPT_MAXREDIRS => 3,
      CURLOPT_TIMEOUT => 15, CURLOPT_CONNECTTIMEOUT => 6, CURLOPT_HTTPHEADER => $headers,
      CURLOPT_PROTOCOLS => CURLPROTO_HTTPS | CURLPROTO_HTTP, CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTPS,
      CURLOPT_ENCODING => '']);
    $body = curl_exec($ch);
    $status = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $err = curl_error($ch);
    curl_close($ch);
    if ($body === false || $status === 0) throw new ApiError(502, 'Couldn\'t reach ' . parse_url($url, PHP_URL_HOST) . ($err !== '' ? ": $err" : '.'));
  } else {
    $ctx = stream_context_create(['http' => ['method' => 'GET', 'header' => implode("\r\n", $headers) . "\r\n", 'timeout' => 15, 'ignore_errors' => true]]);
    $body = @file_get_contents($url, false, $ctx, 0, RESULTS_FETCH_MAX_BYTES + 1);
    $status = 0;
    foreach ($http_response_header ?? [] as $h) if (preg_match('#^HTTP/\S+\s+(\d{3})#', $h, $m)) $status = (int)$m[1];
    if ($body === false || $status === 0) throw new ApiError(502, 'Couldn\'t reach ' . parse_url($url, PHP_URL_HOST) . '. The server may block outgoing connections.');
  }
  if (strlen((string)$body) > RESULTS_FETCH_MAX_BYTES) throw new ApiError(502, 'The results source sent too much data.');
  return [$status, (string)$body];
}

/**
 * The source's matches: [home, away, time (Y-m-d H:i:s, local), home score|null,
 * away score|null, postponed bool]. Only finished ones have scores.
 */
function source_matches(array $src): array {
  if ($src['type'] === 'fixturedownload') {
    [$status, $body] = http_fetch($src['url'], ['Accept: application/json, text/csv;q=0.9, */*;q=0.5']);
    if ($status !== 200) throw new ApiError(502, "fixturedownload.com answered $status. Check the link.");
    return parse_fixturedownload($body);
  }
  if ($src['type'] === 'footballdata') {
    $token = fd_token();
    if ($token === null) throw new ApiError(422, 'Save your football-data.org API key first (it\'s free: sign up at football-data.org).');
    $url = football_data_base() . '/v4/competitions/' . rawurlencode($src['code']) . '/matches';
    [$status, $body] = http_fetch($url, ['X-Auth-Token: ' . $token, 'Accept: application/json']);
    if ($status === 400 || $status === 403) {
      $msg = json_decode($body, true)['message'] ?? '';
      throw new ApiError(502, "football-data.org refused: the free plan may not include {$src['code']}, or the key is wrong." . ($msg !== '' ? " ($msg)" : ''));
    }
    if ($status === 404) throw new ApiError(502, "football-data.org doesn't know the competition code {$src['code']}.");
    if ($status === 429) throw new ApiError(502, 'football-data.org asked the site to slow down (the free plan allows 10 requests a minute). Try again in a minute.');
    if ($status !== 200) throw new ApiError(502, "football-data.org answered $status.");
    return parse_football_data($body);
  }
  return [];
}

function utc_to_local(string $utc): ?string {
  $t = strtotime(str_replace('Z', ' UTC', preg_replace('/T/', ' ', trim($utc), 1)));
  return $t === false ? null : date('Y-m-d H:i:s', $t);
}

function parse_fixturedownload(string $body): array {
  $body = preg_replace('/^\xEF\xBB\xBF/', '', $body);
  $data = json_decode($body, true);
  $out = [];
  if (is_array($data)) {
    foreach ($data as $r) {
      if (!is_array($r)) continue;
      $r = array_change_key_case($r, CASE_LOWER);
      $home = trim((string)($r['hometeam'] ?? ''));
      $away = trim((string)($r['awayteam'] ?? ''));
      if ($home === '' || $away === '') continue;
      $hs = $r['hometeamscore'] ?? null;
      $as = $r['awayteamscore'] ?? null;
      $done = is_numeric($hs) && is_numeric($as);
      $out[] = [$home, $away, utc_to_local((string)($r['dateutc'] ?? '')), $done ? (int)$hs : null, $done ? (int)$as : null, false];
    }
    return $out;
  }
  // CSV: Match Number, Round Number, Date, Location, Home Team, Away Team, Result
  $lines = preg_split('/\R/', trim($body));
  $head = array_map(fn($h) => strtolower(trim($h)), str_getcsv((string)array_shift($lines)));
  $col = fn($n) => array_search($n, $head, true);
  [$h, $a, $res, $dt] = [$col('home team'), $col('away team'), $col('result'), $col('date')];
  if ($h === false || $a === false || $res === false) throw new ApiError(502, 'The fixturedownload.com link didn\'t give a fixture list. Use the JSON or CSV link for the competition.');
  foreach ($lines as $line) {
    if (trim($line) === '') continue;
    $f = str_getcsv($line);
    $home = trim((string)($f[$h] ?? ''));
    $away = trim((string)($f[$a] ?? ''));
    if ($home === '' || $away === '') continue;
    $score = csv_score((string)($f[$res] ?? ''));
    $when = null;
    if ($dt !== false && preg_match('#^(\d{1,2})/(\d{1,2})/(\d{4})\s+(\d{1,2}):(\d{2})#', (string)($f[$dt] ?? ''), $m)) {
      $when = sprintf('%04d-%02d-%02d %02d:%02d:00', $m[3], $m[2], $m[1], $m[4], $m[5]);
    }
    $out[] = [$home, $away, $when, is_array($score) ? $score[0] : null, is_array($score) ? $score[1] : null, $score === 'postponed'];
  }
  return $out;
}

function parse_football_data(string $body): array {
  $d = json_decode($body, true);
  if (!is_array($d) || !isset($d['matches']) || !is_array($d['matches'])) throw new ApiError(502, 'football-data.org sent something unexpected.');
  $out = [];
  foreach ($d['matches'] as $m) {
    $status = (string)($m['status'] ?? '');
    $ft = $m['score']['fullTime'] ?? [];
    $done = $status === 'FINISHED' && is_numeric($ft['home'] ?? null) && is_numeric($ft['away'] ?? null);
    $names = fn($t) => array_values(array_filter([(string)($t['name'] ?? ''), (string)($t['shortName'] ?? ''), (string)($t['tla'] ?? '')]));
    $out[] = [$names($m['homeTeam'] ?? []), $names($m['awayTeam'] ?? []), utc_to_local((string)($m['utcDate'] ?? '')),
              $done ? (int)$ft['home'] : null, $done ? (int)$ft['away'] : null, in_array($status, ['POSTPONED', 'SUSPENDED', 'CANCELLED'], true)];
  }
  return $out;
}

/** A name for matching: lower case, no accents, "&" = "and", without FC/AFC and the like. */
function match_name(string $n): string {
  $n = mb_strtolower(clean_text($n));
  if (function_exists('iconv')) {
    $t = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $n);
    if (is_string($t) && $t !== '') $n = strtolower($t);
  }
  $n = str_replace(['&', '.', "'"], [' and ', '', ''], $n);
  $n = preg_replace('/[^a-z0-9 ]+/', ' ', $n);
  $n = preg_replace('/\b(a?fc|cf|sc|the)\b/', ' ', $n);
  return trim(preg_replace('/\s+/', ' ', $n));
}

function alias_map(string $aliases): array {
  $map = [];
  foreach (explode("\n", $aliases) as $line) {
    if (substr_count($line, '=') !== 1) continue;
    [$theirs, $ours] = array_map('trim', explode('=', $line));
    if ($theirs !== '' && $ours !== '') $map[match_name($theirs)] = match_name($ours);
  }
  return $map;
}

/** Does one of the source's names for a team mean our team? */
function same_team(string $ours, $theirs, array $aliases): bool {
  $o = match_name($ours);
  foreach ((array)$theirs as $t) {
    $k = match_name((string)$t);
    if ($k === '') continue;
    if ($k === $o || ($aliases[$k] ?? null) === $o) return true;
  }
  return false;
}

/**
 * Compare a competition's source with our matches.
 * @return array{rows: array, unmatched: int}
 */
function compare_results(array $comp, bool $withResults): array {
  $src = result_source($comp['id']);
  $theirs = source_matches($src);
  $aliases = alias_map($src['aliases']);
  $rows = [];
  foreach (matches_needing_results($comp['id'], false, $withResults) as $m) {
    $label = clean_text($m['hometeam']) . ' v ' . clean_text($m['awayteam']);
    $best = null;
    $bestGap = PHP_INT_MAX;
    foreach ($theirs as $t) {
      if (!same_team((string)$m['hometeam'], $t[0], $aliases) || !same_team((string)$m['awayteam'], $t[1], $aliases)) continue;
      $gap = $t[2] ? abs(strtotime($t[2]) - strtotime((string)$m['matchdate'])) : 0;
      if ($gap <= RESULTS_MATCH_DAYS * 86400 && $gap < $bestGap) { $best = $t; $bestGap = $gap; }
    }
    $row = ['id' => (int)$m['matchid'], 'label' => $label, 'kickoff' => (string)$m['matchdate'],
            'current' => $m['homescore'] === null ? null : [(int)$m['homescore'], (int)$m['awayscore']], 'result' => null];
    if ($best === null) $row['status'] = 'not_found';
    elseif ($best[5]) $row['status'] = 'postponed';
    elseif ($best[3] === null) $row['status'] = 'no_result';
    else {
      $row['result'] = [$best[3], $best[4]];
      $row['status'] = $row['current'] === null ? 'new' : ($row['current'] === $row['result'] ? 'same' : 'differs');
    }
    $rows[] = $row;
  }
  return ['rows' => $rows, 'unmatched' => count(array_filter($rows, fn($r) => $r['status'] === 'not_found'))];
}

/** Save fetched results. Returns how many were saved. */
function apply_results(array $rows, bool $fixDiffering, string $how, ?array $who = null): int {
  $save = array_values(array_filter($rows, fn($r) => $r['status'] === 'new' || ($fixDiffering && $r['status'] === 'differs')));
  if (!$save) return 0;
  $pdo = db();
  $pdo->beginTransaction();
  foreach ($save as $r) {
    // Only matches that have kicked off, and (unless correcting) still have no result.
    q('UPDATE ' . t('matches') . ' SET homescore = ?, awayscore = ? WHERE lid = ? AND matchid = ? AND matchdate <= ?' . ($r['status'] === 'new' ? ' AND homescore IS NULL' : ''),
      [$r['result'][0], $r['result'][1], lid(), $r['id'], now_str()]);
  }
  $pdo->commit();
  save_standings(compute_standings());
  log_event('result', mb_substr($how . ': ' . implode('; ', array_map(fn($r) => $r['label'] . ' ' . $r['result'][0] . '-' . $r['result'][1], $save)), 0, 255), $who);
  return count($save);
}

function route_admin_fetchresults(): array {
  require_admin();
  $b = body();
  $dry = !empty($b['dry_run']);
  $fix = !empty($b['fix_differing']);
  $only = isset($b['competition_id']) && $b['competition_id'] !== '' && $b['competition_id'] !== null ? (int)$b['competition_id'] : null;
  $out = [];
  $saved = 0;
  foreach (competitions() as $c) {
    if ($only !== null && $c['id'] !== $only) continue;
    if (result_source($c['id'])['type'] === 'none') continue;
    try {
      $cmp = compare_results($c, $fix || $dry);
      $n = $dry ? 0 : apply_results($cmp['rows'], $fix, 'Results fetched by ' . (current_user()['username'] ?? 'an admin'));
      $saved += $n;
      $out[] = ['competition_id' => $c['id'], 'name' => $c['name'], 'rows' => $cmp['rows'], 'saved' => $n, 'error' => null];
    } catch (ApiError $e) {
      $out[] = ['competition_id' => $c['id'], 'name' => $c['name'], 'rows' => [], 'saved' => 0, 'error' => $e->getMessage()];
    }
  }
  if (!$out) fail(422, $only !== null ? 'Set where this competition\'s results come from first.' : 'Set where results come from for at least one competition first.');
  if (!$dry) store_set('rsrc_last', json_encode(['at' => now_str(), 'saved' => $saved, 'auto' => false]));
  return ['competitions' => $out, 'saved' => $saved, 'dry_run' => $dry];
}

// ---------------------------------------------------------------------
// Automatic checks
// ---------------------------------------------------------------------

/** Called on each API request: maybe fetch results after the response is sent. */
function schedule_auto_results(): void {
  try {
    if (!setting_bool('AUTO_RESULTS')) return;
    $last = store_get('rsrc_auto');
    if ($last !== null && strtotime($last) > time() - AUTO_RESULTS_GAP_MINUTES * 60) return;
    if (!matches_needing_results(null, true)) return;
  } catch (Throwable $e) {
    return;
  }
  store_set('rsrc_auto', now_str());   // claim this slot first, so only one request runs it
  register_shutdown_function(function () {
    if (function_exists('fastcgi_finish_request')) @fastcgi_finish_request();
    try {
      run_auto_results();
    } catch (Throwable $e) {
      error_log('Automatic results: ' . $e->getMessage());
    }
  });
}

function run_auto_results(): int {
  $waiting = [];
  foreach (matches_needing_results(null, true) as $m) $waiting[(int)$m['compid']] = true;
  $saved = 0;
  $errors = [];
  foreach (competitions() as $c) {
    if (!isset($waiting[$c['id']]) || result_source($c['id'])['type'] === 'none') continue;
    try {
      $saved += apply_results(compare_results($c, false)['rows'], false, 'Results fetched automatically', ['id' => null, 'username' => null]);
    } catch (ApiError $e) {
      $errors[] = $c['name'] . ': ' . $e->getMessage();
    }
  }
  if ($errors) {
    log_event('results_failed', mb_substr(implode(' ', $errors), 0, 255), ['id' => null, 'username' => null]);
    if (function_exists('discord_error')) discord_error('Automatic results failed', implode("\n", $errors));
  }
  store_set('rsrc_last', json_encode(['at' => now_str(), 'saved' => $saved, 'auto' => true, 'errors' => $errors]));
  return $saved;
}
