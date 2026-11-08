<?php 
/*********************************************************
 * Author: John Astill (c) 2003
 * Date  : 31st January 2003
 * File  : gamestatsclass.php
 ********************************************************/

 /////////////////////////////////////////////////////////
 // Class for holding the results/stats for each entrant.
 /////////////////////////////////////////////////////////
 class GameStats {

   // Username of entrant
   var $username;

   // The number of exact results predicted.
   var $won;

   // The number of correct winning team predictions.
   var $drawn;

   // The number of entries where the result is incorrect.
   var $lost;

   // The number of correct score per team predictions. The value is the sum
   // of the individual scores.
   var $for;

   // The number of goals incorrectly predicted.
   var $against;

   // Goal difference.
   var $diff;

   // Points
   var $points;

   // Predictions
   var $predictions;

   /************************************************************
    * Constructor used for creating a reset gameStat for a user.
    * @param user the name of the entrant.
    ************************************************************/
   function GameStats($user) {

     $this->predictions = 0;
     $this->username = $user;
     $this->won = 0;
     $this->drawn = 0;
     $this->lost = 0;
     $this->for = 0;
     $this->against = 0;
     $this->diff = 0;
     $this->points = 0;
   }

   /************************************************************
    * Function used to calculate points and goal differences.
    * The scoring is determined as follows:
    *   For an exact prediction
    *    o $corrScore points
    *    o for is incremented by the number of goals scored
    *      by each team.
    *   For a correct prediction
    *    o $corrResult points
    *    o for is incremented by the number of goals scored
    *      by each team that is predicted correctly.
    *    o against is incremented by the number of goals 
    *      incorrectly predicted. e.g if a result is 1-1 and
    *      the predicted score was 2-1, against would be incremented
    *      by 1.
    *   For an incorrect prediction
    *    o 0 points
    *    o for is incremented by the number of goals scored
    *      by each team that is predicted correctly.
    *    o against is incremented by the number of goals 
    *      incorrectly predicted. e.g if a result is 1-1 and
    *      the predicted score was 2-1, against would be incremented
    *      by 1.
    * @param predHome   The predicted home number of goals
    * @param predAway   The predicted away number of goals
    * @param actualHome The actual home number of goals
    * @param actualAway The actual away number of goals
    ************************************************************/
   function UpdateStats($predHome, $predAway, $actualHome, $actualAway) {
     global $corrScore, $corrResult;
     // Increment the number of predictions.
     $this->predictions++;

     // Determine if the correct score is predicted.
     if ($predHome == $actualHome && $predAway == $actualAway) {
       $this->won += 1;
       $this->points += $corrScore;
       $this->for += $predHome + $predAway;
       $this->diff = $this->for - $this->against;
       return;
     }

     // Determine if the correct result is predicted. i.e. the correct
     // winning team or draw.
     if (($predHome > $predAway) && ($actualHome > $actualAway) ||
         ($predHome < $predAway) && ($actualHome < $actualAway) ||
         ($predHome == $predAway) && ($actualHome == $actualAway)) {
       $this->points += $corrResult;
       $this->drawn += 1;
     } else {
       $this->lost += 1;
     }

     // Calculate the for and against values.
     if ($predHome == $actualHome) {
       $this->for += $predHome;
     } else {
       if ($predHome > $actualHome) {
         $this->against += $predHome - $actualHome;
       } else {
         $this->against += $actualHome - $predHome;
       }
     }

     if ($predAway == $actualAway) {
       $this->for += $predAway;
     } else {
       if ($predAway > $actualAway) {
         $this->against += $predAway - $actualAway;
       } else {
         $this->against += $actualAway - $predAway;
       }
     }
     $this->diff = $this->for - $this->against;
   }

   function CalculatePoints($predHome, $predAway, $actualHome, $actualAway) {
     global $corrScore, $corrResult;

     // Determine if the correct score is predicted.
     if ($predHome == $actualHome && $predAway == $actualAway) {
       return $corrScore;
     }

     // Determine if the correct result is predicted. i.e. the correct
     // winning team or draw.
     if (($predHome > $predAway) && ($actualHome > $actualAway) ||
         ($predHome < $predAway) && ($actualHome < $actualAway) ||
         ($predHome == $predAway) && ($actualHome == $actualAway)) {
       return $corrResult;
     }

     return 0; 
   }
   
 }
/*************************************************************
* END OF CLASS
*************************************************************/
?>
