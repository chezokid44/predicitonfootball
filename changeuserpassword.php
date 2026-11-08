<?php 
/*********************************************************
 * Author: John Astill
 * Date  : 10th December
 * File  : changeuserpassword.php
 * Desc  : Change the users password.
 ********************************************************/
require "systemvars.php";
require "configvalues.php";
require "sortfunctions.php";
require "security.php";
require ("lang/$languageFile");

if ($User->loggedIn != TRUE) {
  $ip = $_SERVER["REMOTE_ADDR"];
  $port = $_SERVER["REMOTE_PORT"];
  $referer = $_SERVER["HTTP_REFERER"];

  logMsg("Attempt to change a user password by someone not logged in. Could be an attempt to break in. IP Address $ip port $port referring page $referer");
  forward("index.php"); 
  exit;   
}

if (!CheckAdmin($User->usertype)) {
  LogMsg("Attempt by $User->username to change someone elses password.");
  forward("index.php");
  exit;
}

$user = $HTTP_GET_VARS["userid"];
$username = $HTTP_GET_VARS["username"];

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <title>
      <?php echo $My_Profile_for." ".$User->userid?>
    </title>
    <link rel="stylesheet" href="common.css" type="text/css">
    <SCRIPT LANGUAGE="JavaScript">
    <!--

    /***********************************************
     * Check the passwords are equal
     ***********************************************/
    function checkPasswords(form) {

      // Ensure that the passwords is not empty
      if (form.PWD1.value == "") {
        alert(<?php echo "\"$Passwords_is_required \""; ?>);
        return (false);
      }

      // Ensure that the passwords are equal
      if (form.PWD1.value != form.PWD2.value) {
        alert(<?php echo "\"$Passwords_do_not_match \""; ?>);
        return (false);
      }

      if (form.PWD1.value.length >= 32) {
        alert(<?php echo "\"$Password_too_long \""; ?>);
        return (false);
      }

      return true;
    }
    // -->
    </SCRIPT>
  </head>

<body class="MAIN">
<!-- Main table -->
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

  <?php if ($ErrorCode != "") { ?>
  <tr>
  <td colspan="3" bgcolor="red" align="center">
  <!-- Error Message Row -->
  <font class="TBLHEAD">
  <?php echo $ErrorCode ?>
  </font>
  </td>
  </tr>
  <?php 
    // Empty the error code
    $ErrorCode = "";
  }
  ?>

  <tr>
    <!-- Left Column -->
    <td valign="top" class="LEFTCOL">
      <?php include "menus.php"?>
    </td>
    <!-- Central Column -->
    <td valign="top" align="CENTER" class="CENTERCOL">
      <!-- Form for changing the password -->
      <form method="POST" action="changepassword.php?sid=<?php echo $SID; ?>">
        <table class="STANDTB">
          <tr>
            <td class="TBLHEAD" align="CENTER" colspan="2">
              <font class="TBLHEAD">
                <?php echo $Change_Password.": ".$username; ?>
              </font>
            </td>
          </tr>
          <tr>
            <td class="TBLHEAD" align="LEFT">
              <font class="TBLHEAD">
                <?php echo $Password_txt; ?>
              </font>
            </td>
            <td class="TBLHEAD" align="LEFT">
              <font class="TBLHEAD">
                <?php echo $Password_Again; ?>
              </font>
            </td>
          </tr>
          <tr>
            <td class="TBLROW" align="LEFT">
              <font class="TBLROW">
                <input type="hidden" name="USERID" value="<?php echo $user; ?>">
                <input type="hidden" name="USERNAME" value="<?php echo $username; ?>">
                <input type="password" size="20" name="PWD1">
              </font>
            </td>
            <td class="TBLROW" align="LEFT">
              <font class="TBLROW">
                <input type="password" size="20" name="PWD2">
              </font>
            </td>
          </tr>
          <tr>
            <td class="TBLROW" align="CENTER" colspan="2">
              <font class="TBLROW">
                <input type="submit" onClick="return checkPasswords(this.form);" Value="<?php echo $Change_Password_button; ?>" Name="ChangePwd">
              </font>
            </td>
          </tr>
        </table>
      </form>
    </td>
    <!-- Left Column -->
    <td valign="top" class="RIGHTCOL">
      <!-- Show the Prediction stats for the next match -->
      <?php ShowPredictionStatsForNextMatch(); ?>
      
      <!-- Competition Prize -->
      <?php require "Prize.html" ?>
    </td>
  </tr>
</table>

</body>
</html>


