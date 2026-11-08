<?php
/*********************************************************
 * Author: John Astill
 * Date  : 2nd July 2003 (c)
 * File  : emptytables.php
 * Desc  : Empty the configured tables. The table
 *       : names are taken from system vars.
 ********************************************************/
require "systemvars.php";

  $url = $HTTP_POST_VARS["URL"];
  $dbname = $HTTP_POST_VARS["DBASENAME"];
  $username = $HTTP_POST_VARS["USERNAME"];
  $password = $HTTP_POST_VARS["PASSWORD"];
  $create = $HTTP_POST_VARS["CREATEDB"];
  $deldbase = $HTTP_POST_VARS["DELDBASE"];
  $quotedDatabase = DBQuoteIdentifier($dbname);
  $tableNames = array($dbaseUserData, $dbasePredictionData, $dbaseMatchData, $dbaseConfigData, $dbaseMsgData, $dbaseStandings);
  foreach ($tableNames as $tableName) {
    DBQuoteIdentifier($tableName);
  }

  // Connect to the host.
  $link = mysql_connect($url, $username, $password)
      or die("Could not connect to $url");

  if ($deldbase == "deldb") {
    $query = "DROP DATABASE $quotedDatabase";
    $userresult = mysql_query($query)
        or die("Query failed: $query<br>\n".mysql_error());
  } else {
    $query = "DROP TABLE IF EXISTS $quotedDatabase.".DBQuoteIdentifier($dbaseUserData);
    $userresult = mysql_query($query)
      or die("Query failed: $query<br>\n".mysql_error());

    $query = "DROP TABLE IF EXISTS $quotedDatabase.".DBQuoteIdentifier($dbasePredictionData);
    $userresult = mysql_query($query)
      or die("Query failed: $query<br>\n".mysql_error());

    $query = "DROP TABLE IF EXISTS $quotedDatabase.".DBQuoteIdentifier($dbaseMatchData);
    $userresult = mysql_query($query)
      or die("Query failed: $query<br>\n".mysql_error());

    $query = "DROP TABLE IF EXISTS $quotedDatabase.".DBQuoteIdentifier($dbaseConfigData);
    $userresult = mysql_query($query)
      or die("Query failed: $query<br>\n".mysql_error());

    $query = "DROP TABLE IF EXISTS $quotedDatabase.".DBQuoteIdentifier($dbaseMsgData);
    $userresult = mysql_query($query)
      or die("Query failed: $query<br>\n".mysql_error());

    $query = "DROP TABLE IF EXISTS $quotedDatabase.".DBQuoteIdentifier($dbaseStandings);
    $userresult = mysql_query($query)
      or die("Query failed: $query<br>\n".mysql_error());
  }

?>
<html>
  <head>
    <title>
      Deleted database and tables <?php echo $dbname; ?>
    </title>
    <link rel="stylesheet" href="common.css" type="text/css">
  </head>

  <body class="MAIN">

<table>
<tr>
<td>
  <?php require "adminsetupmenu.php"; ?>
</td>
<td colspan="1" align="LEFT" valign="TOP" class="TBLROW">
<font class="TBLROW">
<?php if ($deldbase == "deldb") { ?>
The database <?php echo $dbname?> has now been deleted along with all tables.
<?php } else { ?>
The prediction football tables in database <?php echo $dbname?> has now been deleted.
<?php } ?> 
</font>
</td>
</tr>
</table>

  </body>
</html>

