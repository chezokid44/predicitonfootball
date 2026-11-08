<?php 
/*********************************************************
 * Author: John Astill
 * Date  : 10th December 2001 (c)
 * File  : showuserpredictions.php
 * Desc  : Display the predictions for the currently logged
 *       : in user. If the cookie does not exist, check
 *       : the GET parameters. If no user can be found
 *       : prompt for one.
 *       : If the user is found, then display their data.
 ********************************************************/
require "systemvars.php";
require "configvalues.php";
require "sortfunctions.php";
require "security.php";
require "templates/render.php";

  /*******************************************************
  * Check the user id and password from the cookie.
  *******************************************************/
  $user = $HTTP_GET_VARS["user"];
renderPage("public", array("title" => "Results", "content" => "user-predictions.php"));
