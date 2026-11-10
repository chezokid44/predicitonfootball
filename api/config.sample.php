<?php
if (!defined('PL_APP')) { http_response_code(403); exit; }
/*
 * Prediction League API configuration.
 * Copy this file to config.php and fill in your database details
 * (the same ones as $dbaseHost / $dbaseName / $dbaseUsername /
 * $dbasePassword in the old systemvars.php).
 */
return [
  'db' => [
    'host'     => 'localhost',
    'name'     => 'your_database_name',
    'user'     => 'your_database_user',
    'pass'     => 'your_database_password',
    // Optional: full PDO DSN, overrides host/name when set.
    'dsn'      => null,
  ],

  // Table names: these match the defaults in the old systemvars.php.
  'tables' => [
    'config'      => 'plconfigdata',
    'users'       => 'pluserdata',
    'predictions' => 'plpredictiondata',
    'matches'     => 'plmatchdata',
    'standings'   => 'plstandings',
  ],

  // The league ID used by the old script ($leagueID in systemvars.php).
  'league_id' => 0,

  // Time zone that kick-off times are entered and shown in.
  'timezone' => 'Europe/London',

  // The site's address, used in links in password reset emails.
  'site_url' => 'https://pl.example.co.uk/',

  // Where to find visitors' real IP address when the site is behind a proxy.
  // Cloudflare: 'HTTP_CF_CONNECTING_IP'. Leave null otherwise.
  'client_ip_header' => null,

  // Addresses that are never blocked by the bot traps (for example your own).
  'never_block_ips' => [],

  // When no admin user exists yet, the next person to register becomes
  // admin. Leave false unless you have no other way to create an admin.
  'first_user_becomes_admin' => false,

  // Reject API requests that don't come from this site's own pages
  // (typed into the address bar, links, other websites).
  'strict_client_check' => true,

  // Show detailed error messages in API responses. Keep false in production.
  'debug' => false,
];
