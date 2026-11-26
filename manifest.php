<?php
// Web app manifest: lets phones add the league to the home screen as an app,
// with the league's own name. See README > Phones and tablets.
declare(strict_types=1);
define('PL_APP', true);
$name = 'Prediction League';
try {
  require __DIR__ . '/api/lib.php';
  $t = trim((string)setting('PRED_LEAGUE_TITLE'));
  if ($t !== '') $name = $t;
} catch (Throwable $e) {
  // Not installed yet: use the default name.
}
$short = mb_strlen($name) <= 14 ? $name : 'Predictions';
header('Content-Type: application/manifest+json; charset=utf-8');
header('Cache-Control: public, max-age=3600');
echo json_encode([
  'name' => $name,
  'short_name' => $short,
  'description' => 'Predict the scores, climb the table.',
  'start_url' => './#/',
  'scope' => './',
  'display' => 'standalone',
  'orientation' => 'any',
  'background_color' => '#E9EFE4',
  'theme_color' => '#E9EFE4',
  'icons' => [
    ['src' => 'assets/icons/icon-192.png', 'sizes' => '192x192', 'type' => 'image/png'],
    ['src' => 'assets/icons/icon-512.png', 'sizes' => '512x512', 'type' => 'image/png'],
    ['src' => 'assets/icons/icon-maskable-512.png', 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'maskable'],
  ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
