<?php
/*********************************************************
 * Author: John Astill
 * Date  : 10th December
 * File  : sendpassword.php
 * Desc  : Send the password for the given user to the 
 *       : email address in the users profile. If the 
 *       : user cannot be found, display en error message.
 ********************************************************/
  require "systemvars.php";
  require "configvalues.php";
  require "security.php";
  require "sortfunctions.php";

  $username = $HTTP_POST_VARS["USERID"];
  $response = "Error";

  // Get the email address of the user
  $link = OpenConnection();
  if ($link == FALSE) {
    ErrorNotify("Unable to open connection for Sending Password to $username");
    exit;
  }

  $query = "SELECT * FROM $dbaseUserData where username=? and lid=?";
  $result = DBExecute($query, array($username, (int)$leagueID), $link);
  if ($result === FALSE) {
    die("Unable to find account: ".mysqli_error($link));
  }

    $array = mysql_fetch_assoc($result);
    $userid = $array["userid"];
    $email = $array["email"];
    $password = $array["password"];
    if ($PasswordEncryption == "TRUE") {
      // Create a random password and update the user profile.
      $password = date("DadsLi").$array["userid"];
      $encr = new Encryption($password);
      $encryptpass = $encr->Encrypt();
      $query = "update $dbaseUserData set password=? where userid=? and lid=?";
      $res2 = DBExecute($query, array($encryptpass, (int)$userid, (int)$leagueID), $link);
      if ($res2 === FALSE) {
        die("Unable to update the password: ".mysqli_error($link));
      }
    } else {
      $encr = new Encryption($password);
      $password = $encr->Decrypt();
    }
  if ($email != "") {
    $response = "Password sent to $email for $username.";
    // Send the email.
    $text = "You have requested your password for $PredictionLeagueTitle.\n Your password is $password\nThank you";
    mail($email, "$PredictionLeagueTitle",$text,"From: $adminEmailAddr");

  } else {
    $response = "Sorry, we could not find the user $username. Please check the spelling.";
  }
    
  closeConnection($link);

  require "templates/render.php";
  renderPage("public", array("title" => "Password reminder", "content" => "password-response.php"));
