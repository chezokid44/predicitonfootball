<?php
/*********************************************************
 * Author: John Astill (c) 
 * Date  : 18th December 2001
 * File  : createnewuser.php
 * Desc  : Form for creating a new user.
 ********************************************************/
require "systemvars.php";
require "configvalues.php";
require "sortfunctions.php";
require "security.php";
require ("lang/".GetLangFile());

// If the user is logged in as admin then they are attempting
// to create a new user in a Locked Game.
$isAdmin =  CheckAdmin($User->usertype);
$asAdmin = "FALSE";
if ( ($LockedGame == "TRUE")
   &&($isAdmin == TRUE) ) {
  $asAdmin = "TRUE";
}

$error = $ErrorCode;
ClearErrorCode();

$newicon = $defaulticon;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>
  <?php echo $Create_New_User; ?>
</title>
<link rel="stylesheet" href="common.css" type="text/css">

<SCRIPT LANGUAGE="JavaScript">
<!--

// Check the form is complete. 
// Check that all the entries are complete.
// Check that the two passwords are equal.
// Check the email address 
function checkForm(form) {

	// Ensure a USER ID is entered
	if (form.USERID.value == "") {
		alert(<?php echo "\"$User_ID_Required\""; ?>);
		return (false);
	}

  if (form.USERID.value.length >= 32) {
		alert(<?php echo "\"$User_ID_too_long\""; ?>);
		return (false);
  }

	// Ensure that the passwords is not empty
	if (form.PWD1.value == "") {
		alert(<?php echo "\"$Passwords_is_required\""; ?>);
		return (false);
	}

	// Ensure that the passwords are equal
	if (form.PWD1.value != form.PWD2.value) {
		alert(<?php echo "\"$Passwords_do_not_match\""; ?>);
		return (false);
	}

  if (form.PWD1.value.length >= 10) {
		alert(<?php echo "\"$Password_too_long\""; ?>);
		return (false);
  }

	// Ensure that an email address is entered
	if (form.EMAIL.value == "") {
		alert(<?php echo "\"$Email_address_is_required\""; ?>);
		return (false);
	}

  if (form.EMAIL.value.length >= 60) {
		alert(<?php echo "\"$Email_too_long\""; ?>);
		return (false);
  }

	// Ensure that an email address is valid
	if (form.EMAIL.value.indexOf('@') < 0) {
		alert(<?php echo "\"$Email_address_is_not_valid\""; ?>);
		return (false);
	}

	return true;
}

// -->
</SCRIPT>


</head>

<body class="MAIN">

<table width="800">
<tr>
<td colspan="3" align="center">
<!-- Header Row -->
<?php echo $HeaderRow ?>
</td>
</tr>

<?php if ($error != "") { ?>
<tr>
<td colspan="3" bgcolor="red" align="center">
<!-- Error Message Row -->
<font class="TBLHEAD">
<?php 
  echo $error; 
?>
</font>
</td>
</tr>
<?php 
  }
?>

<tr>
<td valign="top">
<?php require "menus.php"?>
</td>
<td valign="top">
<!-- Show the Users info -->
<form method="POST" action="createuser.php">
<table width="500">

<tr>
<td colspan="3" align="CENTER" class="TBLHEAD">
<font class="TBLHEAD">
<?php echo $User_Info."\n"; ?>
</font>
</td>
</tr>
<tr>
<td align="CENTER" class="TBLHEAD">
<font class="TBLHEAD">
<?php echo $User_ID."\n"; ?>
</font>
</td>
<td class="TBLROW">
<font class="TBLROW">
<input type="TEXT" size="10" name="USERID" value="">
</font>
</td>
<td class="TBLROW">
<font class="TBLROW">
<?php echo $Select_user_ID."\n"; ?>
</font>
</td>
</tr>
<tr>
<td align="CENTER" class="TBLHEAD">
<font class="TBLHEAD">
<?php echo $Password_txt; ?>
</font>
</td>
<td class="TBLROW">
<font class="TBLROW">
<input type="hidden" name="ASADMIN" value="<?php echo $asAdmin;?>">
<input type="password" size="10" name="PWD1">
</font>
</td>
<td class="TBLROW">
<font class="TBLROW">
<?php echo $Password_length."\n"; ?>
</font>
</td>
</tr>
<tr>
<td align="CENTER" class="TBLHEAD">
<font class="TBLHEAD">
<?php echo $Password_txt; ?>
</font>
</td>
<td class="TBLROW">
<font class="TBLROW">
<input type="password" size="10" name="PWD2">
</font>
</td>
<td class="TBLROW">
<font class="TBLROW">
<?php echo $Repeat_password."\n"; ?>
</font>
</td>
</tr>
<tr>
<td align="CENTER" class="TBLHEAD">
<font class="TBLHEAD">
<?php echo $Email_Address_txt."\n"; ?>
</font>
</td>
<td class="TBLROW">
<font class="TBLROW">
<input type="TEXT" size="20" name="EMAIL" value="">
</font>
</td>
<td class="TBLROW">
<font class="TBLROW">
<?php echo $Enter_valid_email_address."\n"; ?>
</font>
</td>
</tr>
<tr>
<td align="CENTER" class="TBLHEAD">
<font class="TBLHEAD">
<?php echo $Icon."\n"; ?>
</font>
</td>
<td class="TBLROW">
<font class="TBLROW">
<?php echo $newicon;?>
<input type="HIDDEN" name="ICON" value="<?php echo $newicon;?>">
</font>
</td>
<td class="TBLROW">
<font class="TBLROW">
<?php echo $Select_icon."\n"; ?>
</font>
</td>
</tr>
<tr>
<td colspan="3" align="CENTER">
<font class="TBLROW">
<input type="SUBMIT" onClick="return checkForm(this.form);" name="CREATE" value="<?php echo $Create; ?>">
</td>
</tr>
</table>
</form>
</td>

<!-- Right Column -->
<td class="RIGHTCOL" align="RIGHT">
  <!-- Show the Prediction stats for the next match -->
  <?php ShowPredictionStatsForNextMatch(); ?>
  
  <!-- Competition Prize -->
  <?php include "prize.html"?>
</td>

</tr>
</table>

</body>
</html>


