<?php require __DIR__."/../page-header.php"; ?>
<main class="league-page">
  <?php if (!empty($page["notice"])) { ?>
    <div class="notice" role="alert"><?php echo htmlspecialchars($page["notice"], ENT_QUOTES, "UTF-8"); ?></div>
  <?php } ?>
  <?php if (!isset($page["show_fixture"]) || $page["show_fixture"]) { ?>
    <section class="matchday-strip" aria-label="Next fixture">
      <?php echo getNextGame(); ?>
    </section>
  <?php } ?>
  <div class="league-grid">
    <aside class="league-sidebar" aria-label="League navigation">
      <?php if (!empty($page["login_panel"])) require "loginpanel.php"; ?>
      <?php if (!empty($page["setup_menu"])) { ?>
        <?php require "adminsetupmenu.php"; ?>
      <?php } else { ?>
        <?php require "menus.php"; ?>
      <?php } ?>
    </aside>
    <section class="standings-region" aria-label="<?php echo htmlspecialchars(isset($page["heading"]) ? $page["heading"] : $pageTitle, ENT_QUOTES, "UTF-8"); ?>">
      <?php if (!empty($page["heading"])) { ?>
        <h2 class="page-heading"><?php echo htmlspecialchars($page["heading"], ENT_QUOTES, "UTF-8"); ?></h2>
      <?php } ?>
      <?php require $contentFile; ?>
    </section>
    <?php if (!isset($page["right_rail"]) || $page["right_rail"]) { ?>
      <aside class="stats-region" aria-label="Match statistics">
        <?php if (!isset($page["show_stats"]) || $page["show_stats"]) ShowPredictionStatsForNextMatch(); ?>
        <?php if (!isset($page["show_prize"]) || $page["show_prize"]) require "prize.html"; ?>
      </aside>
    <?php } ?>
  </div>
</main>
<?php require __DIR__."/../page-footer.php"; ?>