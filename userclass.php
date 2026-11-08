<?php 
/*********************************************************
 * Author: John Astill (c)
 * Date  : 18th July 2003
 * File  : userclass.php
 * Desc  : Class representing a user/player in the 
 *       : prediction league.
 ********************************************************/
class User {
  // The id number the user is identified by.
  var $userid;

  // The name the user is currently using.
  var $username;

  // The users password.
  var $pwd;

  // The email address of the user.
  var $emailaddr;

  // The URL of the icon to be used by the user.
  var $icon;

  // The priveleges of the user.
  var $usertype;

  // the current language the user is logged in with
  var $lang;

  // The day the user created the record.
  var $createdate;

  // Flag to indicate whether the user is logged in.
  var $loggedIn;

  // Default scores used when no prediction is made
  var $dflths;
  var $dfltas;

  // If the user has auto predict enabled
  var $auto;

  // Contructor for the user class.
  function User() {
    $this->userid = "";
    $this->username = "";
    $this->pwd = "";
    $this->emailaddr = "";
    $this->icon = "";
    $this->usertype = "0";
    $this->createdate = "";
    $this->lang = "english";
    $this->loggedIn = FALSE;
    $this->dflths = 0;
    $this->dfltas = 0;
    $this->auto = "N";
  }
}
?>
