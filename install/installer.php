<?php
/*
 * Prediction League installer - logic only (no output).
 * Used by install/index.php.
 */

declare(strict_types=1);

if (!defined('PL_APP')) {
  http_response_code(403);
  exit;
}

final class InstallError extends Exception {}

const INSTALL_TABLES = [
  'config' => 'plconfigdata',
  'users' => 'pluserdata',
  'predictions' => 'plpredictiondata',
  'matches' => 'plmatchdata',
  'standings' => 'plstandings',
  'messages' => 'plmsgdata',
];

/** Validate the install form. Returns [clean values, list of error messages]. */
function install_validate(array $in): array {
  $v = fn($k) => trim((string)($in[$k] ?? ''));
  $errors = [];
  $out = [
    'db_host' => $v('db_host') ?: 'localhost',
    'db_port' => $v('db_port'),
    'db_name' => $v('db_name'),
    'db_user' => $v('db_user'),
    'db_pass' => (string)($in['db_pass'] ?? ''),
    'title' => $v('title'),
    'timezone' => $v('timezone') ?: 'Europe/London',
    'admin_username' => $v('admin_username'),
    'admin_email' => $v('admin_email'),
    'admin_password' => (string)($in['admin_password'] ?? ''),
    'admin_password2' => (string)($in['admin_password2'] ?? ''),
    'site_url' => rtrim($v('site_url'), '/') . '/',
    'cloudflare' => !empty($in['cloudflare']),
  ];

  if (!preg_match('/\A[A-Za-z0-9.\-_]{1,255}\z/', $out['db_host'])) $errors[] = 'Enter the database host, for example localhost.';
  if ($out['db_port'] !== '' && (!ctype_digit($out['db_port']) || (int)$out['db_port'] < 1 || (int)$out['db_port'] > 65535)) $errors[] = 'The database port must be a number, or leave it empty.';
  if (!preg_match('/\A[A-Za-z0-9_\-$]{1,64}\z/', $out['db_name'])) $errors[] = 'Enter the database name (letters, numbers, - and _ only).';
  if ($out['db_user'] === '' || strlen($out['db_user']) > 80) $errors[] = 'Enter the database user name.';
  if ($out['title'] === '') $errors[] = 'Enter a league name.';
  elseif (mb_strlen($out['title']) > 90) $errors[] = 'The league name must be 90 characters or fewer.';
  if (!in_array($out['timezone'], DateTimeZone::listIdentifiers(), true)) $errors[] = 'Choose a time zone from the list.';
  if (!preg_match('#\Ahttps?://[^\s/?\#]+(/[^\s?\#]*)?\z#i', $out['site_url'])) $errors[] = 'Enter the site address, for example https://pl.example.co.uk/.';

  $adminGiven = $out['admin_username'] !== '' || $out['admin_email'] !== '' || $out['admin_password'] !== '';
  $out['create_admin'] = $adminGiven;
  if ($adminGiven) {
    if (!preg_match('/\A[\p{L}\p{N} ._\-]{1,32}\z/u', $out['admin_username'])) $errors[] = 'Admin username: up to 32 letters, numbers, spaces, dots, dashes or underscores.';
    if (!filter_var($out['admin_email'], FILTER_VALIDATE_EMAIL) || strlen($out['admin_email']) > 60) $errors[] = 'Enter a valid admin email address.';
    if (mb_strlen($out['admin_password']) < 8) $errors[] = 'The admin password must be at least 8 characters.';
    elseif ($out['admin_password'] !== $out['admin_password2']) $errors[] = 'The two admin passwords don\'t match.';
  }
  return [$out, $errors];
}

function install_connect(array $in): PDO {
  $dsn = 'mysql:host=' . $in['db_host'] . ($in['db_port'] !== '' ? ';port=' . (int)$in['db_port'] : '')
       . ';dbname=' . $in['db_name'] . ';charset=utf8mb4';
  try {
    return new PDO($dsn, $in['db_user'], $in['db_pass'], [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false,
      PDO::ATTR_TIMEOUT => 5,
    ]);
  } catch (PDOException $e) {
    throw new InstallError('Couldn\'t connect to the database: ' . $e->getMessage()
      . '. Check the host, database name, user and password. The database itself must already exist (create it in your hosting panel first).');
  }
}

/** CREATE TABLE statements. MySQL for real installs; SQLite for testing. */
function install_schema(string $driver): array {
  $t = INSTALL_TABLES;
  if ($driver === 'sqlite') {
    return [
      "CREATE TABLE IF NOT EXISTS {$t['config']} (lid INT NOT NULL DEFAULT 0, grp INT NOT NULL DEFAULT 0, param VARCHAR(32) NOT NULL DEFAULT '', value VARCHAR(90) NOT NULL DEFAULT '', descr TEXT NOT NULL, ro TEXT DEFAULT 'N', PRIMARY KEY (lid, param))",
      "CREATE TABLE IF NOT EXISTS {$t['matches']} (matchid INTEGER PRIMARY KEY AUTOINCREMENT, lid INT NOT NULL DEFAULT 0, matchdate DATETIME NOT NULL, hometeam VARCHAR(30) NOT NULL DEFAULT '', awayteam VARCHAR(30) NOT NULL DEFAULT '', homescore SMALLINT DEFAULT NULL, awayscore SMALLINT DEFAULT NULL)",
      "CREATE TABLE IF NOT EXISTS {$t['messages']} (msgid INTEGER PRIMARY KEY AUTOINCREMENT, lid INT NOT NULL DEFAULT 0, threadid INT, prevmsg INT, sender INT NOT NULL DEFAULT 0, receiver INT NOT NULL DEFAULT 0, subject VARCHAR(255), body TEXT, status TEXT, msgtime TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP)",
      "CREATE TABLE IF NOT EXISTS {$t['predictions']} (lid INT NOT NULL DEFAULT 0, userid INT NOT NULL DEFAULT 0, matchid INT NOT NULL DEFAULT 0, predtime TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, homescore SMALLINT DEFAULT NULL, awayscore SMALLINT DEFAULT NULL, isauto TEXT DEFAULT 'N', PRIMARY KEY (lid, userid, matchid))",
      "CREATE TABLE IF NOT EXISTS {$t['standings']} (lid INT NOT NULL DEFAULT 0, userid INT NOT NULL DEFAULT 0, position SMALLINT NOT NULL DEFAULT 0, pld INT DEFAULT 0, won INT DEFAULT 0, drawn INT DEFAULT 0, lost INT DEFAULT 0, gfor SMALLINT DEFAULT 0, gagainst SMALLINT DEFAULT 0, diff SMALLINT DEFAULT 0, points INT DEFAULT 0, PRIMARY KEY (lid, userid))",
      "CREATE TABLE IF NOT EXISTS {$t['users']} (userid INTEGER PRIMARY KEY AUTOINCREMENT, lid INT NOT NULL DEFAULT 0, username VARCHAR(32) NOT NULL DEFAULT '', password VARCHAR(255) DEFAULT NULL, email VARCHAR(60) DEFAULT NULL, icon VARCHAR(128) DEFAULT 'default.gif', lang VARCHAR(32) DEFAULT 'english', usertype SMALLINT DEFAULT 1, dflths SMALLINT DEFAULT 0, dfltas SMALLINT DEFAULT 0, since DATE DEFAULT NULL, auto TEXT DEFAULT 'N')",
    ];
  }
  $opts = 'ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4';
  return [
    "CREATE TABLE IF NOT EXISTS `{$t['config']}` (
      `lid` INT NOT NULL DEFAULT 0,
      `grp` INT NOT NULL DEFAULT 0,
      `param` VARCHAR(32) NOT NULL DEFAULT '',
      `value` VARCHAR(90) NOT NULL DEFAULT '',
      `descr` TEXT NOT NULL,
      `ro` ENUM('Y','N') DEFAULT 'N',
      PRIMARY KEY (`lid`, `param`)
    ) $opts",
    "CREATE TABLE IF NOT EXISTS `{$t['matches']}` (
      `lid` INT NOT NULL DEFAULT 0,
      `matchid` INT NOT NULL AUTO_INCREMENT,
      `matchdate` DATETIME NOT NULL,
      `hometeam` VARCHAR(30) NOT NULL DEFAULT '',
      `awayteam` VARCHAR(30) NOT NULL DEFAULT '',
      `homescore` SMALLINT UNSIGNED DEFAULT NULL,
      `awayscore` SMALLINT UNSIGNED DEFAULT NULL,
      PRIMARY KEY (`lid`, `matchid`),
      KEY `idx_matchid_auto` (`matchid`)
    ) $opts",
    "CREATE TABLE IF NOT EXISTS `{$t['messages']}` (
      `lid` INT NOT NULL DEFAULT 0,
      `msgid` INT NOT NULL AUTO_INCREMENT,
      `threadid` INT DEFAULT NULL,
      `prevmsg` INT DEFAULT NULL,
      `sender` INT NOT NULL DEFAULT 0,
      `receiver` INT NOT NULL DEFAULT 0,
      `subject` VARCHAR(255) DEFAULT NULL,
      `body` TEXT,
      `status` ENUM('new','read','deleted') DEFAULT NULL,
      `msgtime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`lid`, `msgid`),
      KEY `idx_msgid_auto` (`msgid`)
    ) $opts",
    "CREATE TABLE IF NOT EXISTS `{$t['predictions']}` (
      `lid` INT NOT NULL DEFAULT 0,
      `userid` INT NOT NULL DEFAULT 0,
      `matchid` INT NOT NULL DEFAULT 0,
      `predtime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `homescore` SMALLINT UNSIGNED DEFAULT NULL,
      `awayscore` SMALLINT UNSIGNED DEFAULT NULL,
      `isauto` ENUM('Y','N') DEFAULT 'N',
      PRIMARY KEY (`lid`, `userid`, `matchid`)
    ) $opts",
    "CREATE TABLE IF NOT EXISTS `{$t['standings']}` (
      `lid` INT NOT NULL DEFAULT 0,
      `userid` INT NOT NULL DEFAULT 0,
      `position` SMALLINT NOT NULL DEFAULT 0,
      `pld` INT UNSIGNED DEFAULT 0,
      `won` INT UNSIGNED DEFAULT 0,
      `drawn` INT UNSIGNED DEFAULT 0,
      `lost` INT UNSIGNED DEFAULT 0,
      `gfor` SMALLINT UNSIGNED DEFAULT 0,
      `gagainst` SMALLINT UNSIGNED DEFAULT 0,
      `diff` SMALLINT DEFAULT 0,
      `points` INT UNSIGNED DEFAULT 0,
      PRIMARY KEY (`lid`, `userid`)
    ) $opts",
    "CREATE TABLE IF NOT EXISTS `{$t['users']}` (
      `lid` INT NOT NULL DEFAULT 0,
      `userid` INT NOT NULL AUTO_INCREMENT,
      `username` VARCHAR(32) NOT NULL DEFAULT '',
      `password` VARCHAR(255) DEFAULT NULL,
      `email` VARCHAR(60) DEFAULT NULL,
      `icon` VARCHAR(128) DEFAULT 'default.gif',
      `lang` VARCHAR(32) DEFAULT 'english',
      `usertype` SMALLINT DEFAULT 1,
      `dflths` SMALLINT DEFAULT 0,
      `dfltas` SMALLINT DEFAULT 0,
      `since` DATE DEFAULT NULL,
      `auto` ENUM('Y','N') DEFAULT 'N',
      PRIMARY KEY (`lid`, `userid`),
      KEY `idx_userid_auto` (`userid`)
    ) $opts",
  ];
}

/**
 * Create missing tables, add missing default settings and the admin account.
 * Safe to run against an existing Prediction League database: nothing that
 * already exists is dropped or overwritten (apart from the league name and
 * admin email, which the form sets).
 * Returns a list of what was done.
 */
function install_database(PDO $pdo, array $in, int $leagueId = 0): array {
  $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
  $t = INSTALL_TABLES;
  $done = [];

  $existing = install_existing_tables($pdo, $driver);
  foreach (install_schema($driver) as $sql) {
    $pdo->exec($sql);
  }
  foreach (array_keys(extra_tables()) as $extra) {
    $pdo->exec(extra_table_sql($extra, $driver));
  }
  $created = array_diff(array_merge(array_values($t), array_values(extra_tables())), $existing);
  $done[] = $created
    ? 'Created tables: ' . implode(', ', $created) . '.'
    : 'All tables already existed, so existing data was kept.';

  // Older installs stored MD5 passwords in VARCHAR(40); bcrypt needs more room.
  if ($driver === 'mysql') {
    $len = $pdo->query("SELECT CHARACTER_MAXIMUM_LENGTH FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '{$t['users']}' AND COLUMN_NAME = 'password'")->fetchColumn();
    if ($len !== false && (int)$len < 255) {
      $pdo->exec("ALTER TABLE `{$t['users']}` MODIFY `password` VARCHAR(255) DEFAULT NULL");
      $done[] = 'Widened the password column so passwords are stored with bcrypt.';
    }
  }

  // Default settings: insert any that are missing.
  $exists = $pdo->prepare("SELECT 1 FROM {$t['config']} WHERE lid = ? AND param = ?");
  $insert = $pdo->prepare("INSERT INTO {$t['config']} (lid, grp, param, value, descr, ro) VALUES (?, ?, ?, ?, ?, ?)");
  $defaults = default_settings();
  $defaults['VERSION'] = [4, '2.0', 'Prediction League version.', 'text'];
  $added = 0;
  foreach ($defaults as $param => [$grp, $value, $descr]) {
    $exists->execute([$leagueId, $param]);
    if (!$exists->fetchColumn()) {
      $insert->execute([$leagueId, $grp, $param, $value, $descr, $param === 'VERSION' ? 'Y' : 'N']);
      $added++;
    }
  }
  $done[] = $added ? "Added $added default settings." : 'All settings already existed.';

  $set = $pdo->prepare("UPDATE {$t['config']} SET value = ? WHERE lid = ? AND param = ?");
  $set->execute([$in['title'], $leagueId, 'PRED_LEAGUE_TITLE']);
  if ($in['create_admin']) {
    $set->execute([$in['admin_email'], $leagueId, 'ADMIN_EMAIL']);
  }

  // Admin account.
  $admins = $pdo->prepare("SELECT COUNT(*) FROM {$t['users']} WHERE lid = ? AND usertype >= 4");
  $admins->execute([$leagueId]);
  $adminCount = (int)$admins->fetchColumn();
  if ($in['create_admin']) {
    $taken = $pdo->prepare("SELECT 1 FROM {$t['users']} WHERE lid = ? AND username = ?");
    $taken->execute([$leagueId, $in['admin_username']]);
    if ($taken->fetchColumn()) {
      throw new InstallError('A player called "' . $in['admin_username'] . '" already exists in this database. '
        . 'Choose a different admin username, or leave the admin fields empty to keep the existing accounts.');
    }
    $pdo->prepare("INSERT INTO {$t['users']} (lid, username, password, email, icon, lang, usertype, dflths, dfltas, since, auto)
                   VALUES (?, ?, ?, ?, 'default.gif', 'english', 4, 0, 0, ?, 'N')")
        ->execute([$leagueId, $in['admin_username'], password_hash($in['admin_password'], PASSWORD_DEFAULT),
                   $in['admin_email'], date('Y-m-d')]);
    $done[] = 'Created the admin account "' . $in['admin_username'] . '".';
  } elseif ($adminCount === 0) {
    throw new InstallError('This database has no admin account yet. Fill in the admin username, email and password.');
  } else {
    $done[] = 'Kept the existing ' . ($adminCount === 1 ? 'admin account' : "$adminCount admin accounts") . '.';
  }
  return $done;
}

function install_existing_tables(PDO $pdo, string $driver): array {
  $sql = $driver === 'sqlite'
    ? "SELECT name FROM sqlite_master WHERE type = 'table'"
    : 'SHOW TABLES';
  return array_map('strval', $pdo->query($sql)->fetchAll(PDO::FETCH_COLUMN));
}

/** The contents of api/config.php for these settings. */
function install_config_php(array $in, int $leagueId = 0): string {
  $config = [
    'db' => [
      'host' => $in['db_host'],
      'port' => $in['db_port'] === '' ? null : (int)$in['db_port'],
      'name' => $in['db_name'],
      'user' => $in['db_user'],
      'pass' => $in['db_pass'],
      'dsn' => null,
    ],
    'tables' => INSTALL_TABLES,
    'league_id' => $leagueId,
    'timezone' => $in['timezone'],
    // Address used in links in emails (password resets).
    'site_url' => $in['site_url'],
    // Where to read visitors' real IP address when behind a proxy. Cloudflare: 'HTTP_CF_CONNECTING_IP'.
    'client_ip_header' => $in['cloudflare'] ? 'HTTP_CF_CONNECTING_IP' : null,
    // Addresses never blocked by the bot traps, e.g. ['203.0.113.7'].
    'never_block_ips' => [],
    'first_user_becomes_admin' => false,
    'strict_client_check' => true,
    'debug' => false,
  ];
  return "<?php\n"
    . "if (!defined('PL_APP')) { http_response_code(403); exit; }\n"
    . "// Prediction League configuration, written by the installer on " . date('Y-m-d H:i') . ".\n"
    . "// To run the installer again, delete this file.\n"
    . 'return ' . var_export($config, true) . ";\n";
}

/** Write api/config.php. Returns false if the folder isn't writable. */
function install_write_config(string $path, string $php): bool {
  $tmp = $path . '.tmp' . bin2hex(random_bytes(4));
  if (@file_put_contents($tmp, $php, LOCK_EX) === false) {
    return false;
  }
  @chmod($tmp, 0640);
  if (!@rename($tmp, $path)) {
    @unlink($tmp);
    return false;
  }
  return true;
}
