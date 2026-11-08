<?php 
/*********************************************************
 * Author: John Astill (c) 2002
 * Date  : 9th December 2001
 * File  : statsfunctions.php
 ********************************************************/

 ///////////////////////////////////////////////////
// Get the statistics for the given match 
///////////////////////////////////////////////////
  function getStatsForMatch($results, $matchid) {
    global $dbasePredictionData, $dbaseMatchData, $SID, $Prediction_Stats,$leagueID,$Games_Menu,$Previous,$Next;
   
    // Search for the next date in the dbase.
    $link = OpenConnection();
    if ($link == FALSE) {
      ErrorNotify("Unable to open connection");
      exit;
    }

    // If the matches are ordered, then the first should be the next game.
    $query = "select $dbaseMatchData.matchid, matchdate, hometeam, awayteam, $dbasePredictionData.homescore, $dbasePredictionData.awayscore from $dbasePredictionData inner join $dbaseMatchData on $dbasePredictionData.matchid=$dbaseMatchData.matchid and $dbasePredictionData.lid=$dbaseMatchData.lid where $dbaseMatchData.matchid=? and $dbaseMatchData.lid=?";
    $result = DBExecute($query, array((int)$matchid, (int)$leagueID));
    if ($result === FALSE) {
      die("Unable to load match statistics: ".mysqli_error($link));
    }

    if (mysql_num_rows($result) == 0) {
      $nextmatch = "No match ";
    } else {
      // Get the date of the next game.
      $line = mysql_fetch_array($result, MYSQL_ASSOC);
      $matchid = $line["matchid"];
      $matchdate = $line["matchdate"];
      $hometeam = stripslashes($line["hometeam"]);
      $awayteam = stripslashes($line["awayteam"]);
      $homescore = $line["homescore"];
      $awayscore = $line["awayscore"];
      $key = "$homescore-$awayscore";
      if (array_key_exists($key,$results)) {
        $results[$key] += 1;
      } else {
        $results[$key] = 1;
      }
              
      $count = 1;

      // Loop through the rest of the results, just taking the next games results.
      while ($line  = mysql_fetch_array($result, MYSQL_ASSOC)) {
        if ($matchid == $line["matchid"]) {
          $homescore = $line["homescore"];
          $awayscore = $line["awayscore"];
          $key = "$homescore-$awayscore";
          if (array_key_exists($key,$results)) {
            $results[$key] += 1;
          } else {
            $results[$key] = 1;
          }
          $count++;
        }
      }
    }
    return $results;
  }
 
  /***********************************************
   * Show the prediction statistics for the match
   * on the given matchid.
   * @param matchid
   **********************************************/
  function ShowPredictionStatsForMatch($matchid) {
    global $dbasePredictionData, $dbaseMatchData, $SID, $Prediction_Stats,$leagueID,$Games_Menu,$Previous,$Next;

    $results = Array();
    $results = getStatsForMatch($results, $matchid);
    arsort($results);
?>
<script>
     function setStats(matchid,matchdate,hometeam,awayteam,count,scores,scorescount,numpreds) {

        // Now add the stats
        // Loop through the stats and create the rows.
        var oldstats = document.getElementById("STATSTAB");

        var statsdiv = document.getElementById("STATS");
        // Now create a table
        var table = document.createElement("table");
        table.setAttribute("id","STATSTAB");
        table.className = "PREDTB";

        var thead = document.createElement("thead");
        var row = document.createElement("tr");
        
        var cell = document.createElement("td");
        cell.className = "TBLHEAD";
        cell.colSpan = 2;
        cell.align = "center";
        
        var font = document.createElement("font");
        font.className = "TBLHEAD";
        var text = document.createTextNode("<?php echo $Prediction_Stats; ?>");
        font.appendChild(text);

        var br = document.createElement("br");
        font.appendChild(br);

        // Previous link
        var a = document.createElement("a");
        a.className = "PRED";
        a.setAttribute("target","MATCHSTATS");
        a.setAttribute('href',"getmatchstats.php?sid=<?php echo $SID; ?>&matchid="+matchid+"&matchdate="+matchdate+"&cmd=next");
        text = document.createTextNode("<?php echo "$Previous "; ?>");
        a.appendChild(text);
        font.appendChild(a);

        // Next link
        a = document.createElement("a");
        a.className = "PRED";
        a.setAttribute("target","MATCHSTATS");
        a.setAttribute('href',"getmatchstats.php?sid=<?php echo $SID; ?>&matchid="+matchid+"&matchdate="+matchdate+"&cmd=next");
        text = document.createTextNode("<?php echo " $Next"; ?>");
        a.appendChild(text);
        font.appendChild(a);

        br = document.createElement("br");
        font.appendChild(br);

        // Stats/fixture link
        a = document.createElement("a");
        a.className = "PRED";
        a.setAttribute('href',"showpredictionsformatch.php?sid=<?php echo $SID; ?>&matchid="+matchid+"&date="+matchdate);
        text = document.createTextNode(hometeam+" v "+awayteam);
        a.appendChild(text);
        font.appendChild(a);

        cell.appendChild(font);

        row.appendChild(cell);

        thead.appendChild(row);
        
        table.appendChild(thead);
  
        var tbody = document.createElement("tbody");
        tbody.setAttribute("id","STATS_ROWS");
        
        var predtext;
        var row1;
        var cell1;
        var cell2;
        var font1;
        var img;
        var pct = 0;
        for (i=0; i<count; i++) {
          predtext = document.createTextNode(scores[i]);
          cell1 = document.createElement("td");
          cell1.className = "TBLROW";

          font1 = document.createElement("font");
          font1.className = "TBLROW";
          font1.appendChild(predtext);

          cell1.appendChild(font1);
          row1 = document.createElement("tr");
          row1.appendChild(cell1);

          img = document.createElement("img");
          img.setAttribute("src","percentbar.gif");
          img.setAttribute("alt","Percentage");
          img.height= "10";
          pct = (scorescount[i])/numpreds;
          img.width = pct*40;
          pct = Math.floor(pct * 1000)/10;
          predtext = document.createTextNode(" "+pct+"% ["+scorescount[i]+"]");

          font1 = document.createElement("font");
          font1.className = "TBLROW";
          font1.appendChild(img);
          font1.appendChild(predtext);
          
          cell2 = document.createElement("td");
          cell2.className = "TBLROW";
          cell2.appendChild(font1);

          row1.appendChild(cell2);

          tbody.appendChild(row1);
        }
        table.appendChild(tbody);
        statsdiv.replaceChild(table,oldstats);
     }
</script>

      <iframe id="MATCHSTATS" name="MATCHSTATS" style="width:0px; height:0px;border:0px" src="getgametime.php"></iframe>
      
      <div id="STATS">
      <table id="STATSTAB" class="PREDTB">
      <tr>
      <td align="CENTER" class="TBLHEAD" colspan="2">
      <font class="TBLHEAD">
<?php
      $link = OpenConnection();
      $query = "select * from $dbaseMatchData where matchid=? and lid=?";
      $result = DBExecute($query, array((int)$matchid, (int)$leagueID));
      if ($result === FALSE) {
        die("Unable to get match details: ".mysqli_error($link));
      }

      $line = mysql_fetch_array($result);
      $hometeam = $line["hometeam"];
      $awayteam = $line["awayteam"];
      $matchdate = $line["matchdate"];

      echo " $Prediction_Stats";
      echo "<br><a id='A_PREV' target=\"MATCHSTATS\" href=\"getmatchstats.php?sid=$SID&matchid=$matchid&matchdate=$matchdate&cmd=prev\" class=\"PRED\">";
      echo " $Previous   ";
      echo "</a>";
      echo "<a id='A_NEXT' target=\"MATCHSTATS\" href=\"getmatchstats.php?sid=$SID&matchid=$matchid&matchdate=$matchdate&cmd=next\" class=\"PRED\">";
      echo " $Next";
      echo "</a>";
      echo "<br><a id='A_FIXT' href=\"showpredictionsformatch.php?sid=$SID&matchid=$matchid&date=$matchdate\" class=\"PRED\">";
      echo "<div id='A_FIXT_TEXT'>";
      echo "$hometeam v $awayteam";
      echo "</div>";
      echo "</a>";
?>
      </font>
      </td>
      </tr>

      <div id='STATS_ROWS_DIV'>
      <tbody id='STATS_ROWS'>
<?php
      $sum = array_sum($results);

      $count = count($results);
      // Cycle through the array and print the results.
      foreach ($results as $key => $val) {
?>
        <tr>
        <td align="CENTER" class="TBLROW">
        <font class="TBLROW">
<?php
        echo $key;
?>
        </font>
        </td>
        <td width="100" class="TBLROW">
        <font class="TBLROW">
<?php
        $percentage = floor($val*1000/$sum)/10;
        echo "<img width=\"".($percentage*0.4)."\" height=\"10\" src=\"percentbar.gif\" alt=\"Percentage\"> ";
        echo $percentage."% [$val]";
?>
        </font>
        </td>
        </tr>
<?php
      }
?>
      </tbody>
      </div>
      </table>
      </div>
<?php
    CloseConnection($link);
  }

  /***********************************************
   * Show the prediction statistics for all the 
   * matches.
   * Use dynamic html to allow choosing between
   * pages.
   **********************************************/
  function ShowPredictionStatsForAllMatches() {
  }

  /***********************************************
   *
   **********************************************/
  function ShowPredictionStatsForNextMatch() {
    global $dbasePredictionData, $dbaseMatchData,$leagueID;

    $todaysdate = date("Y-m-d H:i:s");
    
    // Search for the next date in the dbase.
    $link = OpenConnection();
    if ($link == FALSE) {
      ErrorNotify("Unable to open connection");
      exit;
    }

    // If the matches are ordered, then the first should be the next game. If there are two at the same time, only one is shown
    $query = "select * from $dbasePredictionData inner join $dbaseMatchData on $dbasePredictionData.matchid=$dbaseMatchData.matchid and $dbasePredictionData.lid=$dbaseMatchData.lid where matchdate>=? and $dbaseMatchData.lid=? order by matchdate, rand()";
    $result = DBExecute($query, array($todaysdate, (int)$leagueID));
    if ($result === FALSE) {
      die("Unable to load upcoming prediction statistics: ".mysqli_error($link));
    }

    CloseConnection($link);

    if (mysql_num_rows($result) == 0) {
      $nextmatch = "No matches scheduled";
    } else {
      // Get the date of the next game.
      $line = mysql_fetch_array($result, MYSQL_ASSOC);
      $matchid = $line["matchid"];

      ShowPredictionStatsForMatch($matchid);
    }
  }

?>
