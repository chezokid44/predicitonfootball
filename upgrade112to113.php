<?php
require "systemvars.php";

$tables = array(
  $dbaseConfigData,
  $dbaseMatchData,
  $dbaseMsgData,
  $dbasePredictionData,
  $dbaseStandings,
  $dbaseUserData
);

$link = OpenConnection();
foreach ($tables as $table) {
  $tableName = DBQuoteIdentifier($table);
  if ($table == $dbaseMatchData) {
    $index = "idx_matchid_auto";
    $column = "matchid";
  } elseif ($table == $dbaseMsgData) {
    $index = "idx_msgid_auto";
    $column = "msgid";
  } elseif ($table == $dbaseUserData) {
    $index = "idx_userid_auto";
    $column = "userid";
  } else {
    $index = "";
  }

  if ($index != "") {
    $indexQuery = "SELECT COUNT(*) AS index_count FROM information_schema.statistics WHERE table_schema=DATABASE() AND table_name=? AND index_name=?";
    $indexResult = DBExecute($indexQuery, array($table, $index), $link);
    if ($indexResult === FALSE) {
      die("Unable to inspect table indexes: ".mysqli_error($link));
    }
    $indexRow = mysqli_fetch_assoc($indexResult);
    if ((int)$indexRow["index_count"] === 0) {
      $result = mysql_query("ALTER TABLE $tableName ADD INDEX ".DBQuoteIdentifier($index)." (".DBQuoteIdentifier($column).")", $link);
      if ($result === FALSE) {
        die("Unable to add auto-increment index: ".mysqli_error($link));
      }
    }
  }

  $result = mysql_query("ALTER TABLE $tableName ENGINE=InnoDB, CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci", $link);
  if ($result === FALSE) {
    die("Unable to modernize table ".$table.": ".mysqli_error($link));
  }
}

PopulateConfigTable($link, TRUE);

foreach (array($dbasePredictionData => "predtime", $dbaseMsgData => "msgtime") as $table => $column) {
  $query = "ALTER TABLE ".DBQuoteIdentifier($table)." MODIFY ".DBQuoteIdentifier($column)." TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP";
  if (mysql_query($query, $link) === FALSE) {
    die("Unable to modernize timestamp column: ".mysqli_error($link));
  }
}

$query = "REPLACE INTO $dbaseConfigData (lid,param,value,descr,ro) VALUES (?,?,?,?,?)";
if (DBExecute($query, array((int)$leagueID, "VERSION", VERSION, "Current Version", "Y"), $link) === FALSE) {
  die("Unable to update schema version: ".mysqli_error($link));
}

CloseConnection($link);
echo "Database upgraded to version ".VERSION.". Remove this upgrade script from the server.";
?>