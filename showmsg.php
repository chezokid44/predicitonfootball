<?php 
/*********************************************************
 * Author: John Astill
 * Date  : 30th Jan 2003
 * File  : showmsg.php
 * Desc  : Display the message indicated
 *       : the user.
 ********************************************************/
require "systemvars.php";
require "configvalues.php";
require "sortfunctions.php";
require "security.php";
require "templates/render.php";

ClearErrorCode();

if ($User->loggedIn != TRUE) {
  $ip = $_SERVER["REMOTE_ADDR"];
  $port = $_SERVER["REMOTE_PORT"];
  $referer = $_SERVER["HTTP_REFERER"];
  
  logMsg("Attempt to view message by someone not logged in. Could be an attempt to break in. IP Address $ip port $port referring page $referer");
  forward("index.php"); 
  exit;   
}

$msgid = $HTTP_GET_VARS["msgid"];
$fromshowall = $HTTP_GET_VARS["fs"];
$page = array("title" => "Message", "content" => "message-detail.php", "login_panel" => FALSE);
renderPage("public", $page);


