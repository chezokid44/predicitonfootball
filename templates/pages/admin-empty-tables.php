<form method="POST" action="emptytables.php">
  <table class="STANDTB">
    <tr><td class="TBLHEAD">Database Host</td><td><input type="text" name="URL" value=""></td></tr>
    <tr><td class="TBLHEAD">Username</td><td><input type="text" name="USERNAME" value=""></td></tr>
    <tr><td class="TBLHEAD">Password</td><td><input type="password" name="PASSWORD" value=""></td></tr>
    <tr><td class="TBLHEAD">Database Name</td><td><input type="text" name="DBASENAME" value=""></td></tr>
    <tr><td class="TBLROW" colspan="2"><strong>This empties the configured league tables and permanently removes their data.</strong></td></tr>
    <tr><td colspan="2"><button type="submit" name="EMPTY" value="Empty">Empty</button></td></tr>
  </table>
</form>
