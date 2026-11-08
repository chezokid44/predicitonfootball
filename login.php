<?php 
/*********************************************************
 * Author: John Astill
 * Date  : 10th December
 * File  : login.php
 * Desc  : Create the current users session data.
 ********************************************************/
  require "systemvars.php";
  require "configvalues.php";
  require "security.php";

  /*******************************************
  * Validate the users login. If it is 
  * correct log them in and forward to the
  * Main Page.
  *******************************************/
  $username = $HTTP_POST_VARS["LOGIN"];
  $pwd = $HTTP_POST_VARS["PWD"];

  login($username, $pwd);
?>
