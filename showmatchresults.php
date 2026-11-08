<?php
require "systemvars.php";
require "configvalues.php";
require "sortfunctions.php";
require "security.php";
require "templates/render.php";

renderPage("public", array("title" => "Results", "content" => "match-results.php"));
?>
