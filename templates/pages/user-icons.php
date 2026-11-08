<h2 class="page-heading"><?php echo htmlspecialchars($Profile_Icon_Selection, ENT_QUOTES, "UTF-8"); ?></h2>
<div class="icon-grid">
  <?php
  $iconFiles = glob("icons/*");
  if ($iconFiles === FALSE) {
    echo '<p class="notice">Unable to load icons.</p>';
  } else {
    foreach ($iconFiles as $iconPath) {
      if (!is_file($iconPath) || !preg_match('/\.(gif|jpe?g|png|webp)$/i', $iconPath)) {
        continue;
      }
      $iconName = basename($iconPath);
      $iconUrl = "icons/".rawurlencode($iconName);
      $selectUrl = "selecticon.php?sid=".rawurlencode($SID)."&icon=".rawurlencode($iconName);
      echo '<a class="icon-choice" href="'.htmlspecialchars($selectUrl, ENT_QUOTES, "UTF-8").'">';
      echo '<img src="'.htmlspecialchars($iconUrl, ENT_QUOTES, "UTF-8").'" alt="'.htmlspecialchars($iconName, ENT_QUOTES, "UTF-8").'">';
      echo '</a>';
    }
  }
  ?>
</div>