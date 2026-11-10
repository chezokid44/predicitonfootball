<?php
// The app's page. Each asset's address includes its last-modified time, so
// browsers and Cloudflare fetch new versions as soon as files are uploaded,
// even though the server tells them to cache assets for a long time.
$asset = function (string $file): string {
  $path = __DIR__ . '/' . $file;
  return htmlspecialchars($file . '?v=' . (is_file($path) ? filemtime($path) : '0'), ENT_QUOTES);
};
// The page names asset versions, so it must never be cached by a page cache
// such as CloudPanel's Varnish (browsers may still revalidate it).
if (class_exists('ClpVarnish', false) && method_exists('ClpVarnish', 'setCacheLifetime')) {
  ClpVarnish::setCacheLifetime(0);
}
header('Cache-Control: no-cache, must-revalidate, private');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');
?>
<!doctype html>
<html lang="en-GB">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Prediction League</title>
  <meta name="description" content="Predict the scores, climb the table.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700&family=Barlow:wght@400;500;600&display=swap">
  <link rel="stylesheet" href="<?= $asset('assets/app.css') ?>">
  <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect x='3' y='3' width='26' height='26' fill='%23E9EFE4' stroke='%23C4122F' stroke-width='3'/%3E%3Ctext x='16' y='23' font-family='Arial' font-weight='700' font-size='17' text-anchor='middle' fill='%231C2B4A'%3E2%3C/text%3E%3C/svg%3E">
</head>
<body>
  <a class="skip" href="#main">Skip to content</a>
  <!-- Bot trap: hidden from people and screen readers; well-behaved crawlers skip it (robots.txt). -->
  <a href="hp/" class="trap" rel="nofollow" tabindex="-1" aria-hidden="true">Members</a>
  <header class="masthead">
    <div class="masthead-inner">
      <a class="brand" href="#/" id="brand">Prediction League</a>
      <nav class="nav" id="nav" aria-label="Main"></nav>
    </div>
  </header>

  <main id="main" class="page" tabindex="-1">
    <p class="loading">Loading the league…</p>
  </main>

  <footer class="foot" id="foot"></footer>

  <div class="toast" id="toast" role="status" aria-live="polite" hidden></div>

  <script src="<?= $asset('assets/app.js') ?>" defer></script>
</body>
</html>
