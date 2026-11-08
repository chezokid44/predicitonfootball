<?php
require "systemvars.php";
require "templates/render.php";

renderPage("setup", array(
  "title" => "Create database tables",
  "heading" => "Database and Table Administration [LeagueID $leagueID]",
  "content" => "admin-create-tables.php"
));
?>
