<form method="POST" action="importdbase.php">
  <table class="STANDTB">
    <tr><td class="TBLROW" colspan="2">Import data from a version 0.43-0.50 database. The source database and table names must be distinct from the destination tables.</td></tr>
    <tr><td class="TBLHEAD">Database Host</td><td><input type="text" name="URL" value="localhost"></td></tr>
    <tr><td class="TBLHEAD">Username</td><td><input type="text" name="USERNAME"></td></tr>
    <tr><td class="TBLHEAD">Password</td><td><input type="password" name="PASSWORD"></td></tr>
    <tr><td class="TBLHEAD">Database Name</td><td><input type="text" name="DBASENAME" value="PredictionLeague"></td></tr>
    <tr><td class="TBLHEAD">User Data Table</td><td><input type="text" name="USERDATATBL" value="UserData"></td></tr>
    <tr><td class="TBLHEAD">Match Data Table</td><td><input type="text" name="MATCHDATATBL" value="MatchData"></td></tr>
    <tr><td class="TBLHEAD">Prediction Data Table</td><td><input type="text" name="PREDDATATBL" value="PredictionData"></td></tr>
    <tr><td colspan="2"><button type="submit" name="IMPORT" value="IMPORT">Import</button></td></tr>
  </table>
</form>
