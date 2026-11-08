<?php
require "systemvars.php";
require "configvalues.php";
require "sortfunctions.php";
require "security.php";
require "lang/".GetLangFile();
require "templates/render.php";

$link = OpenConnection();
$query = "SELECT COUNT(*) AS admin_count FROM $dbaseUserData WHERE lid=? AND usertype=?";
$result = DBExecute($query, array((int)$leagueID, 4), $link);
if ($result === FALSE) {
  die("Unable to count admin users: ".mysqli_error($link));
}
$row = mysqli_fetch_assoc($result);
CloseConnection($link);

renderPage("setup", array(
  "title" => "Create admin user",
  "heading" => "Create the admin user",
  "content" => "admin-create-admin-user.php",
  "scripts" => array("js/admin-user-validation.js"),
  "data" => array("numAdminUsers" => (int)$row["admin_count"])
));
?>
