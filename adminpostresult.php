<?php 
/*********************************************************
 * Author: John Astill
 * Date  : 9th December
 * File  : adminpostresult.php
 * Desc  : The selected fixture is received in the GET
 *       : parameters. Show a form to the administrator.
 *       : When select is hit, update the database.
 * TODO  : Add javascript to validate the input. i.e. the
 *       : scores must be numeric values.
 ********************************************************/
require "systemvars.php";
require "configvalues.php";
require "sortfunctions.php";
require "security.php";
?>
<html>
<head>
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
  <form method="POST" action="postresult.php<?php echo "?SID=$SID";?>">
  <table class="CENTER">
  <tr>
  <td colspan="7" align="CENTER" class="TBLHEAD">
  <font class="TBLHEAD">
  Enter Result
  </font>
  </td>
  </tr>

  <tr>
  <td align="CENTER" class="TBLHEAD">
  <font class="TBLHEAD">
  Date
  </font>
  </td>

  <td align="CENTER" class="TBLHEAD">
  <font class="TBLHEAD">
  Home Team
  </font>
  </td>
  <td align="CENTER" class="TBLHEAD">
  <font class="TBLHEAD">
  <!-- None -->
  &nbsp;
  </font>
  </td>

  <td align="CENTER" class="TBLHEAD">
  <font class="TBLHEAD">
  <!-- None -->
  &nbsp;
  </font>
  </td>

  <td align="CENTER" class="TBLHEAD">
  <font class="TBLHEAD">
  <!-- None -->
  &nbsp;
  </font>
  </td>

  <td align="CENTER" class="TBLHEAD">
  <font class="TBLHEAD">
  Away Team
  </font>
  </td>
  <td align="CENTER" class="TBLHEAD">
  <font class="TBLHEAD">
  <!-- None -->
  &nbsp;
  </font>
  </td>
  </tr>

  <tr>
  <td class="TBLROW" align="CENTER">
  <font class="TBLROW">
  <?php
    $date = $HTTP_GET_VARS["matchdate"];
    echo GetDateFromDatetime($date)." ".GetTimeFromDatetime($date);
  ?>
  <input type="hidden" size="2" name="MID" value="<?php echo $HTTP_GET_VARS["mid"] ?>">
  </font>
  </td>
  <td class="TBLROW" align="CENTER">
  <font class="TBLROW">
  <?php echo $HTTP_GET_VARS["hometeam"]; ?>
  </font>
  </td>
  <td class="TBLROW" align="CENTER">
  <font class="TBLROW">
  <input type="text" size="2" name="HOMESCORE">
  </font>
  </td>
  <td class="TBLROW" align="CENTER">
  <font class="TBLROW">
  v
  </font>
  </td>
  <td class="TBLROW" align="CENTER">
  <font class="TBLROW">
  <input type="text" size="2" name="AWAYSCORE">
  </font>
  </td>
  <td class="TBLROW" align="CENTER">
  <font class="TBLROW">
  <?php echo $HTTP_GET_VARS["awayteam"]; ?>
  </font>
  </td>
  <td align="center" class="TBLROW">
  <font class="TBLROW">
  <input type="submit" name="POST" value="Post">
  </font>
  </td>
  </tr>
  </table>
  </form>
</td>
<td valign="TOP">
  <!-- Show the Prediction stats for the next match -->
  <?php ShowPredictionStatsForNextMatch(); ?>
  
  <!-- Competition Prize -->
  <?php require "prize.html"?>
</td>
</tr>
</table>
</body>
</html>
