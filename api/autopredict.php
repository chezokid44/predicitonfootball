<?php
/*
 * Prediction League - automatic predictions from the teams' recent form.
 *
 * Players who switch this on get a prediction for any match they haven't
 * predicted when predictions close. It's filled in the next time anyone uses
 * the site, so no cron job is needed. Only results from before the match are
 * used, so the prediction is the same whenever it's made, and only matches
 * whose predictions close after the player switched it on are covered.
 */

declare(strict_types=1);

if (!defined('PL_APP')) {
  http_response_code(403);
  exit;
}

const FORM_MATCHES = 6;        // recent matches looked at per team
const FORM_PRIOR_WEIGHT = 2.0; // how strongly few-match teams lean to the league average

function team_key(string $name): string {
  return mb_strtolower(trim(preg_replace('/\s+/u', ' ', clean_text($name))));
}

/**
 * Recent form for one team from results before $before (newest first):
 * weighted goals scored and conceded per game, points per game, matches used.
 */
function team_form(string $team, array $history, string $before): array {
  $key = team_key($team);
  $games = [];
  for ($i = count($history) - 1; $i >= 0 && count($games) < FORM_MATCHES; $i--) {
    $h = $history[$i];
    if ($h['matchdate'] >= $before) continue;
    if ($h['home'] === $key) $games[] = [$h['hs'], $h['as']];
    elseif ($h['away'] === $key) $games[] = [$h['as'], $h['hs']];
  }
  $sumW = $for = $against = $pts = 0.0;
  foreach ($games as $i => [$scored, $conceded]) {
    $w = FORM_MATCHES - $i;  // most recent counts most
    $sumW += $w;
    $for += $w * $scored;
    $against += $w * $conceded;
    $pts += $w * ($scored > $conceded ? 3 : ($scored === $conceded ? 1 : 0));
  }
  return $sumW > 0
    ? ['n' => count($games), 'for' => $for / $sumW, 'against' => $against / $sumW, 'ppg' => $pts / $sumW]
    : ['n' => 0, 'for' => 0.0, 'against' => 0.0, 'ppg' => 1.35];
}

/**
 * Predict a score from form. Each side's expected goals blend its attack
 * with the other side's defence, shrunk towards the league average when a
 * team has played few matches, scaled for home advantage and nudged by the
 * difference in points per game. The expected goals are then rounded.
 */
function form_prediction(string $home, string $away, string $kickoff, array $history): array {
  $before = array_values(array_filter($history, fn($h) => $h['matchdate'] < $kickoff));
  // League scoring for home and away sides, blended with typical football
  // figures (1.5 and 1.2 goals a game, weighted as 10 matches) so a short or
  // unusual run of results can't remove home advantage altogether.
  $n = count($before);
  $homeAvg = (array_sum(array_column($before, 'hs')) + 1.5 * 10) / ($n + 10);
  $awayAvg = (array_sum(array_column($before, 'as')) + 1.2 * 10) / ($n + 10);
  $league = max(0.3, ($homeAvg + $awayAvg) / 2);

  $h = team_form($home, $history, $kickoff);
  $a = team_form($away, $history, $kickoff);
  $shrink = fn($value, $games) => ($value * $games + $league * FORM_PRIOR_WEIGHT) / ($games + FORM_PRIOR_WEIGHT);
  $hAttack = $shrink($h['for'], $h['n']);
  $hDefence = $shrink($h['against'], $h['n']);
  $aAttack = $shrink($a['for'], $a['n']);
  $aDefence = $shrink($a['against'], $a['n']);

  $formGap = $h['ppg'] - $a['ppg'];  // -3 to 3
  $xgHome = ($hAttack + $aDefence) / 2 * ($homeAvg / $league) * (1 + 0.08 * $formGap);
  $xgAway = ($aAttack + $hDefence) / 2 * ($awayAvg / $league) * (1 - 0.08 * $formGap);
  $round = fn($x) => (int)max(0, min(6, round(max(0.0, $x))));
  return [
    'home' => $round($xgHome),
    'away' => $round($xgAway),
    'xg' => [round($xgHome, 2), round($xgAway, 2)],
    'form' => ['home' => $h, 'away' => $a],
  ];
}

/** All results in the league, oldest first, with normalised team names. */
function result_history(): array {
  $rows = q('SELECT matchdate, hometeam, awayteam, homescore, awayscore FROM ' . t('matches') . '
             WHERE lid = ? AND homescore IS NOT NULL AND awayscore IS NOT NULL ORDER BY matchdate, matchid', [lid()])->fetchAll();
  return array_map(fn($r) => ['matchdate' => (string)$r['matchdate'], 'home' => team_key($r['hometeam']), 'away' => team_key($r['awayteam']),
                              'hs' => (int)$r['homescore'], 'as' => (int)$r['awayscore']], $rows);
}

/**
 * Make automatic predictions that are due. Returns how many were added.
 * Never throws: a problem here must not stop the page loading.
 */
function run_auto_predictions(): int {
  static $ran = false;
  if ($ran) return 0;
  $ran = true;
  try {
    if (!setting_bool('AUTOPRED_ON')) return 0;
    $users = array_filter(prefs_map(), fn($p) => $p['auto_predict'] && $p['auto_since']);
    if (!$users) return 0;

    // Matches whose predictions have closed since the earliest switch-on.
    $cut = cutoff_seconds();
    $earliest = min(array_column($users, 'auto_since'));
    $matches = q('SELECT matchid, matchdate, hometeam, awayteam FROM ' . t('matches') . '
                  WHERE lid = ? AND matchdate <= ? AND matchdate > ? ORDER BY matchdate',
      [lid(), date('Y-m-d H:i:s', time() + $cut), date('Y-m-d H:i:s', strtotime($earliest) + $cut)])->fetchAll();
    if (!$matches) return 0;

    $ids = array_map('intval', array_keys($users));
    $existing = [];
    $in = implode(',', array_fill(0, count($matches), '?'));
    foreach (q('SELECT userid, matchid FROM ' . t('predictions') . " WHERE lid = ? AND matchid IN ($in)",
               array_merge([lid()], array_column($matches, 'matchid'))) as $p) {
      $existing[(int)$p['userid'] . ':' . (int)$p['matchid']] = true;
    }
    $live = array_flip(array_map('intval', q('SELECT userid FROM ' . t('users') . ' WHERE lid = ?', [lid()])->fetchAll(PDO::FETCH_COLUMN)));

    $history = null;
    $added = 0;
    $ins = db()->prepare('INSERT INTO ' . t('predictions') . ' (lid, userid, matchid, predtime, homescore, awayscore, isauto) VALUES (?, ?, ?, ?, ?, ?, ?)');
    foreach ($matches as $m) {
      if (is_postponed((int)$m['matchid'])) continue;  // rearranged later: predicted then, if still needed
      $deadline = prediction_deadline((string)$m['matchdate']);
      $comp = competition_map()[(int)$m['matchid']] ?? null;
      // Only for competitions the player plays (fixtures outside competitions count for everyone).
      $due = array_filter($ids, fn($uid) => isset($live[$uid]) && $users[$uid]['auto_since'] <= $deadline
                                            && !isset($existing[$uid . ':' . (int)$m['matchid']])
                                            && ($comp === null || !function_exists('plays_competition') || plays_competition($uid, $comp)));
      if (!$due) continue;
      $history = $history ?? result_history();
      $p = form_prediction((string)$m['hometeam'], (string)$m['awayteam'], (string)$m['matchdate'], $history);
      foreach ($due as $uid) {
        try {
          $ins->execute([lid(), $uid, (int)$m['matchid'], $deadline, $p['home'], $p['away'], 'Y']);
          $added++;
        } catch (PDOException $e) {
          // Another request added it at the same moment.
        }
      }
    }
    if ($added) {
      log_event('auto_predict', "Made $added automatic " . ($added === 1 ? 'prediction' : 'predictions'), ['id' => null, 'username' => null]);
      save_standings(compute_standings());
    }
    return $added;
  } catch (Throwable $e) {
    error_log('Prediction League automatic predictions: ' . $e);
    return 0;
  }
}
