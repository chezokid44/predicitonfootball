<form method="POST" action="deletetables.php">
  <table class="STANDTB">
    <tr><td class="TBLHEAD">Database Host</td><td><input type="text" name="URL" value=""></td></tr>
    <tr><td class="TBLHEAD">Username</td><td><input type="text" name="USERNAME" value=""></td></tr>
    <tr><td class="TBLHEAD">Password</td><td><input type="password" name="PASSWORD" value=""></td></tr>
    <tr><td class="TBLHEAD">Database Name</td><td><input type="text" name="DBASENAME" value=""></td></tr>
    <tr><td class="TBLHEAD">Delete Database</td><td><input type="checkbox" name="DELDBASE" value="deldb"></td></tr>
    <tr><td class="TBLROW" colspan="2"><strong>This permanently deletes the database and all its data. Make sure this is intentional.</strong></td></tr>
    <tr><td colspan="2"><button type="submit" name="DELETE" value="Delete">Delete</button></td></tr>
  </table>
</form>
