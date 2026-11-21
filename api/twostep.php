<?php
/**
 * Account protection: 2-step log-in and passkeys.
 *
 *  - Authenticator apps (Google Authenticator, Microsoft Authenticator, Authy,
 *    1Password...): 6-digit codes, RFC 6238 (SHA-1, 30 seconds, 6 digits).
 *  - Passkeys and security keys (YubiKey...): WebAuthn. A key can be the
 *    second step after the password, and a passkey can log in on its own
 *    (it checks a PIN, fingerprint or face, so it's two steps in one).
 *  - Recovery codes: 10 single-use codes for when the phone or key is lost.
 *
 * Log-in with 2-step on: the password puts the session in a "pending" state
 * (no user yet); login.2fa finishes it with a code or a key. Password reset
 * links work the same way, so a reset can't skip the second step.
 *
 * Changes to any of this need the current password. Admins can switch 2-step
 * off for a player who has lost everything (Admin > Players).
 */
declare(strict_types=1);

if (!defined('PL_APP')) {
  http_response_code(404);
  exit;
}

const TWO_STEP_PENDING_SECONDS = 300;   // time to finish the second step
const TWO_STEP_MAX_TRIES = 5;           // wrong codes before starting again
const WEBAUTHN_CHALLENGE_SECONDS = 300;
const RECOVERY_CODE_COUNT = 10;

function twostep_routes(): array {
  return [
    'login.2fa'              => ['POST', 'route_login_2fa'],
    'login.2fa.options'      => ['POST', 'route_login_2fa_options'],
    'passkey.options'        => ['POST', 'route_passkey_options'],
    'passkey.login'          => ['POST', 'route_passkey_login'],
    'security'               => ['GET',  'route_security'],
    'security.totp.start'    => ['POST', 'route_security_totp_start'],
    'security.totp.confirm'  => ['POST', 'route_security_totp_confirm'],
    'security.totp.off'      => ['POST', 'route_security_totp_off'],
    'security.key.options'   => ['POST', 'route_security_key_options'],
    'security.key.add'       => ['POST', 'route_security_key_add'],
    'security.key.rename'    => ['POST', 'route_security_key_rename'],
    'security.key.remove'    => ['POST', 'route_security_key_remove'],
    'security.codes'         => ['POST', 'route_security_codes'],
    'admin.user.reset2fa'    => ['POST', 'route_admin_user_reset2fa'],
  ];
}

// ---------------------------------------------------------------------
// Status
// ---------------------------------------------------------------------

function user_totp(int $uid): ?array {
  $r = with_table('totp', fn() => q('SELECT secret, last_step FROM ' . xt('totp') . ' WHERE lid = ? AND userid = ?', [lid(), $uid])->fetch());
  return $r ?: null;
}

/** @return array[] the user's passkeys and security keys */
function user_keys(int $uid): array {
  return with_table('webauthn', fn() => q('SELECT * FROM ' . xt('webauthn') . ' WHERE lid = ? AND userid = ? ORDER BY id', [lid(), $uid])->fetchAll());
}

function recovery_left(int $uid): int {
  return (int)with_table('recovery', fn() => q('SELECT COUNT(*) AS n FROM ' . xt('recovery') . ' WHERE lid = ? AND userid = ? AND used IS NULL', [lid(), $uid])->fetch()['n']);
}

/** Whether logging in needs a second step (an authenticator app or a key). */
function two_step_on(int $uid): bool {
  return user_totp($uid) !== null || user_keys($uid) !== [];
}

/** How the user can finish logging in: totp, key, recovery. */
function two_step_methods(int $uid): array {
  $m = [];
  if (user_totp($uid)) $m[] = 'totp';
  if (user_keys($uid)) $m[] = 'key';
  if (recovery_left($uid) > 0) $m[] = 'recovery';
  return $m;
}

/** For the session: the signed-in user's 2-step summary. */
function two_step_summary(int $uid): array {
  $keys = user_keys($uid);
  return [
    'on' => user_totp($uid) !== null || $keys !== [],
    'totp' => user_totp($uid) !== null,
    'keys' => count($keys),
    'passkeys' => count(array_filter($keys, fn($k) => (int)$k['discoverable'] === 1)),
    'recovery_left' => recovery_left($uid),
  ];
}

/** Admins must have 2-step on before using admin pages, if REQUIRE_2FA_ADMINS is on. */
function admin_needs_two_step(array $user): bool {
  return $user['is_admin'] && setting_bool('REQUIRE_2FA_ADMINS') && !two_step_on((int)$user['id']);
}

// ---------------------------------------------------------------------
// Pending log-ins
// ---------------------------------------------------------------------

/** Called once the password (or a reset link) has checked out. */
function start_two_step(int $uid, string $how): array {
  session_regenerate_id(true);
  unset($_SESSION['uid']);
  $_SESSION['pending'] = ['uid' => $uid, 'at' => time(), 'tries' => 0, 'how' => $how];
  return two_step_methods($uid);
}

function pending_user(): ?int {
  $p = $_SESSION['pending'] ?? null;
  if (!is_array($p)) return null;
  if (time() - (int)$p['at'] > TWO_STEP_PENDING_SECONDS) {
    unset($_SESSION['pending']);
    return null;
  }
  return (int)$p['uid'];
}

/** For route_session: what a half-logged-in browser can do next. */
function pending_info(): ?array {
  $uid = pending_user();
  if ($uid === null) return null;
  $u = q('SELECT username FROM ' . t('users') . ' WHERE lid = ? AND userid = ?', [lid(), $uid])->fetch();
  if (!$u) { unset($_SESSION['pending']); return null; }
  return ['username' => clean_text($u['username']), 'methods' => two_step_methods($uid)];
}

function finish_login(int $uid, string $detail): array {
  session_regenerate_id(true);
  unset($_SESSION['pending'], $_SESSION['webauthn']);
  $_SESSION['uid'] = $uid;
  $u = q('SELECT username FROM ' . t('users') . ' WHERE lid = ? AND userid = ?', [lid(), $uid])->fetch();
  log_event('login', $detail, ['id' => $uid, 'username' => clean_text($u['username'] ?? '')]);
  return route_session();
}

function two_step_failed(int $uid, string $what): void {
  $_SESSION['pending']['tries'] = (int)($_SESSION['pending']['tries'] ?? 0) + 1;
  $u = q('SELECT username FROM ' . t('users') . ' WHERE lid = ? AND userid = ?', [lid(), $uid])->fetch();
  log_event('login_failed', $what, ['id' => $uid, 'username' => clean_text($u['username'] ?? '')]);
  usleep(400000);
  if ($_SESSION['pending']['tries'] >= TWO_STEP_MAX_TRIES) {
    unset($_SESSION['pending']);
    fail(401, 'Too many wrong codes. Log in with your password again.', 'two_step_restart');
  }
}

function route_login_2fa(): array {
  if (recent_events('login_failed', 900) >= 10) {
    fail(429, 'Too many failed log-in attempts from your connection. Wait 15 minutes and try again.');
  }
  $uid = pending_user();
  if ($uid === null) fail(401, 'Your log-in timed out. Enter your password again.', 'two_step_restart');
  $b = body();

  if (isset($b['assertion']) && is_array($b['assertion'])) {
    $key = webauthn_check_assertion($b['assertion'], 'login2fa', false);
    if (!$key || (int)$key['userid'] !== $uid) {
      two_step_failed($uid, 'Security key not accepted at the second step');
      fail(401, 'That key isn\'t registered to this account. Try another key or a code.');
    }
    return finish_login($uid, 'Logged in (password and ' . clean_text($key['name']) . ')');
  }

  $code = preg_replace('/[\s-]/', '', (string)($b['code'] ?? ''));
  if (preg_match('/^\d{6}$/', $code)) {
    $t = user_totp($uid);
    if ($t && totp_check($uid, $t, $code)) return finish_login($uid, 'Logged in (password and authenticator app)');
    two_step_failed($uid, 'Wrong authenticator code');
    fail(401, 'That code isn\'t right. Check the time on your phone is correct, and use the newest code.');
  }
  if (preg_match('/^[A-Za-z0-9]{10}$/', $code)) {
    if (use_recovery_code($uid, $code)) {
      $left = recovery_left($uid);
      $res = finish_login($uid, "Logged in (password and a recovery code; $left left)");
      return $res + ['recovery_left' => $left];
    }
    two_step_failed($uid, 'Wrong recovery code');
    fail(401, 'That recovery code isn\'t right, or it has been used.');
  }
  fail(422, 'Enter the 6-digit code from your app, or a 10-character recovery code.');
  return [];
}

function route_login_2fa_options(): array {
  $uid = pending_user();
  if ($uid === null) fail(401, 'Your log-in timed out. Enter your password again.', 'two_step_restart');
  $keys = user_keys($uid);
  if (!$keys) fail(422, 'This account has no security keys.');
  return ['publicKey' => webauthn_request_options('login2fa', $keys, 'discouraged')];
}

function route_passkey_options(): array {
  if (recent_events('login_failed', 900) >= 10) {
    fail(429, 'Too many failed log-in attempts from your connection. Wait 15 minutes and try again.');
  }
  return ['publicKey' => webauthn_request_options('passkey', [], 'required')];
}

function route_passkey_login(): array {
  if (recent_events('login_failed', 900) >= 10) {
    fail(429, 'Too many failed log-in attempts from your connection. Wait 15 minutes and try again.');
  }
  $a = body()['assertion'] ?? null;
  $key = is_array($a) ? webauthn_check_assertion($a, 'passkey', true) : null;
  if (!$key) {
    log_event('login_failed', 'Passkey not accepted');
    usleep(400000);
    fail(401, 'That passkey isn\'t registered here. Log in with your password, then add the passkey on Your account.');
  }
  return finish_login((int)$key['userid'], 'Logged in with a passkey (' . clean_text($key['name']) . ')');
}

// ---------------------------------------------------------------------
// Account: security
// ---------------------------------------------------------------------

function require_current_password(array $b): void {
  $u = require_user();
  $row = q('SELECT password FROM ' . t('users') . ' WHERE lid = ? AND userid = ?', [lid(), $u['id']])->fetch();
  if (!$row || !verify_password((string)($b['password'] ?? ''), (string)$row['password'])) {
    log_event('security_failed', 'Wrong password when changing 2-step settings');
    usleep(400000);
    fail(403, 'Your current password isn\'t right.', 'bad_password');
  }
}

function route_security(): array {
  $u = require_user();
  $keys = array_map(fn($k) => [
    'id' => (int)$k['id'], 'name' => clean_text($k['name']), 'passkey' => (int)$k['discoverable'] === 1,
    'created' => $k['created'], 'last_used' => $k['lastused'],
  ], user_keys($u['id']));
  return two_step_summary($u['id']) + ['key_list' => $keys, 'required' => admin_needs_two_step($u)];
}

function route_security_totp_start(): array {
  $u = require_user();
  require_current_password(body());
  if (user_totp($u['id'])) fail(422, 'An authenticator app is already set up. Turn it off first to move it to a new phone.');
  $secret = base32_encode(random_bytes(20));
  $_SESSION['totp_setup'] = ['secret' => $secret, 'at' => time()];
  $issuer = trim(preg_replace('/[:\x00-\x1F]/', ' ', (string)setting('PRED_LEAGUE_TITLE'))) ?: 'Prediction League';
  $label = rawurlencode($issuer) . ':' . rawurlencode($u['username']);
  $uri = 'otpauth://totp/' . $label . '?secret=' . $secret . '&issuer=' . rawurlencode($issuer) . '&algorithm=SHA1&digits=6&period=30';
  return ['secret' => $secret, 'uri' => $uri, 'issuer' => $issuer];
}

function route_security_totp_confirm(): array {
  $u = require_user();
  $s = $_SESSION['totp_setup'] ?? null;
  if (!is_array($s) || time() - (int)$s['at'] > 900) fail(422, 'Setting up took too long. Start again.');
  $code = preg_replace('/\s/', '', (string)(body()['code'] ?? ''));
  $step = totp_match($s['secret'], $code);
  if ($step === null) fail(422, 'That code isn\'t right. Check the time on your phone is correct, and enter the newest code.');
  with_table('totp', function () use ($u, $s, $step) {
    q('DELETE FROM ' . xt('totp') . ' WHERE lid = ? AND userid = ?', [lid(), $u['id']]);
    q('INSERT INTO ' . xt('totp') . ' (lid, userid, secret, last_step, created) VALUES (?, ?, ?, ?, ?)', [lid(), $u['id'], $s['secret'], $step, now_str()]);
  });
  unset($_SESSION['totp_setup']);
  log_event('security', 'Turned on 2-step log-in with an authenticator app');
  $codes = recovery_left($u['id']) > 0 ? null : new_recovery_codes($u['id']);
  return route_security() + ['codes' => $codes];
}

function route_security_totp_off(): array {
  $u = require_user();
  require_current_password(body());
  with_table('totp', fn() => q('DELETE FROM ' . xt('totp') . ' WHERE lid = ? AND userid = ?', [lid(), $u['id']]));
  log_event('security', 'Turned off the authenticator app');
  if (!two_step_on($u['id'])) clear_recovery_codes($u['id']);
  return route_security();
}

function route_security_key_options(): array {
  $u = require_user();
  $b = body();
  require_current_password($b);
  $passkey = !empty($b['passkey']);
  return ['publicKey' => webauthn_creation_options($u, $passkey)];
}

function route_security_key_add(): array {
  $u = require_user();
  $b = body();
  $name = trim(preg_replace('/[\x00-\x1F\x7F]/u', '', (string)($b['name'] ?? '')));
  $name = mb_substr($name, 0, 60);
  $c = is_array($b['credential'] ?? null) ? $b['credential'] : fail(422, 'No key was sent.');
  $key = webauthn_check_registration($c, (int)$u['id']);
  $count = count(user_keys($u['id']));
  if ($count >= 20) fail(422, 'You can have up to 20 keys. Remove one first.');
  if ($name === '') $name = $key['discoverable'] ? 'Passkey ' . ($count + 1) : 'Security key ' . ($count + 1);
  with_table('webauthn', fn() => q('INSERT INTO ' . xt('webauthn') . ' (lid, userid, credid, pubkey, alg, signcount, name, discoverable, transports, created, lastused) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL)',
    [lid(), $u['id'], $key['credid'], $key['pubkey'], $key['alg'], $key['signcount'], $name, $key['discoverable'] ? 1 : 0, $key['transports'], now_str()]));
  log_event('security', 'Added ' . ($key['discoverable'] ? 'a passkey' : 'a security key') . ' (' . $name . ')');
  $codes = recovery_left($u['id']) > 0 ? null : new_recovery_codes($u['id']);
  return route_security() + ['codes' => $codes];
}

function route_security_key_rename(): array {
  $u = require_user();
  $b = body();
  $name = mb_substr(trim(preg_replace('/[\x00-\x1F\x7F]/u', '', (string)($b['name'] ?? ''))), 0, 60);
  if ($name === '') fail(422, 'Give the key a name.');
  $n = with_table('webauthn', fn() => q('UPDATE ' . xt('webauthn') . ' SET name = ? WHERE lid = ? AND userid = ? AND id = ?', [$name, lid(), $u['id'], (int)($b['id'] ?? 0)])->rowCount());
  if (!$n) fail(404, 'That key isn\'t on your account.');
  return route_security();
}

function route_security_key_remove(): array {
  $u = require_user();
  $b = body();
  require_current_password($b);
  $k = with_table('webauthn', fn() => q('SELECT name FROM ' . xt('webauthn') . ' WHERE lid = ? AND userid = ? AND id = ?', [lid(), $u['id'], (int)($b['id'] ?? 0)])->fetch());
  if (!$k) fail(404, 'That key isn\'t on your account.');
  q('DELETE FROM ' . xt('webauthn') . ' WHERE lid = ? AND userid = ? AND id = ?', [lid(), $u['id'], (int)$b['id']]);
  log_event('security', 'Removed the key "' . clean_text($k['name']) . '"');
  if (!two_step_on($u['id'])) clear_recovery_codes($u['id']);
  return route_security();
}

function route_security_codes(): array {
  $u = require_user();
  require_current_password(body());
  if (!two_step_on($u['id'])) fail(422, 'Turn on 2-step log-in first.');
  $codes = new_recovery_codes($u['id']);
  log_event('security', 'Made new recovery codes');
  return route_security() + ['codes' => $codes];
}

function route_admin_user_reset2fa(): array {
  $admin = require_admin();
  $id = (int)(body()['id'] ?? 0);
  $u = q('SELECT userid, username FROM ' . t('users') . ' WHERE lid = ? AND userid = ?', [lid(), $id])->fetch();
  if (!$u) fail(404, 'That player doesn\'t exist.');
  if ($id === (int)$admin['id']) fail(422, 'Use Your account to change your own 2-step log-in.');
  with_table('totp', fn() => q('DELETE FROM ' . xt('totp') . ' WHERE lid = ? AND userid = ?', [lid(), $id]));
  with_table('webauthn', fn() => q('DELETE FROM ' . xt('webauthn') . ' WHERE lid = ? AND userid = ?', [lid(), $id]));
  clear_recovery_codes($id);
  log_event('security', 'Switched off 2-step log-in for ' . clean_text($u['username']) . ' (authenticator app, keys and recovery codes removed)');
  return ['ok' => true];
}

// ---------------------------------------------------------------------
// Recovery codes
// ---------------------------------------------------------------------

function recovery_hash(string $code): string {
  return hash('sha256', 'plrc:' . strtolower(preg_replace('/[\s-]/', '', $code)));
}

/** @return string[] new codes (shown once), replacing any old ones */
function new_recovery_codes(int $uid): array {
  $alphabet = 'abcdefghjkmnpqrstuvwxyz23456789';
  $codes = [];
  for ($i = 0; $i < RECOVERY_CODE_COUNT; $i++) {
    $c = '';
    for ($j = 0; $j < 10; $j++) $c .= $alphabet[random_int(0, strlen($alphabet) - 1)];
    $codes[] = substr($c, 0, 5) . '-' . substr($c, 5);
  }
  with_table('recovery', function () use ($uid, $codes) {
    clear_recovery_codes($uid);
    foreach ($codes as $c) {
      q('INSERT INTO ' . xt('recovery') . ' (lid, userid, code_hash, used, created) VALUES (?, ?, ?, NULL, ?)', [lid(), $uid, recovery_hash($c), now_str()]);
    }
  });
  return $codes;
}

function clear_recovery_codes(int $uid): void {
  with_table('recovery', fn() => q('DELETE FROM ' . xt('recovery') . ' WHERE lid = ? AND userid = ?', [lid(), $uid]));
}

function use_recovery_code(int $uid, string $code): bool {
  return (bool)with_table('recovery', fn() => q('UPDATE ' . xt('recovery') . ' SET used = ? WHERE lid = ? AND userid = ? AND code_hash = ? AND used IS NULL',
    [now_str(), lid(), $uid, recovery_hash($code)])->rowCount());
}

// ---------------------------------------------------------------------
// Authenticator apps (TOTP, RFC 6238)
// ---------------------------------------------------------------------

function base32_encode(string $bin): string {
  $alpha = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
  $bits = '';
  foreach (str_split($bin) as $ch) $bits .= str_pad(decbin(ord($ch)), 8, '0', STR_PAD_LEFT);
  $out = '';
  foreach (str_split($bits, 5) as $chunk) $out .= $alpha[bindec(str_pad($chunk, 5, '0'))];
  return $out;
}

function base32_decode(string $s): string {
  $alpha = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
  $s = strtoupper(preg_replace('/[^A-Za-z2-7]/', '', $s));
  $bits = '';
  foreach (str_split($s) as $ch) $bits .= str_pad(decbin(strpos($alpha, $ch)), 5, '0', STR_PAD_LEFT);
  $out = '';
  foreach (str_split($bits, 8) as $byte) if (strlen($byte) === 8) $out .= chr(bindec($byte));
  return $out;
}

function totp_code(string $secret, int $step): string {
  $h = hash_hmac('sha1', pack('J', $step), base32_decode($secret), true);
  $o = ord($h[19]) & 0x0F;
  $n = ((ord($h[$o]) & 0x7F) << 24) | (ord($h[$o + 1]) << 16) | (ord($h[$o + 2]) << 8) | ord($h[$o + 3]);
  return str_pad((string)($n % 1000000), 6, '0', STR_PAD_LEFT);
}

/** The time step the code matches (allowing one step of clock drift each way), or null. */
function totp_match(string $secret, string $code, int $after = -1): ?int {
  if (!preg_match('/^\d{6}$/', $code)) return null;
  $now = intdiv(time(), 30);
  foreach ([0, -1, 1] as $d) {
    $step = $now + $d;
    if ($step > $after && hash_equals(totp_code($secret, $step), $code)) return $step;
  }
  return null;
}

/** Check a code for a user; each code works once. */
function totp_check(int $uid, array $t, string $code): bool {
  $step = totp_match((string)$t['secret'], $code, (int)$t['last_step']);
  if ($step === null) return false;
  $n = with_table('totp', fn() => q('UPDATE ' . xt('totp') . ' SET last_step = ? WHERE lid = ? AND userid = ? AND last_step < ?', [$step, lid(), $uid, $step])->rowCount());
  return $n === 1;
}

// ---------------------------------------------------------------------
// WebAuthn (passkeys and security keys)
// ---------------------------------------------------------------------

function b64url(string $bin): string {
  return rtrim(strtr(base64_encode($bin), '+/', '-_'), '=');
}

function b64url_decode(string $s): string {
  $d = base64_decode(strtr($s, '-_', '+/') . str_repeat('=', (4 - strlen($s) % 4) % 4), true);
  return $d === false ? '' : $d;
}

/** The site's host name: the WebAuthn "relying party". */
function webauthn_rp_id(): string {
  $host = parse_url(site_url(), PHP_URL_HOST);
  if (!$host) $host = explode(':', (string)($_SERVER['HTTP_HOST'] ?? 'localhost'))[0];
  return strtolower((string)$host);
}

function webauthn_user_handle(int $uid): string {
  return substr(hash('sha256', 'pl-user:' . lid() . ':' . $uid, true), 0, 16);
}

function webauthn_challenge(string $purpose): string {
  $c = random_bytes(32);
  $_SESSION['webauthn'] = ['challenge' => b64url($c), 'purpose' => $purpose, 'at' => time()];
  return b64url($c);
}

function webauthn_creation_options(array $u, bool $passkey): array {
  $exclude = array_map(fn($k) => ['type' => 'public-key', 'id' => $k['credid']], user_keys((int)$u['id']));
  $title = (string)setting('PRED_LEAGUE_TITLE') ?: 'Prediction League';
  return [
    'challenge' => webauthn_challenge($passkey ? 'register-passkey' : 'register-key'),
    'rp' => ['id' => webauthn_rp_id(), 'name' => mb_substr($title, 0, 60)],
    'user' => ['id' => b64url(webauthn_user_handle((int)$u['id'])), 'name' => $u['username'], 'displayName' => $u['username']],
    'pubKeyCredParams' => [['type' => 'public-key', 'alg' => -7], ['type' => 'public-key', 'alg' => -8], ['type' => 'public-key', 'alg' => -257]],
    'timeout' => 120000,
    'attestation' => 'none',
    'excludeCredentials' => $exclude,
    'authenticatorSelection' => $passkey
      ? ['residentKey' => 'required', 'requireResidentKey' => true, 'userVerification' => 'required']
      : ['residentKey' => 'discouraged', 'requireResidentKey' => false, 'userVerification' => 'discouraged'],
  ];
}

function webauthn_request_options(string $purpose, array $keys, string $uv): array {
  $o = [
    'challenge' => webauthn_challenge($purpose),
    'rpId' => webauthn_rp_id(),
    'timeout' => 120000,
    'userVerification' => $uv,
  ];
  if ($keys) {
    $o['allowCredentials'] = array_map(fn($k) => ['type' => 'public-key', 'id' => $k['credid']]
      + ($k['transports'] !== '' ? ['transports' => explode(',', $k['transports'])] : []), $keys);
  }
  return $o;
}

/** Check clientDataJSON: type, challenge (single use) and origin. Returns the raw JSON. */
function webauthn_client_data(string $json, string $type, array $purposes): void {
  $cd = json_decode($json, true);
  $w = $_SESSION['webauthn'] ?? null;
  unset($_SESSION['webauthn']);   // single use
  if (!is_array($cd) || ($cd['type'] ?? '') !== $type) fail(422, 'The key\'s answer wasn\'t understood. Try again.');
  if (!is_array($w) || !in_array($w['purpose'], $purposes, true) || time() - (int)$w['at'] > WEBAUTHN_CHALLENGE_SECONDS
      || !hash_equals((string)$w['challenge'], (string)($cd['challenge'] ?? ''))) {
    fail(422, 'That took too long, or the page was out of date. Try again.');
  }
  $origin = parse_url((string)($cd['origin'] ?? ''));
  $host = strtolower((string)($origin['host'] ?? ''));
  $local = in_array($host, ['localhost', '127.0.0.1'], true);
  if ($host !== webauthn_rp_id() || !(($origin['scheme'] ?? '') === 'https' || $local)) {
    fail(422, 'This key was used from a different address (' . ($cd['origin'] ?? '?') . '). Use ' . site_url() . '.');
  }
}

/** Parse authenticator data. */
function webauthn_auth_data(string $ad): array {
  if (strlen($ad) < 37) fail(422, 'The key\'s answer was too short.');
  $r = [
    'rpIdHash' => substr($ad, 0, 32),
    'flags' => ord($ad[32]),
    'signCount' => unpack('N', substr($ad, 33, 4))[1],
  ];
  if (!hash_equals(hash('sha256', webauthn_rp_id(), true), $r['rpIdHash'])) fail(422, 'This key was made for a different site.');
  if (!($r['flags'] & 0x01)) fail(422, 'The key wasn\'t touched. Try again.');
  if ($r['flags'] & 0x40) {
    if (strlen($ad) < 55) fail(422, 'The key\'s answer was too short.');
    $len = unpack('n', substr($ad, 53, 2))[1];
    $r['credId'] = substr($ad, 55, $len);
    $pos = 55 + $len;
    $r['publicKey'] = cbor_decode($ad, $pos);
  }
  return $r;
}

/** Check a new key. Returns what to store. */
function webauthn_check_registration(array $c, int $uid): array {
  $resp = $c['response'] ?? [];
  $clientJson = b64url_decode((string)($resp['clientDataJSON'] ?? ''));
  $w = $_SESSION['webauthn'] ?? null;
  $passkey = is_array($w) && ($w['purpose'] ?? '') === 'register-passkey';
  webauthn_client_data($clientJson, 'webauthn.create', ['register-passkey', 'register-key']);
  $att = b64url_decode((string)($resp['attestationObject'] ?? ''));
  $pos = 0;
  $obj = cbor_decode($att, $pos);
  if (!is_array($obj) || !isset($obj['authData']) || !is_string($obj['authData'])) fail(422, 'The key\'s answer wasn\'t understood.');
  $ad = webauthn_auth_data($obj['authData']);
  if (!isset($ad['credId'], $ad['publicKey'])) fail(422, 'The key didn\'t send its public key.');
  if ($passkey && !($ad['flags'] & 0x04)) fail(422, 'A passkey needs your PIN, fingerprint or face. Try again, or add it as a security key.');
  [$pub, $alg] = cose_to_key($ad['publicKey']);
  $credid = b64url($ad['credId']);
  if (strlen($credid) > 255) fail(422, 'This key\'s ID is too long to store.');
  $taken = with_table('webauthn', fn() => q('SELECT userid FROM ' . xt('webauthn') . ' WHERE lid = ? AND credid = ?', [lid(), $credid])->fetch());
  if ($taken) fail(422, 'This key is already registered.');
  $transports = array_values(array_filter((array)($c['transports'] ?? []), fn($t) => is_string($t) && preg_match('/^[a-z-]{2,20}$/', $t)));
  $discoverable = $passkey || !empty($c['clientExtensionResults']['credProps']['rk']);
  return ['credid' => $credid, 'pubkey' => $pub, 'alg' => $alg, 'signcount' => $ad['signCount'],
          'discoverable' => $discoverable, 'transports' => implode(',', array_slice($transports, 0, 6))];
}

/** Check a log-in with a key. Returns the key's row, or null. */
function webauthn_check_assertion(array $a, string $purpose, bool $needUv): ?array {
  $resp = $a['response'] ?? [];
  $clientJson = b64url_decode((string)($resp['clientDataJSON'] ?? ''));
  webauthn_client_data($clientJson, 'webauthn.get', [$purpose]);
  $credid = (string)($a['rawId'] ?? $a['id'] ?? '');
  if ($credid === '' || strlen($credid) > 255) return null;
  $key = with_table('webauthn', fn() => q('SELECT * FROM ' . xt('webauthn') . ' WHERE lid = ? AND credid = ?', [lid(), b64url(b64url_decode($credid))])->fetch());
  if (!$key) return null;
  $adRaw = b64url_decode((string)($resp['authenticatorData'] ?? ''));
  $ad = webauthn_auth_data($adRaw);
  if ($needUv && !($ad['flags'] & 0x04)) fail(422, 'Logging in with a passkey needs your PIN, fingerprint or face.');
  $sig = b64url_decode((string)($resp['signature'] ?? ''));
  $data = $adRaw . hash('sha256', $clientJson, true);
  if (!webauthn_verify((string)$key['pubkey'], (int)$key['alg'], $data, $sig)) return null;
  $stored = (int)$key['signcount'];
  if ($ad['signCount'] !== 0 || $stored !== 0) {
    if ($ad['signCount'] <= $stored) {
      log_event('security', 'A key for user ' . (int)$key['userid'] . ' sent an old counter (it may have been copied); refused');
      return null;
    }
  }
  q('UPDATE ' . xt('webauthn') . ' SET signcount = ?, lastused = ? WHERE lid = ? AND id = ?', [$ad['signCount'], now_str(), lid(), (int)$key['id']]);
  return $key;
}

function webauthn_verify(string $pub, int $alg, string $data, string $sig): bool {
  if ($alg === -8) {
    $raw = base64_decode($pub, true);
    return $raw !== false && strlen($raw) === SODIUM_CRYPTO_SIGN_PUBLICKEYBYTES && strlen($sig) === SODIUM_CRYPTO_SIGN_BYTES
      && sodium_crypto_sign_verify_detached($sig, $data, $raw);
  }
  $k = openssl_pkey_get_public($pub);
  if ($k === false) return false;
  return openssl_verify($data, $sig, $k, OPENSSL_ALGO_SHA256) === 1;
}

/** COSE key to [PEM or base64 raw key, alg]. Supports ES256 (-7), EdDSA (-8) and RS256 (-257). */
function cose_to_key($cose): array {
  if (!is_array($cose)) fail(422, 'The key\'s public key wasn\'t understood.');
  $kty = $cose[1] ?? null;
  $alg = $cose[3] ?? null;
  if ($kty === 2 && $alg === -7 && ($cose[-1] ?? null) === 1) {
    $x = $cose[-2] ?? ''; $y = $cose[-3] ?? '';
    if (!is_string($x) || !is_string($y) || strlen($x) !== 32 || strlen($y) !== 32) fail(422, 'The key\'s public key wasn\'t understood.');
    $der = hex2bin('3059301306072a8648ce3d020106082a8648ce3d030107034200') . "\x04" . $x . $y;
    return [der_to_pem($der), -7];
  }
  if ($kty === 1 && $alg === -8 && ($cose[-1] ?? null) === 6) {
    $x = $cose[-2] ?? '';
    if (!is_string($x) || strlen($x) !== 32) fail(422, 'The key\'s public key wasn\'t understood.');
    return [base64_encode($x), -8];
  }
  if ($kty === 3 && $alg === -257) {
    $n = $cose[-1] ?? ''; $e = $cose[-2] ?? '';
    if (!is_string($n) || !is_string($e) || $n === '' || $e === '') fail(422, 'The key\'s public key wasn\'t understood.');
    $rsa = asn1_int($n) . asn1_int($e);
    $rsa = "\x30" . asn1_len(strlen($rsa)) . $rsa;
    $bit = "\x03" . asn1_len(strlen($rsa) + 1) . "\0" . $rsa;
    $algId = hex2bin('300d06092a864886f70d0101010500');
    $der = "\x30" . asn1_len(strlen($algId . $bit)) . $algId . $bit;
    return [der_to_pem($der), -257];
  }
  fail(422, 'This kind of key isn\'t supported. Try another key, or the authenticator app.');
  return [];
}

function asn1_int(string $v): string {
  $v = ltrim($v, "\0");
  if ($v === '' || (ord($v[0]) & 0x80)) $v = "\0" . $v;
  return "\x02" . asn1_len(strlen($v)) . $v;
}

function asn1_len(int $n): string {
  if ($n < 0x80) return chr($n);
  $b = ltrim(pack('N', $n), "\0");
  return chr(0x80 | strlen($b)) . $b;
}

function der_to_pem(string $der): string {
  return "-----BEGIN PUBLIC KEY-----\n" . chunk_split(base64_encode($der), 64, "\n") . "-----END PUBLIC KEY-----\n";
}

/** Minimal CBOR decoder (what WebAuthn needs: ints, byte/text strings, arrays, maps, simple values). */
function cbor_decode(string $s, int &$pos, int $depth = 0) {
  if ($depth > 16 || $pos >= strlen($s)) fail(422, 'The key\'s answer wasn\'t understood.');
  $ib = ord($s[$pos++]);
  $major = $ib >> 5;
  $info = $ib & 0x1F;
  $len = $info;
  if ($info === 24) { $len = ord($s[$pos] ?? "\0"); $pos += 1; }
  elseif ($info === 25) { $len = unpack('n', substr($s, $pos, 2))[1]; $pos += 2; }
  elseif ($info === 26) { $len = unpack('N', substr($s, $pos, 4))[1]; $pos += 4; }
  elseif ($info === 27) { $len = unpack('J', substr($s, $pos, 8))[1]; $pos += 8; }
  elseif ($info > 27) fail(422, 'The key\'s answer wasn\'t understood.');
  switch ($major) {
    case 0: return $len;
    case 1: return -1 - $len;
    case 2:
    case 3:
      if ($len > strlen($s) - $pos) fail(422, 'The key\'s answer wasn\'t understood.');
      $v = substr($s, $pos, $len); $pos += $len; return $v;
    case 4:
      if ($len > 1000) fail(422, 'The key\'s answer wasn\'t understood.');
      $a = [];
      for ($i = 0; $i < $len; $i++) $a[] = cbor_decode($s, $pos, $depth + 1);
      return $a;
    case 5:
      if ($len > 1000) fail(422, 'The key\'s answer wasn\'t understood.');
      $m = [];
      for ($i = 0; $i < $len; $i++) {
        $k = cbor_decode($s, $pos, $depth + 1);
        $m[is_int($k) || is_string($k) ? $k : (string)$i] = cbor_decode($s, $pos, $depth + 1);
      }
      return $m;
    case 6: return cbor_decode($s, $pos, $depth + 1);   // tags: ignore
    case 7:
      if ($info === 20) return false;
      if ($info === 21) return true;
      if ($info === 22 || $info === 23) return null;
      return null;   // floats aren't used by WebAuthn
  }
  return null;
}
