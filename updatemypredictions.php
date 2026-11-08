<?php 
/*********************************************************
 * Author: John Astill
 * Date  : 25th Feb 2003 (c)
 * File  : updatemypredictions.php
 * Desc  : Allow the user to update the predictions.
 ********************************************************/
require "systemvars.php";
require "configvalues.php";
require "sortfunctions.php";
require "security.php";

$isJsonRequest = isset($_SERVER["CONTENT_TYPE"]) && stripos($_SERVER["CONTENT_TYPE"], "application/json") !== FALSE;
$payload = $isJsonRequest ? json_decode(file_get_contents("php://input"), TRUE) : $_POST;

function PredictionResponse($statusCode, $body) {
  http_response_code($statusCode);
  header("Content-Type: application/json; charset=utf-8");
  echo json_encode($body);
  exit;
}

if ($User->loggedIn == FALSE) {
  if ($isJsonRequest) {
    PredictionResponse(401, array("success" => FALSE, "error" => "Please sign in again."));
  }
  $ip = $_SERVER["REMOTE_ADDR"];
  $port = $_SERVER["REMOTE_PORT"];
  $referer = $_SERVER["HTTP_REFERER"];
  
  LogMsg("Attempt by user not logged in to make predictions. This could be a spoofed page. IP Address $ip port $port referring page "); 
  forward("index.php");
  exit;
}
$userid = (int)$User->userid;

if (!is_array($payload) || !isset($payload["csrf_token"]) || !is_string($payload["csrf_token"]) || !hash_equals($_SESSION["csrf_token"], $payload["csrf_token"])) {
  if ($isJsonRequest) {
    PredictionResponse(403, array("success" => FALSE, "error" => "Your session token has expired. Reload the page and try again."));
  }
  http_response_code(403);
  die("Your session token has expired. Reload the page and try again.");
}

$submittedPredictions = isset($payload["predictions"]) && is_array($payload["predictions"]) ? $payload["predictions"] : array();
if (count($submittedPredictions) > 500) {
  if ($isJsonRequest) {
    PredictionResponse(400, array("success" => FALSE, "error" => "Too many predictions in one request."));
  }
  http_response_code(400);
  die("Too many predictions in one request.");
}

// Form submissions use match IDs as keys; JSON submissions carry match_id values.
$scoresByMatch = array();
foreach ($submittedPredictions as $key => $prediction) {
  if (!is_array($prediction)) {
    continue;
  }
  $matchIdValue = isset($prediction["match_id"]) ? $prediction["match_id"] : $key;
  $matchId = filter_var($matchIdValue, FILTER_VALIDATE_INT, array("options" => array("min_range" => 1)));
  if ($matchId === FALSE) {
    continue;
  }
  $homeScoreValue = isset($prediction["home_score"]) ? $prediction["home_score"] : "";
  $awayScoreValue = isset($prediction["away_score"]) ? $prediction["away_score"] : "";
  if ($homeScoreValue === "" || $awayScoreValue === "") {
    continue;
  }
  $homeScore = filter_var($homeScoreValue, FILTER_VALIDATE_INT, array("options" => array("min_range" => 0, "max_range" => 65535)));
  $awayScore = filter_var($awayScoreValue, FILTER_VALIDATE_INT, array("options" => array("min_range" => 0, "max_range" => 65535)));
  if ($homeScore === FALSE || $awayScore === FALSE) {
    if ($isJsonRequest) {
      PredictionResponse(422, array("success" => FALSE, "error" => "Scores must be whole numbers from 0 to 65535."));
    }
    http_response_code(422);
    die("Scores must be whole numbers from 0 to 65535.");
  }
  $scoresByMatch[$matchId] = array($homeScore, $awayScore);
}

if (count($scoresByMatch) === 0) {
  if ($isJsonRequest) {
    PredictionResponse(200, array("success" => TRUE, "saved" => 0, "redirect" => "showmypredictions.php?sid=".rawurlencode($SID)));
  }
  forward("showmypredictions.php?sid=".rawurlencode($SID));
}

$link = OpenConnection();
if (!mysqli_begin_transaction($link)) {
  die("Unable to start prediction update: ".mysqli_error($link));
}

$matchIds = array_keys($scoresByMatch);
$placeholders = implode(",", array_fill(0, count($matchIds), "?"));
$query = "SELECT matchid,matchdate FROM $dbaseMatchData WHERE lid=? AND matchid IN ($placeholders) FOR UPDATE";
$parameters = array_merge(array((int)$leagueID), $matchIds);
$matchResult = DBExecute($query, $parameters, $link);
if ($matchResult === FALSE) {
  mysqli_rollback($link);
  die("Unable to verify prediction fixtures: ".mysqli_error($link));
}

$eligibleMatchIds = array();
while ($match = mysqli_fetch_assoc($matchResult)) {
  if (CompareDatetime($match["matchdate"]) >= 0) {
    $eligibleMatchIds[(int)$match["matchid"]] = TRUE;
  }
}
mysqli_free_result($matchResult);

$values = array();
$parameters = array();
foreach ($scoresByMatch as $matchId => $scores) {
  if (!isset($eligibleMatchIds[$matchId])) {
    continue;
  }
  $values[] = "(?,?,?,?,?)";
  $parameters[] = (int)$leagueID;
  $parameters[] = (int)$userid;
  $parameters[] = (int)$matchId;
  $parameters[] = $scores[0];
  $parameters[] = $scores[1];
}

if (count($values) > 0) {
  $query = "REPLACE INTO $dbasePredictionData (lid,userid,matchid,homescore,awayscore) VALUES ".implode(",", $values);
  if (DBExecute($query, $parameters, $link) === FALSE) {
    mysqli_rollback($link);
    die("Unable to save predictions: ".mysqli_error($link));
  }
}

if (!mysqli_commit($link)) {
  mysqli_rollback($link);
  die("Unable to commit predictions: ".mysqli_error($link));
}
CloseConnection($link);

if ($isJsonRequest) {
  PredictionResponse(200, array("success" => TRUE, "saved" => count($values), "redirect" => "showmypredictions.php?sid=".rawurlencode($SID)));
}
forward("showmypredictions.php?sid=".rawurlencode($SID));
?>

