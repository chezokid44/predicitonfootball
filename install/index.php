<?php
/*
 * Prediction League installer.
 * Open /install/ in a browser on a new server. It creates the database
 * tables and default settings, adds the admin account and writes
 * api/config.php. Once api/config.php exists, this page is locked.
 */

declare(strict_types=1);
define('PL_APP', true);
require __DIR__ . '/../api/lib.php';
require __DIR__ . '/installer.php';

$configPath = dirname(__DIR__) . '/api/config.php';

header('Cache-Control: no-store');
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('X-Robots-Tag: noindex');
header("Content-Security-Policy: default-src 'self'; style-src 'self' https://fonts.googleapis.com; font-src https://fonts.gstatic.com; form-action 'self'; frame-ancestors 'none'");

function h($v): string {
  return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function page(string $title, string $body): void {
  echo '<!doctype html><html lang="en-GB"><head><meta charset="utf-8">'
    . '<meta name="viewport" content="width=device-width, initial-scale=1">'
    . '<meta name="robots" content="noindex">'
    . '<title>' . h($title) . '</title>'
    . '<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>'
    . '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700&family=Barlow:wght@400;500;600&display=swap">'
    . '<link rel="stylesheet" href="../assets/app.css"></head><body>'
    . '<header class="masthead"><div class="masthead-inner"><span class="brand">Set up your prediction league</span></div></header>'
    . '<main class="page">' . $body . '</main></body></html>';
  exit;
}

// Locked once installed.
if (is_file($configPath)) {
  http_response_code(403);
  page('Already set up', '<h1>Already set up</h1>
    <p class="lede">This league is already installed, so the installer is switched off.</p>
    <p><a class="btn" href="../">Go to the league</a></p>
    <p class="form-foot">To install again, delete <code>api/config.php</code> on the server first. Your data stays in the database.
    Delete the <code>install</code> folder from the server once you\'re finished with it.</p>');
}

session_name('PLINSTALL');
session_set_cookie_params(['httponly' => true, 'samesite' => 'Strict', 'secure' => is_https(), 'path' => '/']);
session_start();
if (empty($_SESSION['install_token'])) {
  $_SESSION['install_token'] = bin2hex(random_bytes(32));
}

$guessUrl = (is_https() ? 'https://' : 'http://') . ($_SERVER['HTTP_HOST'] ?? 'localhost')
  . rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '/install/index.php')), '/') . '/';
$values = ['db_host' => 'localhost', 'db_port' => '', 'db_name' => '', 'db_user' => '', 'title' => '',
           'timezone' => 'Europe/London', 'admin_username' => '', 'admin_email' => '',
           'site_url' => $guessUrl, 'cloudflare' => !empty($_SERVER['HTTP_CF_CONNECTING_IP'])];
$errors = [];

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
  if (!hash_equals($_SESSION['install_token'], (string)($_POST['token'] ?? ''))) {
    $errors[] = 'The form expired. Fill it in again and resubmit.';
  } else {
    [$clean, $errors] = install_validate($_POST);
    $values = array_merge($values, array_intersect_key($clean, $values));
    if (!$errors) {
      try {
        date_default_timezone_set($clean['timezone']);
        $pdo = install_connect($clean);
        $done = install_database($pdo, $clean);
        $php = install_config_php($clean);
        $iconDir = dirname(__DIR__) . '/uploads/icons';
        if (!is_dir($iconDir)) @mkdir($iconDir, 0755, true);
        $done[] = is_writable($iconDir)
          ? 'Created the uploads/icons folder for player pictures.'
          : 'Couldn\'t create a writable uploads/icons folder. Create it and make it writable to allow player pictures.';
        $pageDir = dirname(__DIR__) . '/uploads/pages';
        if (!is_dir($pageDir)) @mkdir($pageDir, 0755, true);
        if (!is_writable($pageDir)) $done[] = 'Couldn\'t create a writable uploads/pages folder. Create it and make it writable to add pictures to pages.';
        $badgeDir = dirname(__DIR__) . '/uploads/badges';
        if (!is_dir($badgeDir)) @mkdir($badgeDir, 0755, true);
        if (!is_writable($badgeDir)) $done[] = 'Couldn\'t create a writable uploads/badges folder. Create it and make it writable to add team badges.';
        $written = install_write_config($configPath, $php);
        unset($_SESSION['install_token']);

        $list = '<ul>' . implode('', array_map(fn($d) => '<li>' . h($d) . '</li>', $done)) . '</ul>';
        if ($written) {
          page('Your league is ready', '<h1>Your league is ready</h1>' . $list . '
            <p>Saved the settings to <code>api/config.php</code>. The installer is now locked.</p>
            <div class="notice"><p><strong>Delete the <code>install</code> folder from the server</strong> now that you\'re finished.</p></div>
            <p><a class="btn" href="../#/login">Log in to your league</a></p>');
        }
        page('One more step', '<h1>One more step</h1>' . $list . '
          <div class="notice"><p>The database is ready, but the server wouldn\'t let the installer save <code>api/config.php</code>.</p></div>
          <p>Create the file <code>api/config.php</code> on the server with exactly this content, then delete the <code>install</code> folder:</p>
          <p><textarea class="config-text" readonly rows="22">' . h($php) . '</textarea></p>
          <p><a class="btn" href="../#/login">Log in to your league</a></p>');
      } catch (InstallError $e) {
        $errors[] = $e->getMessage();
      } catch (Throwable $e) {
        error_log('Prediction League installer: ' . $e);
        $errors[] = 'The database returned an error: ' . $e->getMessage();
      }
    }
  }
}

$zones = '';
foreach (DateTimeZone::listIdentifiers() as $z) {
  $zones .= '<option' . ($z === $values['timezone'] ? ' selected' : '') . '>' . h($z) . '</option>';
}
$errorHtml = $errors
  ? '<div class="notice" role="alert">' . implode('', array_map(fn($e) => '<p class="error">' . h($e) . '</p>', $errors)) . '</div>'
  : '';
$field = fn($name, $label, $type = 'text', $extra = '', $hint = '') =>
  '<label>' . h($label) . ($hint ? '<span class="hint">' . h($hint) . '</span>' : '')
  . '<input type="' . $type . '" name="' . $name . '"' . ($type !== 'password' ? ' value="' . h($values[$name] ?? '') . '"' : '') . " $extra></label>";

page('Set up your prediction league', '<h1>Set up your league</h1>
  <p class="lede">This creates the database tables, the default settings and your admin account.
  If the database already has a Prediction League, its players, fixtures and predictions are kept.</p>' . $errorHtml . '
  <form class="stack install-form" method="post" action="" autocomplete="off">
    <input type="hidden" name="token" value="' . h($_SESSION['install_token']) . '">
    <h2>Database</h2>
    <p class="muted tight">Create an empty MySQL or MariaDB database and user in your hosting panel first.</p>
    ' . $field('db_host', 'Host', 'text', 'required', 'Usually localhost.') . '
    ' . $field('db_port', 'Port', 'text', 'inputmode="numeric" placeholder="3306"', 'Leave empty for the default.') . '
    ' . $field('db_name', 'Database name', 'text', 'required') . '
    ' . $field('db_user', 'Database user', 'text', 'required') . '
    ' . $field('db_pass', 'Database password', 'password', 'autocomplete="new-password"') . '
    <h2>League</h2>
    ' . $field('title', 'League name', 'text', 'required maxlength="90"', 'Shown at the top of every page. You can change it later.') . '
    <label>Time zone<span class="hint">Kick-off times are entered and shown in this time zone.</span>
      <select name="timezone">' . $zones . '</select></label>
    ' . $field('site_url', 'Site address', 'url', 'required', 'Used in password reset emails. Check it\'s the address players use.') . '
    <label class="check"><input type="checkbox" name="cloudflare" value="1"' . ($values['cloudflare'] ? ' checked' : '') . '>
      <span>This site is behind Cloudflare <span class="hint">So the activity log records visitors\' real IP addresses.</span></span></label>
    <h2>Admin account</h2>
    <p class="muted tight">Leave these empty only if the database already has an admin account.</p>
    ' . $field('admin_username', 'Username', 'text', 'maxlength="32" autocomplete="off"') . '
    ' . $field('admin_email', 'Email address', 'email', 'maxlength="60"') . '
    ' . $field('admin_password', 'Password', 'password', 'minlength="8" autocomplete="new-password"', 'At least 8 characters.') . '
    ' . $field('admin_password2', 'Password again', 'password', 'minlength="8" autocomplete="new-password"') . '
    <div><button class="btn" type="submit">Set up the league</button></div>
  </form>');
