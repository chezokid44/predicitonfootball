<?php
if ($logfile == "") {
  echo "<p>Logfile is not configured.</p>";
} else {
  $logContents = @file_get_contents($logfile);
  if ($logContents === FALSE) {
    echo "<p>Unable to read the configured logfile.</p>";
  } else {
    echo "<pre class=\"log-output\">".htmlspecialchars($logContents, ENT_QUOTES, "UTF-8")."</pre>";
  }
}
?>