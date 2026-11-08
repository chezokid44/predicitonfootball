<?php 
/*********************************************************
 * Author: John Astill
 * Date  : 31st December
 * File  : changepassword.php
 * Desc  : Change the users given password.
 ********************************************************/
require "systemvars.php";
require "configvalues.php";
require "sortfunctions.php";
require "security.php";

 $user = $HTTP_POST_VARS["USERID"];
 $password = $HTTP_POST_VARS["PWD1"];

 $ip = $_SERVER["REMOTE_ADDR"];
 $port = $_SERVER["REMOTE_PORT"];
 $referer = $_SERVER["HTTP_REFERER"];
 
 logMsg("Password is being changed for user [$user] from ip address ".$ip." port ".$port);
 
 // Make sure that the page has not been spoofed and that a user is logged in.
 if ($User->loggedIn != TRUE) {
   logMsg("Attempt to change password by someone not logged in. Could be an attempt to break in. IP Address $ip port $port referring page $referer");
   forward("index.php"); 
   exit;   
 }
  
 // Encrypt the password if password encryption is enabled
 $encr = new Encryption($password);
 $encryptpass = $encr->Encrypt();

 $link = OpenConnection();

 $query = "UPDATE $dbaseUserData set password=? where userid=? and lid=?";
 $result = DBExecute($query, array($encryptpass, (int)$user, (int)$leagueID));
 if ($result === FALSE) {
   die("Unable to update password: ".mysqli_error($link));
 }

 CloseConnection($link);

 if ($result == TRUE) {
   // The password has changed, update the stats info in the session
   // data and forward to the Index.
   header("Location: index.php?sid=$SID"); 
   /* Make sure that code below does not get executed when we redirect. */
   exit; 
 } else {
   // Go back to sending page.
   forward("showmyprofile.php?sid=$SID"); 
 }
?>
