<?php
require "systemvars.php";
require "templates/render.php";

renderPage("standalone", array(
  "title" => "Prediction Football Release Notes",
  "heading" => "Release Notes",
  "content" => "release-notes.php",
  "show_fixture" => FALSE
));
?>
