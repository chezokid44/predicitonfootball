<?php
/*
 * Prediction League - sending email.
 * Uses PHP's mail() or a small SMTP client (STARTTLS, SSL or plain, AUTH LOGIN),
 * chosen in Admin > Settings > Email.
 */

declare(strict_types=1);

if (!defined('PL_APP')) {
  http_response_code(403);
  exit;
}

final class MailError extends Exception {}

/** The league's normal email account (Settings > Email). */
function mail_account_main(): array {
  return [
    'from' => setting('MAIL_FROM'), 'from_name' => setting('MAIL_FROM_NAME') ?: (setting('PRED_LEAGUE_TITLE') ?: 'Prediction League'),
    'transport' => setting('MAIL_TRANSPORT') === 'smtp' ? 'smtp' : 'mail',
    'host' => setting('SMTP_HOST'), 'port' => (int)setting('SMTP_PORT') ?: 587, 'secure' => setting('SMTP_SECURE'),
    'user' => setting('SMTP_USER'), 'pass' => setting('SMTP_PASS'),
    'where' => 'Admin > Settings > Email',
  ];
}

function mail_account_ok(array $a): bool {
  if ($a['from'] === '' || !filter_var($a['from'], FILTER_VALIDATE_EMAIL)) return false;
  return $a['transport'] !== 'smtp' || $a['host'] !== '';
}

function mail_configured(): bool {
  return mail_account_ok(mail_account_main());
}

function mail_clean_header(string $v): string {
  return trim(preg_replace('/[\r\n\t]+/', ' ', $v));
}

function mail_encode_header(string $v): string {
  $v = mail_clean_header($v);
  return preg_match('/[^\x20-\x7E]/', $v) ? '=?UTF-8?B?' . base64_encode($v) . '?=' : $v;
}

/** Send a plain-text email, from the league's account or another one. Throws MailError on failure. */
function send_mail(string $to, string $subject, string $text, ?array $account = null): void {
  $a = $account ?? mail_account_main();
  if (!mail_account_ok($a)) {
    throw new MailError("Email isn't set up. An admin can set it up in {$a['where']}.");
  }
  if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
    throw new MailError('That email address isn\'t valid.');
  }
  $from = $a['from'];
  $fromName = $a['from_name'];
  $domain = substr(strrchr($from, '@'), 1) ?: 'localhost';

  $body = quoted_printable_encode(str_replace(["\r\n", "\r"], "\n", $text));
  $body = str_replace(["\r\n", "\n"], ["\n", "\r\n"], $body);
  $headers = [
    'Date' => date(DATE_RFC2822),
    'Message-ID' => '<' . bin2hex(random_bytes(12)) . '@' . $domain . '>',
    'From' => mail_encode_header($fromName) . ' <' . $from . '>',
    'MIME-Version' => '1.0',
    'Content-Type' => 'text/plain; charset=UTF-8',
    'Content-Transfer-Encoding' => 'quoted-printable',
    'X-Mailer' => 'Prediction League',
  ];

  if ($a['transport'] !== 'smtp') {
    $lines = [];
    foreach ($headers as $k => $v) $lines[] = "$k: $v";
    if (!@mail($to, mail_encode_header($subject), $body, implode("\r\n", $lines))) {
      throw new MailError('The server\'s mail function refused the message. Check the server\'s mail setup, or switch to an SMTP server in Settings.');
    }
    return;
  }

  $headers = ['To' => $to, 'Subject' => mail_encode_header($subject)] + $headers;
  $message = '';
  foreach ($headers as $k => $v) $message .= "$k: $v\r\n";
  $message .= "\r\n" . $body;
  smtp_send($from, $to, $message, $a);
}

function smtp_send(string $from, string $to, string $message, array $a): void {
  $host = $a['host'];
  $port = (int)$a['port'] ?: 587;
  $secure = $a['secure'];
  $target = ($secure === 'ssl' ? 'ssl://' : 'tcp://') . $host . ':' . $port;

  $ctx = stream_context_create(['ssl' => ['verify_peer' => true, 'verify_peer_name' => true, 'SNI_enabled' => true]]);
  $fp = @stream_socket_client($target, $errno, $errstr, 10, STREAM_CLIENT_CONNECT, $ctx);
  if (!$fp) {
    throw new MailError("Couldn't connect to the SMTP server $host:$port ($errstr). Check the server name, port and encryption.");
  }
  stream_set_timeout($fp, 15);

  $read = function () use ($fp): array {
    $text = '';
    while (($line = fgets($fp, 1024)) !== false) {
      $text .= $line;
      if (strlen($line) < 4 || $line[3] !== '-') break;
    }
    return [(int)substr($text, 0, 3), trim($text)];
  };
  $cmd = function (string $line, array $ok) use ($fp, $read): string {
    if ($line !== '') fwrite($fp, $line . "\r\n");
    [$code, $text] = $read();
    if (!in_array($code, $ok, true)) {
      $shown = preg_match('/^AUTH|^[A-Za-z0-9+\/=]{8,}$/', $line) ? '(login details)' : $line;
      throw new MailError('The SMTP server said: ' . ($text ?: 'nothing') . ($shown !== '' ? " (after $shown)" : ''));
    }
    return $text;
  };

  try {
    $cmd('', [220]);
    $ehlo = 'EHLO ' . (preg_replace('/[^A-Za-z0-9.\-]/', '', (string)($_SERVER['SERVER_NAME'] ?? '')) ?: 'localhost');
    $caps = $cmd($ehlo, [250]);
    if ($secure === 'tls') {
      if (stripos($caps, 'STARTTLS') === false) {
        throw new MailError('The SMTP server doesn\'t offer TLS on this port. Try port 465 with SSL.');
      }
      $cmd('STARTTLS', [220]);
      if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT)) {
        throw new MailError('Couldn\'t start an encrypted connection with the SMTP server.');
      }
      $caps = $cmd($ehlo, [250]);
    }
    $user = $a['user'];
    if ($user !== '') {
      $cmd('AUTH LOGIN', [334]);
      $cmd(base64_encode($user), [334]);
      $cmd(base64_encode($a['pass']), [235]);
    }
    $cmd('MAIL FROM:<' . $from . '>', [250]);
    $cmd('RCPT TO:<' . $to . '>', [250, 251]);
    $cmd('DATA', [354]);
    // Dot-stuffing: a line starting with "." gets an extra one.
    $data = preg_replace('/^\./m', '..', $message);
    $cmd($data . "\r\n.", [250]);
    $cmd('QUIT', [221, 250]);
  } finally {
    fclose($fp);
  }
}
