<?php 
/*********************************************************
 * Author: John Astill
 * Date  : 10th December 2001 (c)
 * File  : showmyprofile.php
 * Desc  : Display the predictions for the currently logged
 *       : in user. If the cookie does not exist for the
 *       : user,show them a login page.
 ********************************************************/
require "systemvars.php";
require "configvalues.php";
require "sortfunctions.php";
require "security.php";
require ("lang/".GetLangFile());

if ($User->loggedIn == FALSE) {
  $ip = $_SERVER["REMOTE_ADDR"];
  $port = $_SERVER["REMOTE_PORT"];
  $referer = $_SERVER["HTTP_REFERER"];
  
  LogMsg("Attempt by user not logged in to make predictions. This could be a spoofed page. IP Address $ip port $port referring page "); 
  forward("index.php");
  exit;
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset?>">
    <title>
      <?php echo $My_Profile_for." ".$User->username?>
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

      if (form.PWD1.value.length >= 10) {
        alert(<?php echo "\"$Password_too_long \""; ?>);
        return (false);
      }

      return true;
    }

    /***********************************************
     * Check the form is complete. 
     * Check the email address 
     ***********************************************/
    function checkForm(form) {

      // Ensure that an email address is entered
      if (form.EMAIL.value == "") {
        alert(<?php echo "\"$Email_address_is_required \""; ?>);
        return (false);
      }

      if (form.EMAIL.value.length >= 60) {
        alert(<?php echo "\"$Email_too_long \""; ?>);
        return (false);
      }

      // Ensure that an email address is valid
      if (form.EMAIL.value.indexOf('@') < 0) {
        alert(<?php echo "\"$Email_address_is_not_valid \""; ?>);
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

  <?php 
    if ($ErrorCode != "") { 
  ?>
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
    $Error = "";
  }
  ?>

  <tr>
    <!-- Left Column -->
    <td valign="top" class="LEFTCOL">
      <?php require "menus.php"?>
    </td>
    <!-- Central Column -->
    <td valign="top" align="CENTER" class="CENTERCOL">
      <!-- Show the Users info -->
      <form method="POST" action="updateprofile.php">
        <table class="STANDTB">
          <tr>
            <td align="CENTER" class="TBLHEAD">
              <font class="TBLHEAD">
                <?php echo $User_Name; ?>
              </font>
            </td>
            <td align="CENTER" class="TBLHEAD">
              <font class="TBLHEAD">
                <?php echo $Email_Address_txt; ?>
              </font>
            </td>
            <td align="CENTER" class="TBLHEAD">
              <font class="TBLHEAD">
                <?php echo $Icon; ?>
              </font>
            </td>
            <td align="CENTER" class="TBLHEAD">
              <font class="TBLHEAD">
                <?php echo $Language; ?> 
              </font>
            </td>
          </tr>
          <tr>
            <td class="TBLROW" valign="top">
              <font class="TBLROW">
                <input type="TEXT" size="10" name="USERNAME" value="<?php echo $User->username ?>">
              </font>
            </td>
            <td class="TBLROW" valign="top">
              <font class="TBLROW">
                <input type="text" size="20" name="EMAIL" value="<?php echo $User->emailaddr ?>">
              </font>
            </td>
            <td class="TBLROW">
              <font class="TBLROW" valign="top">
                <img src="<?php echo $User->icon ?>" border="0">
              </font>
            </td>
            <td class="TBLROW" valign="top">
              <font class="TBLROW">
                <select name="LANG">
                  <?php GetLanguageOptions($User->lang) ?>
                </select>
              </font>
            </td>
          </tr>
<!--
          <tr>
            <td class="TBLHEAD" align="CENTER" valign="top" colspan="4">
              <font class="TBLHEAD">
                <?php echo $Lang_Enable_Auto_Predictions; ?>
              </font>
            </td>
          </tr>
          <tr>
            <td class="TBLROW" valign="top" align="left" colspan="4">
              <font class="TBLROW">
                <input type="checkbox" size="2" name="AUTO" value="Y" <?php if ($User->auto == 'Y') echo "checked"?>>
                <?php echo $Lang_Enable; ?>
              </font>
            </td>
          </tr>
-->
          <tr>
            <td class="TBLROW" align="CENTER" colspan="5">
              <input type="submit" onClick="return checkForm(this.form);" value="<?php echo $Change_Profile; ?>" name="Change">
            </td>
          </tr>

        </table>
      </form>

<?php 
  if ($UploadIcons == "TRUE") {
?>
      <!-- Form uploading an icon -->
      <form method="POST" enctype="multipart/form-data" action="uploadicon.php">
        <table class="STANDTB">
          <tr>
            <td class="TBLHEAD" align="CENTER" colspan="3">
              <font class="TBLHEAD">
                <?php echo $Upload_Icon; ?>
              </font>
            </td>
          </tr>
          <tr>
            <td class="TBLROW" align="LEFT">
              <font class="TBLROW">
                <?php echo $Upload_Instructions; ?>
              </font>
            </td>
          </tr>
          <tr>
            <td class="TBLROW" align="LEFT">
              <font class="TBLROW">
                <input type="file" size="40" name="ICONNAME">
              </font>
            </td>
          </tr>
          <tr>
            <td class="TBLROW" align="CENTER" colspan="3">
              <font class="TBLROW">
                <input type="submit" Value="<?php echo $Upload_Icon; ?>" Name="UploadIcon">
              </font>
            </td>
          </tr>
        </table>
      </form>
<?php
  }
?>

      <!-- Form for changing the password -->
      <form method="POST" action="changepassword.php">
        <table class="STANDTB">
          <tr>
            <td class="TBLHEAD" align="CENTER" colspan="3">
              <font class="TBLHEAD">
                <?php echo $Change_Password; ?>
              </font>
            </td>
          </tr>
          <tr>
            <td class="TBLHEAD" align="LEFT">
              <font class="TBLHEAD">
                <?php echo $Old_Password_txt; ?>
              </font>
            </td>
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
                <input type="password" size="10" name="OLDPWD">
              </font>
            </td>
            <td class="TBLROW" align="LEFT">
              <font class="TBLROW">
                <input type="hidden" name="USERID" value="<?php echo $User->userid; ?>">
                <input type="password" size="10" name="PWD1">
              </font>
            </td>
            <td class="TBLROW" align="LEFT">
              <font class="TBLROW">
                <input type="password" size="10" name="PWD2">
              </font>
            </td>
          </tr>
          <tr>
            <td class="TBLROW" align="CENTER" colspan="3">
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
      <?php require "prize.html" ?>
    </td>
  </tr>
</table>

</body>
</html>


