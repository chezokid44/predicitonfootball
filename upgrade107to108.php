<?php
/*********************************************************
 * Author: John Astill (c)
 * Date  : 31st July 2003
 * File  : upgrade107to108.php
 * This is a utility for upgrading the database from version 1.07
 * to version 1.08 
 *
 * 1 Backup your current files, especially your systemvars.php and
 *   common.css
 * 2 Backup your database data. This utility should not cause any problems
 *   but it does not hurt to play safe.
 * 3 Install the new files in the same directory as your original files
 * 4 Modify systemvars.php and common.css to match your configuration.
 * 5 Open this file in your browser to make the database changes.
 * 6 Remove this file from the server and any other file where the
 *   name starts with "upgrade" and ends with "php".
 *********************************************************/
   require "systemvars.php";
   require "configvalues.php";
   require "encryptionclass.php";
   $link = OpenConnection();

   // Add the current version to the config table
   $query = "replace into $dbaseConfigData (lid,param,value,descr,ro) values ($leagueID,\"VERSION\",\"1.08\",\"Current Version\",'Y')";
   $result = mysql_query($query) or die("Unable to upgrade table entries: $query<br>".mysql_error());
   
   CloseConnection($link);

   echo "The tables have been updated to version 1.08, please remove this file from your server!!!!!<br>";
?>
