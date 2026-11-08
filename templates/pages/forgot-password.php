<form method="POST" action="sendpassword.php">
  <table class="HELPTB">
    <tr><td class="TBLHEAD" colspan="2"><font class="TBLHEAD">Password Reminder</font></td></tr>
    <tr>
      <td class="TBLHEAD"><font class="TBLHEAD">User ID</font></td>
      <td class="TBLROW"><input type="text" size="20" name="USERID" value="<?php echo htmlspecialchars($User->userid, ENT_QUOTES, "UTF-8"); ?>"></td>
    </tr>
    <tr><td class="TBLROW" colspan="2">Enter your user ID and request a password email.</td></tr>
    <tr><td class="TBLROW" colspan="2"><input type="submit" name="Send" value="Send"></td></tr>
  </table>
</form>