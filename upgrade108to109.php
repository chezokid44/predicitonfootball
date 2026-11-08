<?php
/*********************************************************
 * Author: John Astill (c)
 * Date  : 14 Sept 2003
 * File  : upgrade108to109.php
 * This is a utility for upgrading the database from version 1.08
 * to version 1.09 
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

   // Set the config parameter for viewing 24 hour clock
  SetConfigParam(3,"USE24HR","Show the times as the 24 hour clock. TRUE or FALSE.","TRUE","N");

   // Set the config parameter for allowing users to upload icons
  SetConfigParam(3,"UPLOADICONS","Allow the users to upload icons, TRUE or FALSE.","TRUE","N");
  SetConfigParam(3,"ICONSIZE","Maximum file size in KB of the icon","50","N");

   // Add the current version to the config table
   $query = "replace into $dbaseConfigData (lid,param,value,descr,ro) values ($leagueID,\"VERSION\",".VERSION.",\"Current Version\",'Y')";
   $result = mysql_query($query) or die("Unable to upgrade table entries: $query<br>".mysql_error());
   
   CloseConnection($link);

   echo "The tables have been updated to version 1.09, please remove this file from your server!!!!!<br>";
?>
