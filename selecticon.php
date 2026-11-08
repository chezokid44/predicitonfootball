<?php
/*********************************************************
 * Author: John Astill
 * Date  : 10th December
 * File  : selecticon.php
 * Desc  : Selects the icon the user wants.
 ********************************************************/
require "systemvars.php";
require "configvalues.php";
require "security.php";

if ($User->loggedIn != TRUE) {
  $ip = $_SERVER["REMOTE_ADDR"];
  $port = $_SERVER["REMOTE_PORT"];
  $referer = $_SERVER["HTTP_REFERER"];
  
  logMsg("Attempt to select icon by someone not logged in. Could be an attempt to break in. IP Address $ip port $port referring page $referer");
  forward("index.php"); 
  exit;   
}

$iconName = basename($HTTP_GET_VARS["icon"]);
$icon = "icons/".$iconName;
// Connect to the host.
$link = OpenConnection();

$query = "UPDATE $dbaseUserData set icon=? where userid=? and lid=?";
$result = DBExecute($query, array($icon, (int)$User->userid, (int)$leagueID));
if ($result === FALSE) {
  die("Unable to update icon: ".mysqli_error($link));
}

CloseConnection($link);

$User->icon = $icon;
$HTTP_SESSION_VARS["User"] = $User;

/* Redirect browser to PHP web site */
header("Location: userselecticon.php"); 
?>
