<?php
/*********************************************************
 * Author: John Astill
 * Date  : 10th December
 * File  : createdbase.php
 * Desc  : Create the required database and tables for
 *       : the prediction league. This script interacts
 *       : with the given database to create the correct
 *       : structure of tables for the prediction league.
 *       : If the tables are created successfully, the 
 *       : created structure of database and tables is 
 *       : displayed.
 ********************************************************/
require "systemvars.php";

  $url = $HTTP_POST_VARS["URL"];
  $dbname = $HTTP_POST_VARS["DBASENAME"];
  $username = $HTTP_POST_VARS["USERNAME"];
  $password = $HTTP_POST_VARS["PASSWORD"];
  $create = $HTTP_POST_VARS["CREATEDB"];
  $createtables = $HTTP_POST_VARS["CREATETABLES"];

  if (!preg_match('/\A[A-Za-z0-9_]{1,64}\z/', $dbname)) {
    die("Invalid database name");
  }
  $userDataTblName = $dbaseUserData;
  $predDataTblName = $dbasePredictionData;
  $matchDataTblName = $dbaseMatchData;
  $standingsTblName = $dbaseStandings;
  $tableNames = array($userDataTblName, $predDataTblName, $matchDataTblName, $standingsTblName, $dbaseConfigData, $dbaseMsgData);
  foreach ($tableNames as $tableName) {
    if (!preg_match('/\A[A-Za-z0-9_]{1,64}\z/', $tableName)) {
      die("Invalid table name in configuration");
    }
  }

  // Connect to the host.
  $link = mysql_connect($url, $username, $password)
      or die("Could not connect to $url");
  mysqli_set_charset($link, "utf8mb4");

  // Only create the database if requested to.
  if ($create == "TRUE") {
    // Create the database
    $db = mysql_query("CREATE DATABASE `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci",$link);
    if ($db == FALSE) {
      echo "Unable to create database $dbname. Make sure $dbname does not already exist\n";
      exit;
    }
  }

  if ($createtables == "TRUE") {
    $query = "CREATE TABLE `$dbname`.`$dbaseUserData` (lid INT NOT NULL, userid INT NOT NULL AUTO_INCREMENT, username VARCHAR(".(int)$userlen.") NOT NULL, password VARCHAR(".(int)$passlen."), email VARCHAR(".(int)$emaillen."), icon VARCHAR(".(int)$fnamelen."), lang VARCHAR(32), usertype SMALLINT, dflths SMALLINT DEFAULT 0, dfltas SMALLINT DEFAULT 0, since DATE, auto ENUM('Y','N') DEFAULT 'N', PRIMARY KEY (lid,userid), KEY idx_userid_auto (userid)) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci";
    $userresult = mysql_query($query)
        or die("Query failed: $query<br>\n".mysql_error());

    $query = "CREATE TABLE `$dbname`.`$dbasePredictionData` (lid INT NOT NULL, userid INT NOT NULL, matchid INT NOT NULL, predtime TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, homescore SMALLINT UNSIGNED, awayscore SMALLINT UNSIGNED, isauto ENUM('Y','N') DEFAULT 'N', PRIMARY KEY(lid,userid,matchid)) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci";
    $userresult = mysql_query($query)
      or die("Query failed: $query<br>\n".mysql_error());

    $query = "CREATE TABLE `$dbname`.`$dbaseMatchData` (lid INT NOT NULL, matchid INT NOT NULL AUTO_INCREMENT, matchdate DATETIME NOT NULL, hometeam VARCHAR(".(int)$teamlen.") NOT NULL, awayteam VARCHAR(".(int)$teamlen.") NOT NULL, homescore SMALLINT UNSIGNED, awayscore SMALLINT UNSIGNED, PRIMARY KEY(lid,matchid), KEY idx_matchid_auto (matchid)) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci";
    $userresult = mysql_query($query)
      or die("Query failed: $query<br>\n".mysql_error());

    $query = "CREATE TABLE `$dbname`.`$dbaseStandings` (lid INT NOT NULL, userid INT NOT NULL, position SMALLINT NOT NULL, pld INT UNSIGNED, won INT UNSIGNED, drawn INT UNSIGNED, lost INT UNSIGNED, gfor SMALLINT UNSIGNED, gagainst SMALLINT UNSIGNED, diff SMALLINT, points INT UNSIGNED, PRIMARY KEY(lid,userid)) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci";
    $userresult = mysql_query($query)
      or die("Query failed: $query<br>\n".mysql_error());

    $query = "CREATE TABLE `$dbname`.`$dbaseConfigData` (lid INT NOT NULL, grp INT NOT NULL, param VARCHAR(32) NOT NULL, value VARCHAR(90) NOT NULL, descr TEXT NOT NULL, ro ENUM('Y','N') DEFAULT 'N', PRIMARY KEY(lid,param)) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci";
    $userresult = mysql_query($query)
      or die("Query failed: $query<br>\n".mysql_error());

    $query = "CREATE TABLE `$dbname`.`$dbaseMsgData` (lid INT NOT NULL, msgid INT NOT NULL AUTO_INCREMENT, threadid INT, prevmsg INT, sender INT NOT NULL, receiver INT NOT NULL, subject VARCHAR(255), body TEXT, status ENUM('new','read','deleted'), msgtime TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(lid,msgid), KEY idx_msgid_auto (msgid)) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci";
    $userresult = mysql_query($query)
      or die("Query failed: $query<br>\n".mysql_error());
  }

  mysql_select_db($dbname, $link)
      or die("Could not select database $dbname: ".mysql_error($link));
  PopulateConfigTable($link);
?>
<html>
  <head>
    <title>
      Created Database <?php echo $dbname; ?>
    </title>
    <link rel="stylesheet" href="common.css" type="text/css">
  </head>

  <body class="MAIN">
    <table>
      <tr>
        <td class="TBLHEAD" colspan="4" align="CENTER">
          <font class="TBLHEAD">
            Database and Table Administration
          </font>
        </td>
      </tr>
      <tr>
        <td class="LEFTCOL">
          <font class="TBLHEAD">
            <?php require "adminsetupmenu.php"; ?>
          </font>
        </td>
        <td class="CENTERCOL" colspan="3" align="CENTER">
          <font class="TBLHEAD">
<table width="400">
<tr>
<td colspan="2" align="CENTER" class="TBLHEAD">
<font class="TBLHEAD">
<b>
Database <?php echo $dbname ?>
</b>
</font>
</td>
</tr>
<tr>
<td align="CENTER" class="TBLHEAD">
<font class="TBLHEAD">
Table
</font>
</td>
<td align="CENTER" class="TBLHEAD">
<font class="TBLHEAD">
Attributes
</font>
</td>
</tr>
<?php
  
  // List the table names.
  $quotedDatabase = DBQuoteIdentifier($dbname);
  $tbl_list = mysqli_query($link, "SHOW TABLES FROM $quotedDatabase");
  if ($tbl_list === FALSE) {
    die("Unable to list tables: ".mysqli_error($link));
  }
  while ($tableRow = mysqli_fetch_row($tbl_list)) {
    echo "<tr><td valign=\"TOP\" class=\"TBLROW\"><font class=\"TBLROW\">";
      $tableName = $tableRow[0];
      echo htmlspecialchars($tableName, ENT_QUOTES, "UTF-8");

      // List the field names.
      $quotedTable = DBQuoteIdentifier($tableName);
      $fields = mysqli_query($link, "SHOW COLUMNS FROM $quotedDatabase.$quotedTable");
      if ($fields === FALSE) {
        die("Unable to list columns: ".mysqli_error($link));
      }

      echo "</font></td>";
      echo "<td class=\"TBLROW\"><font class=\"TBLROW\">";
      while ($fieldRow = mysqli_fetch_assoc($fields)) {
          echo htmlspecialchars($fieldRow["Field"], ENT_QUOTES, "UTF-8") . "<br>\n";
      }
      mysqli_free_result($fields);
    echo "</font></td></tr>";
  }
  mysqli_free_result($tbl_list);
?>
</table>

          </font>
        </td>
      </tr>
    </table>

  </body>
</html>

