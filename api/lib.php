<?php
/*
 * Prediction League API - shared functions.
 * Works against the original Prediction League MySQL tables.
 */

declare(strict_types=1);

// Only run when loaded by api/index.php or the installer, never directly.
if (!defined('PL_APP')) {
  http_response_code(403);
  exit;
}

/**
 * Never let a page cache store these responses. CloudPanel's Varnish Cache
 * ignores Cache-Control: no-store and would hand every visitor the same
 * session and security token (so nobody can log in); its controller is told
 * directly with a cache lifetime of 0.
 */
function no_shared_cache(): void {
  if (class_exists('ClpVarnish', false) && method_exists('ClpVarnish', 'setCacheLifetime')) {
    ClpVarnish::setCacheLifetime(0);
  }
  if (!headers_sent()) {
    header('Cache-Control: no-store, no-cache, must-revalidate, private, max-age=0');
    header('Pragma: no-cache');
    header('X-Accel-Expires: 0');
  }
}
no_shared_cache();

const USER_NORMAL = 1;
const USER_ADMIN  = 4;

// ---------------------------------------------------------------------
// Configuration and database
// ---------------------------------------------------------------------

function cfg(?string $key = null) {
  static $config = null;
  if ($config === null) {
    if (isset($GLOBALS['PL_CONFIG_OVERRIDE'])) {
      $config = $GLOBALS['PL_CONFIG_OVERRIDE'];  // used by the installer
    } else {
      $file = __DIR__ . '/config.php';
      if (!is_file($file)) {
        fail(503, 'The league isn\'t set up yet. Open /install/ on this site to set it up.', 'not_installed');
      }
      $config = require $file;
    }
    $config += [
      'tables' => [], 'league_id' => 0, 'timezone' => 'Europe/London',
      'first_user_becomes_admin' => false, 'debug' => false,
      'strict_client_check' => true,
    ];
    $config['tables'] += [
      'config' => 'plconfigdata', 'users' => 'pluserdata',
      'predictions' => 'plpredictiondata', 'matches' => 'plmatchdata',
      'standings' => 'plstandings', 'messages' => 'plmsgdata',
    ];
  }
  return $key === null ? $config : ($config[$key] ?? null);
}

function db(): PDO {
  static $pdo = null;
  if ($pdo === null) {
    $c = cfg('db');
    $dsn = $c['dsn'] ?? null;
    if (!$dsn) {
      $dsn = sprintf('mysql:host=%s;%sdbname=%s;charset=utf8mb4', $c['host'],
        empty($c['port']) ? '' : 'port=' . (int)$c['port'] . ';', $c['name']);
    }
    $pdo = new PDO($dsn, $c['user'] ?? null, $c['pass'] ?? null, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false,
    ]);
  }
  return $pdo;
}

function is_mysql(): bool {
  return db()->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql';
}

/** Quoted table name from config. */
function t(string $name): string {
  $table = cfg('tables')[$name];
  if (!preg_match('/\A[A-Za-z0-9_]{1,64}\z/', $table)) {
    fail(500, 'Invalid table name in config.');
  }
  return is_mysql() ? "`$table`" : "\"$table\"";
}

function lid(): int {
  return (int)cfg('league_id');
}

function q(string $sql, array $params = []): PDOStatement {
  $stmt = db()->prepare($sql);
  $stmt->execute($params);
  return $stmt;
}

function now_str(): string {
  return date('Y-m-d H:i:s');
}

// ---------------------------------------------------------------------
// HTTP helpers
// ---------------------------------------------------------------------

final class ApiError extends Exception {
  public int $status;
  public string $errorCode;
  public function __construct(int $status, string $message, string $errorCode = '') {
    parent::__construct($message);
    $this->status = $status;
    $this->errorCode = $errorCode;
  }
}

function fail(int $status, string $message, string $errorCode = ''): void {
  throw new ApiError($status, $message, $errorCode);
}

function respond($data, int $status = 200): void {
  http_response_code($status);
  header('Content-Type: application/json; charset=utf-8');
  header('Cache-Control: no-store, no-cache, must-revalidate, private, max-age=0');
  header('X-Content-Type-Options: nosniff');
  header('X-Frame-Options: DENY');
  header('Cross-Origin-Resource-Policy: same-origin');
  echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function body(): array {
  static $data = null;
  if ($data === null) {
    $raw = file_get_contents('php://input');
    $data = $raw === '' || $raw === false ? [] : json_decode($raw, true);
    if (!is_array($data)) {
      fail(400, 'The request body must be JSON.');
    }
  }
  return $data;
}

function str_in(array $src, string $key, int $max, bool $required = true): string {
  $v = isset($src[$key]) ? trim((string)$src[$key]) : '';
  if ($required && $v === '') {
    fail(422, ucfirst(str_replace('_', ' ', $key)) . ' is required.');
  }
  if (mb_strlen($v) > $max) {
    fail(422, ucfirst(str_replace('_', ' ', $key)) . " must be $max characters or fewer.");
  }
  return $v;
}

function score_in($v): ?int {
  if ($v === null || $v === '') {
    return null;
  }
  if (!is_numeric($v) || (int)$v != $v || (int)$v < 0 || (int)$v > 99) {
    fail(422, 'Scores must be whole numbers from 0 to 99.');
  }
  return (int)$v;
}

function clean_text(?string $v): string {
  // Older versions of the script stored text with addslashes().
  return stripslashes((string)$v);
}

// ---------------------------------------------------------------------
// Sessions, CSRF and the current user
// ---------------------------------------------------------------------

function is_https(): bool {
  if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') return true;
  if (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https') return true;
  if (strpos($_SERVER['HTTP_CF_VISITOR'] ?? '', 'https') !== false) return true;
  return false;
}

function start_session(): void {
  session_name('PLSESSION');
  session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => is_https(),
    'httponly' => true,
    'samesite' => 'Lax',
  ]);
  session_start();
  if (empty($_SESSION['csrf']) || !is_string($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
  }
}

/** Every request except ?r=session must carry this session's token. */
function check_token(): void {
  $sent = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
  if (!is_string($sent) || !hash_equals($_SESSION['csrf'], $sent)) {
    fail(403, 'Your session has expired. Reload the page and try again.', 'token');
  }
}

/** "host[:port]" from a URL, lower-cased, or '' if it can't be parsed. */
function url_host(string $url): string {
  $p = parse_url($url);
  if (!$p || empty($p['host'])) return '';
  return strtolower($p['host']) . (isset($p['port']) ? ':' . $p['port'] : '');
}

/**
 * Only accept requests made by this site's own pages, not typed into the
 * address bar, followed from a link, or sent from another website.
 * Browsers send Sec-Fetch-* and Origin headers that pages can't fake; older
 * browsers fall back to the Referer check. A script outside a browser can
 * still copy these headers, so logins and admin checks remain the real
 * protection for data.
 */
function guard_request(): void {
  if (!cfg('strict_client_check')) return;
  $deny = function () {
    fail(403, 'This address is only for the Prediction League app. Open the site\'s home page instead.', 'client');
  };
  $host = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));
  if (($_SERVER['HTTP_X_PL_CLIENT'] ?? '') !== '1') $deny();
  if (($_SERVER['HTTP_SEC_FETCH_MODE'] ?? '') === 'navigate') $deny();
  $site = (string)($_SERVER['HTTP_SEC_FETCH_SITE'] ?? '');
  if ($site !== '' && $site !== 'same-origin') $deny();
  $origin = (string)($_SERVER['HTTP_ORIGIN'] ?? '');
  if ($origin !== '' && url_host($origin) !== $host) $deny();
  if ($site === '' && $origin === '') {
    $referer = (string)($_SERVER['HTTP_REFERER'] ?? '');
    if ($referer === '' || url_host($referer) !== $host) $deny();
  }
}

/** The logged-in user, reloaded from the database on every request. */
function current_user(bool $reload = false): ?array {
  static $user = false;
  if ($user === false || $reload) {
    $user = null;
    if (!empty($_SESSION['uid'])) {
      $row = q('SELECT userid, username, email, usertype, since, icon FROM ' . t('users') . ' WHERE lid = ? AND userid = ?',
        [lid(), (int)$_SESSION['uid']])->fetch();
      if ($row) {
        $user = public_user($row);
      } else {
        unset($_SESSION['uid']);
      }
    }
  }
  return $user;
}

function public_user(array $row): array {
  return [
    'id' => (int)$row['userid'],
    'username' => clean_text($row['username']),
    'email' => (string)($row['email'] ?? ''),
    'is_admin' => (int)$row['usertype'] >= USER_ADMIN,
    'since' => $row['since'] ?? null,
    'icon' => icon_url($row['icon'] ?? null),
  ];
}

function require_user(): array {
  $u = current_user();
  if (!$u) fail(401, 'Log in to do that.');
  return $u;
}

function require_admin(): array {
  $u = require_user();
  if (!$u['is_admin']) fail(403, 'Only admins can do that.');
  return $u;
}

// ---------------------------------------------------------------------
// Passwords (compatible with the old MD5 / plain storage)
// ---------------------------------------------------------------------

function password_column_length(): int {
  static $len = null;
  if ($len === null) {
    $len = 255;
    if (is_mysql()) {
      $row = q('SELECT CHARACTER_MAXIMUM_LENGTH AS len FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
        [cfg('tables')['users'], 'password'])->fetch();
      $len = $row ? (int)$row['len'] : 40;
    }
  }
  return $len;
}

function make_password_hash(string $password): string {
  if (password_column_length() >= 60) {
    return password_hash($password, PASSWORD_DEFAULT);
  }
  // Column not migrated yet (VARCHAR(40)): fall back to the old format.
  return setting('PASSWORDENCRYPT') === 'TRUE' ? md5($password) : urlencode($password);
}

function verify_password(string $password, string $stored): bool {
  if (preg_match('/\A\$(2y|2a|argon2)/', $stored)) {
    return password_verify($password, $stored);
  }
  if (preg_match('/\A[a-f0-9]{32}\z/i', $stored)) {
    return hash_equals(strtolower($stored), md5($password));
  }
  return $stored !== '' && hash_equals($stored, urlencode($password));
}

function password_needs_upgrade(string $stored): bool {
  if (password_column_length() < 60) return false;
  if (!preg_match('/\A\$(2y|2a|argon2)/', $stored)) return true;
  return password_needs_rehash($stored, PASSWORD_DEFAULT);
}

// ---------------------------------------------------------------------
// Settings (plconfigdata), with self-healing defaults
// ---------------------------------------------------------------------

function default_settings(): array {
  // [group, value, description, type]
  return [
    'CORR_SCORE'        => [1, '3', 'Points for predicting the exact score.', 'number'],
    'CORR_RESULT'       => [1, '1', 'Points for predicting the right result (home win, away win or draw) but not the exact score.', 'number'],
    'CORR_HOME_SCORE'   => [1, '1', 'Not used in scoring (kept for compatibility with the old script).', 'number'],
    'CORR_AWAY_SCORE'   => [1, '1', 'Not used in scoring (kept for compatibility with the old script).', 'number'],
    'CORR_MARGIN'       => [1, '1', 'Not used in scoring (kept for compatibility with the old script).', 'number'],
    'PRED_LEAGUE_TITLE' => [2, 'Prediction League', 'The name of the league, shown at the top of every page.', 'text'],
    'HOME_PAGE_URL'     => [2, '', 'Optional link shown in the footer.', 'url'],
    'HOME_PAGE_TITLE'   => [2, '', 'Text for the footer link.', 'text'],
    'DEF_ICON'          => [2, 'default.gif', 'Default icon for new users (old script).', 'text'],
    'ADMIN_EMAIL'       => [2, '', 'Administrator contact address.', 'email'],
    'ADMIN_SIGNATURE'   => [2, '', 'Signature for administrator emails (old script).', 'text'],
    'CHATROOM_URL'      => [2, '', 'Chat room link (old script).', 'url'],
    'LOGFILE'           => [2, '', 'Log file path (old script).', 'text'],
    'DEFAULT_LANGUAGE'  => [2, 'lang.english.php', 'Default language file (old script).', 'text'],
    'MULT_USERS'        => [2, 'FALSE', 'Allow more than one player to use the same email address.', 'bool'],
    'TZ_OFFSET'         => [2, '0', 'Old script only. The new site uses the time zone in api/config.php.', 'number'],
    'USE24HR'           => [2, 'TRUE', 'Show times on the 24-hour clock.', 'bool'],
    'REV_USER_PREDS'    => [2, 'FALSE', 'Old script only. The new site shows open matches first, then played matches newest first.', 'bool'],
    'USERS_PER_PAGE'    => [2, '80', 'Old script only.', 'number'],
    'MAX_ADMIN_USERS'   => [2, '1', 'The maximum number of admins.', 'number'],
    'USE_MESSAGING'     => [3, 'TRUE', 'Let players send each other private messages.', 'bool'],
    'HIDE_0_PREDS'      => [3, 'TRUE', 'Leave players with no predictions out of the league table.', 'bool'],
    'NUMMULTFIXTS'      => [3, '10', 'How many blank rows to show when adding several fixtures at once.', 'number'],
    'VIEWUSERPREDS'     => [3, 'TRUE', 'Let players see each other\'s predictions before kick-off.', 'bool'],
    'UPLOADICONS'       => [3, 'TRUE', 'Let players upload a picture for their profile.', 'bool'],
    'ICONSIZE'          => [3, '50', 'Old script only. Uploaded pictures can be up to 5 MB and are resized.', 'number'],
    'LOG_DAYS'          => [3, '365', 'Keep the activity log for this many days.', 'number'],
    'PASSWORDENCRYPT'   => [3, 'TRUE', 'Store passwords as MD5 when the password column has not been upgraded. Passwords are stored with bcrypt once it has.', 'bool'],
    'LOCKEDGAME'        => [3, 'FALSE', 'Close registration so only admins can add players.', 'bool'],
    'PRED_CUTOFF_MINS'  => [3, '30', 'Predictions close this many minutes before kick-off (0 means at kick-off). Everyone\'s predictions are shown once they close.', 'number'],
    'BOT_CHECK'         => [3, 'TRUE', 'Check log-in, sign-up and password reset forms for bots (a hidden honeypot field and a puzzle the browser solves in the background).', 'bool'],
    'BOT_POW_BITS'      => [3, '16', 'Size of the bot-check puzzle, from 8 to 22. Each extra 1 doubles the work: 16 takes a computer about a quarter of a second.', 'number'],
    'BOT_BLOCK_HOURS'   => [3, '24', 'How many hours an address is blocked from logging in after it falls into a bot trap (0 to only log it).', 'number'],
    'BAN_STRIKES'       => [3, '5', 'Ban an address permanently after this many strikes. Every block is a strike, and so is each try from a blocked address (at most one every 10 minutes). 0 means never ban automatically.', 'number'],
    'BAN_WHOLE_SITE'    => [3, 'TRUE', 'Permanently banned addresses are shut out of the whole site (otherwise only log-in, sign-up and password resets).', 'bool'],
    'AUTOPRED_ON'       => [3, 'TRUE', 'Let players switch on automatic predictions: if they haven\'t predicted a match when predictions close, one is made from the teams\' recent form.', 'bool'],
    'LEAGUES_ON'        => [3, 'TRUE', 'Let players create private leagues and invite friends with a code.', 'bool'],
    'MAX_LEAGUES_PER_PLAYER' => [3, '5', 'How many private leagues each player can create (admins have no limit).', 'number'],
    'MAX_LEAGUE_MEMBERS' => [3, '100', 'The most players a private league can have.', 'number'],
    'AUTO_PREDICT'      => [3, 'FALSE', 'Old script only. Automatic predictions are set with AUTOPRED_ON.', 'bool'],
    'BLOCK_ALERT_ON'    => [6, 'FALSE', 'Email a daily list of newly blocked addresses (bot traps, honeypot, failed bot checks). One email a day at most, and none on days with no new blocks.', 'bool'],
    'BLOCK_ALERT_TO'    => [6, '', 'Who gets the daily list. Leave empty to send it to every admin with an email address.', 'email'],
    'BLOCK_ALERT_HOUR'  => [6, '8', 'Hour of the day (0 to 23, league time) from which the daily list is sent. It goes with the first visit to the site after that hour.', 'number'],
    'ALERT_FROM'        => [6, '', 'Address the list is sent from: the address of the separate alerts email account.', 'email'],
    'ALERT_SMTP_HOST'   => [6, '', 'SMTP server for the alerts account, for example smtp.gmail.com. The daily list always uses this account, never the main email settings.', 'text'],
    'ALERT_SMTP_PORT'   => [6, '587', 'Alerts SMTP port: usually 587 with TLS or 465 with SSL.', 'number'],
    'ALERT_SMTP_SECURE' => [6, 'tls', 'Alerts SMTP encryption.', 'select', ['tls' => 'TLS (STARTTLS)', 'ssl' => 'SSL', 'none' => 'None']],
    'ALERT_SMTP_USER'   => [6, '', 'Alerts SMTP user name.', 'text'],
    'ALERT_SMTP_PASS'   => [6, '', 'Alerts SMTP password. Stored on the server and never shown again.', 'secret'],
    'MAIL_FROM'         => [5, '', 'Address emails are sent from, for example league@yourdomain.co.uk. Leave empty to turn email off.', 'email'],
    'MAIL_FROM_NAME'    => [5, '', 'Name emails are sent from. Leave empty to use the league name.', 'text'],
    'MAIL_TRANSPORT'    => [5, 'mail', 'How to send email: the server\'s own mail function, or an SMTP server such as your email provider.', 'select', ['mail' => 'Server mail function', 'smtp' => 'SMTP server']],
    'SMTP_HOST'         => [5, '', 'SMTP server, for example smtp.gmail.com. Only used with SMTP.', 'text'],
    'SMTP_PORT'         => [5, '587', 'SMTP port: usually 587 with TLS or 465 with SSL.', 'number'],
    'SMTP_SECURE'       => [5, 'tls', 'SMTP encryption.', 'select', ['tls' => 'TLS (STARTTLS)', 'ssl' => 'SSL', 'none' => 'None']],
    'SMTP_USER'         => [5, '', 'SMTP user name.', 'text'],
    'SMTP_PASS'         => [5, '', 'SMTP password. Stored on the server and never shown again.', 'secret'],
  ];
}

function settings(bool $reload = false): array {
  static $values = null;
  if ($values === null || $reload) {
    $values = [];
    foreach (q('SELECT param, value FROM ' . t('config') . ' WHERE lid = ?', [lid()]) as $row) {
      $values[$row['param']] = (string)$row['value'];
    }
    $missing = array_diff_key(default_settings(), $values);
    if ($missing) {
      $ins = db()->prepare('INSERT INTO ' . t('config') . ' (lid, grp, param, value, descr, ro) VALUES (?, ?, ?, ?, ?, ?)');
      foreach ($missing as $param => [$grp, $value, $descr]) {
        try {
          $ins->execute([lid(), $grp, $param, $value, $descr, 'N']);
        } catch (PDOException $e) {
          // Another request inserted it first; ignore.
        }
        $values[$param] = $value;
      }
    }
  }
  return $values;
}

function setting(string $key): string {
  return settings()[$key] ?? (default_settings()[$key][1] ?? '');
}

function setting_bool(string $key): bool {
  return strtoupper(setting($key)) === 'TRUE';
}

function public_settings(): array {
  return [
    'title' => setting('PRED_LEAGUE_TITLE') ?: 'Prediction League',
    'home_page_url' => setting('HOME_PAGE_URL'),
    'home_page_title' => setting('HOME_PAGE_TITLE'),
    'points_exact' => (int)setting('CORR_SCORE'),
    'points_result' => (int)setting('CORR_RESULT'),
    'view_user_predictions' => setting_bool('VIEWUSERPREDS'),
    'registration_open' => !setting_bool('LOCKEDGAME'),
    'use_24hr' => setting_bool('USE24HR'),
    'latest_first' => setting_bool('REV_USER_PREDS'),
    'multi_fixture_rows' => max(1, min(50, (int)setting('NUMMULTFIXTS') ?: 10)),
    'timezone' => (string)cfg('timezone'),
    'messaging' => setting_bool('USE_MESSAGING'),
    'icons' => setting_bool('UPLOADICONS'),
    'email' => mail_configured(),
    'cutoff_minutes' => (int)(cutoff_seconds() / 60),
    'auto_predict' => setting_bool('AUTOPRED_ON'),
    'bot_check' => setting_bool('BOT_CHECK'),
    'leagues' => setting_bool('LEAGUES_ON'),
    'menu' => function_exists('public_menu') ? public_menu() : null,
    'home_page' => function_exists('home_page_slug') ? home_page_slug() : null,
  ] + (function_exists('public_seasons') ? public_seasons() : []) + [
    'competitions' => array_map(fn($c) => ['id' => $c['id'], 'name' => $c['name'], 'short' => $c['short']], competitions()),
  ];
}

// ---------------------------------------------------------------------
// Matches and scoring
// ---------------------------------------------------------------------

function parse_kickoff(string $value): string {
  $value = trim(str_replace('T', ' ', $value));
  foreach (['Y-m-d H:i:s', 'Y-m-d H:i'] as $format) {
    $d = DateTime::createFromFormat('!' . $format, $value);
    if ($d && $d->format($format) === $value) {
      return $d->format('Y-m-d H:i:s');
    }
  }
  fail(422, 'Kick-off must be a date and time, for example 2026-11-14 15:00.');
  return '';
}

/** Seconds before kick-off that predictions close (PRED_CUTOFF_MINS, 0 to 24 hours). */
function cutoff_seconds(): int {
  return max(0, min(1440, (int)setting('PRED_CUTOFF_MINS'))) * 60;
}

/** When predictions close for a fixture kicking off at $kickoff ("Y-m-d H:i:s"). */
function prediction_deadline(string $kickoff): string {
  return date('Y-m-d H:i:s', strtotime($kickoff) - cutoff_seconds());
}

function predictions_closed(string $kickoff): bool {
  return prediction_deadline($kickoff) <= now_str();
}

function match_out(array $m): array {
  $kickoff = (string)$m['matchdate'];
  $started = $kickoff <= now_str();
  $closes = prediction_deadline($kickoff);
  $home = $m['homescore'] ?? null;
  $away = $m['awayscore'] ?? null;
  return [
    'id' => (int)$m['matchid'],
    'kickoff' => $kickoff,
    'kickoff_iso' => (new DateTime($kickoff))->format(DATE_ATOM),
    'home_team' => clean_text($m['hometeam']),
    'away_team' => clean_text($m['awayteam']),
    'locked' => $closes <= now_str(),   // predictions closed
    'started' => $started,              // kicked off
    'closes' => $closes,
    'result' => ($home === null || $away === null) ? null : ['home' => (int)$home, 'away' => (int)$away],
    'competition' => match_competition((int)$m['matchid']),
    'home_badge' => function_exists('badge_for') ? badge_for((string)$m['hometeam']) : null,
    'away_badge' => function_exists('badge_for') ? badge_for((string)$m['awayteam']) : null,
    'season' => function_exists('season_for_date') ? (season_for_date($kickoff)['id'] ?? null) : null,
  ];
}

function get_match(int $id): array {
  $m = q('SELECT * FROM ' . t('matches') . ' WHERE lid = ? AND matchid = ?', [lid(), $id])->fetch();
  if (!$m) fail(404, 'That fixture no longer exists.');
  return $m;
}

/**
 * Points for one prediction, matching the old GameStats::CalculatePoints().
 * $scoring is ['exact' => n, 'result' => n]; the site's settings by default.
 */
function points_for(int $ph, int $pa, int $ah, int $aa, ?array $scoring = null): int {
  $scoring = $scoring ?? site_scoring();
  if ($ph === $ah && $pa === $aa) return (int)$scoring['exact'];
  if (($ph <=> $pa) === ($ah <=> $aa)) return (int)$scoring['result'];
  return 0;
}

function site_scoring(): array {
  return ['exact' => (int)setting('CORR_SCORE'), 'result' => (int)setting('CORR_RESULT')];
}

function outcome_for(int $ph, int $pa, int $ah, int $aa): string {
  if ($ph === $ah && $pa === $aa) return 'exact';
  if (($ph <=> $pa) === ($ah <=> $aa)) return 'result';
  return 'miss';
}

/** Every prediction on a match that has kicked off, loaded once per request. */
function kicked_off_predictions(bool $reload = false): array {
  static $rows = null;
  if ($rows === null || $reload) {
    $rows = [];
    $map = competition_map();
    foreach (q('SELECT p.userid, p.matchid, m.matchdate, m.homescore AS mhs, m.awayscore AS mas, p.homescore AS phs, p.awayscore AS pas
                FROM ' . t('predictions') . ' p
                INNER JOIN ' . t('matches') . ' m ON p.matchid = m.matchid AND p.lid = m.lid
                WHERE m.matchdate <= ? AND m.lid = ?', [now_str(), lid()]) as $r) {
      $r['compid'] = $map[(int)$r['matchid']] ?? null;
      $rows[] = $r;
    }
  }
  return $rows;
}

/**
 * Calculate a league table, matching the old UpdateStandingsTable(): only
 * matches that have kicked off count, and a prediction only scores once the
 * result is in. Options:
 *   users      null for everyone, or the user ids in the league
 *   comps      null for every fixture, or competition ids (fixtures outside them don't count)
 *   scoring    ['exact' => n, 'result' => n], the site's by default
 *   from       only matches kicking off at or after this time
 *   show_empty also list players with no predictions (default: the site's HIDE_0_PREDS)
 */
function compute_table(array $o = []): array {
  $scoring = $o['scoring'] ?? site_scoring();
  $users = isset($o['users']) ? array_flip(array_map('intval', $o['users'])) : null;
  $comps = isset($o['comps']) ? array_flip(array_map('intval', $o['comps'])) : null;
  $from = $o['from'] ?? null;
  $to = $o['to'] ?? null;
  $showEmpty = $o['show_empty'] ?? !setting_bool('HIDE_0_PREDS');

  $stats = [];
  $blank = fn() => ['played' => 0, 'exact' => 0, 'result' => 0, 'miss' => 0,
                    'for' => 0, 'against' => 0, 'diff' => 0, 'points' => 0];
  foreach (kicked_off_predictions() as $r) {
    $uid = (int)$r['userid'];
    if ($users !== null && !isset($users[$uid])) continue;
    if ($comps !== null && ($r['compid'] === null || !isset($comps[$r['compid']]))) continue;
    if ($from !== null && (string)$r['matchdate'] < $from) continue;
    if ($to !== null && (string)$r['matchdate'] > $to) continue;
    $stats[$uid] = $stats[$uid] ?? $blank();
    if ($r['phs'] === null || $r['pas'] === null || $r['mhs'] === null || $r['mas'] === null) {
      continue;
    }
    [$ph, $pa, $ah, $aa] = [(int)$r['phs'], (int)$r['pas'], (int)$r['mhs'], (int)$r['mas']];
    $s = &$stats[$uid];
    $s['played']++;
    $s['points'] += points_for($ph, $pa, $ah, $aa, $scoring);
    $s[outcome_for($ph, $pa, $ah, $aa)]++;
    // Goal "for" and "against", as in the old GameStats::UpdateStats().
    foreach ([[$ph, $ah], [$pa, $aa]] as [$p, $a]) {
      if ($p === $a) $s['for'] += $p; else $s['against'] += abs($p - $a);
    }
    $s['diff'] = $s['for'] - $s['against'];
    unset($s);
  }

  $names = [];
  $icons = [];
  foreach (q('SELECT userid, username, icon FROM ' . t('users') . ' WHERE lid = ?', [lid()]) as $u) {
    $names[(int)$u['userid']] = clean_text($u['username']);
    $icons[(int)$u['userid']] = icon_url($u['icon']);
  }
  if ($showEmpty) {
    foreach ($users ?? $names as $uid => $_) {
      $stats[$uid] = $stats[$uid] ?? $blank();
    }
  }

  $table = [];
  foreach ($stats as $uid => $s) {
    if (!isset($names[$uid])) continue;  // prediction from a deleted user
    $table[] = ['user_id' => $uid, 'username' => $names[$uid], 'icon' => $icons[$uid]] + $s;
  }

  $cmp = function ($a, $b) {
    if ($a['points'] !== $b['points']) return $b['points'] <=> $a['points'];
    if (($a['played'] === 0) !== ($b['played'] === 0)) return $a['played'] === 0 ? 1 : -1;
    if ($a['diff'] !== $b['diff']) return $b['diff'] <=> $a['diff'];
    return $b['played'] <=> $a['played'];
  };
  usort($table, function ($a, $b) use ($cmp) {
    return $cmp($a, $b) ?: strcasecmp($a['username'], $b['username']);
  });

  // Equal records share a position.
  foreach ($table as $i => &$row) {
    $row['position'] = ($i > 0 && $cmp($table[$i - 1], $row) === 0) ? $table[$i - 1]['position'] : $i + 1;
  }
  unset($row);
  return $table;
}

/** The overall table: every player, every fixture, the site's scoring. */
function compute_standings(): array {
  kicked_off_predictions(true);
  return compute_table();
}

/** Write the table to plstandings so anything still reading it stays correct. */
function save_standings(array $table): void {
  $pdo = db();
  $pdo->beginTransaction();
  try {
    q('DELETE FROM ' . t('standings') . ' WHERE lid = ?', [lid()]);
    $ins = $pdo->prepare('INSERT INTO ' . t('standings') . '
      (lid, userid, position, pld, won, drawn, lost, gfor, gagainst, diff, points) VALUES (?,?,?,?,?,?,?,?,?,?,?)');
    foreach ($table as $i => $r) {
      $ins->execute([lid(), $r['user_id'], $i + 1, $r['played'], $r['exact'], $r['result'], $r['miss'],
                     $r['for'], $r['against'], $r['diff'], $r['points']]);
    }
    $pdo->commit();
  } catch (Throwable $e) {
    $pdo->rollBack();
    throw $e;
  }
}

function admin_count(): int {
  return (int)q('SELECT COUNT(*) AS n FROM ' . t('users') . ' WHERE lid = ? AND usertype >= ?', [lid(), USER_ADMIN])->fetch()['n'];
}

/** Can the current viewer see $ownerId's prediction for this match? */
function can_see_prediction(array $match, int $ownerId): bool {
  $viewer = current_user();
  if ($viewer && $viewer['id'] === $ownerId) return true;
  return setting_bool('VIEWUSERPREDS') || predictions_closed((string)$match['matchdate']);
}

// ---------------------------------------------------------------------
// Player icons
// ---------------------------------------------------------------------

const ICON_DIR = 'uploads/icons';

/** Public URL (relative to the site root) of an uploaded icon, or null. */
function icon_url(?string $icon): ?string {
  $icon = (string)$icon;
  return preg_match('#\Auploads/icons/u\d+-[a-f0-9]{16}\.png\z#', $icon) ? $icon : null;
}

function icon_dir(): string {
  return dirname(__DIR__) . '/' . ICON_DIR;
}

// ---------------------------------------------------------------------
// Request details
// ---------------------------------------------------------------------

/** The visitor's IP. Set 'client_ip_header' in config.php when behind a proxy such as Cloudflare. */
function client_ip(): string {
  $header = (string)(cfg('client_ip_header') ?? '');
  if ($header !== '' && !empty($_SERVER[$header])) {
    $ip = trim(explode(',', (string)$_SERVER[$header])[0]);
    if (filter_var($ip, FILTER_VALIDATE_IP)) return $ip;
  }
  return (string)($_SERVER['REMOTE_ADDR'] ?? '');
}

/** The site's address for links in emails, e.g. https://pl.example.co.uk/ */
function site_url(): string {
  $url = (string)(cfg('site_url') ?? '');
  if ($url === '') {
    // Fallback for configs written before site_url existed.
    $path = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '/api/index.php')), '/');
    $url = (is_https() ? 'https://' : 'http://') . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $path;
  }
  return rtrim($url, '/') . '/';
}

// ---------------------------------------------------------------------
// Tables added by the new site (created on first use if missing)
// ---------------------------------------------------------------------

function extra_tables(): array {
  return ['log' => 'plactivitylog', 'resets' => 'plresettokens', 'prefs' => 'pluserprefs',
          'comps' => 'plcompetitions', 'matchcomp' => 'plmatchcomp', 'blocks' => 'plblocklist',
          'leagues' => 'plleagues', 'leaguecomps' => 'plleaguecomps', 'members' => 'plleaguemembers',
          'follows' => 'plfollows', 'pages' => 'plpages', 'menu' => 'plmenu',
          'seasons' => 'plseasons', 'teams' => 'plteams', 'compteams' => 'plcompteams'];
}

function extra_table_sql(string $name, string $driver): string {
  $tables = extra_tables();
  if ($name === 'log') {
    return $driver === 'sqlite'
      ? "CREATE TABLE IF NOT EXISTS {$tables['log']} (id INTEGER PRIMARY KEY AUTOINCREMENT, lid INT NOT NULL DEFAULT 0, created DATETIME NOT NULL, userid INT DEFAULT NULL, username VARCHAR(32) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, action VARCHAR(40) NOT NULL, detail VARCHAR(255) DEFAULT NULL)"
      : "CREATE TABLE IF NOT EXISTS `{$tables['log']}` (
          `id` INT NOT NULL AUTO_INCREMENT,
          `lid` INT NOT NULL DEFAULT 0,
          `created` DATETIME NOT NULL,
          `userid` INT DEFAULT NULL,
          `username` VARCHAR(32) DEFAULT NULL,
          `ip` VARCHAR(45) DEFAULT NULL,
          `action` VARCHAR(40) NOT NULL,
          `detail` VARCHAR(255) DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `idx_lid_created` (`lid`, `created`),
          KEY `idx_action_ip` (`action`, `ip`, `created`)
        ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4";
  }
  if ($name === 'blocks') {
    return $driver === 'sqlite'
      ? "CREATE TABLE IF NOT EXISTS {$tables['blocks']} (lid INT NOT NULL DEFAULT 0, ip VARCHAR(45) NOT NULL, reason VARCHAR(100) DEFAULT NULL, created DATETIME NOT NULL, expires DATETIME NOT NULL, strikes INT NOT NULL DEFAULT 1, permanent INT NOT NULL DEFAULT 0, last_strike DATETIME DEFAULT NULL, PRIMARY KEY (lid, ip))"
      : "CREATE TABLE IF NOT EXISTS `{$tables['blocks']}` (
          `lid` INT NOT NULL DEFAULT 0,
          `ip` VARCHAR(45) NOT NULL,
          `reason` VARCHAR(100) DEFAULT NULL,
          `created` DATETIME NOT NULL,
          `expires` DATETIME NOT NULL,
          `strikes` INT NOT NULL DEFAULT 1,
          `permanent` TINYINT NOT NULL DEFAULT 0,
          `last_strike` DATETIME DEFAULT NULL,
          PRIMARY KEY (`lid`, `ip`)
        ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4";
  }
  if ($name === 'comps') {
    return $driver === 'sqlite'
      ? "CREATE TABLE IF NOT EXISTS {$tables['comps']} (compid INTEGER PRIMARY KEY AUTOINCREMENT, lid INT NOT NULL DEFAULT 0, name VARCHAR(40) NOT NULL, short_name VARCHAR(8) NOT NULL DEFAULT '', position INT NOT NULL DEFAULT 0)"
      : "CREATE TABLE IF NOT EXISTS `{$tables['comps']}` (
          `compid` INT NOT NULL AUTO_INCREMENT,
          `lid` INT NOT NULL DEFAULT 0,
          `name` VARCHAR(40) NOT NULL,
          `short_name` VARCHAR(8) NOT NULL DEFAULT '',
          `position` INT NOT NULL DEFAULT 0,
          PRIMARY KEY (`compid`),
          KEY `idx_lid` (`lid`, `position`)
        ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4";
  }
  if ($name === 'matchcomp') {
    return $driver === 'sqlite'
      ? "CREATE TABLE IF NOT EXISTS {$tables['matchcomp']} (lid INT NOT NULL DEFAULT 0, matchid INT NOT NULL, compid INT NOT NULL, PRIMARY KEY (lid, matchid))"
      : "CREATE TABLE IF NOT EXISTS `{$tables['matchcomp']}` (
          `lid` INT NOT NULL DEFAULT 0,
          `matchid` INT NOT NULL,
          `compid` INT NOT NULL,
          PRIMARY KEY (`lid`, `matchid`),
          KEY `idx_comp` (`lid`, `compid`)
        ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4";
  }
  if ($name === 'seasons') {
    return $driver === 'sqlite'
      ? "CREATE TABLE IF NOT EXISTS {$tables['seasons']} (seasonid INTEGER PRIMARY KEY AUTOINCREMENT, lid INT NOT NULL DEFAULT 0, name VARCHAR(30) NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, is_current INT NOT NULL DEFAULT 0)"
      : "CREATE TABLE IF NOT EXISTS `{$tables['seasons']}` (
          `seasonid` INT NOT NULL AUTO_INCREMENT,
          `lid` INT NOT NULL DEFAULT 0,
          `name` VARCHAR(30) NOT NULL,
          `start_date` DATE NOT NULL,
          `end_date` DATE NOT NULL,
          `is_current` TINYINT NOT NULL DEFAULT 0,
          PRIMARY KEY (`seasonid`),
          KEY `idx_dates` (`lid`, `start_date`)
        ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4";
  }
  if ($name === 'teams') {
    return $driver === 'sqlite'
      ? "CREATE TABLE IF NOT EXISTS {$tables['teams']} (teamid INTEGER PRIMARY KEY AUTOINCREMENT, lid INT NOT NULL DEFAULT 0, name VARCHAR(30) NOT NULL, short_name VARCHAR(5) NOT NULL DEFAULT '', badge VARCHAR(80) DEFAULT NULL, UNIQUE (lid, name))"
      : "CREATE TABLE IF NOT EXISTS `{$tables['teams']}` (
          `teamid` INT NOT NULL AUTO_INCREMENT,
          `lid` INT NOT NULL DEFAULT 0,
          `name` VARCHAR(30) NOT NULL,
          `short_name` VARCHAR(5) NOT NULL DEFAULT '',
          `badge` VARCHAR(80) DEFAULT NULL,
          PRIMARY KEY (`teamid`),
          UNIQUE KEY `uniq_name` (`lid`, `name`)
        ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4";
  }
  if ($name === 'compteams') {
    return $driver === 'sqlite'
      ? "CREATE TABLE IF NOT EXISTS {$tables['compteams']} (seasonid INT NOT NULL, compid INT NOT NULL, teamid INT NOT NULL, PRIMARY KEY (seasonid, compid, teamid))"
      : "CREATE TABLE IF NOT EXISTS `{$tables['compteams']}` (
          `seasonid` INT NOT NULL,
          `compid` INT NOT NULL,
          `teamid` INT NOT NULL,
          PRIMARY KEY (`seasonid`, `compid`, `teamid`)
        ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4";
  }
  if ($name === 'pages') {
    return $driver === 'sqlite'
      ? "CREATE TABLE IF NOT EXISTS {$tables['pages']} (pageid INTEGER PRIMARY KEY AUTOINCREMENT, lid INT NOT NULL DEFAULT 0, slug VARCHAR(60) NOT NULL, title VARCHAR(100) NOT NULL, body MEDIUMTEXT NOT NULL, status VARCHAR(10) NOT NULL DEFAULT 'published', audience VARCHAR(10) NOT NULL DEFAULT 'all', is_home INT NOT NULL DEFAULT 0, updated DATETIME NOT NULL, updated_by INT DEFAULT NULL, UNIQUE (lid, slug))"
      : "CREATE TABLE IF NOT EXISTS `{$tables['pages']}` (
          `pageid` INT NOT NULL AUTO_INCREMENT,
          `lid` INT NOT NULL DEFAULT 0,
          `slug` VARCHAR(60) NOT NULL,
          `title` VARCHAR(100) NOT NULL,
          `body` MEDIUMTEXT NOT NULL,
          `status` VARCHAR(10) NOT NULL DEFAULT 'published',
          `audience` VARCHAR(10) NOT NULL DEFAULT 'all',
          `is_home` TINYINT NOT NULL DEFAULT 0,
          `updated` DATETIME NOT NULL,
          `updated_by` INT DEFAULT NULL,
          PRIMARY KEY (`pageid`),
          UNIQUE KEY `uniq_slug` (`lid`, `slug`)
        ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4";
  }
  if ($name === 'menu') {
    return $driver === 'sqlite'
      ? "CREATE TABLE IF NOT EXISTS {$tables['menu']} (lid INT NOT NULL DEFAULT 0, position INT NOT NULL, item VARCHAR(40) NOT NULL, PRIMARY KEY (lid, position))"
      : "CREATE TABLE IF NOT EXISTS `{$tables['menu']}` (
          `lid` INT NOT NULL DEFAULT 0,
          `position` INT NOT NULL,
          `item` VARCHAR(40) NOT NULL,
          PRIMARY KEY (`lid`, `position`)
        ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4";
  }
  if ($name === 'leagues') {
    return $driver === 'sqlite'
      ? "CREATE TABLE IF NOT EXISTS {$tables['leagues']} (leagueid INTEGER PRIMARY KEY AUTOINCREMENT, lid INT NOT NULL DEFAULT 0, name VARCHAR(60) NOT NULL, owner INT NOT NULL, code CHAR(8) NOT NULL UNIQUE, all_comps INT NOT NULL DEFAULT 1, pts_exact INT NOT NULL DEFAULT 3, pts_result INT NOT NULL DEFAULT 1, start_at DATETIME DEFAULT NULL, created DATETIME NOT NULL)"
      : "CREATE TABLE IF NOT EXISTS `{$tables['leagues']}` (
          `leagueid` INT NOT NULL AUTO_INCREMENT,
          `lid` INT NOT NULL DEFAULT 0,
          `name` VARCHAR(60) NOT NULL,
          `owner` INT NOT NULL,
          `code` CHAR(8) NOT NULL,
          `all_comps` TINYINT NOT NULL DEFAULT 1,
          `pts_exact` INT NOT NULL DEFAULT 3,
          `pts_result` INT NOT NULL DEFAULT 1,
          `start_at` DATETIME DEFAULT NULL,
          `created` DATETIME NOT NULL,
          PRIMARY KEY (`leagueid`),
          UNIQUE KEY `uniq_code` (`code`),
          KEY `idx_owner` (`lid`, `owner`)
        ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4";
  }
  if ($name === 'leaguecomps') {
    return $driver === 'sqlite'
      ? "CREATE TABLE IF NOT EXISTS {$tables['leaguecomps']} (leagueid INT NOT NULL, compid INT NOT NULL, PRIMARY KEY (leagueid, compid))"
      : "CREATE TABLE IF NOT EXISTS `{$tables['leaguecomps']}` (
          `leagueid` INT NOT NULL,
          `compid` INT NOT NULL,
          PRIMARY KEY (`leagueid`, `compid`)
        ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4";
  }
  if ($name === 'members') {
    return $driver === 'sqlite'
      ? "CREATE TABLE IF NOT EXISTS {$tables['members']} (leagueid INT NOT NULL, userid INT NOT NULL, role VARCHAR(10) NOT NULL DEFAULT 'member', joined DATETIME NOT NULL, PRIMARY KEY (leagueid, userid))"
      : "CREATE TABLE IF NOT EXISTS `{$tables['members']}` (
          `leagueid` INT NOT NULL,
          `userid` INT NOT NULL,
          `role` VARCHAR(10) NOT NULL DEFAULT 'member',
          `joined` DATETIME NOT NULL,
          PRIMARY KEY (`leagueid`, `userid`),
          KEY `idx_user` (`userid`)
        ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4";
  }
  if ($name === 'follows') {
    return $driver === 'sqlite'
      ? "CREATE TABLE IF NOT EXISTS {$tables['follows']} (lid INT NOT NULL DEFAULT 0, userid INT NOT NULL, compid INT NOT NULL, PRIMARY KEY (lid, userid, compid))"
      : "CREATE TABLE IF NOT EXISTS `{$tables['follows']}` (
          `lid` INT NOT NULL DEFAULT 0,
          `userid` INT NOT NULL,
          `compid` INT NOT NULL,
          PRIMARY KEY (`lid`, `userid`, `compid`)
        ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4";
  }
  if ($name === 'prefs') {
    return $driver === 'sqlite'
      ? "CREATE TABLE IF NOT EXISTS {$tables['prefs']} (lid INT NOT NULL DEFAULT 0, userid INT NOT NULL, allow_email INT NOT NULL DEFAULT 0, notify_messages INT NOT NULL DEFAULT 0, auto_predict INT NOT NULL DEFAULT 0, auto_since DATETIME DEFAULT NULL, PRIMARY KEY (lid, userid))"
      : "CREATE TABLE IF NOT EXISTS `{$tables['prefs']}` (
          `lid` INT NOT NULL DEFAULT 0,
          `userid` INT NOT NULL,
          `allow_email` TINYINT NOT NULL DEFAULT 0,
          `notify_messages` TINYINT NOT NULL DEFAULT 0,
          `auto_predict` TINYINT NOT NULL DEFAULT 0,
          `auto_since` DATETIME DEFAULT NULL,
          PRIMARY KEY (`lid`, `userid`)
        ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4";
  }
  return $driver === 'sqlite'
    ? "CREATE TABLE IF NOT EXISTS {$tables['resets']} (token_hash CHAR(64) NOT NULL PRIMARY KEY, lid INT NOT NULL DEFAULT 0, userid INT NOT NULL, created DATETIME NOT NULL, expires DATETIME NOT NULL, used INT NOT NULL DEFAULT 0)"
    : "CREATE TABLE IF NOT EXISTS `{$tables['resets']}` (
        `token_hash` CHAR(64) NOT NULL,
        `lid` INT NOT NULL DEFAULT 0,
        `userid` INT NOT NULL,
        `created` DATETIME NOT NULL,
        `expires` DATETIME NOT NULL,
        `used` TINYINT NOT NULL DEFAULT 0,
        PRIMARY KEY (`token_hash`),
        KEY `idx_user` (`lid`, `userid`)
      ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4";
}

/** Quoted name of one of the extra tables. */
function xt(string $name): string {
  $table = extra_tables()[$name];
  return is_mysql() ? "`$table`" : "\"$table\"";
}

/** Run $fn; if the extra table doesn't exist yet, create it and try again. */
function with_table(string $name, callable $fn) {
  try {
    return $fn();
  } catch (PDOException $e) {
    $missing = $e->getCode() === '42S02' || stripos($e->getMessage(), 'no such table') !== false;
    if (!$missing) throw $e;
    db()->exec(extra_table_sql($name, is_mysql() ? 'mysql' : 'sqlite'));
    return $fn();
  }
}

// ---------------------------------------------------------------------
// Activity log
// ---------------------------------------------------------------------

/** Record an event. Never throws: logging must not break the request. */
function log_event(string $action, string $detail = '', ?array $user = null): void {
  try {
    $user = $user ?? current_user();
    with_table('log', fn() => q('INSERT INTO ' . xt('log') . ' (lid, created, userid, username, ip, action, detail) VALUES (?, ?, ?, ?, ?, ?, ?)',
      [lid(), now_str(), $user['id'] ?? null, isset($user['username']) ? mb_substr($user['username'], 0, 32) : null,
       mb_substr(client_ip(), 0, 45), mb_substr($action, 0, 40), mb_substr($detail, 0, 255)]));
    if (random_int(1, 200) === 1) {
      $days = max(7, (int)setting('LOG_DAYS') ?: 365);
      q('DELETE FROM ' . xt('log') . ' WHERE lid = ? AND created < ?', [lid(), date('Y-m-d H:i:s', time() - $days * 86400)]);
    }
  } catch (Throwable $e) {
    error_log('Prediction League log: ' . $e->getMessage());
  }
}

/** How many times $action was logged from this IP in the last $seconds. */
function recent_events(string $action, int $seconds, ?string $ip = null): int {
  try {
    return (int)with_table('log', fn() => q('SELECT COUNT(*) AS n FROM ' . xt('log') . ' WHERE lid = ? AND action = ? AND ip = ? AND created >= ?',
      [lid(), $action, $ip ?? client_ip(), date('Y-m-d H:i:s', time() - $seconds)])->fetch()['n']);
  } catch (Throwable $e) {
    return 0;
  }
}

// ---------------------------------------------------------------------
// Player email preferences (both off unless the player opts in)
// ---------------------------------------------------------------------

/** Add the automatic-prediction columns to a pluserprefs table made by an earlier version. */
function ensure_prefs_columns(): void {
  static $checked = false;
  if ($checked) return;
  $checked = true;
  with_table('prefs', function () {
    try {
      q('SELECT auto_predict, auto_since FROM ' . xt('prefs') . ' WHERE 1 = 0');
    } catch (PDOException $e) {
      if (stripos($e->getMessage(), 'no such table') !== false || $e->getCode() === '42S02') throw $e;
      db()->exec('ALTER TABLE ' . xt('prefs') . ' ADD COLUMN auto_predict ' . (is_mysql() ? 'TINYINT' : 'INT') . ' NOT NULL DEFAULT 0');
      db()->exec('ALTER TABLE ' . xt('prefs') . ' ADD COLUMN auto_since DATETIME DEFAULT NULL');
    }
  });
}

const NO_PREFS = ['allow_email' => false, 'notify_messages' => false, 'auto_predict' => false, 'auto_since' => null];

/** userid => preferences for the given ids (or everyone). */
function prefs_map(?array $ids = null): array {
  ensure_prefs_columns();
  $rows = with_table('prefs', fn() => q('SELECT userid, allow_email, notify_messages, auto_predict, auto_since FROM ' . xt('prefs') . ' WHERE lid = ?', [lid()])->fetchAll());
  $out = [];
  foreach ($rows as $r) {
    $out[(int)$r['userid']] = ['allow_email' => (bool)$r['allow_email'], 'notify_messages' => (bool)$r['notify_messages'],
                               'auto_predict' => (bool)$r['auto_predict'], 'auto_since' => $r['auto_since'] ?: null];
  }
  if ($ids !== null) {
    $filtered = [];
    foreach ($ids as $id) $filtered[(int)$id] = $out[(int)$id] ?? NO_PREFS;
    return $filtered;
  }
  return $out;
}

function user_prefs(int $uid): array {
  return prefs_map([$uid])[$uid];
}

// ---------------------------------------------------------------------
// Competitions (plcompetitions) and which competition a fixture is in (plmatchcomp)
// ---------------------------------------------------------------------

/** All competitions in display order: id, name, short, position. */
function competitions(bool $reload = false): array {
  static $list = null;
  if ($list === null || $reload) {
    $rows = with_table('comps', fn() => q('SELECT compid, name, short_name, position FROM ' . xt('comps') . ' WHERE lid = ? ORDER BY position, name', [lid()])->fetchAll());
    $list = array_map(fn($r) => ['id' => (int)$r['compid'], 'name' => (string)$r['name'],
      'short' => (string)$r['short_name'] !== '' ? (string)$r['short_name'] : (string)$r['name'], 'position' => (int)$r['position']], $rows);
  }
  return $list;
}

/** matchid => compid for every categorised fixture. */
function competition_map(bool $reload = false): array {
  static $map = null;
  if ($map === null || $reload) {
    $map = [];
    foreach (with_table('matchcomp', fn() => q('SELECT matchid, compid FROM ' . xt('matchcomp') . ' WHERE lid = ?', [lid()])->fetchAll()) as $r) {
      $map[(int)$r['matchid']] = (int)$r['compid'];
    }
  }
  return $map;
}

function competition_by_id(int $id): ?array {
  foreach (competitions() as $c) if ($c['id'] === $id) return $c;
  return null;
}

/** ['id','name','short'] of the fixture's competition, or null. */
function match_competition(int $matchId): ?array {
  $cid = competition_map()[$matchId] ?? null;
  $c = $cid ? competition_by_id($cid) : null;
  return $c ? ['id' => $c['id'], 'name' => $c['name'], 'short' => $c['short']] : null;
}

/** A competition id from input: null/0/'' means none; otherwise it must exist. */
function competition_id_in($v): ?int {
  if ($v === null || $v === '' || (int)$v === 0) return null;
  if (!competition_by_id((int)$v)) fail(422, 'That competition doesn\'t exist. Reload the page and choose again.');
  return (int)$v;
}

function set_match_competition(int $matchId, ?int $compId): void {
  q('DELETE FROM ' . xt('matchcomp') . ' WHERE lid = ? AND matchid = ?', [lid(), $matchId]);
  if ($compId) {
    q('INSERT INTO ' . xt('matchcomp') . ' (lid, matchid, compid) VALUES (?, ?, ?)', [lid(), $matchId, $compId]);
  }
}
