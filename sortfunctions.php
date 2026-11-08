<?php 
/*********************************************************
 * Author: John Astill (c) 2002
 * Date  : 9th December 2001
 * File  : sortfunctions.php
 ********************************************************/

require "gamestatsclass.php";
require "datefunctions.php";
require "statsfunctions.php";
require ("lang/".GetLangFile());

   /******************************************************
   * Compare the two classes passed.
   * The order of attributes compared is as follows:
   *   Points
   *   Goal Difference
   *   Games Played
   * @param a
   * @param b
   * @return <0 if a < b
   *          0
   *         >0
   ******************************************************/
   function compare($a, $b) {
     $lessthan = -1;
     $morethan = 1;

     if ($a->points < $b->points) {
       return $morethan;
     }
     if ($a->points > $b->points) {
       return $lessthan;
     }

     // Points must be equal

     if ($a->predictions == 0 && $b->predictions != 0) {
       return $morethan;
     }

     if ($a->predictions != 0 && $b->predictions == 0) {
       return $lessthan;
     }

     if ($a->diff < $b->diff) {
       return $morethan;
     }
     if ($a->diff > $b->diff) {
       return $lessthan;
     }

     if ($a->predictions < $b->predictions) {
       return $morethan;
     }
     if ($a->predictions > $b->predictions) {
       return $lessthan;
     }


     // Goal diff must be equal

     // a draw
     return 0;
   }

  /////////////////////////////////////////////
  // Sorting functions for the prediction data.
  // These functions also cause the output to
  // be written.
  /////////////////////////////////////////////
  
  /****************************************************
   * Search the Match table for the next game.
   ***************************************************/
  function getNextGame() {
    global $dbaseMatchData, $SID, $Next_Match,$No_Matches_Scheduled,$leagueID;

    $todaysdate = date("Y-m-d H:i:s");
    $tz = date("T");
    
    // Search for the next date in the dbase.
    $link = OpenConnection();
    if ($link == FALSE) {
      ErrorNotify("Unable to open connection");
      exit;
    }

    // If the matches are ordered, then the first should be the next game.
    $query = "select * from $dbaseMatchData where lid=? and matchdate>=? order by matchdate";
    $result = DBExecute($query, array((int)$leagueID, $todaysdate));
    if ($result === FALSE) {
      die("Unable to load next match: ".mysqli_error($link));
    }

    $count = mysql_num_rows($result);
    if ($count == 0) {
      $nextmatch = "<b>$No_Matches_Scheduled</b>";
    } else {
      $line = mysql_fetch_array($result, MYSQL_ASSOC);
      $matchid = $line["matchid"];
      $matchdate = $line["matchdate"];
      $textdate = convertDatetimeToScreenDate($matchdate);
      $hometeam = stripslashes($line["hometeam"]);
      $awayteam = stripslashes($line["awayteam"]);
      $nextmatch = "<b>$Next_Match: <a href=\"showpredictionsformatch.php?sid=$SID&matchid=$matchid&date=$matchdate\">$hometeam v $awayteam </a></b> $textdate";
    }

    CloseConnection($link);

    return $nextmatch;
  }

  /**********************************************************
   * Get and Display the user predictions.
   * Display the predictions from the users prediction table
   * entries and also any games from the MatchTable that
   * have no entry in the Users Prediction data.
   **********************************************************/
  function GetUserPredictions($user) {
    global $dbasePredictionData, $dbaseMatchData, $reverseUserPredictions, $SID, $User, $Predictions, $Predict, $leagueID;
    global $P, $W, $POS, $D, $D, $L, $Away, $Home, $F, $A, $GD, $User_Name,$PTS,$Date,$Result, $Predictions_For;

    $link = OpenConnection();
    if ($link == FALSE) {
      echo "Unable to connect to the dbase";
      return;
    }
    
    // At this point this is only used in the table header.
    $date = GetDateFromDatetime(date("Y-m-d"));
    
    // Select the fixtures from both tables. 
  $userquery = "SELECT $dbaseMatchData.matchdate,$dbaseMatchData.hometeam,$dbaseMatchData.awayteam,$dbaseMatchData.homescore,$dbaseMatchData.awayscore,$dbasePredictionData.homescore,$dbasePredictionData.awayscore, $dbaseMatchData.matchid FROM $dbaseMatchData LEFT JOIN $dbasePredictionData ON $dbasePredictionData.matchid=$dbaseMatchData.matchid AND userid = ? AND $dbaseMatchData.lid=$dbasePredictionData.lid where $dbaseMatchData.lid=? ORDER BY $dbaseMatchData.matchdate"; 

    if ($reverseUserPredictions == "TRUE") {
      $userquery = "$userquery desc";
    }
    $userresult = DBExecute($userquery, array((int)$user, (int)$leagueID));
    if ($userresult === FALSE) {
      die("Unable to load predictions: ".mysqli_error($link));
    }

    // Display the username as a header.
?>
    <form method="POST" action="updatemypredictions.php?sid=<?php echo rawurlencode($SID); ?>" data-prediction-form>
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION["csrf_token"], ENT_QUOTES, "UTF-8"); ?>">
    <table class="STANDTB">
    <tr>
    <td class="TBLHEAD" colspan="8" align="center">
    <font class="TBLHEAD">
    <?php echo "$Predictions_For $User->username [$date]"?>
    </font>
    </td>
    </tr>
    <tr>
    <td colspan="8" class="TBLROW" align="CENTER">
    <input type="SUBMIT" name="$Predict" value="Predict">
    </td>
    </tr>
    <tr>
    <td align="center" class="TBLHEAD"><font class="TBLHEAD"><?php echo $Date; ?></font></td>
    <td align="center" class="TBLHEAD"><font class="TBLHEAD"><?php echo $Home; ?></font></td>
    <td align="center" class="TBLHEAD"><font class="TBLHEAD"><?php echo $F; ?></font></td>
    <td align="center" class="TBLHEAD"><font class="TBLHEAD">&nbsp;</font></td>
    <td align="center" class="TBLHEAD"><font class="TBLHEAD"><?php echo $A; ?></font></td>
    <td align="center" class="TBLHEAD"><font class="TBLHEAD"><?php echo $Away; ?></font></td>
    <td align="center" class="TBLHEAD"><font class="TBLHEAD"><?php echo $Result; ?></font></td>
    <td align="center" class="TBLHEAD"><font class="TBLHEAD"><?php echo $PTS; ?></font></td>
    </tr>
<?php
    $count = 1;
    // First loop. Used to get all the users.
    while ($userline = mysql_fetch_array($userresult, MYSQL_BOTH)) {
      // For each user display all their predictions.
      // against the actual result.
      $hometeam = stripslashes($userline[1]);
      $awayteam = stripslashes($userline[2]);
      $homeresult = $userline[3];
      $awayresult = $userline[4];
      $homescore = $userline[5];
      $awayscore = $userline[6];
      $matchid = $userline[7];
      $date = $userline["matchdate"];
      $datestr = GetDateFromDateTime($date);
      $timestr = GetTimeFromDateTime($date);

      echo "<tr>\n";
      echo "<td class=\"TBLROW\">\n";
      echo "<font class=\"TBLROW\">\n";
      echo "<a href=\"showpredictionsformatch.php?sid=$SID&matchid=$matchid&date=$date\">$datestr</a>\n";
      echo "<br><small>$timestr</small>\n";
      echo "</font>\n";
      echo "</td>\n";

      echo "<td class=\"TBLROW\"><font class=\"TBLROW\">$hometeam</font></td>\n";

      // User has not made a prediction
      if ($homescore == null) {
        $homescore = "";
        $awayscore = "";
      } 
      if (CompareDatetime($date) > 0) {
        $escapedHomeScore = htmlspecialchars((string)$homescore, ENT_QUOTES, "UTF-8");
        $escapedAwayScore = htmlspecialchars((string)$awayscore, ENT_QUOTES, "UTF-8");
        $escapedHomeTeam = htmlspecialchars($hometeam, ENT_QUOTES, "UTF-8");
        $escapedAwayTeam = htmlspecialchars($awayteam, ENT_QUOTES, "UTF-8");

        echo "<td class=\"TBLROW\" align=\"CENTER\">\n";
        echo "<font class=\"TBLROW\">\n";
        echo "<input type=\"number\" name=\"predictions[$matchid][home_score]\" min=\"0\" max=\"65535\" step=\"1\" inputmode=\"numeric\" aria-label=\"$escapedHomeTeam predicted score\" data-prediction-match=\"$matchid\" data-prediction-side=\"home_score\" value=\"$escapedHomeScore\">\n";
        echo "</font>\n";
        echo "</td>\n";

        echo "<td class=\"TBLROW\" align=\"CENTER\"><font class=\"TBLROW\">v</font></td>\n";
        echo "<td class=\"TBLROW\" align=\"CENTER\"><font class=\"TBLROW\"><input type=\"number\" name=\"predictions[$matchid][away_score]\" min=\"0\" max=\"65535\" step=\"1\" inputmode=\"numeric\" aria-label=\"$escapedAwayTeam predicted score\" data-prediction-match=\"$matchid\" data-prediction-side=\"away_score\" value=\"$escapedAwayScore\"></font></td>\n";
        echo "<td class=\"TBLROW\"><font class=\"TBLROW\">$awayteam</font></td>\n";
        echo "<td class=\"TBLROW\" align=\"CENTER\">\n";
        echo "<font class=\"TBLROW\">\n";
        echo "-\n";
        echo "</font>\n";
        echo "</td>\n";
        echo "<td align=\"CENTER\" class=\"TBLROW\"><font class=\"TBLROW\"></font></td>\n";
      } else {
        $points = 0;
        if ($homescore != "" and $homeresult != "") {
          $points = GameStats::CalculatePoints($homescore, $awayscore, $homeresult, $awayresult);
        }
        echo "<td class=\"TBLROW\" align=\"CENTER\"><font class=\"TBLROW\">$homescore</font></td>\n";
        echo "<td class=\"TBLROW\" align=\"CENTER\"><font class=\"TBLROW\">v</font></td>\n";
        echo "<td class=\"TBLROW\" align=\"CENTER\"><font class=\"TBLROW\">$awayscore</font></td>\n";
        echo "<td class=\"TBLROW\"><font class=\"TBLROW\">$awayteam</font></td>\n";
        echo "<td align=\"CENTER\" class=\"TBLROW\"><font class=\"TBLROW\">$homeresult - $awayresult</font></td>\n";
        echo "<td align=\"CENTER\" class=\"TBLROW\"><font class=\"TBLROW\">$points</font></td>\n";
      }

      echo "</tr>\n";
      $count++;
    }
    echo "<tr>\n";
    echo "<td colspan=\"8\" class=\"TBLROW\" align=\"CENTER\">\n";
    echo "<input type=\"SUBMIT\" name=\"$Predict\" value=\"Predict\">\n";
    echo "</td>\n";
    echo "</tr>\n";
    echo "<tr><td colspan=\"8\"><div class=\"prediction-status\" data-prediction-status role=\"status\" aria-live=\"polite\"></div></td></tr>\n";
    echo "</table>\n";
    echo "</form>\n";

    CloseConnection($link);
  }

  /**********************************************************
   * Display the user predictions.
   * Display the predictions from the users prediction table
   * entries and also any games from the MatchTable that
   * have no entry in the Users Prediction data.
   **********************************************************/
  function ShowUserPredictions($userid) {
    global $dbasePredictionData, $dbaseUserData, $dbaseMatchData, $SID, $leagueID,$User;
    global $P, $W, $POS, $D, $D, $L, $Away, $Home, $F, $A, $GD, $User_Name,$PTS,$Date,$Result,$Predictions, $ViewUserPredictions;

    $link = OpenConnection();
    if ($link == FALSE) {
      echo "Unable to connect to the dbase";
      return;
    }
    
    $userquery = "SELECT username FROM $dbaseUserData WHERE lid=? and userid=?";
    $unameres = DBExecute($userquery, array((int)$leagueID, (int)$userid));
    if ($unameres === FALSE) {
      die("Unable to get username: ".mysqli_error($link));
    }
    $usernameln = mysql_fetch_row($unameres);
    $username = stripslashes($usernameln[0]);
    
    $userquery = "SELECT hometeam,awayteam,$dbasePredictionData.homescore,$dbasePredictionData.awayscore,$dbaseMatchData.matchid,matchdate FROM $dbasePredictionData inner join $dbaseUserData on $dbasePredictionData.userid=$dbaseUserData.userid and $dbaseUserData.lid=$dbasePredictionData.lid inner join $dbaseMatchData on $dbasePredictionData.matchid=$dbaseMatchData.matchid and $dbaseMatchData.lid=$dbasePredictionData.lid WHERE $dbasePredictionData.userid=? and $dbaseMatchData.lid=? order by $dbaseMatchData.matchdate";
    $userresult = DBExecute($userquery, array((int)$userid, (int)$leagueID));
    if ($userresult === FALSE) {
      die("Unable to load user predictions: ".mysqli_error($link));
    }

    // Display the username as a header.
    echo "<tr>";
    echo "<td class=\"TBLHEAD\" colspan=\"6\" align=\"center\"><font class=\"TBLHEAD\">$Predictions [$username]</font></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td align=\"center\" class=\"TBLHEAD\"><font class=\"TBLHEAD\">$Date</font></td>";
    echo "<td align=\"center\" class=\"TBLHEAD\"><font class=\"TBLHEAD\">$Home</font></td>";
    echo "<td align=\"center\" class=\"TBLHEAD\"><font class=\"TBLHEAD\">&nbsp;</font></td>";
    echo "<td align=\"center\" class=\"TBLHEAD\"><font class=\"TBLHEAD\">&nbsp;</font></td>";
    echo "<td align=\"center\" class=\"TBLHEAD\"><font class=\"TBLHEAD\">&nbsp;</font></td>";
    echo "<td align=\"center\" class=\"TBLHEAD\"><font class=\"TBLHEAD\">$Away</font></td>";
    echo "</tr>";
    // First loop. Used to get all the users.
    while ($userline = mysql_fetch_array($userresult, MYSQL_ASSOC)) {
      // For each user display all their predictions.
      // against the actual result.
      $hometeam = stripslashes($userline["hometeam"]);
      $awayteam = stripslashes($userline["awayteam"]);
      $homescore = $userline["homescore"];
      $awayscore = $userline["awayscore"];
      $matchid = $userline["matchid"];
      $date = $userline["matchdate"];
      $datetext = GetDateFromDatetime($date);
      $time = GetTimeFromDatetime($date);

      if ($ViewUserPredictions == "TRUE" || (CompareDatetime($date) < 0) || ($username == $User->username)) {
        echo "<tr>";
        echo "<td class=\"TBLROW\"><font class=\"TBLROW\"><a href=\"showpredictionsformatch.php?sid=$SID&matchid=$matchid&date=$date\">$datetext</a> $time</font></td>";
        echo "<td class=\"TBLROW\"><font class=\"TBLROW\">$hometeam</font></td>";
        echo "<td class=\"TBLROW\"><font class=\"TBLROW\">$homescore</font></td>";
        echo "<td class=\"TBLROW\"><font class=\"TBLROW\">v</font></td>";
        echo "<td class=\"TBLROW\"><font class=\"TBLROW\">$awayscore</font></td>";
        echo "<td class=\"TBLROW\"><font class=\"TBLROW\">$awayteam</font></td>";
        echo "</tr>";
      }
    }

    CloseConnection($link);
  }

  /*****************************************************************
   * Get the predictions for the given date.
   * @param the date for the game in the same format as the dbase.
   *****************************************************************/
  function GetPredictionsForMatch($matchid, $date) {
    global $dbasePredictionData, $dbaseMatchData, $dbaseUserData, $SID, $User, $leagueID;
    global $P, $W, $POS, $D, $D, $L, $Away, $Home, $F, $A, $GD, $User_Name,$PTS,$Date,$Result,$Predictions, $ViewUserPredictions;

    $link = OpenConnection();
    if ($link == FALSE) {
      echo "Unable to connect to the dbase";
      return;
    }
    
    $userquery = "select $dbasePredictionData.userid, $dbaseUserData.username, $dbaseMatchData.hometeam, $dbaseMatchData.awayteam, $dbasePredictionData.homescore, $dbasePredictionData.awayscore, $dbaseMatchData.matchdate from $dbasePredictionData inner join $dbaseMatchData on $dbasePredictionData.matchid=$dbaseMatchData.matchid and $dbasePredictionData.lid=$dbaseMatchData.lid inner join $dbaseUserData on $dbasePredictionData.userid=$dbaseUserData.userid and $dbasePredictionData.lid=$dbaseUserData.lid where $dbaseMatchData.matchid=? and $dbaseMatchData.lid=? order by matchdate";
    $userresult = DBExecute($userquery, array((int)$matchid, (int)$leagueID));
    if ($userresult === FALSE) {
      die("Unable to load match predictions: ".mysqli_error($link));
    }

    // Display the username as a header.
    $datetext = convertDatetimeToScreenDate($date);
    echo "<table class=\"STANDTB\">";
    echo "<tr>";
    echo "<td class=\"TBLHEAD\" colspan=\"7\" align=\"center\"><font class=\"TBLHEAD\">$Predictions [$datetext]</font></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td align=\"center\" class=\"TBLHEAD\"><font class=\"TBLHEAD\">$Date</font></td>";
    echo "<td align=\"center\" class=\"TBLHEAD\"><font class=\"TBLHEAD\">$User_Name</font></td>";
    echo "<td align=\"center\" class=\"TBLHEAD\"><font class=\"TBLHEAD\">$Home</font></td>";
    echo "<td align=\"center\" class=\"TBLHEAD\"><font class=\"TBLHEAD\"></font></td>";
    echo "<td align=\"center\" class=\"TBLHEAD\"><font class=\"TBLHEAD\"></font></td>";
    echo "<td align=\"center\" class=\"TBLHEAD\"><font class=\"TBLHEAD\"></font></td>";
    echo "<td align=\"center\" class=\"TBLHEAD\"><font class=\"TBLHEAD\">$Away</font></td>";
    echo "</tr>";
    // First loop. Used to get all the users.
    while ($userline = mysql_fetch_array($userresult, MYSQL_ASSOC)) {
      // For each user display all their predictions.
      // against the actual result.
      $userid = $userline["userid"];
      $username = stripslashes($userline["username"]);
      $hometeam = stripslashes($userline["hometeam"]);
      $awayteam = stripslashes($userline["awayteam"]);
      $homescore = $userline["homescore"];
      $awayscore = $userline["awayscore"];
      $date = $userline["matchdate"];

      // The date is in datetime format YYYY-MM-DD HH:MM:SS , pull off date
      $datetext = GetDateFromDatetime($date);

      if ($ViewUserPredictions == "TRUE" || (CompareDatetime($date) < 0) || ($username == $User->username)) {
        echo "<tr>";
        echo "<td class=\"TBLROW\"><font class=\"TBLROW\">$datetext</font></td>";
        echo "<td class=\"TBLROW\"><font class=\"TBLROW\"><a href=\"showuserpredictions.php?sid=$SID&user=$userid\">$username</a></font></td>";
        echo "<td class=\"TBLROW\"><font class=\"TBLROW\">$hometeam</font></td>";
        echo "<td align=\"CENTER\" class=\"TBLROW\"><font class=\"TBLROW\">$homescore</font></td>";
        echo "<td align=\"CENTER\" class=\"TBLROW\"><font class=\"TBLROW\">v</font></td>";
        echo "<td align=\"CENTER\" class=\"TBLROW\"><font class=\"TBLROW\">$awayscore</font></td>";
        echo "<td class=\"TBLROW\"><font class=\"TBLROW\">$awayteam</font></td>";
        echo "</tr>";
      }
    }
    echo "</table>";

    CloseConnection($link);
  }

  /****************************************************************
   * Get and display the results in a table format.
   ****************************************************************/
  function GetResults() {
    global $dbaseMatchData, $SID, $leagueID;
    global $P, $W, $POS, $D, $D, $L, $Away, $Home, $F, $A, $GD, $User_Name,$PTS,$Date,$Result, $FixturesResults;

    // Create the connection to the database
    $link = OpenConnection();
    if ($link == FALSE) {
      echo "Unable to connect to the dbase";
      return;
    }
    
    $userquery = "SELECT * FROM $dbaseMatchData where lid='$leagueID' order by matchdate";
    $userresult = mysql_query($userquery)
        or die("Query failed: $userquery");

    // Display the username as a header.
    echo "<table class=\"STANDTB\">\n";
    echo "<tr>\n";
    echo "<td class=\"TBLHEAD\" colspan=\"6\" align=\"center\"><font class=\"TBLHEAD\">$FixturesResults</font></td>\n";
    echo "</tr>\n";
    echo "<tr>\n";
    echo "<td width=\"80\" align=\"center\" class=\"TBLHEAD\"><font class=\"TBLHEAD\">$Date</font></td>\n";
    echo "<td width=\"150\" align=\"center\" class=\"TBLHEAD\"><font class=\"TBLHEAD\">$Home</font></td>\n";
    echo "<td align=\"center\" class=\"TBLHEAD\"><font class=\"TBLROW\">G</font></td>\n";
    echo "<td align=\"center\" class=\"TBLHEAD\"><font class=\"TBLROW\">v</font></td>\n";
    echo "<td align=\"center\" class=\"TBLHEAD\"><font class=\"TBLROW\">G</font></td>\n";
    echo "<td width=\"150\" align=\"center\" class=\"TBLHEAD\"><font class=\"TBLHEAD\">$Away</font></td>\n";
    echo "</tr>\n";
    // First loop. Used to get all the users.
    while ($userline = mysql_fetch_array($userresult, MYSQL_ASSOC)) {
      // For each user display all their predictions.
      // against the actual result.
      $matchid = $userline["matchid"];
      $hometeam = stripslashes($userline["hometeam"]);
      $awayteam = stripslashes($userline["awayteam"]);
      $homescore = $userline["homescore"];
      if ($homescore == null) {
        $homescore = "&nbsp;";
      }
      $awayscore = $userline["awayscore"];
      if ($awayscore == null) {
        $awayscore = "&nbsp;";
      }
      $date = $userline["matchdate"];
      $datetext = GetDateFromDatetime($date);

      echo "<tr>\n";
      echo "<td class=\"TBLROW\"><font class=\"TBLROW\"><a href=\"showpredictionsformatch.php?sid=$SID&matchid=$matchid&date=$date\">$datetext</a></font></td>\n";
      echo "<td class=\"TBLROW\"><font class=\"TBLROW\">$hometeam</font></td>\n";
      echo "<td align=\"CENTER\" class=\"TBLROW\"><font class=\"TBLROW\">$homescore</font></td>\n";
      echo "<td align=\"CENTER\" class=\"TBLROW\"><font class=\"TBLROW\">v</font></td>\n";
      echo "<td align=\"CENTER\" class=\"TBLROW\"><font class=\"TBLROW\">$awayscore</font></td>\n";
      echo "<td class=\"TBLROW\"><font class=\"TBLROW\">$awayteam</font></td>\n";
      echo "</tr>\n";
    }
    echo "</table>\n";

    CloseConnection($link);
  }

  /***********************************************************************
   * Encode parameters.
   * The parameters passed as GET values cannot contain spaces. This
   * must be replaced with a + .
   ***********************************************************************/
  function EncodeParam($param) {
    return str_replace(" ","+",$param);
  }

  /*****************************************************************
   * Forward to the given address
   * @url the address to go to.
   *****************************************************************/
  function forward($url) {
    /* Redirect browser */
    header("Location: $url"); 
    /* Make sure that code below does not get executed when we redirect. */
    exit; 
  }
  
  /*****************************************************************
   * If the standings table exists, update it.
   *****************************************************************/
  function UpdateStandingsTable() {
    // Allow access to the global table name.
    global $dbaseUserData, $dbasePredictionData, $dbaseMatchData, $dbaseStandings, $hideNoPredictions, $leagueID;

    /////////////////////////////////////////////////////////////////	
    // Calculate the new standings
    /////////////////////////////////////////////////////////////////	

    // Update the league right after the game has started. The null result value is checked for.
    $todaysdate = date("Y-m-d H:i:s");

    // Array for sorting the table based on points scored
    $points = Array();

    // Array holding users
    $gameStats = Array();
    
    // Connecting, selecting database
    $link = OpenConnection();
    if ($link == FALSE) {
      echo "Unable to connect to dbase.";
      return;
    }

    // Get a table containing the userid, predicted scores and actual scores. This will not actually show users with 0 predictions
    $predquery = "SELECT $dbasePredictionData.userid, $dbaseMatchData.homescore as mhs, $dbaseMatchData.awayscore as mas, $dbasePredictionData.homescore as phs, $dbasePredictionData.awayscore as pas FROM $dbasePredictionData inner join $dbaseMatchData on $dbasePredictionData.matchid=$dbaseMatchData.matchid and $dbasePredictionData.lid=$dbaseMatchData.lid where $dbaseMatchData.matchdate<=? and $dbaseMatchData.lid=?";

    $predresult = DBExecute($predquery, array($todaysdate, (int)$leagueID), $link);
    if ($predresult === FALSE) {
      die("Unable to calculate standings: ".mysqli_error($link));
    }

    $num_rows = mysql_num_rows($predresult);
    if ($num_rows == 0) {
      // Delete the current contents
      $query = "delete from $dbaseStandings where lid=?";
      if (FALSE == DBExecute($query, array((int)$leagueID), $link)) {
        LogMsg("Unable to delete current standings");
        return;
      }
    } else {
          
      // Getting the individual predictions from the database
      while ($predline = mysql_fetch_array($predresult, MYSQL_BOTH)) {
        reset ($predline);
        $userid = $predline["userid"];
        if (array_key_exists($userid,$gameStats) == false) {
          $gameStats[$userid] = new GameStats($userid);
        }
        $predictedHome = $predline["phs"];
        $predictedAway = $predline["pas"];
        $actualHome = $predline["mhs"];
        $actualAway = $predline["mas"];
        if ($predictedHome != "" and $actualHome != "") {
          $gameStats[$userid]->UpdateStats($predictedHome,
                                           $predictedAway,
                                           $actualHome,
                                           $actualAway);
        }
      }
      
      // If the display of users with 0 predictions is disabled and this user has no predictions
      // then continue.
      if ($hideNoPredictions == "FALSE") {
        $userquery = "select * from $dbaseUserData where lid=?";
        $userresult = DBExecute($userquery, array((int)$leagueID), $link);
        if ($userresult === FALSE) {
          die("Unable to get user data: ".mysqli_error($link));
        }
        while ($user = mysql_fetch_array($userresult)) {
          $userid = $user["userid"];

          if (!array_key_exists($userid,$gameStats)) {
//          if ($gameStats[$userid] == null) {
            $gameStats[$userid] = new GameStats($userid);
          }
        }
      }
  
      mysql_free_result($predresult);
  
      // Sort in descending order. Keep the keys intact.
      // The compare can be a class method in PHP 4.1.1, but will not work with 4.0.3
      if (sizeof($gameStats) > 0) {
        uasort($gameStats, "compare");
  
        // Delete the current contents
        $query = "delete from $dbaseStandings where lid=?";
        if (FALSE == DBExecute($query, array((int)$leagueID), $link)) {
          LogMsg("Unable to delete current standings");
          return;
        }
      
        // Update the table with the contents of game stats
        $count = 1;
        foreach ($gameStats as $key => $val) {
          $userid = $key;
          $pld = $val->predictions;
          $won = $val->won;
          $drawn = $val->drawn;
          $lost = $val->lost;
          $gfor = $val->for;
          $gagainst = $val->against;
          $diff = $val->diff;
          $points = $val->points;
     
          $query = "insert into $dbaseStandings (lid, userid, position, pld, won, drawn, lost, gfor, gagainst, diff, points) values (?,?,?,?,?,?,?,?,?,?,?)";
          $result = DBExecute($query, array((int)$leagueID, (int)$userid, $count, $pld, $won, $drawn, $lost, $gfor, $gagainst, $diff, $points), $link);
          if (FALSE == $result) {
            LogMsg("Unable to enter current standings: ".mysqli_error($link));
            return;
          }
          $count ++;
        }  
      }
    }
    // Now update the automatic predictions.
    //MakeAutoPredictions();

    // Closing connection
    CloseConnection($link);
  }

  /*****************************************************************
   * Make the auto predictions.
   * This is only used if enabled, it will cause predictions to be 
   * made for all users that have chosen to enable this feature and
   * have not yet made a prediction for a fixture.
   *****************************************************************/
  function MakeAutoPredictions() {
    global $autoPredict, $leagueID, $dbaseUserData, $dbaseMatchData, $dbasePredictionData;
  
    // If auto predictions are disabled then do nowt.
    if ($autoPredict == "FALSE") {
      return;
    }

    $now = date("Y-m-d H:i:s");

    // Unplayed Matches with no predictions for players or predictions with 
    // auto flag set.

    // for each player that has enabled auto predictions get a list of 
    // missing predictions and create an auto prediction.
    $link = OpenConnection();
    $query = "select * from $dbaseUserData where lid=? and auto=?";
    $players = DBExecute($query, array((int)$leagueID, "Y"), $link);
    if ($players === FALSE) {
      die("Unable to determine auto players: ".mysqli_error($link));
    }
    while ($player = mysql_fetch_array($players)) {
      // For this player get the outstanding predictions. This is 
      // matches where the player has not predictions or the predictions
      // are already predicted automatically
      $uid = $player["userid"];
      $q2 = "select * from $dbaseMatchData where matchdate>? and lid=?";
      $r2 = DBExecute($q2, array($now, (int)$leagueID), $link);
      if ($r2 === FALSE) {
        die("Unable to get matches: ".mysqli_error($link));
      }
      while ($match = mysql_fetch_array($r2)) {
        $mid = $match["matchid"];
        $q3 = "select * from $dbasePredictionData where lid=? and matchid=? and userid=?";
        $r3 = DBExecute($q3, array((int)$leagueID, (int)$mid, (int)$uid), $link);
        if ($r3 === FALSE) {
          die("Unable to get predictions: ".mysqli_error($link));
        }
        if (mysql_num_rows($r3) == 0) {
          // No predictions, therefore make one.
        } else {
          // If the prediction is auto, then make one
          $predict = mysql_fetch_array($r3);
          $isauto = $predict["isauto"];
          if ($isauto == 'Y') {
            // Make prediction
          }
        }
      }
    }
    CloseConnection($link);
  }

  /*****************************************************************
   * Show the current standings. If the Standings table is enabled
   * use it, else calculate the standings on the fly.
   *****************************************************************/
  function ShowStandingsTable() {
    global $dbaseUserData, $dbasePredictionData, $dbaseMatchData, $dbaseStandings, $HTTP_GET_VARS, $SID, $usersPerPage, $Prediction_League_Standings, $Send_Msg, $User, $useMessaging, $User;
    global $P, $W, $POS, $D, $D, $L, $Away, $Home, $F, $A, $GD, $User_Name,$PTS,$Date,$Result,$leagueID;

    
    $page = 0;
    if (array_key_exists("page",$HTTP_GET_VARS)) {
      $page = $HTTP_GET_VARS["page"];
    }
    // Update the league right after the game has started. The null result value is chekced for.
    $todaysdate = date("Y-m-d H:i:s");

    // Connecting, selecting database
    $link = OpenConnection();
    if ($link == FALSE) {
      echo "Unable to connect to dbase.";
      return;
    }

    $query = "select * from $dbaseStandings inner join $dbaseUserData on $dbaseStandings.userid=$dbaseUserData.userid and $dbaseUserData.lid=$dbaseStandings.lid where $dbaseUserData.lid=? order by position";
    $result = DBExecute($query, array((int)$leagueID), $link);
    if ($result === FALSE) {
      die("Unable to read standings: ".mysqli_error($link));
    }
  $numUsers = mysql_num_rows($result);
?>
<table class="STANDTB">
<?php 
  if ($numUsers > $usersPerPage) {
?>
<tr>
  <td colspan="11" align="left" class="TBLROW">
    <table>
    <tr>
    <font class="TBLROW">
<?php
  for ($i=0,$j=0; $i<$numUsers; $i+=$usersPerPage,$j++) {
    
    echo "<td width=\"80\" class=\"TBLHEAD\">";
    echo "<a href=\"index.php?sid=$SID&page=$j\">";
    echo $i+1;
    echo "-";
    echo $i+$usersPerPage;
    echo "</a>";
    echo "</td>";
  }
?>
    </font>
    </tr>
    </table>
  </td>
</tr>
<?php
  }
?>
<tr>
  <td colspan="11" align="center" class="TBLHEAD">
    <font class="TBLHEAD"><?php echo $Prediction_League_Standings." ".date("d M Y");?></font>
  </td>
</tr>
<tr>
  <td class="TBLPOS">
    <font class="TBLHEAD"><?php echo $POS; ?></font>
  </td>
  <?php if ($User->loggedIn == TRUE && $useMessaging == "TRUE") { ?>
  <td class="TBLPOS">
    <font class="TBLHEAD"></font>
  </td>
  <?php } ?>
  <td class="TBLUSER">
    <font class="TBLHEAD"><?php echo $User_Name; ?></font>
  </td>
  <td class="TBLPLAYED">
    <font class="TBLHEAD"><?php echo $P; ?></font>
  </td>
  <td class="TBLWON">
    <font class="TBLHEAD"><?php echo $W; ?></font>
  </td>
  <td class="TBLDRAWN">
    <font class="TBLHEAD"><?php echo $D; ?></font>
  </td>
  <td class="TBLLOST">
    <font class="TBLHEAD"><?php echo $L; ?></font>
  </td>
  <td class="TBLFOR">
    <font class="TBLHEAD"><?php echo $F; ?></font>
  </td>
  <td class="TBLAGAINST">
    <font class="TBLHEAD"><?php echo $A; ?></font>
  </td>
  <td class="TBLGD">
    <font class="TBLHEAD"><?php echo $GD; ?></font>
  </td>
  <td class="TBLPTS">
    <font class="TBLHEAD"><b><?php echo $PTS; ?></b></font>
  </td>
</tr>

<?php
    // Display the table for people with results.
    $count = 1;
    while($line = mysql_fetch_array($result)) {
      $username = stripslashes($line["username"]);
      $userid = $line["userid"];
      $pld = $line["pld"];
      $won = $line["won"];
      $drawn = $line["drawn"];
      $lost = $line["lost"];
      $gfor = $line["gfor"];
      $gagainst = $line["gagainst"];
      $diff = $line["diff"];
      $points = $line["points"];
      /* Only display the selected page */
      if (($count <= ($page*$usersPerPage)) ||
         ($count > (($page*$usersPerPage)+$usersPerPage))) {
         $count++;
         continue;
      }

     echo " <tr>\n";

     $class = "TBLROW";
     if ($count == 1) {
       $class="LEADER";
     }
?>
        <td align="CENTER" class="<?php echo $class?>">
          <font class="<?php echo $class?>">
            <?php echo $count?>
          </font>
        </td>
        <?php if ($User->loggedIn == TRUE && $useMessaging == "TRUE") { ?>
        <td class="<?php echo $class?>" align="CENTER" valign="MIDDLE">
          <font class="<?php echo $class?>">
            <a href="createmsg.php?<?php echo "sid=$SID&userid=$userid"?>">
              <img src="img/emailiconold18x12.gif" alt="<?php echo $Send_Msg?>" height="12" width="18" border="0">
            </a>
          </font>
        </td>
        <?php } ?>
        <td class="<?php echo $class?>">
          <font class="<?php echo $class?>">
            <a href="showuserpredictions.php?<?php echo "sid=$SID&user=$userid"?>"><?php echo $username?></a>
          </font>
        </td>
        <td align="CENTER" class="<?php echo $class?>">
          <font class="<?php echo $class?>">
            <!-- Played -->
            <?php echo $pld?>
          </font>
        </td>
        <td align="CENTER" class="<?php echo $class?>">
          <font class="<?php echo $class?>">
            <!-- Won -->
            <?php echo $won; ?>
          </font>
        </td>
        <td align="CENTER" class="<?php echo $class?>">
          <font class="<?php echo $class?>">
            <!-- Drawn -->
            <?php echo $drawn; ?>
          </font>
        </td>
        <td align="CENTER" class="<?php echo $class?>">
          <font class="<?php echo $class?>">
            <!-- Lost -->
            <?php echo $lost; ?>
          </font>
        </td>
        <td align="CENTER" class="<?php echo $class?>">
          <font class="<?php echo $class?>">
            <?php echo $gfor; ?>
          </font>
        </td>
        <td align="CENTER" class="<?php echo $class?>">
          <font class="<?php echo $class?>">
            <?php echo $gagainst; ?>
          </font>
        </td>
        <td align="CENTER" class="<?php echo $class?>">
          <font class="<?php echo $class?>">
            <?php echo $diff; ?>
          </font>
        </td>
        <td align="CENTER" class="<?php echo $class?>">
          <font class="<?php echo $class?>">
            <b>
              <?php echo $points?>
            </b>
          </font>
        </td>
      </tr>
<?php
    $count++;
    }
    echo "</table>";
    // Display a table of the new users. Uncomment the next line to 
    // show the users.
    //showNewUsers($newUsers);
  }

  /**************************************************
   * This function determines the player(s) with the
   * most points for the week prior to the button
   * being pressed.
   *************************************************/
  function CalculateWinnerOfTheWeek() {
    global $dbaseUserData, $dbasePredictionData, $dbaseMatchData, $dbaseStandings, $HTTP_GET_VARS, $hideNoPredictions, $leagueID;
    $secondsInWeek = 604800;
    $now = time();
    $lastWeek = $now - $secondsInWeek;
    echo "$secondsInWeek<br>";
    echo "$now<br>$lastWeek<br>";
    $nowdate = date("Y-m-d H:i:s",$now);
    $lwdate = date("Y-m-d H:i:s",$lastWeek);
    echo "$nowdate<br>$lwdate";

    // Allow access to the global table name.
    $page = $HTTP_GET_VARS["page"];

    /////////////////////////////////////////////////////////////////	
    // Calculate the new standings
    /////////////////////////////////////////////////////////////////	

    // Update the league right after the game has started. The null result value is checked for.
    $todaysdate = $nowdate;

    // Array for sorting the table based on points scored
    $points;

    // Array holding users
    $gameStats;
    
    // Connecting, selecting database
    $link = OpenConnection();
    if ($link == FALSE) {
      echo "Unable to connect to dbase.";
      return;
    }

    // Get a table containing the userid, predicted scores and actual scores. This will not actually show users with 0 predictions
    $predquery = "SELECT $dbasePredictionData.userid, $dbaseMatchData.homescore as mhs, $dbaseMatchData.awayscore as mas, $dbasePredictionData.homescore as phs, $dbasePredictionData.awayscore as pas FROM $dbasePredictionData inner join $dbaseMatchData on $dbasePredictionData.matchid=$dbaseMatchData.matchid and $dbasePredictionData.lid=$dbaseMatchData.lid where $dbaseMatchData.matchdate<=? and $dbaseMatchData.lid=? and $dbaseMatchData.matchdate>?";

    $predresult = DBExecute($predquery, array($todaysdate, (int)$leagueID, $lwdate), $link);
    if ($predresult === FALSE) {
      die("Unable to calculate weekly standings: ".mysqli_error($link));
    }

    // Getting the individual predictions from the database
    while ($predline = mysql_fetch_array($predresult, MYSQL_BOTH)) {
      reset ($predline);
      $userid = $predline["userid"];
      if ($gameStats[$userid] == null) {
        $gameStats[$userid] = new GameStats($userid);
      }
      $predictedHome = $predline["phs"];
      $predictedAway = $predline["pas"];
      $actualHome = $predline["mhs"];
      $actualAway = $predline["mas"];
      $gameStats[$userid]->UpdateStats($predictedHome,
                                       $predictedAway,
                                       $actualHome,
                                       $actualAway);
    }

    // If the display of users with 0 predictions is disabled and this user has no predictions
    // then continue.
    if ($hideNoPredictions == "FALSE") {
      $userquery = "select * from $dbaseUserData where lid=?";
      $userresult = DBExecute($userquery, array((int)$leagueID), $link);
      if ($userresult === FALSE) {
        die("Unable to get user data: ".mysqli_error($link));
      }
      while ($user = mysql_fetch_array($userresult)) {
        $userid = $user["userid"];
        if ($gameStats[$userid] == null) {
          $gameStats[$userid] = new GameStats($userid);
        }
      }
    }

    mysql_free_result($predresult);

    // Sort in descending order. Keep the keys intact.
    // The compare can be a class method in PHP 4.1.1, but will not work with 4.0.3
    if (sizeof($gameStats) > 0) {
      uasort($gameStats, "compare");

      // Update the table with the contents of game stats
      $count = 1;
      foreach ($gameStats as $key => $val) {
        $userid = $key;
        $username = $val->username;
        $pld = $val->predictions;
        $won = $val->won;
        $drawn = $val->drawn;
        $lost = $val->lost;
        $gfor = $val->for;
        $gagainst = $val->against;
        $diff = $val->diff;
        $points = $val->points;
   
        echo "<br>$username [ $userid ] $pld $won $drawn $lost $gfor $gagainst $diff $points<br>\n";
        //$query = "insert into $dbaseStandings (lid, userid, position, pld, won, drawn, lost, gfor, gagainst, diff, points) values ('$leagueID',\"$userid\", \"$count\", \"$pld\", \"$won\", \"$drawn\", \"$lost\", \"$gfor\", \"$gagainst\", \"$diff\", \"$points\")";
        //$result = mysql_query($query) or die("Unable to update standings table: $query\n".mysql_error());
        //if (FALSE == $result) {
          //LogMsg ("Unable to enter current standings: $query");
          //return;
        //}
        $count ++;
      }  
    }

    // Now update the automatic predictions.
    MakeAutoPredictions();

    // Closing connection
    CloseConnection($link);

  }

/*******************************************************
* Get all the results with no values that are passed 
* the current time + 1.5
*******************************************************/
function GetOustandingResults() {
  // Array holding the current fixtures.
  global $dbaseMatchData, $Enter_Result, $SID, $leagueID;

  $link = OpenConnection();
  if ($link == FALSE) {
    echo "Unable to connect to the dbase ";
    return;
  }

  // Add 90m mins to the kickoff time.
  $offs = 60*90;
  $date = date("Y-m-d H:i:s",time()-$offs);
  //$date = date("Y-m-d H:i:s");
   
  $matchquery = "SELECT * FROM $dbaseMatchData where lid=? and matchdate<=? and homescore is null order by matchdate desc";
  $matchresult = DBExecute($matchquery, array((int)$leagueID, $date), $link);
  if ($matchresult === FALSE) {
    die("Unable to load outstanding results: ".mysqli_error($link));
  }
?>
  <form method="POST" action="enteroutstandingresults.php?sid=<?php echo $SID; ?>">
  <table class="STANDTB">
  <tr>
  <td align="center" class="TBLHEAD" colspan="6"><font class="TBLHEAD">Current Fixtures</font></td>
  </tr>
  <tr>
  <td align="center" class="TBLHEAD"><font class="TBLHEAD">Date</font></td>
  <td align="center" class="TBLHEAD"><font class="TBLHEAD">Home</font></td>
  <td align="center" class="TBLHEAD"><font class="TBLHEAD"></font></td>
  <td align="center" class="TBLHEAD"><font class="TBLHEAD"></font></td>
  <td align="center" class="TBLHEAD"><font class="TBLHEAD"></font></td>
  <td align="center" class="TBLHEAD"><font class="TBLHEAD">Away</font></td>
<?php
  $count = 0;
  while ($matchdata = mysql_fetch_array($matchresult,MYSQL_ASSOC)) {
    $matchdate = $matchdata["matchdate"];
    $matchid = $matchdata["matchid"];
    $hometeam = stripslashes($matchdata["hometeam"]);
    $awayteam = stripslashes($matchdata["awayteam"]);
    echo "\n<tr>\n";
    echo "<td class=\"TBLROW\">\n";
    echo "<font class=\"TBLROW\">\n";
    echo "<a href=\"adminpostresult.php?sid=$SID&matchdate=$matchdate&hometeam=".EncodeParam($hometeam)."&awayteam=".EncodeParam($awayteam)."\">";
    echo convertDatetimeToScreenDate($matchdate);
    echo "</a>\n";
    echo "</font>\n";
    echo "</td>\n";
    echo "<td align=\"CENTER\" class=\"TBLROW\"><font class=\"TBLROW\">\n";
    echo "<font class=\"TBLROW\">\n";
    echo "$hometeam\n";
    echo "</font>\n";
    echo "</td>\n";
    echo "<td align=\"CENTER\" class=\"TBLROW\">\n";
    echo "<font class=\"TBLROW\">\n";
    echo "<input type=\"HIDDEN\" name=\"MID$count\" value=\"$matchid\">\n";
    echo "<input type=\"text\" size=\"2\" name=\"GF$count\">\n";
    echo "</font>\n";
    echo "</td>\n";
    echo "<td align=\"CENTER\" class=\"TBLROW\">\n";
    echo "<font class=\"TBLROW\">\n";
    echo "v\n";
    echo "</font>\n";
    echo "</td>\n";
    echo "<td class=\"TBLROW\">\n";
    echo "<font class=\"TBLROW\">\n";
    echo "<input type=\"text\" size=\"2\" name=\"GA$count\">\n";
    echo "</font>\n";
    echo "</td>\n";
    echo "<td class=\"TBLROW\">\n";
    echo "<font class=\"TBLROW\">\n";
    echo "$awayteam\n";
    echo "</font>\n";
    echo "</td>\n";
    echo "</tr>\n";
    $count++;
  }
?>
  <tr>
  <td class="TBLROW" colspan="6" align="CENTER">
  <font class="TBLROW">
  <input type="HIDDEN" name="COUNT" value="<?php echo $count; ?>">
  <input type="submit" name="Submit" value="<?php echo $Enter_Result; ?>">
  </font>
  </td>
  </table>
  </form>
<?php
  CloseConnection($link);
}

/*********************************************************
 * Determine the language file to use.
 * If the user is logged in, use their language selection
 * else use the default from systemvars.php $languageFile.
 ********************************************************/
function GetLangFile() {
  global $languageFile,$User;

  if ($User->loggedIn == true && $User->lang != "") {
    return "lang.".$User->lang.".php";
  }
  return $languageFile;
}

/*********************************************************
 * Determine the available languages and offer to the user.
 * The available languages are determined by looking in
 * the lang directory.
 ********************************************************/
function GetLanguageOptions($currlang) {
  $f = opendir("lang");
  if ($f == FALSE) {
    echo "<option>Cant open lang dir</option>";
    exit;
  }

  $arr = array();
  
  $file = readdir($f); 
  while (false != $file) { 
    if ($file != "." && $file != "..") { 
      $nm = StripLang($file);
      $arr[$nm] = $nm;
    } 
    $file = readdir($f); 
  }
  foreach ($arr as $key => $val) {
    $selected = "";
    if ($key == $currlang) {
      $selected = "selected";
    } else {
      $selected = "";
    }
    echo "<option $selected>".$key."</option>\n"; 
  }
  closedir($f);
}

/*********************************************************
 * Strip the lang. from the start and the .php from the
 * end.
 ********************************************************/
function StripLang($fn) {
  return substr($fn,5,strrpos($fn,".php")-5);
}

?>
