<?php
/*********************************************************
 * Author: John Astill
 * Date  : 10th December
 * File  : createuser.php
 * Desc  : Create the required tables entries for a new user.
 ********************************************************/
require "systemvars.php";
require "configvalues.php";
require "sortfunctions.php";
require "security.php";

$username = $HTTP_POST_VARS["USERID"];
$password = $HTTP_POST_VARS["PWD1"];
$email = $HTTP_POST_VARS["EMAIL"];
$icon = $HTTP_POST_VARS["ICON"];
$asAdmin = $HTTP_POST_VARS["ASADMIN"];

require ("lang/".GetLangFile());

/**
 * Determine if the given email exists.
 * @param email the email to look for.
 */
function doesEmailExist($email) {
  global $dbaseUserData, $leagueID;

  $link = OpenConnection();
  if ($link == FALSE) {
    ErrorNotify("Unable to open connection");
    exit;
  }

  $query = "SELECT email from $dbaseUserData where lid=? and email=?";
  $result = DBExecute($query, array((int)$leagueID, $email));

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

/**********************************************************
 * Determine if the given user exists.
 * @param user the user to look for.
 *********************************************************/
function doesUserExist($user) {
  global $dbaseUserData, $leagueID;

  $link = OpenConnection();
  if ($link == FALSE) {
    ErrorNotify("Unable to open connection");
    exit;
  }

  $query = "SELECT username from $dbaseUserData where lid=? and username=?";
  $result = DBExecute($query, array((int)$leagueID, $user));

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

/*****************************************************
 * Entry Point 
 *****************************************************/
if (TRUE == doesUserExist($username)) {
  ErrorRedir($Username_already_exists,"createnewuser.php");
}

/*
 * The admin can configure the system to allow more than one user per password. 
 */
if ($allowMultipleUserPerEmail == "FALSE") {
  if (TRUE == doesEmailExist($email)) {
    ErrorRedir($One_user_per_email,"createnewuser.php");
  }
}

// Make sure there is a username
if ($username == "") {
  ErrorRedir($Username_required,"createnewuser.php");
}

// Make sure there is a password
if ($password == "") {
  ErrorRedir($Password_required,"createnewuser.php");
}

// Encrypt the password if password encryption is enabled
$encr = new Encryption($password);
$encryptpass = $encr->Encrypt();
$password = $encr->pwd;

// Connect to the host.
$link = OpenConnection();

$todaysDate = date("Y-m-d");
$query = "INSERT INTO $dbaseUserData (lid,username,password,email,icon,usertype,since) values (?,?,?,?,?,?,?)";
$result = DBExecute($query, array((int)$leagueID, $username, $encryptpass, $email, $icon, 1, $todaysDate));
if ($result === FALSE) {
  die("Unable to create user: ".mysqli_error($link));
}

CloseConnection($link);

// Email the administrator the new user
$text = "New User created.\nUser = $username\nPassword = $password\nEmail = $email\nIcon = $icon\nSent to $adminEmailAddr\nVersion = ".VERSION;
@mail($adminEmailAddr, "$PredictionLeagueTitle New User",$text,"From: $email");
@mail($email,$Email_Subject_txt,$Email_Answer_txt,"From: $adminEmailAddr");

// Add the new user to the standings table.
UpdateStandingsTable();

if ($asAdmin == "TRUE") {
  forward("showusers.php?sid=$SID");
} else {
  // Now that the user has been created, log them in.
  login($username,$password);
}
?>
