<?php 
/*********************************************************
 * Author: John Astill
 * Date  : 10th December
 * File  : logout.php
 * Desc  : Log the current user out. Removes any
 *       : session info.
 ********************************************************/
  require "systemvars.php";

  UnregisterUser();

  /* Redirect browser to PHP web site */
  header("Location: index.php?sid="); 
  /* Make sure that code below does not get executed when we redirect. */
  exit; 
?>
