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
  $quotedDatabase = DBQuoteIdentifier($dbname);
  $quotedUserTable = DBQuoteIdentifier($dbaseUserData);
  $quotedPredictionTable = DBQuoteIdentifier($dbasePredictionData);
  $quotedMatchTable = DBQuoteIdentifier($dbaseMatchData);
  $quotedStandingsTable = DBQuoteIdentifier($dbaseStandings);
  $quotedMessageTable = DBQuoteIdentifier($dbaseMsgData);
  $userDataTblName = $dbaseUserData;
  $predDataTblName = $dbasePredictionData;
  $matchDataTblName = $dbaseMatchData;
  $standingsTblName = $dbaseStandings;

  // Connect to the host.
  $link = mysql_connect($url, $username, $password)
      or die("Could not connect to $url");

    $query = "DELETE FROM $quotedDatabase.$quotedUserTable WHERE lid=?";
    if (DBExecute($query, array((int)$leagueID), $link) === FALSE) die("Unable to clear users: ".DBError($link));

    $query = "DELETE FROM $quotedDatabase.$quotedPredictionTable WHERE lid=?";
    if (DBExecute($query, array((int)$leagueID), $link) === FALSE) die("Unable to clear predictions: ".DBError($link));

    $query = "DELETE FROM $quotedDatabase.$quotedMatchTable WHERE lid=?";
    if (DBExecute($query, array((int)$leagueID), $link) === FALSE) die("Unable to clear matches: ".DBError($link));

    $query = "DELETE FROM $quotedDatabase.$quotedStandingsTable WHERE lid=?";
    if (DBExecute($query, array((int)$leagueID), $link) === FALSE) die("Unable to clear standings: ".DBError($link));

    $query = "DELETE FROM $quotedDatabase.$quotedMessageTable WHERE lid=?";
    if (DBExecute($query, array((int)$leagueID), $link) === FALSE) die("Unable to clear messages: ".DBError($link));

?>
<html>
  <head>
    <title>
      Emptied database tables <?php echo $dbname; ?>
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
All the tables except the config table has been emptied.<br>You will need to recreate your admin user to be able to login.
</font>
</td>
</tr>
</table>

  </body>
</html>

