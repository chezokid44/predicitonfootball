<?php
function renderPage($layout, $page) {
  $layouts = array(
    "public" => __DIR__."/layouts/public.php",
    "admin" => __DIR__."/layouts/admin.php",
    "standalone" => __DIR__."/layouts/standalone.php",
    "setup" => __DIR__."/layouts/setup.php"
  );
  if (!isset($layouts[$layout]) || !is_array($page) || empty($page["content"])) {
    die("Invalid page template configuration");
  }

  $contentRoot = realpath(__DIR__."/pages");
  $contentFile = realpath($contentRoot.DIRECTORY_SEPARATOR.$page["content"]);
  if ($contentFile === FALSE || strpos($contentFile, $contentRoot.DIRECTORY_SEPARATOR) !== 0) {
    die("Page template not found");
  }

  extract($GLOBALS, EXTR_SKIP);
  if (isset($page["data"]) && is_array($page["data"])) {
    extract($page["data"], EXTR_SKIP);
  }
  $pageTitle = isset($page["title"]) ? $page["title"] : $PredictionLeagueTitle;
  $pageScripts = isset($page["scripts"]) ? $page["scripts"] : array();
  require $layouts[$layout];
}
?>