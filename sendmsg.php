<?php
/*********************************************************
 * Author: John Astill (c) 2003
 * Date  : 20 February 2003
 * File  : sendmsg.php
 ********************************************************/
require "systemvars.php";
require "configvalues.php";
require "sortfunctions.php";
require "security.php";

if ($User->loggedIn != TRUE) {
  $ip = $_SERVER["REMOTE_ADDR"];
  $port = $_SERVER["REMOTE_PORT"];
  $referer = $_SERVER["HTTP_REFERER"];
  
  logMsg("Attempt to send a message by someone not logged in. Could be an attempt to break in. IP Address $ip port $port referring page $referer");
  forward("index.php"); 
  exit;   
}

$tgtuser = $HTTP_POST_VARS["TGTUSER"];
$subject = $HTTP_POST_VARS["SUBJECT"];
$message = $HTTP_POST_VARS["MESSAGE"];
$srcuser = $User->userid;

$link = OpenConnection();
$query = "insert into $dbaseMsgData (lid,sender,receiver,subject,body,status) values (?,?,?,?,?,?)";
if (DBExecute($query, array((int)$leagueID, (int)$srcuser, (int)$tgtuser, $subject, $message, "new")) === FALSE) {
  die("Unable to send message: ".mysqli_error($link));
}

$query = "select * from $dbaseUserData where userid=? and lid=?";
$res = DBExecute($query, array((int)$tgtuser, (int)$leagueID));
if ($res === FALSE) {
  die("Unable to read recipient details: ".mysqli_error($link));
}
$line = mysql_fetch_array($res);

$email = $line["email"];
$uname = $line["username"];

CloseConnection($link);

// Email the recipient to let them know they have received a new message.
require ("lang/".GetLangFile());
@mail($email,$New_Message_Subject,$New_Message_Body) or logMsg("Unable to send mail to $email: $New_Message_Body");

forward("index.php?sid=$SID");
?>
