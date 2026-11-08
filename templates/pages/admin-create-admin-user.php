<?php if ($numAdminUsers >= $maxAdminUsers) { ?>
  <p>The maximum number of admin users for league ID <?php echo htmlspecialchars($leagueID, ENT_QUOTES, "UTF-8"); ?> has been created.</p>
  <?php if ($numAdminUsers > $maxAdminUsers) { ?>
    <p class="notice">There are <?php echo (int)$numAdminUsers; ?> admin users, above the configured limit of <?php echo (int)$maxAdminUsers; ?>.</p>
  <?php } ?>
<?php } else { ?>
  <form method="POST" action="createadminuser.php" data-admin-user-form>
    <table class="STANDTB">
      <tr><td class="TBLHEAD" colspan="2">Admin User Administration [LeagueID <?php echo htmlspecialchars($leagueID, ENT_QUOTES, "UTF-8"); ?>]</td></tr>
      <tr><td class="TBLROW">Admin user name</td><td><input type="text" name="USER" maxlength="31" required></td></tr>
      <tr><td class="TBLROW">Admin email address</td><td><input type="email" name="EMAIL" maxlength="60"></td></tr>
      <tr><td class="TBLROW">Password</td><td><input type="password" name="PASSWORD" maxlength="31" required></td></tr>
      <tr><td class="TBLROW">Password again</td><td><input type="password" name="PASSWORD2" maxlength="31" required></td></tr>
      <tr><td colspan="2"><button type="submit" name="CREATE" value="<?php echo htmlspecialchars($Create, ENT_QUOTES, "UTF-8"); ?>"><?php echo htmlspecialchars($Create, ENT_QUOTES, "UTF-8"); ?></button></td></tr>
    </table>
  </form>
<?php } ?>
