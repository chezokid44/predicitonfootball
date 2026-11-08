<?php
require "systemvars.php";
require "configvalues.php";
require "sortfunctions.php";
require "security.php";
require "lang/".GetLangFile();

if ($User->loggedIn != TRUE) {
  forward("index.php");
  exit;
}

$tgtuserid = isset($HTTP_GET_VARS["userid"]) ? (int)$HTTP_GET_VARS["userid"] : 0;
$subject = isset($HTTP_GET_VARS["subj"]) ? $HTTP_GET_VARS["subj"] : "";

function getUsername($userid) {
  global $dbaseUserData, $leagueID;
  $link = OpenConnection();
  $query = "SELECT username FROM $dbaseUserData WHERE lid=? AND userid=? LIMIT 1";
  $result = DBExecute($query, array((int)$leagueID, (int)$userid), $link);
  if ($result === FALSE) {
    die("Unable to look up recipient: ".mysqli_error($link));
  }
  $user = mysqli_fetch_assoc($result);
  CloseConnection($link);
  return $user ? $user["username"] : "Unknown";
}

$tgtusername = getUsername($tgtuserid);
require "templates/render.php";
renderPage("public", array(
  "title" => $Create_Msg,
  "content" => "compose-message.php",
  "data" => array("tgtuserid" => $tgtuserid, "tgtusername" => $tgtusername, "subject" => $subject)
));
?>
