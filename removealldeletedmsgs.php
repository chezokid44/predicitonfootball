<?php
/*********************************************************
 * Author: John Astill (c) 2003
 * Date  : 20 February 2003
 * File  : removealldeletedmsgs.php
 ********************************************************/
require "systemvars.php";
require "configvalues.php";
require "sortfunctions.php";
require "security.php";

if ($User->loggedIn != TRUE) {
  $ip = $_SERVER["REMOTE_ADDR"];
  $port = $_SERVER["REMOTE_PORT"];
  $referer = $_SERVER["HTTP_REFERER"];
  
  logMsg("Attempt to delete read messages by someone not logged in. Could be an attempt to break in. IP Address $ip port $port referring page $referer");
  forward("index.php"); 
  exit;   
}

if (!CheckAdmin($User->usertype)) {
  LogMsg("User $User->username tried to delete all deleted messages");
  forward("index.php");
  exit;
}

$query = "delete from $dbaseMsgData where status=\"deleted\" and lid='$leagueID'";

$link = OpenConnection();
mysql_query($query) or die("unable to insert message : $query");

CloseConnection($link);

forward("viewallmessages.php?sid=$SID");
?>
