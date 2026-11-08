<?php
/*********************************************************
 * Author: John Astill (c) 2002
 * Date  : 3rd January 2002
 * File  : modifyfixture.php
 *       : Modifies the given fixture in the database.
 *       : Makes sure both the Match table and Prediction
 *       : table are updated.
 *********************************************************/
  require "systemvars.php";
  require "configvalues.php";
  require "sortfunctions.php";
  require "security.php";

  if ($User->loggedIn != TRUE) {
    $ip = $_SERVER["REMOTE_ADDR"];
    $port = $_SERVER["REMOTE_PORT"];
    $referer = $_SERVER["HTTP_REFERER"];
  
    logMsg("Attempt to modify a fixture by someone not logged in. Could be an attempt to break in. IP Address $ip port $port referring page $referer");
    forward("index.php"); 
    exit;   
  }

  // Security check, probably not necessary.
  if (!CheckAdmin($User->usertype)) {
    LogMsg("User $User->userid tried to modify fixtures without correct authority.\n");
    ErrorRedir("Only admin can do that!","index.php?sid=$SID");
  }

  $date = $HTTP_POST_VARS["DATE"];
  $time = $HTTP_POST_VARS["TIME"];
  if ($time == "") {
    $time = "00:00:00";
  }
  $datetime="$date $time";

  // Now allow for timezone offset.
  $datetime = RevTimeZoneOffset($datetime);

  $hometeam = $HTTP_POST_VARS["HOMETEAM"];
  $awayteam = $HTTP_POST_VARS["AWAYTEAM"];

  if ($hometeam == "") {
    ErrorRedir("The fixture was not correct, please enter a hometeam.","adminenterfixture.php?sid=$SID");
    exit;
  }

  if ($awayteam == "") {
    ErrorRedir("The fixture was not correct, please enter an awayteam.","adminenterfixture.php?sid=$SID");
    exit;
  }

  $matchid = $HTTP_POST_VARS["MATCHID"];

  // If the post is a success, go to the adminenterresult page.
  $link = OpenConnection();
  
  // change the fixture in the match data.
  $query = "update $dbaseMatchData set matchdate=?, hometeam=?, awayteam=? where matchid=? and lid=?";
  $result = DBExecute($query, array($datetime, $hometeam, $awayteam, (int)$matchid, (int)$leagueID));
  if ($result === FALSE) {
    die("Unable to change fixture: ".mysqli_error($link));
  }

  // Log the changes
  $rows = mysql_affected_rows();
  LogMsg("Modified fixture.\n$rows affected.");
  
  // Close the connection with the database.
  CloseConnection($link);

  /* Redirect browser to PHP web site */
  forward("adminenterfixture.php?sid=$SID"); 
?>
