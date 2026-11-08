<?php 
/*********************************************************
 * Author: John Astill
 * Date  : 22nd September 2003 (c)
 * File  : uploadicon.php
 * Desc  : Upload an icon to the icons directory
 ********************************************************/
require "systemvars.php";
require "configvalues.php";
require "sortfunctions.php";
require "security.php";

if ($User->loggedIn != TRUE) {
  $ip = $_SERVER["REMOTE_ADDR"];
  $port = $_SERVER["REMOTE_PORT"];
  $referer = $_SERVER["HTTP_REFERER"];
  
  logMsg("Attempt to upload icon by someone not logged in. Could be an attempt to break in. IP Address $ip port $port referring page $referer");
  forward("index.php"); 
  exit;   
}

$hdir = "htdocs/PredictionLeague109a/icons/";
$name = "";

// Make sure that the posted file has been uploaded.
if ((array_key_exists("ICONNAME",$_FILES)) && (is_uploaded_file($_FILES["ICONNAME"]['tmp_name']) == TRUE)) {
  $filesize = $_FILES["ICONNAME"]['size'];
  $origname = $_FILES["ICONNAME"]['name'];
  if ( ($MaxIconFileSize*1024) < $filesize) {
    ErrorRedir("File size of $origname is too large [$filesize bytes]. Max file size is $MaxIconFileSize Kb","showmyprofile.php?sid=$SID");
  } else {
    $origname = $_FILES["ICONNAME"]['name'];
    $name = $origname;
    
    // Make sure that the target file does not exist.
    if (file_exists("icons/".$name)) {
      ErrorRedir("Unable to copy the file $origname into the icons directory as a file with that name already exists.","showmyprofile.php?sid=$SID");
    }
    
    if (move_uploaded_file( $_FILES["ICONNAME"]['tmp_name'],$hdir.$name) == FALSE) {
      ErrorRedir("Unable to copy the file $origname into the icons directory.","showmyprofile.php?sid=$SID");
    }
  }
} else {
  $ip = $_SERVER['REMOTE_ADDR'];
  $port = $_SERVER['REMOTE_PORT'];
  LogMsg("Possible upload attack. Someone is attempting to upload files directly to this script from IP $ip Port $port."); 
}

forward("userselecticon.php?sid=$SID");
?>