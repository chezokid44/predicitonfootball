<?php
require "systemvars.php";
require "configvalues.php";
require "sortfunctions.php";
require "security.php";
require "lang/".GetLangFile();
require "templates/render.php";

renderPage("public", array("title" => $Profile_Icon_Selection, "content" => "user-icons.php"));
?>
