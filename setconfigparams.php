<?php
/*********************************************************
 * Author: John Astill (c)
 * Date  : 20th March 2003
 * File  : setconfigparams.php
 *********************************************************/
require "systemvars.php";
require "configvalues.php";
require "security.php";
require "sortfunctions.php";

// By now the admin user should be logged in.
if ($User->loggedIn == FALSE) {
  echo "Only an Admin User can perform this function";
  exit;
}
if (!CheckAdmin($User->usertype)) {
  echo "Only an Admin User can perform this function";
  exit;
}

function SetParam($param, $val) {
  global $link, $dbaseConfigData, $leagueID;

  $query = "update $dbaseConfigData set value=? where param=? and lid=?";
  return DBExecute($query, array($val, $param, (int)$leagueID));
}
  
$referer = "adminconfigleague.php?sid=$SID";
if (array_key_exists("Default",$HTTP_POST_VARS)) {
  if ($HTTP_POST_VARS["Default"] == "Default") {
    PopulateConfigTable();
  }
} else {

  $link = OpenConnection();

  foreach ($HTTP_POST_VARS as $param => $val) {

    if ($param == "REFERER") {
      $referer = $val;
    }
    if ($param == "Change" || $param == "Default") {
      continue;
    }
    SetParam($param, $val);
  }

  CloseConnection($link);
}
forward($referer);
?>
