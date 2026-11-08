<?php
/*********************************************************
 * Author: John Astill
 * Date  : 9th December
 * File  : dbasefunctions.php
 ********************************************************/

if (!defined("MYSQL_ASSOC")) define("MYSQL_ASSOC", MYSQLI_ASSOC);
if (!defined("MYSQL_NUM")) define("MYSQL_NUM", MYSQLI_NUM);
if (!defined("MYSQL_BOTH")) define("MYSQL_BOTH", MYSQLI_BOTH);

if (!function_exists("mysql_connect")) {
  $GLOBALS["_legacy_mysql_connection"] = FALSE;

  function mysql_connect($host, $username, $password) {
    mysqli_report(MYSQLI_REPORT_OFF);
    $connection = mysqli_connect($host, $username, $password);
    $GLOBALS["_legacy_mysql_connection"] = $connection;
    return $connection;
  }

  function mysql_query($query, $connection = NULL) {
    if ($connection === NULL) {
      $connection = $GLOBALS["_legacy_mysql_connection"];
    }
    return $connection ? mysqli_query($connection, $query) : FALSE;
  }

  function mysql_close($connection = NULL) {
    if ($connection === NULL) {
      $connection = $GLOBALS["_legacy_mysql_connection"];
    }
    return $connection ? mysqli_close($connection) : FALSE;
  }

  function mysql_error($connection = NULL) {
    if ($connection === NULL) {
      $connection = $GLOBALS["_legacy_mysql_connection"];
    }
    return $connection ? mysqli_error($connection) : "No active database connection";
  }

  function mysql_select_db($database, $connection = NULL) {
    if ($connection === NULL) {
      $connection = $GLOBALS["_legacy_mysql_connection"];
    }
    return $connection ? mysqli_select_db($connection, $database) : FALSE;
  }

  function mysql_fetch_array($result, $mode = MYSQL_BOTH) {
    return mysqli_fetch_array($result, $mode);
  }

  function mysql_fetch_row($result) {
    return mysqli_fetch_row($result);
  }

  function mysql_fetch_assoc($result) {
    return mysqli_fetch_assoc($result);
  }

  function mysql_free_result($result) {
    return mysqli_free_result($result);
  }

  function mysql_num_rows($result) {
    return mysqli_num_rows($result);
  }

  function mysql_affected_rows($connection = NULL) {
    if ($connection === NULL) {
      $connection = $GLOBALS["_legacy_mysql_connection"];
    }
    return $connection ? mysqli_affected_rows($connection) : FALSE;
  }
}

function DBExecute($query, $parameters = array(), $connection = NULL) {
  if ($connection === NULL) {
    $connection = $GLOBALS["_legacy_mysql_connection"];
  }
  if (!$connection) {
    return FALSE;
  }

  $statement = mysqli_prepare($connection, $query);
  if (!$statement) {
    return FALSE;
  }
  if (count($parameters) > 0) {
    $types = "";
    $boundParameters = array();
    foreach ($parameters as $index => $parameter) {
      $types .= is_int($parameter) ? "i" : (is_float($parameter) ? "d" : "s");
      $boundParameters[$index] = &$parameters[$index];
    }
    if (!mysqli_stmt_bind_param($statement, $types, ...$boundParameters)) {
      mysqli_stmt_close($statement);
      return FALSE;
    }
  }
  if (!mysqli_stmt_execute($statement)) {
    mysqli_stmt_close($statement);
    return FALSE;
  }
  $hasResult = mysqli_stmt_field_count($statement) > 0;
  $result = mysqli_stmt_get_result($statement);
  mysqli_stmt_close($statement);
  if ($hasResult && $result === FALSE) {
    return FALSE;
  }
  return $result === FALSE ? TRUE : $result;
}

function DBQuoteIdentifier($identifier) {
  if (!preg_match('/\A[A-Za-z0-9_]{1,64}\z/', (string)$identifier)) {
    die("Invalid database identifier");
  }
  return "`".$identifier."`";
}

function DBError($connection = NULL) {
  if ($connection === NULL) {
    $connection = $GLOBALS["_legacy_mysql_connection"];
  }
  return $connection ? mysqli_error($connection) : "No active database connection";
}

/*******************************************************
* Open a connection to the database and select the
* database.
* The database name and connection information is
* taken from Global Configration files.
* @return TRUE if the connection is created 
* successfully
*******************************************************/
function OpenConnection() {

  // Allow access to the global table name.
  global $dbaseHost, $dbaseName, $dbaseUsername, $dbasePassword;

  // Connecting, selecting database
  $link = mysql_connect($dbaseHost, $dbaseUsername, $dbasePassword)
      or die("Could not connect\n".mysql_error());

  //mysql_select_db($dbaseName, $link)
  mysqli_set_charset($link, "utf8mb4");
  mysql_query("USE ".DBQuoteIdentifier($dbaseName), $link)
      or die("Could not select database $dbaseName for link[$link]\n".mysql_error());

  return $link;
}

/*******************************************************
* Close the database connection.
* @param link - the link returned when the connection
*               was opened.
*******************************************************/
function CloseConnection($link) {
  // Closing connection
  mysql_close($link);
}

/*********************************************************
 * Compare the two given dates.
 * @param d1 the first date
 * @param d2 the second date
 * @return -1 if d1 < d2
 *          0 if d1 = d2
 *          1 if d1 > d2
 ********************************************************/
function CompareDates($d1, $d2) {
  if ($d1 < $d2) {
    return -1;
  }
  if ($d1 > $d2) {
    return 1;
  }
  return 0;
}

/*********************************************************
 * Compare the given date with the current date.
 * Strip the individual components from the date in the
 * format YYYY-MM-DD.
 * @param d1 the first date
 * @return -1 if d1 < Current date
 *          0 if d1 = Current date
 *          1 if d1 > Current date
 ********************************************************/
function CompareDate($d1) {
  $year = substr($d1,0,4);
  $month = substr($d1,5,2);
  $day = substr($d1,8,2);

  $currentyear = date("Y");
  $currentmonth = date("m");
  $currentday = date("d");

  // Test the year first
  if ($year < $currentyear) {
    return -1;
  }
  // Test the year first
  if ($year > $currentyear) {
    return 1;
  }

  // At this point the year is the same.
  // Test the month
  if ($month < $currentmonth) {
    return -1;
  }
  if ($month > $currentmonth) {
    return 1;
  }

  // Finally the day
  if ($day < $currentday) {
    return -1;
  }
  if ($day > $currentday) {
    return 1;
  }

  // They are equal
  return 0;
}

/*********************************************************
 * Compare the given datetime with the current datetime.
 * Strip the individual components from the date in the
 * format YYYY-MM-DD.
 * @param d1 the first date
 * @return -1 if d1 < Current date
 *          0 if d1 = Current date
 *          1 if d1 > Current date
 ********************************************************/
function CompareDatetime($date) {
  // If the date isn't today, return the CompareDate value.
  $result = CompareDate($date);
  if ($result != 0) {
    return $result;
  }

  $currentHours = date("H");
  $currentMinutes = date("i");
  $hours = substr($date,11,2);
  $minutes = substr($date,14,2);
  if ($hours < $currentHours) {
    return -1;
  } else if ($hours > $currentHours) {
    return 1;
  }

  if ($minutes < $currentMinutes) {
    return -1;
  } else if ($minutes > $currentMinutes) {
    return 1;
  }

  // Equal
  return 0;
}

function SetConfigParam($grp, $param, $desc, $value, $ro) {
  global $dbaseConfigData, $leagueID, $configConnection, $configPreserveExisting;

  $queryVerb = !empty($configPreserveExisting) ? "INSERT IGNORE INTO" : "REPLACE INTO";
  $query = "$queryVerb $dbaseConfigData (lid,grp,param,descr,value,ro) VALUES (?,?,?,?,?,?)";
  if (DBExecute($query, array((int)$leagueID, (int)$grp, $param, $desc, $value, $ro), $configConnection) === FALSE) {
    die("Unable to set config var: ".DBError($configConnection));
  }
}

function PopulateConfigTable($connection = NULL, $preserveExisting = FALSE) {
  global $configConnection, $configPreserveExisting;
  global $defaultAdminEmailAddr, $defaultAdminSignature, $defaultChatRoomURL, $defaultLogfile, $defaultLanguageFile, $defaultAutoPredict;
  $ownsConnection = ($connection === NULL);
  $configConnection = $ownsConnection ? OpenConnection() : $connection;
  $configPreserveExisting = $preserveExisting;

  // Scoring
  SetConfigParam(1,"CORR_HOME_SCORE","The number of points awarded for predicting the correct home score","1","N");
  SetConfigParam(1,"CORR_AWAY_SCORE","The number of points awarded for predicting the correct away score","1","N");
  SetConfigParam(1,"CORR_MARGIN","Number of points for predicting the correct margin of points.","1","N");
  SetConfigParam(1,"CORR_RESULT","Points awarded for predicting the correct winner or draw, i,e. correct result.","1","N");
  SetConfigParam(1,"CORR_SCORE","Points for predicting the correct score.","3","N");

  // Title and descriptions
  SetConfigParam(2,"PRED_LEAGUE_TITLE","The title for the prediction league","Prediction Football.com","N");
  SetConfigParam(2,"DEF_ICON","The default icon for the users.","default.gif","N");
  SetConfigParam(2,"HOME_PAGE_URL","Your home page URL. This will be a link from the menu. If you leave this blank, no menu will show up","http://www.predictionfootball.com","N");
  SetConfigParam(2,"HOME_PAGE_TITLE","The title of your home page. Will show as the link on the menu.","Prediction Football.com","N");
  SetConfigParam(2,"ADMIN_EMAIL","Administrator contact address used for account and system emails.",$defaultAdminEmailAddr,"N");
  SetConfigParam(2,"ADMIN_SIGNATURE","Signature appended to administrator emails.",$defaultAdminSignature,"N");
  SetConfigParam(2,"CHATROOM_URL","Optional chat-room URL shown in the navigation menu.",$defaultChatRoomURL,"N");
  SetConfigParam(2,"LOGFILE","Optional server logfile path visible to administrators.",$defaultLogfile,"N");
  SetConfigParam(2,"DEFAULT_LANGUAGE","Default language file, for example lang.english.php.",$defaultLanguageFile,"N");
  SetConfigParam(2,"MULT_USERS","Set this if you want to allow multiple users to share the same email address. Values can be TRUE or FALSE","FALSE","N");
  SetConfigParam(2,"TZ_OFFSET","If your server is in a different time zone than your games, set this to the required offset","0","N");
  SetConfigParam(2,"USE24HR","Display the time using the 24 hour clock, TRUE or FALSE","TRUE","N");
  SetConfigParam(2,"REV_USER_PREDS","Change the order in which the user sees their predictions. Values can be TRUE or FALSE","TRUE","N");
  SetConfigParam(2,"USERS_PER_PAGE","The number of users displayed in the table on one page.","80","N");
  SetConfigParam(2,"MAX_ADMIN_USERS","The maximum number of admin users allowed. Keep this to a minimum for security reasons.","1","N");
  SetConfigParam(3,"USE_MESSAGING","Enable the use of messaging between users. Values can be TRUE or FALSE","TRUE","N");
  SetConfigParam(3,"HIDE_0_PREDS","Hide the display of users with 0 predictions. Changing this value will require you to recalculate the standings to see the effect. Values can be TRUE or FALSE","TRUE","N");
  //SetConfigParam(3,"ISAUTO","Enable the use of automatic predictions. Values can be TRUE or FALSE","TRUE","N");
  SetConfigParam(3,"NUMMULTFIXTS","The number of multiple fixtures to display when adding multiple fixtures.","12","N");
  SetConfigParam(3,"VIEWUSERPREDS","Can players see each others predictions, TRUE or FALSE","TRUE","N");
  SetConfigParam(3,"UPLOADICONS","Can users upload their own icons, TRUE or FALSE","TRUE","N");
  SetConfigParam(3,"ICONSIZE","Maximum file size in KB of the icon","50","N");
  SetConfigParam(3,"PASSWORDENCRYPT","Enable the encryption of user passwords. If you are enabling then the users must use the forgot password function to get their new password. TRUE or FALSE.","TRUE","N");
  SetConfigParam(3,"LOCKEDGAME","Lock the game so that only admins can create users","FALSE","N");
  SetConfigParam(3,"AUTO_PREDICT","Enable automatic predictions where supported.",$defaultAutoPredict,"N");
  
  // Version
  SetConfigParam(4,"VERSION","The currently running version of the Prediction League scripts",VERSION,"Y");

  if ($ownsConnection) {
    CloseConnection($configConnection);
  }
  $configConnection = NULL;
  $configPreserveExisting = FALSE;

}
?>
