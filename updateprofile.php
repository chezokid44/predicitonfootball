<?php
/*********************************************************
 * Author: John Astill
 * Date  : 23rd January 2002 (c)
 * File  : updateprofile.php
 * Desc  : Update the users profile information.
 ********************************************************/
require "systemvars.php";
require "configvalues.php";
require "sortfunctions.php";
require "security.php";

if ($User->loggedIn != TRUE) {
  $ip = $_SERVER["REMOTE_ADDR"];
  $port = $_SERVER["REMOTE_PORT"];
  $referer = $_SERVER["HTTP_REFERER"];
  
  logMsg("Attempt to update a profile by someone not logged in. Could be an attempt to break in. IP Address $ip port $port referring page $referer");
  forward("index.php"); 
  exit;   
}

$email = $HTTP_POST_VARS["EMAIL"];
$username = $HTTP_POST_VARS["USERNAME"];
$lang = $HTTP_POST_VARS["LANG"];

$dflths = "0";
$dfltas = "0";
$auto = "N";

if ($auto != "Y") {
  $auto = "N";
}

$userid = $User->userid;

/**
 * Determine if the given user exists.
 * @param user the user to look for.
 */
function doesUsernameExist($user) {
  global $dbaseUserData,$leagueID;

  $link = OpenConnection();
  if ($link == FALSE) {
    ErrorNotify("Unable to open connection");
    exit;
  }

  $query = "SELECT username from $dbaseUserData where username=? and lid=?";
  $result = DBExecute($query, array($user, (int)$leagueID));

  if ($result == FALSE) {
    ErrorNotify("Query Failed : $query");
    CloseConnection($link);
    return TRUE;
  }
  
  // If we get >0 rows, the username already exists.
  $numrows = mysql_num_rows($result);

  CloseConnection($link);

  if ($numrows == 0) {
    return FALSE;
  }

  return TRUE;
} 

function doesUseridExist($user) {
  global $dbaseUserData,$leagueID;

  $link = OpenConnection();
  if ($link == FALSE) {
    ErrorNotify("Unable to open connection");
    exit;
  }

  $query = "SELECT username from $dbaseUserData where userid=? and lid=?";
  $result = DBExecute($query, array((int)$user, (int)$leagueID));

  if ($result == FALSE) {
    ErrorNotify("Query Failed : $query");
    CloseConnection($link);
    return TRUE;
  }
  
  // If we get >0 rows, the username already exists.
  $numrows = mysql_num_rows($result);

  CloseConnection($link);

  if ($numrows == 0) {
    return FALSE;
  }

  return TRUE;
} 

if (FALSE == doesUseridExist($userid)) {
  ErrorRedir("User $userid does not exist","showmyprofile.php?sid=".$SID);
}

// Make sure there is a username
if ($username == "") {
  ErrorRedir("Username required, please choose a name","showmyprofile.php?sid=".$SID);
}

if ($username != $User->username) {
  if (TRUE == doesUsernameExist($username)) {
    ErrorRedir("Username $username already being used.","showmyprofile.php?sid=".$SID);
  }
}

// Connect to the host.
$link = OpenConnection();
if ($link == FALSE) {
  ErrorRedir("Unable to open connection","showmyprofile.php?sid=".$SID);
}

$query = "update $dbaseUserData set email=?, username=?, lang=?, dfltas=?, dflths=? where userid=? and lid=?";
$result = DBExecute($query, array($email, $username, $lang, $dfltas, $dflths, (int)$userid, (int)$leagueID));
if ($result === FALSE) {
  die("Unable to update profile: ".mysqli_error($link));
}

CloseConnection($link);

$User->emailaddr = $email;
$User->username = $username;
$User->lang = $lang;
$User->dflths = $dflths;
$User->dfltas = $dfltas;
$User->auto = $auto;

$HTTP_SESSION_VARS["User"] = $User;

// Return to the create user page, but set an error message.
// As REFERER will not be set by all browsers, return to the 
// page that should have sent us here.
forward("showmyprofile.php?sid=".$SID); 
?>
