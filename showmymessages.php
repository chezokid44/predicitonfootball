<?php 
/*********************************************************
 * Author: John Astill
 * Date  : 30th Jan 2003
 * File  : showmymessages.php
 * Desc  : Display the messages sent by and received by
 *       : the user.
 ********************************************************/
require "systemvars.php";
require "configvalues.php";
require "sortfunctions.php";
require "security.php";
require "templates/render.php";

if ($User->loggedIn != TRUE) {
  $ip = $_SERVER["REMOTE_ADDR"];
  $port = $_SERVER["REMOTE_PORT"];
  $referer = $_SERVER["HTTP_REFERER"];
  
  logMsg("Attempt to view messages by someone not logged in. Could be an attempt to break in. IP Address $ip port $port referring page $referer");
  forward("index.php"); 
  exit;   
}

ClearErrorCode();
$page = array("title" => "$My_Messages_For $User->username", "content" => "my-messages.php", "login_panel" => FALSE);
renderPage("public", $page);


