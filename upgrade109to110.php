<?php
/*********************************************************
 * Author: John Astill (c)
 * Date  : 19th Nov 2003
 * File  : upgrade109to110.php
 * This is a utility for upgrading the database from version 1.09
 * to version 1.10 
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

   // Set the config parameter for password encryption. The default value for upgrade is FALSE.
   SetConfigParam(3,"PASSWORDENCRYPT","Enable the encryption of user passwords. If you are enabling then the users must use the forgot password function to get their new password. TRUE or FALSE.","FALSE","N");

   // Add the current version to the config table
   $query = "replace into $dbaseConfigData (lid,param,value,descr,ro) values ($leagueID,\"VERSION\",".VERSION.",\"Current Version\",'Y')";
   $result = mysql_query($query) or die("Unable to upgrade table entries: $query<br>".mysql_error());
   
   CloseConnection($link);

   echo "The tables have been updated to version 1.10, please remove this file from your server!!!!!<br>";
?>
