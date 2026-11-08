<?php 
/*********************************************************
 * Author: John Astill (c)
 * Date  : 9th December 2001
 * File  : adminenterfixture.php
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
  <td align="center" class="TBLHEAD"><font class="TBLHEAD">&nbsp;</font></td>
  <td align="center" class="TBLHEAD"><font class="TBLHEAD">&nbsp;</font></td>
<?php
  while ($matchdata = mysql_fetch_array($matchresult,MYSQL_ASSOC)) {
    $matchid = $matchdata["matchid"];
    $matchdate = $matchdata["matchdate"];
    $hometeam = stripslashes($matchdata["hometeam"]);
    $awayteam = stripslashes($matchdata["awayteam"]);
    // Get the date and time in user friendly format.
    $oldserverdate = $matchdate;
    $rawdate = GetRawDateFromDateTime($matchdate);
    $date = GetDateFromDateTime($matchdate);
    $time = GetTimeFromDateTime($matchdate);
    $time24hr = GetTimeFromDateTime24hr($matchdate);
    echo "<tr>\n";
    echo "<td class=\"TBLROW\"><font class=\"TBLROW\">".$date."</font></td>\n";
    echo "<td class=\"TBLROW\"><font class=\"TBLROW\">".$time."</font></td>";
    echo "<td align=\"CENTER\" class=\"TBLROW\"><font class=\"TBLROW\">$hometeam</font></td>\n";
    echo "<td align=\"CENTER\" class=\"TBLROW\"><font class=\"TBLROW\">".$matchdata["homescore"]."</font></td>\n";
    echo "<td align=\"CENTER\" class=\"TBLROW\"><font class=\"TBLROW\">v</font></td>\n";
    echo "<td class=\"TBLROW\"><font class=\"TBLROW\">".$matchdata["awayscore"]."</font></td>\n";
    echo "<td class=\"TBLROW\"><font class=\"TBLROW\">$awayteam</font></td>\n";
    echo "<td class=\"TBLROW\"><font class=\"TBLROW\"><a onMouseOver=\"top.window.status='Modify fixture';return true\" href=\"javascript:void modifyfixture('$matchid','$oldserverdate','$rawdate','$time24hr','".addslashes($hometeam)."','".addslashes($awayteam)."');\">Modify</a></font></td>\n";
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
    <script type="text/javascript" language="javascript">
     /****************************************************************
      * Simple function to replace the contents a the form values.
      * Change the page content to show the modify form.
      * <a href="javascript:void modifyfixture('$date','$time','$hometeam','$awayteam');">Modify</a>
      ****************************************************************/
     function addfixture() {
        var old = document.getElementById("addform");
        old.setAttribute("action","modifyfixture.php?sid=<?php echo $SID?>");
        old.setAttribute("method","POST");

        // Get a reference to the parent
        var bodyRef = document.getElementById("formdate");
        var newInput = document.createElement("div");
        var newText = document.createTextNode('');
        // Get a reference to the block being replaced
        old = document.getElementById("formdatetext");
        newInput.setAttribute("id","formdatetext");
        newInput.appendChild(newText);
        // Replace the piece
        bodyRef.replaceChild(newInput,old);

        bodyRef = document.getElementById("formtime");
        newInput = document.createElement("div");
        newText = document.createTextNode('');
        old = document.getElementById("formtimetext");
        newInput.setAttribute("id","formtimetext");
        newInput.appendChild(newText);
        bodyRef.replaceChild(newInput,old);

        bodyRef = document.getElementById("formhome");
        newInput = document.createElement("div");
        newText = document.createTextNode('');
        old = document.getElementById("formhometext");
        newInput.setAttribute("id","formhometext");
        newInput.appendChild(newText);
        bodyRef.replaceChild(newInput,old);

        bodyRef = document.getElementById("formaway");
        newInput = document.createElement("div");
        newText = document.createTextNode('');
        old = document.getElementById("formawaytext");
        newInput.setAttribute("id","formawaytext");
        newInput.appendChild(newText);
        bodyRef.replaceChild(newInput,old);

        bodyRef = document.getElementById("formsubmit");
        newInput = document.createElement("<input value='Add' name='Add'>");
        old = document.getElementById("formsubmitinput");
        newInput.setAttribute("id","formsubmitinput");
        newInput.setAttribute("name","Add");
        newInput.setAttribute("type","submit");
        bodyRef.replaceChild(newInput,old);
     }

     // Change the display to show the fixture to be modified.
     function modifyfixture(matchid,serverdate,date,time,home,away) {
       document.AddFixture.DATE.value = date;
       document.AddFixture.TIME.value = time;
       document.AddFixture.HOMETEAM.value = home;
       document.AddFixture.AWAYTEAM.value = away;

        // DOM stuff for updating the date
        // Get a reference to the parent
        var old = document.getElementById("addform");
        old.setAttribute("action","modifyfixture.php?sid=<?php echo $SID ?>");

        old = document.getElementById("OLDDATE");
        old.setAttribute("value",serverdate);

        old = document.getElementById("OLDHOME");
        old.setAttribute("value",home);

        old = document.getElementById("OLDAWAY");
        old.setAttribute("value",away);

        old = document.getElementById("MATCHID");
        old.setAttribute("value",matchid);

        var bodyRef = document.getElementById("formdate");
        var newInput = document.createElement("div");
        var newText = document.createTextNode(date);
        old = document.getElementById("formdatetext");
        newInput.setAttribute("id","formdatetext");
        newInput.appendChild(newText);
        bodyRef.replaceChild(newInput,old);

        bodyRef = document.getElementById("formtime");
        newInput = document.createElement("div");
        newText = document.createTextNode(time);
        old = document.getElementById("formtimetext");
        newInput.setAttribute("id","formtimetext");
        newInput.appendChild(newText);
        bodyRef.replaceChild(newInput,old);

        bodyRef = document.getElementById("formhome");
        newInput = document.createElement("div");
        newText = document.createTextNode(home);
        old = document.getElementById("formhometext");
        newInput.setAttribute("id","formhometext");
        newInput.appendChild(newText);
        bodyRef.replaceChild(newInput,old);

        bodyRef = document.getElementById("formaway");
        newInput = document.createElement("div");
        newText = document.createTextNode(away);
        old = document.getElementById("formawaytext");
        newInput.setAttribute("id","formawaytext");
        newInput.appendChild(newText);
        bodyRef.replaceChild(newInput,old);

        bodyRef = document.getElementById("formsubmit");
        newInput = document.createElement("<input name='Modify' value='Modify'>");
        old = document.getElementById("formsubmitinput");
        newInput.setAttribute("id","formsubmitinput");
        newInput.setAttribute("type","submit");
        bodyRef.replaceChild(newInput,old);

     }
    </script>
</head>

<body class="MAIN">

<?php echo $HeaderRow ?>
<table class="MAINTB">

  <?php if ($ErrorCode != "") { ?>
      <tr>
        <td colspan="3" bgcolor="red" align="center">
          <!-- Error Message Row -->
          <font class="TBLHEAD">
          <?php 
            echo $ErrorCode; 
          ?>
          </font>
        </td>
      </tr>
  <?php 
    }
  ?>

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
  <form id="addform" name="AddFixture" method="POST" action="addfixture.php?sid=<?php echo $SID; ?>">
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
  <td align="CENTER" class="TBLHEAD">
  <font class="TBLHEAD">
  <input id="formresetinput" onclick="addfixture()" type="reset" name="RESET" value="Reset">
  </font>
  </td>
  </tr>
  <!-- Content Row -->
  <tr>
  <td class="TBLROW" align="CENTER">
  <font id="formdate" class="TBLROW">
  <div id="formdatetext"></div>
  <input type="text" name="DATE" size="10">
  <input id="OLDDATE" type="hidden" name="OLDDATE" value="">
  </font>
  </td>
  <td class="TBLROW" align="CENTER">
  <font id="formtime" class="TBLROW">
  <div id="formtimetext"></div>
  <input type="text" name="TIME" size="8">
  </font>
  </td>
  <td class="TBLROW" align="CENTER">
  <font id="formhome" class="TBLROW">
  <div id="formhometext"></div>
  <input type="text" name="HOMETEAM" size="15">
  <input id="MATCHID" type="hidden" name="MATCHID" value="<?php echo $matchid ?>">
  <input id="OLDHOME" type="hidden" name="OLDHOME" value="">
  </font>
  </td>
  <td class="TBLROW" align="CENTER">
  <font id="formaway" class="TBLROW">
  <div id="formawaytext"></div>
  <input type="text" name="AWAYTEAM" size="15">
  <input id="OLDAWAY" type="hidden" name="OLDAWAY" value="">
  </font>
  </td>
  <td id="formsubmit" align="center" class="TBLROW">
  <input id="formsubmitinput" type="submit" name="ADD" value="Add">
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
