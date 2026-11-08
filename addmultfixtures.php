<?php
/*********************************************************
 * Author: John Astill (c)
 * Date  : 3rd January 2002
 * File  : addmultfixtures.php
 * Desc  : Add multiple fixtures to the league.
 *********************************************************/
  require "systemvars.php";
  require "configvalues.php";
  require "sortfunctions.php";
  require "security.php";

  // Make sure the user trying to add the fixture is admin
  // an admin or above.
  if (!CheckAdmin($User->usertype)) {
    // Forward to somewhere
    LogMsg("Attempt to add multiple fixtures by User $User->userid with insufficient permissions from $REMOTE_ADDR:$REMOTE_PORT.");
    forward("index.php");
  }

  $link = OpenConnection();
  for ($i=0; $i<$NumMultFixts; $i++) {

    $date = $HTTP_POST_VARS["DATE$i"];
    $time = $HTTP_POST_VARS["TIME$i"];
    if ($time == "") {
      $time = "00:00:00";
    }
    $datetime="$date $time";
    // Now allow for timezone offset.
    $datetime = RevTimeZoneOffsetNo24Hour($datetime);

    $hometeam = $HTTP_POST_VARS["HOMETEAM$i"];
    $awayteam = $HTTP_POST_VARS["AWAYTEAM$i"];

    if ($datetime == "" or $hometeam == "" or $awayteam == "") {
      continue;
    }

    // If the post is a success, go to the adminenterresult page.
    $query = "insert into $dbaseMatchData (lid,matchdate,hometeam,awayteam) VALUES (?,?,?,?)";
    $result = DBExecute($query, array((int)$leagueID, $datetime, $hometeam, $awayteam));
    if ($result === FALSE) {
      die("Unable to add fixtures: ".mysqli_error($link));
    }

    // Log the addition
    LogMsg("Added fixture $date $time $hometeam $awayteam.\n$query");
  }
  CloseConnection($link);

  /* Redirect browser to PHP web site */
  header("Location: adminenterfixture.php"); 
  /* Make sure that code below does not get executed when we redirect. */
  exit; 
?>
