<?php
/*********************************************************
 * Author: John Astill (c)
 * Date  : 10th May 2002
 * File  : createadminuser.php
 * Desc  : Create the required tables entries for a new user.
 ********************************************************/
require "systemvars.php";
require "configvalues.php";
require "security.php";

$username = $HTTP_POST_VARS["USER"];
$password = $HTTP_POST_VARS["PASSWORD"];
$email = $HTTP_POST_VARS["EMAIL"];
if ($email == "") {
  $email = $adminEmailAddr;
}

$icon = $defaulticon;
$encr = new Encryption($password);
$encryptpass = $encr->Encrypt();

/**
 * Determine if the given user exists.
 * @param user the user to look for.
 */
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

/* Entry Point */
if (TRUE == doesUserExist($username)) {
  ErrorRedir("User $username already exists, please choose another name","admincreateadminuser.php");
}

// Make sure there is a username
if ($username == "") {
  ErrorRedir("Username required, please choose a name","admincreateadminuser.php");
}

// Make sure there is a password
if ($password == "") {
  ErrorRedir("Password required, please choose a name","admincreateadminuser.php");
}

// Connect to the host.
$link = OpenConnection();

$todaysDate = date("Y-m-d");
$query = "INSERT INTO $dbaseUserData (lid,username,password,email,icon,usertype,since) values (?,?,?,?,?,?,?)";
$result = DBExecute($query, array((int)$leagueID, $username, $encryptpass, $email, $icon, 4, $todaysDate));
if ($result === FALSE) {
  die("Unable to create admin user: ".mysqli_error($link));
}

CloseConnection($link);

// Email the administrator the new user
$text = "New User created.\nUser = $username\nPassword = $password\nEmail = $email\nIcon = $icon\nSent to $adminEmailAddr\nVersion = ".VERSION;
@mail($adminEmailAddr, "$PredictionLeagueTitle New User",$text,"From: $email");
$text = "Hi $username,\n\nWelcome the the $PredictionLeagueTitle. Thank you for joining us.\n\nPlease check your details are correct. If you need to change anything please log into the Prediction League and modify your profile.\n\nPassword = $password\nEmail = $email.\n\nGood Luck\n\n$PLHome";
@mail($email, "Welcome to the $PredictionLeagueTitle",$text,"From: $email");

// Now that the user has been created, log them in.
loginwithtarget($username,$password,"adminshowtimeinfo.php?sid=$SID");
?>
