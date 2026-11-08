<?php 
/*********************************************************
 * Author: John Astill (c)
 * Date  : 1st August 2003
 * File  : adminentermultfixtures.php
 * This page allows an administrator to add
 * extra fixtures to the prediction league.
 * The current contents of the fixture list will
 * also be displayed.
 ********************************************************/
require "systemvars.php";
require "configvalues.php";
require "sortfunctions.php";
require "security.php";

/*******************************************************
* Function to create an indexed array from the database
* table holding the fixtures.
*******************************************************/
function GetCurrentFixtures() {
  // Array holding the current fixtures.
  global $dbaseMatchData, $leagueID, $SID;

  $link = OpenConnection();
  if ($link == FALSE) {
    echo "Unable to connect to the dbase ";
    return;
  }
   
  $matchquery = "SELECT * FROM $dbaseMatchData where lid='$leagueID' order by matchdate";
  $matchresult = mysql_query($matchquery)
     or die("Query failed: $matchquery");
?>
  <table class="STANDTB">
  <tr>
  <td align="center" class="TBLHEAD" colspan="9"><font class="TBLHEAD">Current Fixtures</font></td>
  </tr>
  <tr>
  <td align="center" class="TBLHEAD"><font class="TBLHEAD">Date</font></td>
  <td align="center" class="TBLHEAD"><font class="TBLHEAD">Time</font></td>
  <td align="center" class="TBLHEAD"><font class="TBLHEAD">Home</font></td>
  <td align="center" class="TBLHEAD"><font class="TBLHEAD">&nbsp;</font></td>
  <td align="center" class="TBLHEAD"><font class="TBLHEAD">&nbsp;</font></td>
  <td align="center" class="TBLHEAD"><font class="TBLHEAD">&nbsp;</font></td>
  <td align="center" class="TBLHEAD"><font class="TBLHEAD">Away</font></td>
  <!--<td align="center" class="TBLHEAD"><font class="TBLHEAD">&nbsp;</font></td>-->
  <td align="center" class="TBLHEAD"><font class="TBLHEAD">&nbsp;</font></td>
<?php
  while ($matchdata = mysql_fetch_array($matchresult,MYSQL_ASSOC)) {
    $matchid = $matchdata["matchid"];
    $matchdate = $matchdata["matchdate"];
    $hometeam = $matchdata["hometeam"];
    $awayteam = $matchdata["awayteam"];
    // Get the date and time in user friendly format.
    $date = GetDateFromDateTime($matchdate);
    $time = GetTimeFromDateTime($matchdate);
    echo "<tr>\n";
    echo "<td class=\"TBLROW\"><font class=\"TBLROW\">".$date."</font></td>\n";
    echo "<td class=\"TBLROW\"><font class=\"TBLROW\">".$time."</font></td>";
    echo "<td align=\"CENTER\" class=\"TBLROW\"><font class=\"TBLROW\">$hometeam</font></td>\n";
    echo "<td align=\"CENTER\" class=\"TBLROW\"><font class=\"TBLROW\">".$matchdata["homescore"]."</font></td>\n";
    echo "<td align=\"CENTER\" class=\"TBLROW\"><font class=\"TBLROW\">v</font></td>\n";
    echo "<td class=\"TBLROW\"><font class=\"TBLROW\">".$matchdata["awayscore"]."</font></td>\n";
    echo "<td class=\"TBLROW\"><font class=\"TBLROW\">$awayteam</font></td>\n";
    echo "<td class=\"TBLROW\"><font class=\"TBLROW\"><a onMouseOver=\"top.window.status='Delete fixture';return true\" href=\"deletefixture.php?sid=$SID&matchid=$matchid\" onclick=\"return confirm('Are you sure you want to delete this fixture and all related predictions?');\">Delete</a></font></td>\n";
    echo "</tr>\n";
  }
  echo "</table>\n";

  CloseConnection($link);
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset?>">
<title>
Fixture Administration
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
<td id="forms" valign="top">
  <form id="addform" name="AddFixture" method="POST" action="addmultfixtures.php">
  <table class="STANDTB">
  <tr>
  <td colspan="5" align="CENTER" class="TBLHEAD">
  <font class="TBLHEAD">
  Fixture Administration
  </font>
  </td>
  </tr>

  <tr>
  <td align="CENTER" valign="TOP" class="TBLHEAD">
  <font class="TBLHEAD">
  </font>
  </td>
  <td align="CENTER" valign="TOP" class="TBLHEAD">
  <font class="TBLHEAD">
  Date<br><small>[YYYY-MM-DD]</small>
  </font>
  </td>
  <td align="CENTER" valign="TOP" class="TBLHEAD">
  <font class="TBLHEAD">
  Time <small>(24Hr)</small><br><small>[HH:MM:SS]</small>
  </font>
  </td>
  <td align="CENTER" valign="TOP" class="TBLHEAD">
  <font class="TBLHEAD">
  Home Team
  </font>
  </td>
  <td align="CENTER" valign="TOP" class="TBLHEAD">
  <font class="TBLHEAD">
  Away Team
  </font>
  </td>
  </tr>
  <!-- Content Rows -->
<?php 
  for ($i=0; $i<$NumMultFixts; $i++) {
?>
  <tr>
  <td class="TBLROW" align="CENTER">
  <font id="formcount" class="TBLROW">
  <?php echo $i+1; ?>
  </font>
  </td>
  <td class="TBLROW" align="CENTER">
  <font id="formdate" class="TBLROW">
  <div id="formdatetext"></div>
  <input type="text" name="DATE<?php echo $i; ?>" size="10">
  </font>
  </td>
  <td class="TBLROW" align="CENTER">
  <font id="formtime" class="TBLROW">
  <div id="formtimetext"></div>
  <input type="text" name="TIME<?php echo $i; ?>" size="8">
  </font>
  </td>
  <td class="TBLROW" align="CENTER">
  <font id="formhome" class="TBLROW">
  <div id="formhometext"></div>
  <input type="text" name="HOMETEAM<?php echo $i; ?>" size="15">
  </font>
  </td>
  <td class="TBLROW" align="CENTER">
  <font id="formaway" class="TBLROW">
  <div id="formawaytext"></div>
  <input type="text" name="AWAYTEAM<?php echo $i; ?>" size="15">
  </font>
  </td>
</tr>
<?php
  }
?>
<tr>
  <td id="formsubmit" align="center" class="TBLROW" colspan="5">
  <input id="formsubmitinput" type="submit" name="ADD" value="Add">
  <input id="formresetinput" onclick="addfixture()" type="reset" name="RESET" value="Reset">
  </td>
  </tr>
  </table>
  </form>
  <?php GetCurrentFixtures(); ?>
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
