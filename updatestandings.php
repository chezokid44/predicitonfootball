<?php
/*********************************************************
 * Author: John Astill
 * Date  : 10th December 2001 (c)
 * File  : updatestandings.php
 * Desc  : Update the standings table
 ********************************************************/
require "systemvars.php";
require "configvalues.php";
require "sortfunctions.php";
require "security.php";
  
  UpdateStandingsTable();
  forward("index.php");
?>
