<?php
require "statsfunctions.php";

/*********************************************************
 * Author: John Astill (c)
 * Date  : 20th March 2003
 * File  : getleaderboard.php
 * Descr : This file is used in conjunction with the 
 *       : index file and dynamic html to allow the 
 *       : leading tipsters to be displayed in an html
 *       : file.
 *********************************************************/
  require "systemvars.php";

  // If you want to change the number of displayed users, change this value.
  $cmd = $HTTP_GET_VARS["cmd"];
  $mid = $HTTP_GET_VARS["matchid"];
  $mdate = $HTTP_GET_VARS["matchdate"];
  
  $prev = 0;
  $first = 0;
  $newmid = 0;

  $link = OpenConnection();
//  $query = "select distinct matchid, * from $dbaseMatchData inner join $dbasePredictionData on $dbaseMatchData.lid=$dbasePredictionData.lid and $dbaseMatchData.matchid=$dbasePredictionData.matchid order by matchdate, matchid";
  $query = "select * from $dbaseMatchData where lid = ? order by matchdate, matchid";
  $result = DBExecute($query, array((int)$leagueID));

  if (mysql_num_rows($result) == 1) {
    $newmid = $mid;    
  } else {
    $id = $mid;
    // Now we have all the matches that have the same date
    while ($line = mysql_fetch_array($result)) {
      $id = $line["matchid"];
      if ($first == 0) {
        $first = $id;
      }
      if ($id == $mid && $prev != 0) {
        if ($cmd == "prev") {
          $newmid = $prev; 
          break;
        } 
      }
      if ($mid == $prev && $cmd == "next") {
        $newmid = $id;
        break;
      }
      $prev = $id;
    }
    if ($mid == $first && $cmd == "prev") {
      $newmid = $id;
    }
    if ($id == $mid && $cmd == "next") {
      $newmid = $first;
    }
  }
  
  // Now work out the stats for this match.
  $query = "select * from $dbaseMatchData where matchid=? and lid=?";
  $result = DBExecute($query, array((int)$newmid, (int)$leagueID));
  if ($result === FALSE) {
    die("Unable to get match data: ".mysqli_error($link));
  }
  $line = mysql_fetch_array($result);
  $hometeam = $line["hometeam"];
  $awayteam = $line["awayteam"];
  
  $results = Array();
  $results = getStatsForMatch($results, $newmid);
  arsort($results);
  $count = count($results);
  
  $numpreds = array_sum($results);

  CloseConnection($link);
?>
<script type="text/javascript">
  var count = <?php echo $count; ?>;
  var scores = new Array(<?php echo $count; ?>);
  var predcount = new Array(<?php echo $count; ?>);
  var numpreds = <?php echo $numpreds; ?>;
<?php

$count = 0;
foreach ($results as $key => $val) {

?>
  scores[<?php echo $count; ?>] = "<?php echo "$key";?>";
  predcount[<?php echo $count; ?>] = "<?php echo "$val";?>";
<?php
  $count ++;
}
?>
  window.parent.setStats("<?php echo $newmid; ?>","<?php echo $mdate; ?>","<?php echo $hometeam; ?>","<?php echo $awayteam; ?>",count,scores,predcount,numpreds);
</script>
