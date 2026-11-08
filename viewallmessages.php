<?php 
/*********************************************************
 * Author: John Astill
 * Date  : 30th Jan 2003
 * File  : viewallmessages.php
 * Desc  : Show all the messages for the admin.
 ********************************************************/
require "systemvars.php";
require "configvalues.php";
require "sortfunctions.php";
require "security.php";
require "templates/render.php";

// Make sure that the user has admin priveleges.
if (!CheckAdmin($User->usertype)) {
  forward("index.php?sid=$SID");
}

ClearErrorCode();
$page = array("title" => "View all messages", "content" => "all-messages.php", "login_panel" => FALSE);
renderPage("admin", $page);


