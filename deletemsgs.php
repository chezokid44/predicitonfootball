<?php
/*********************************************************
 * Author: John Astill (c) 2003
 * Date  : 20 February 2003
 * File  : deletemsgs.php
 ********************************************************/
require "systemvars.php";
require "configvalues.php";
require "sortfunctions.php";
require "security.php";

if ($User->loggedIn != TRUE) {
  $ip = $_SERVER["REMOTE_ADDR"];
  $port = $_SERVER["REMOTE_PORT"];
  $referer = $_SERVER["HTTP_REFERER"];

  logMsg("Attempt to delete user messages by someone not logged in. Could be an attempt to break in. IP Address $ip port $port referring page $referer");
  forward("index.php"); 
  exit;   
}


$tgtuser = "";
if (array_key_exists("TGTUSER",$HTTP_POST_VARS)) {
  $tgtuser = $HTTP_POST_VARS["TGTUSER"];
}

$subject = "";
if (array_key_exists("SUBJECT",$HTTP_POST_VARS)) {
  $subject = $HTTP_POST_VARS["SUBJECT"];
}

$message = "";
if (array_key_exists("MESSAGE",$HTTP_POST_VARS)) {
  $message = $HTTP_POST_VARS["MESSAGE"];
}

$srcuser = $User->userid;


// Message IDs are submitted as checkbox field names.
$messageIds = array();
foreach ($HTTP_POST_VARS as $field => $value) {
  if ($field !== "DELETE" && ctype_digit((string)$field)) {
    $messageIds[] = (int)$field;
  }
}

if (count($messageIds) > 0) {
  $link = OpenConnection();
  $placeholders = implode(",", array_fill(0, count($messageIds), "?"));
  $query = "UPDATE $dbaseMsgData SET status=? WHERE lid=? AND receiver=? AND msgid IN ($placeholders)";
  $parameters = array_merge(array("deleted", (int)$leagueID, (int)$User->userid), $messageIds);
  if (DBExecute($query, $parameters) === FALSE) {
    die("Unable to update messages: ".mysqli_error($link));
  }
  CloseConnection($link);
}

forward("showmymessages.php?sid=$SID");
?>
