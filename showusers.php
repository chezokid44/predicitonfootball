<?php 
/*********************************************************
 * Author: John Astill
 * Date  : 23rd January 2002 (c)
 * File  : showusers.php
 * Desc  : Display a table with all the user details 
 *       : except the password.
 ********************************************************/
require "systemvars.php";
require "configvalues.php";
require "sortfunctions.php";
require "security.php";
require "templates/render.php";

if ($User->loggedIn != TRUE) {
  $ip = $_SERVER["REMOTE_ADDR"];
  $port = $_SERVER["REMOTE_PORT"];
  $referer = $_SERVER["HTTP_REFERER"];

  logMsg("Attempt to view user data by someone not logged in. Could be an attempt to break in. IP Address $ip port $port referring page $referer");
  forward("index.php"); 
  exit;   
}

// If the user is not an admin user then they can't see this page.
if ($User->usertype < 4) {
  // Go back to the prediction index page.
  forward("index.php");
}

/////////////////////////////////////////////////////////
// Show a list of the users.
/////////////////////////////////////////////////////////
function ShowUsers() {
  global $dbaseUserData,$SID,$leagueID;

  $link = OpenConnection();
  if ($link == FALSE) {
    echo "Unable to connect to the dbase";
    return;
  }
  
  $userquery = "SELECT * FROM $dbaseUserData where lid='$leagueID'";
  $userresult = mysql_query($userquery)
      or die("Query failed: $userquery");

  // Display the username as a header.
?>
  <table class="STANDTB">
  <tr>
  <td class="TBLHEAD" colspan="8" align="center"><font class="TBLHEAD">Users</font></td>
  </tr>
  <tr>
  <td align="center" class="TBLHEAD"><font class="TBLHEAD">User ID</font></td>
  <td align="center" class="TBLHEAD"><font class="TBLHEAD">Admin</font></td>
  <td align="center" class="TBLHEAD"><font class="TBLHEAD">Username</font></td>
  <td align="center" class="TBLHEAD"><font class="TBLHEAD">Email</font></td>
  <td align="center" class="TBLHEAD"><font class="TBLHEAD">Since</font></td>
  <td align="center" class="TBLHEAD"><font class="TBLHEAD">&nbsp;</font></td>
  <td align="center" class="TBLHEAD"><font class="TBLHEAD">&nbsp;</font></td>
  <td align="center" class="TBLHEAD"><font class="TBLHEAD">&nbsp;</font></td>
  </tr>
<?php
  // First loop. Used to get all the users.
  while ($userline = mysql_fetch_array($userresult, MYSQL_ASSOC)) {
    // For each user display all their predictions.
    // against the actual result.
    $username = $userline["username"];
    $userid = $userline["userid"];
    $usertype = $userline["usertype"];
    $icon = $userline["icon"];
    $email = $userline["email"];
    $since = $userline["since"];

    $isAdmin = ($usertype >= 4) ? "Yes" : "";
    echo "<tr>\n";
    echo "<td class=\"TBLROW\"><font class=\"TBLROW\">$userid</font></td>\n";
    echo "<td class=\"TBLROW\"><font class=\"TBLROW\">$isAdmin</font></td>\n";
    echo "<td class=\"TBLROW\"><font class=\"TBLROW\">$username</font></td>\n";
    echo "<td class=\"TBLROW\"><font class=\"TBLROW\"><a href=\"mailto:$email\">$email</a></font></td>\n";
    echo "<td class=\"TBLROW\"><font class=\"TBLROW\">$since</font></td>\n";
    echo "<td class=\"TBLROW\"><font class=\"TBLROW\"><a href=\"deleteuser.php?sid=$SID&userid=$userid\" onclick=\"return confirm('Are you sure you want to delete ".addslashes($username)." and all their predictions');\"><small>delete user</small></a></font></td>\n";
    echo "<td class=\"TBLROW\"><font class=\"TBLROW\"><a href=\"changeuserpassword.php?sid=$SID&userid=$userid&username=$username\"><small>change user password</small></a></font></td>\n";
    if ($usertype >= 4) {
      echo "<td class=\"TBLROW\"><font class=\"TBLROW\"><a href=\"modifyuseradmin.php?sid=$SID&admin=Y&userid=$userid&username=$username\"><small>Remove admin privileges from this user</small></a></font></td>\n";
    } else {
      echo "<td class=\"TBLROW\"><font class=\"TBLROW\"><a href=\"modifyuseradmin.php?sid=$SID&admin=N&userid=$userid&username=$username\"><small>Make this user an admin user</small></a></font></td>\n";
    }
    echo "</tr>\n";
  }
  echo "</table>\n";
  CloseConnection($link);
}

$page = array("title" => "Users", "content" => "users.php", "notice" => $ErrorCode);
ClearErrorCode();
renderPage("admin", $page);
