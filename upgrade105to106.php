<?php
/*********************************************************
 * Author: John Astill (c)
 * Date  : 7th July 2003
 * File  : upgrade105to106.php
 * This is a utility for upgrading the database from version 1.05
 * to version 1.06 
 *
 * This file is required because the current config parameters are 
 * restricted to 32 characters.
 *
 * 1 Backup your current files, especially your systemvars.php and
 *   common.css
 * 2 Backup your database data. This utility should not cause any problems
 *   but it does not hurt to play safe.
 * 3 Install the new files in the same directory as your original files
 * 4 Modify systemvars.php and common.css to match your configuration.
 * 5 Open this file in your browser to make the database changes.
 * 6 Remove this file from the server.
 *********************************************************/
   require "systemvars.php";
   require "configvalues.php";
   require "encryptionclass.php";
   $link = OpenConnection();

   // Alter the config data table. This will not affect the contents.
   $query = "alter table $dbaseConfigData change column value value varchar(96)";
   $result = mysql_query($query) or die("Unable to upgrade table entries: $query<br>".mysql_error());

   // Alter the config data table. This will not affect the contents.
   $query = "alter table $dbaseConfigData add column (ro enum('Y','N') default 'N')";
   $result = mysql_query($query) or die("Unable to upgrade table entries: $query<br>".mysql_error());
   
   // Add the current version to the config table
   $query = "replace into $dbaseConfigData (lid,param,value,descr,ro) values ($leagueID,\"VERSION\",".VERSION.",\"Current Version\",'Y')";
   $result = mysql_query($query) or die("Unable to upgrade table entries: $query<br>".mysql_error());
   
   CloseConnection($link);

   echo "The tables have been updated to version 1.06, please remove this file from your server!!!!!<br>";
?>
