<?php require __DIR__."/../page-header.php"; ?>
<main class="league-page standalone-page">
  <?php if (!empty($page["heading"])) { ?>
    <h2 class="page-heading"><?php echo htmlspecialchars($page["heading"], ENT_QUOTES, "UTF-8"); ?></h2>
  <?php } ?>
  <?php require $contentFile; ?>
</main>
<?php require __DIR__."/../page-footer.php"; ?>