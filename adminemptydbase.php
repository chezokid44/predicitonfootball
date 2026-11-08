<?php
require "systemvars.php";
require "templates/render.php";

renderPage("setup", array(
  "title" => "Empty database tables",
  "heading" => "Empty the tables",
  "content" => "admin-empty-tables.php"
));
?>
