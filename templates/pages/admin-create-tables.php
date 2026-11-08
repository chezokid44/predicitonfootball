<form method="POST" action="createdbase.php">
  <table class="STANDTB">
    <tr><td class="TBLHEAD">Database Host</td><td><input type="text" name="URL" value="localhost"></td></tr>
    <tr><td class="TBLHEAD">Username</td><td><input type="text" name="USERNAME" value=""></td></tr>
    <tr><td class="TBLHEAD">Password</td><td><input type="password" name="PASSWORD" value=""></td></tr>
    <tr><td class="TBLHEAD">Create Tables</td><td><input type="checkbox" name="CREATETABLES" value="TRUE" checked></td></tr>
    <tr><td class="TBLHEAD">Create Database</td><td><input type="checkbox" name="CREATEDB" value="TRUE" checked></td></tr>
    <tr><td class="TBLHEAD">Database Name</td><td><input type="text" name="DBASENAME" value="<?php echo htmlspecialchars($dbaseName, ENT_QUOTES, "UTF-8"); ?>"></td></tr>
    <tr><td colspan="2"><button type="submit" name="CREATE" value="CREATE">Create</button></td></tr>
  </table>
</form>
