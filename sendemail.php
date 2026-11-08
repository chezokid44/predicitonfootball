<?php
/*********************************************************
 * Author: John Astill (c)
 * Date  : 21st June 2003
 * File  : sendemail.php
 * Desc  : Send the email to all the users.
 ********************************************************/
require "systemvars.php";
require "configvalues.php";
require "sortfunctions.php";
require "security.php";

function check_php_version($version) {
  global $adminEmailAddr,$leagueID;

  // intval used for version like "4.0.4pl1"
  $testVer=intval(str_replace(".", "",$version));
  $curVer=intval(str_replace(".", "",phpversion()));
  if( $curVer < $testVer )
    return false;
  return true;
}

  // Users are stored in a string
  $users = "";

  $subject = $HTTP_POST_VARS["SUBJECT"];
  $body = $HTTP_POST_VARS["BODY"];

  // Get all the users.
  $link = OpenConnection();

  $query = "select username, email from $dbaseUserData where lid='$leagueID'";
  $result = mysql_query($query)
    or die("Unable to perform query: $query");

  // Removed the BCC option as it appears that sending a single email can file when one of
  // the addresses is not valid.
  // Send as individual emails.
  while ($line = mysql_fetch_array($result)) {
    $user = $line["email"];
    if (FALSE == @mail($user,$subject,$body,
        "From: $adminEmailAddr\r\n"
        ."Reply-To: $adminEmailAddr\r\n")) {
      LogMsg("Unable to send email to user from $adminEmailAddr, to $user subject: $subject<br>$body");
      if (FALSE == @mail($user,$subject,$body,
                   "From: $adminEmailAddr\r\n"
                   ."Reply-To: $adminEmailAddr\r\n")) {
      }
    }
  }

  CloseConnection($link);
  forward("index.php");
?>
