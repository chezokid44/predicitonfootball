<?php
/*********************************************************
 * Author: John Astill
 * Date  : 10th December
 * File  : deleteuser.php
 * Desc  : Delete the given user from the user table
 *       : and predictions from the prediction table.
 ********************************************************/
require "systemvars.php";
require "configvalues.php";
require "sortfunctions.php";
require "security.php";

$userid = $HTTP_GET_VARS["userid"];

if ($User->loggedIn != TRUE) {
  $ip = $_SERVER["REMOTE_ADDR"];
  $port = $_SERVER["REMOTE_PORT"];
  $referer = $_SERVER["HTTP_REFERER"];

  logMsg("Attempt to delete a user by someone not logged in. Could be an attempt to break in. IP Address $ip port $port referring page $referer");
  forward("index.php"); 
  exit;   
}

// Make sure the user trying to perform the delete is 
// an admin or above.
if (!CheckAdmin($User->usertype)) {
  // Forward to somewhere
  LogMsg("Attempt to remove user $userid by User $User->userid with insufficient permissions from $REMOTE_ADDR:$REMOTE_PORT.");
  forward("index.php");
}

/**
 * Determine if the given user exists.
 * @param user the user to look for.
 */
function doesUserExist($userid) {
  global $dbaseUserData, $leagueID;

  $link = OpenConnection();
  if ($link == FALSE) {
    ErrorNotify("Unable to open connection");
    exit;
  }

  $query = "SELECT userid from $dbaseUserData where lid=? and userid=?";
  $result = DBExecute($query, array((int)$leagueID, (int)$userid));

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
if (FALSE == doesUserExist($userid)) {
  ErrorRedir("User $username does not exist","showusers.php");
}

// Connect to the host.
$link = OpenConnection();

// Delete from the UserData table
$query = "DELETE FROM $dbaseUserData where lid=? and userid=?";
$result = DBExecute($query, array((int)$leagueID, (int)$userid));
if ($result === FALSE) {
  die("Unable to delete user: ".mysqli_error($link));
}

// Delete from the PredictionData table
$query = "DELETE FROM $dbasePredictionData where lid=? and userid=?";
$result = DBExecute($query, array((int)$leagueID, (int)$userid));
if ($result === FALSE) {
  die("Unable to delete user predictions: ".mysqli_error($link));
}

// Close the connection to the database
CloseConnection($link);

// Email the administrator the deleted user
$text = "User deleted.\nUser = $userid\nSent to $adminEmailAddr.\nVersion = ".VERSION;
@mail($adminEmailAddr, "$PredictionLeagueTitle New User",$text,"From: $email");

// Log the action
LogMsg($text);

// Go back to show users page.
forward("showusers.php");
?>
