<?php
require "systemvars.php";
require "configvalues.php";
require "sortfunctions.php";
require "security.php";

if (!CheckAdmin($User->usertype)) {
  forward("index.php?sid=".rawurlencode($SID));
}

require "templates/render.php";
renderPage("admin", array(
  "title" => "Logfile viewer",
  "heading" => "Logfile viewer",
  "content" => "log-viewer.php",
  "show_prize" => FALSE
));
?>
