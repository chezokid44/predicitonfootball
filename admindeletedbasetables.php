<?php
require "systemvars.php";
require "templates/render.php";

renderPage("setup", array(
  "title" => "Delete database tables",
  "heading" => "Delete the tables and database",
  "content" => "admin-delete-tables.php"
));
?>
