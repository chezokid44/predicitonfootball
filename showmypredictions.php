<?php 
/*********************************************************
 * Author: John Astill
 * Date  : 10th December
 * File  : showmypredictions.php
 * Desc  : Display the predictions for the currently logged
 *       : in user. If the cookie does not exist for the
 *       : user,show them a login page.
 ********************************************************/
require "systemvars.php";
require "configvalues.php";
require "sortfunctions.php";
require "security.php";
require "templates/render.php";

if ($User->loggedIn != TRUE) {
  $ip = $_SERVER["REMOTE_ADDR"];
  $port = $_SERVER["REMOTE_PORT"];
  $referer = $_SERVER["HTTP_REFERER"] ?? "";
   logMsg("Attempt to view user predictions by someone not logged in. Could be an attempt to break in. IP Address $ip port $port referring page $referer");
   forward("index.php"); 
   exit;   
}

ClearErrorCode();
$page = array(
  "title" => "$My_Predictions_For $User->username",
  "content" => "my-predictions.php",
  "scripts" => array("js/predictions.js"),
  "login_panel" => FALSE
);
renderPage("public", $page);


