<?php 
/*********************************************************
 * Author: John Astill (c)
 * Date  : 30th Jan 2003
 * File  : sessiondatacookies.php
 * Desc  : Data that is required during a logged in 
 *       : session. Matters not if the session uses 
 *       : cookies or PHP sessions.
 *       : This file is not used but provides a template
 *       : should a cookie implementation be required.
 ********************************************************/

/*************************************************************
** User parameters used in sessions. This
** needs to be global
*************************************************************/
$User = new User;
$a = stripslashes($HTTP_COOKIE_VARS["USER"]);
$User = unserialize($a);
$userid = $User->userid;
$username = $User->username;

/*************************************************************
** Error Code
** needs to be global
*************************************************************/
$ErrorCode = stripslashes($HTTP_COOKIE_VARS["ERRORCODE"]);

function RegisterUser($User) {
  // Try to register up to 3 times
  $errcnt = 0;
  $s = serialize($User);
  while (!setcookie("USER",$s,time()+3600)) {
    $errmsg = "Unable to register User variable during login for $userid : ".$User;
    LogMsg($errmsg);
    if ($errcnt >= 3) {
      // Try to register manually
      @mail("john@astill.org",$PredictionLeagueTitle." Error",$errmsg);
      break;
    }
    $errcnt++;
  }
  if ($errcnt == 0) {
    LogMsg("Successfully registered $username during login");
  }
}

?>
