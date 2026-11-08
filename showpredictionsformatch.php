<?php
require "systemvars.php";
require "configvalues.php";
require "sortfunctions.php";
require "security.php";
require "templates/render.php";

$matchid = $HTTP_GET_VARS["matchid"];
$date = $HTTP_GET_VARS["date"];
renderPage("public", array("title" => "Match predictions", "content" => "match-predictions.php"));
?>
