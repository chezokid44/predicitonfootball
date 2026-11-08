<?php 
/*********************************************************
 * Author: John Astill (c)
 * Date  : 9th December
 * File  : index.php
 ********************************************************/
  require "systemvars.php";
  require "configvalues.php";
  require "sortfunctions.php";
  require "security.php";
  require "templates/render.php";

  $page = array(
    "title" => $PredictionLeagueTitle,
    "content" => "standings.php",
    "notice" => $ErrorCode
  );
  ClearErrorCode();
  renderPage("public", $page);

