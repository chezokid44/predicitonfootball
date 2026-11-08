<?php
$pageTitle = isset($pageTitle) ? $pageTitle : $PredictionLeagueTitle;
?>
<!doctype html>
<html>
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, "UTF-8"); ?></title>
    <link rel="stylesheet" href="common.css" type="text/css">
    <?php
    if (isset($pageScripts) && is_array($pageScripts)) {
      foreach ($pageScripts as $pageScript) {
        echo '<script src="'.htmlspecialchars($pageScript, ENT_QUOTES, "UTF-8").'" defer></script>';
      }
    }
    ?>
  </head>
  <body class="MAIN">
    <header class="league-header">
      <div class="league-brand">
        <span class="league-mark" aria-hidden="true">PL</span>
        <h1><?php echo htmlspecialchars($PredictionLeagueTitle, ENT_QUOTES, "UTF-8"); ?></h1>
      </div>
      <div class="league-id">LEAGUE <?php echo htmlspecialchars($leagueID, ENT_QUOTES, "UTF-8"); ?></div>
    </header>
    <?php if (!empty($HeaderRow)) { ?>
      <div class="custom-banner"><?php echo $HeaderRow; ?></div>
    <?php } ?>