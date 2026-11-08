<?php 
/*********************************************************
 * Author: John Astill (c)
 * Date  : 17th August 2002
 * File  : emailallusers.php
 * Desc  : Email all the users. 
 *       : Create a form that mails to the users.
 ********************************************************/
require "systemvars.php";
require "configvalues.php";
require "sortfunctions.php";
require "security.php";

if ($User->loggedIn != TRUE) {
  $ip = $_SERVER["REMOTE_ADDR"];
  $port = $_SERVER["REMOTE_PORT"];
  $referer = $_SERVER["HTTP_REFERER"];

  logMsg("Attempt to email all users by someone not logged in. Could be an attempt to break in. IP Address $ip port $port referring page $referer");
  forward("index.php"); 
  exit;   
}

// Make sure the user trying to send the email is 
// an admin or above.
if (!CheckAdmin($User->usertype)) {
  // Forward to somewhere
  LogMsg("Attempt to send email by User $User->userid with insufficient permissions from $REMOTE_ADDR:$REMOTE_PORT.");
  forward("index.php");
}
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset?>">
<title>
Email Users
</title>
<link rel="stylesheet" href="common.css" type="text/css">
</head>

<body class="MAIN">

<table width="800">
<tr>
  <td colspan="3" align="center">
    <!-- Header Row -->
    <?php echo $HeaderRow ?>
  </td>
</tr>
<!-- Display the next game -->
<tr>
  <td colspan="3" align="center" class="TBLROW">
    <font class="TBLROW">
      <?php echo getNextGame() ?>
    </font>
  </td>
</tr>
<tr>
<td class="LEFTCOL">
<?php require "menus.php"?>
</td>
<td class="CENTERCOL">
<font class="TBLROW">
<form method="POST" action="sendemail.php">
  Subject: <input type="TEXT" name="SUBJECT" size="40"><br>
  <textarea name="BODY" cols="40" rows="10"></textarea><br>
  <input type="SUBMIT" name="Send" value="Send">
</form>
</font>
</td>
<td class="RIGHTCOL">
  <?php require "loginpanel.php"?>

  <!-- Show the Prediction stats for the next match -->
  <?php ShowPredictionStatsForNextMatch(); ?>
  
  <!-- Competition Prize -->
  <?php require "prize.html"?>
</td>
</tr>
</table>

</body>
</html>
