<?php 
/*********************************************************
 * Author: John Astill (c)
 * Date  : 9th December
 * File  : security.php
 * Desc  : usertypes:
 *       :     1 - Normal User
 *       :     2 - Priveleged User
 *       :     4 - Admin User
 *       :     8 - Root User
 *
 ********************************************************/

  require "encryptionclass.php";
  
  function login($username, $pwd) {
    global $SID;
    $location = "showmypredictions.php?sid=$SID"; 
    loginwithtarget($username,$pwd,$location);
  }
  
  function loginwithtarget($username, $pwd, $location) {
    global $User, $SID;

    // If the user login has timed out, forward to the
    // index.
    if (CheckUserLogin($username, $pwd) == FALSE) {
      /* Redirect browser to Prediction Index web site */
      ErrorRedir("Username or password invalid","index.php"); 
      /* Make sure that code below does not get executed when we redirect. */
      exit; 
    }

    RegisterUser($User);

    /* Redirect browser to user predictions web site */
    header("Location: $location"); 
    /* Make sure that code below does not get executed when we redirect. */
    exit; 
  }

 /********************************************************
  * Check if the given user has admin priveleges.
  * @param perms the users current permissions.
  *******************************************************/
  function CheckAdmin($perms) {
    global $dbaseUserData;

    $NormalUser = 1;
    $PrivelegedUser = 2;
    $AdminUser = 4;
    $RootUser = 8;
    
    return $perms >= $AdminUser;
  }

 /********************************************************
  * Check if the given user is logged in.
  * @param user the user to check.
  * @param pwd the password of the user to check.
  *******************************************************/
  function CheckUserLogin($username, $pwd) {
    // Needs global include SystemVars.php
    global $dbaseUserData, $User,$leagueID;

    // if encryption enabled encrypt the password
    $encr = new Encryption($pwd);
    $pwd = $encr->Encrypt($pwd);

    // Default the login to false.
    $User = new User;
    $User->loggedIn = FALSE;
    $User->usertype = 1;

    $link = OpenConnection();
    if ($link == FALSE) {
      return FALSE;
    }

    $userquery = "SELECT * FROM $dbaseUserData where username = ? and lid = ?";
    $userresult = DBExecute($userquery, array($username, (int)$leagueID));
    if ($userresult === FALSE) {
      CloseConnection($link);
      return FALSE;
    }
    if ($userline = mysql_fetch_array($userresult, MYSQL_ASSOC)) {
      if ($pwd == $userline["password"]) {
        // The passwords are equal. Log the user in, and update the 
        // data.
        $User->userid =  $userline["userid"];
        $User->username = stripslashes($username);
        $User->emailaddr = $userline["email"];
        $User->icon = $userline["icon"];
        $User->usertype = $userline["usertype"];
        $User->createdate = $userline["since"];
        $User->lang = $userline["lang"];
        $User->dflths = $userline["dflths"];
        $User->dfltas = $userline["dfltas"];
        $User->lang = $userline["lang"];
        $User->auto = $userline["auto"];
        $User->loggedIn = TRUE;

        return TRUE;
      }
    }
    CloseConnection($link);
    return FALSE;
  }
?>
