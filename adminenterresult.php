<?php 
/*********************************************************
 * Author: John Astill
 * Date  : 9th December
 * File  : adminenterresult.php
 * This page allows an administrator to enter a
 * result.
 ********************************************************/
require "systemvars.php";
require "configvalues.php";
require "sortfunctions.php";
require "security.php";

$reverse = 0;
if (array_key_exists("reverse",$_GET)) {
  $reverse = $_GET["reverse"];
}

/*******************************************************
* Function to create an indexed array from the database
* table holding the fixtures.
*******************************************************/
function GetCurrentFixtures($reverse) {
  // Array holding the current fixtures.
  global $dbaseMatchData, $leagueID, $SID;

  $link = OpenConnection();
  if ($link == FALSE) {
    echo "Unable to connect to the dbase ";
    return;
  }
   
  if ($reverse == 1) {
    $reverse = 0;
    $matchquery = "SELECT * FROM $dbaseMatchData where lid='$leagueID' order by matchdate";
  } else {
    $reverse = 1;
    $matchquery = "SELECT * FROM $dbaseMatchData where lid='$leagueID' order by matchdate desc";
  }
  $matchresult = mysql_query($matchquery)
     or die("Query failed: $matchquery");
?>
  <table class="STANDTB">
  <tr>
  <td align="center" class="TBLHEAD" colspan="6"><font class="TBLHEAD">Current Fixtures</font></td>
  </tr>
  <tr>
  <td align="center" class="TBLHEAD"><font class="TBLHEAD">Date [<a href="adminenterresult.php?sid=<?php echo $SID;?>&reverse=<?php echo $reverse;?>">reverse</a>]</font></td>
  <td align="center" class="TBLHEAD"><font class="TBLHEAD">Home</font></td>
  <td align="center" class="TBLHEAD"><font class="TBLHEAD"></font></td>
  <td align="center" class="TBLHEAD"><font class="TBLHEAD"></font></td>
  <td align="center" class="TBLHEAD"><font class="TBLHEAD"></font></td>
  <td align="center" class="TBLHEAD"><font class="TBLHEAD">Away</font></td>
<?php
  while ($matchdata = mysql_fetch_array($matchresult,MYSQL_ASSOC)) {
    $mid = $matchdata["matchid"];
    $matchdate = $matchdata["matchdate"];
    $hometeam = $matchdata["hometeam"];
    $awayteam = $matchdata["awayteam"];
    echo "<tr>";
    echo "<td class=\"TBLROW\"><font class=\"TBLROW\">";
    echo "<a href=\"adminpostresult.php?sid=$SID&mid=$mid&matchdate=$matchdate&hometeam=".EncodeParam($hometeam)."&awayteam=".EncodeParam($awayteam)."\">";
    echo convertDatetimeToScreenDate($matchdate);
    echo "</a></font></td>";
    echo "<td align=\"CENTER\" class=\"TBLROW\"><font class=\"TBLROW\">".$hometeam."</font></td>";
    echo "<td align=\"CENTER\" class=\"TBLROW\"><font class=\"TBLROW\">".$matchdata["homescore"]."</font></td>";
    echo "<td align=\"CENTER\" class=\"TBLROW\"><font class=\"TBLROW\">v</font></td>";
    echo "<td class=\"TBLROW\"><font class=\"TBLROW\">".$matchdata["awayscore"]."</font></td>";
    echo "<td class=\"TBLROW\"><font class=\"TBLROW\">".$awayteam."</font></td>";
    echo "</tr>";
  }
  echo "</table>";

  CloseConnection($link);
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset?>">
<title>
Result Administration
</title>
<link rel="stylesheet" href="common.css" type="text/css">
</head>

<body class="MAIN">

<?php echo $HeaderRow ?>
<table class="MAINTB">
      <!-- Display the next game -->
      <tr>
        <td colspan="3" align="center" class="TBLROW">
          <font class="TBLROW">
            <?php echo getNextGame() ?>
          </font>
        </td>
      </tr>
<tr>
<td valign="top">
<?php 
  require "menus.php";
?>
</td>
<td valign="TOP">
  <?php GetCurrentFixtures($reverse); ?>
</td>
        <!-- Right Column -->
        <td class="RIGHTCOL" align="RIGHT">
          <!-- Show the login panel if required -->
          <?php require "loginpanel.php" ?>

          <!-- Show the Prediction stats for the next match -->
          <?php ShowPredictionStatsForNextMatch(); ?>
          
          <!-- Competition Prize -->
          <?php require "prize.html"?>
</td>
</tr>
</table>
</body>
</html>
