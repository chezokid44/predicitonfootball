<?php 
/*********************************************************
 * Author: John Astill (c)
 * Date  : 30th Jan 2003
 * File  : sessiondata.php
 * Desc  : Data that is required during a logged in 
 *       : session. Matters not if the session uses 
 *       : cookies or PHP sessions.
 ********************************************************/

/*************************************************************
** Store User data in session information
** needs to be global
*************************************************************/
$User = new User;

$SID="";
if (array_key_exists("sid",$HTTP_GET_VARS)) {
  $SID = $HTTP_GET_VARS["sid"];
}

// For some servers it is necessary to update the session file
// directory.
if ($sessionFileDir != "") {
  session_save_path($sessionFileDir);
}

if ($SID == "") {
  session_start();
  $SID = session_id();
} else {
  session_id($SID);
  session_start();
}
$HTTP_SESSION_VARS = &$_SESSION;
if (!isset($_SESSION["csrf_token"]) || !is_string($_SESSION["csrf_token"]) || strlen($_SESSION["csrf_token"]) !== 64) {
  $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

if (array_key_exists("User",$HTTP_SESSION_VARS)) {
  $User = $HTTP_SESSION_VARS["User"];
}

// These are needed to suppress warnings in the language files.
$userid = $User->userid;
$username = $User->username;
$email = "";
$password = "";

$ErrorCode = "";
if (array_key_exists("ErrorCode",$HTTP_SESSION_VARS)) {
  $ErrorCode = $HTTP_SESSION_VARS["ErrorCode"];
}

unset($_SESSION["ErrorCode"]);

/*************************************************************
** Store User data in session information
** needs to be global
*************************************************************/
function RegisterUser() {
  global $User;
  $_SESSION["User"] = $User;
}

/*************************************************************
** Delete User data in session information
** needs to be global
*************************************************************/
function UnregisterUser() {
  unset($_SESSION["User"]);
}

?>
