<?php 
/*********************************************************
 * Author: John Astill
 * Date  : 19th January 2002
 * File  : helpindex.php
 ********************************************************/
  require "systemvars.php";
  require "configvalues.php";
  require "sortfunctions.php";
  require "security.php";
  require ("lang/".GetLangFile());
  require "templates/render.php";
  renderPage("public", array("title" => $Prediction_League_Help_topic, "content" => "help-index.php"));

