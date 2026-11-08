<?php
require "systemvars.php";
require "templates/render.php";

renderPage("setup", array(
  "title" => "Import league data",
  "heading" => "Import data from a previous league version",
  "content" => "admin-import.php"
));
?>
