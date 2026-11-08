<?php
/*********************************************************
 * Author: John Astill (c)
 * Date  : 20th March 2003
 * File  : configvalues.php
 *********************************************************/
  $link = OpenConnection();

  // Read every config row for this league into $arr.
  $readConfig = function ($link) use ($dbaseConfigData, $leagueID) {
    $query = "select * from $dbaseConfigData where lid='$leagueID'";
    $result = mysql_query($query, $link)
      or die("Unable to read config data: $query<br>\n".mysql_error($link));
    $rows = array();
    while ($line = mysql_fetch_array($result)) {
      $rows[$line["param"]] = $line["value"];
    }
    return $rows;
  };

  $arr = $readConfig($link);

  // Self-heal: if any required settings are missing (empty or partially
  // imported database, or a league ID with no rows) insert the defaults.
  // PopulateConfigTable(..., TRUE) uses INSERT IGNORE, so settings that
  // already exist are never overwritten.
  $requiredConfigParams = array(
    "CORR_HOME_SCORE", "CORR_AWAY_SCORE", "CORR_MARGIN", "CORR_RESULT", "CORR_SCORE",
    "PRED_LEAGUE_TITLE", "DEF_ICON", "HOME_PAGE_URL", "HOME_PAGE_TITLE", "ADMIN_EMAIL",
    "ADMIN_SIGNATURE", "CHATROOM_URL", "LOGFILE", "DEFAULT_LANGUAGE", "MULT_USERS",
    "TZ_OFFSET", "USE24HR", "REV_USER_PREDS", "USERS_PER_PAGE", "MAX_ADMIN_USERS",
    "USE_MESSAGING", "HIDE_0_PREDS", "NUMMULTFIXTS", "VIEWUSERPREDS", "UPLOADICONS",
    "ICONSIZE", "PASSWORDENCRYPT", "LOCKEDGAME", "AUTO_PREDICT", "VERSION"
  );
  if (count(array_diff($requiredConfigParams, array_keys($arr))) > 0) {
    PopulateConfigTable($link, TRUE);
    $arr = $readConfig($link);
  }

  // There is a tight cohesion between the coding and the contents of the config
  // database. Each value falls back to the default defined in systemvars.php
  // so a missing row can never produce an "Undefined array key" warning.
  $hideNoPredictions = $arr["HIDE_0_PREDS"] ?? "TRUE";
  $useMessaging = $arr["USE_MESSAGING"] ?? "TRUE";
  $corrHomeScore = $arr["CORR_HOME_SCORE"] ?? $corrHomeScore;
  $corrAwayScore = $arr["CORR_AWAY_SCORE"] ?? $corrAwayScore;
  $corrMargin = $arr["CORR_MARGIN"] ?? $corrMargin;
  $corrResult = $arr["CORR_RESULT"] ?? $corrResult;
  $corrScore = $arr["CORR_SCORE"] ?? $corrScore;
  $PredictionLeagueTitle = $arr["PRED_LEAGUE_TITLE"] ?? $PredictionLeagueTitle;
  $defaulticon = $arr["DEF_ICON"] ?? $defaulticon;
  $adminEmailAddr = $arr["ADMIN_EMAIL"] ?? $adminEmailAddr;
  $adminSignature = $arr["ADMIN_SIGNATURE"] ?? $adminSignature;
  $chatRoomURL = $arr["CHATROOM_URL"] ?? $chatRoomURL;
  $logfile = $arr["LOGFILE"] ?? $logfile;
  $languageCandidate = isset($arr["DEFAULT_LANGUAGE"]) ? basename($arr["DEFAULT_LANGUAGE"]) : $languageFile;
  if (preg_match('/\Alang\.[A-Za-z0-9_-]+\.php\z/', $languageCandidate) && is_file(__DIR__."/lang/".$languageCandidate)) {
    $languageFile = $languageCandidate;
  }
  $homePage = $arr["HOME_PAGE_URL"] ?? $homePage;
  $homePageTitle = $arr["HOME_PAGE_TITLE"] ?? $homePageTitle;
  $allowMultipleUserPerEmail = $arr["MULT_USERS"] ?? $allowMultipleUserPerEmail;
  $timezoneOffset = $arr["TZ_OFFSET"] ?? $timezoneOffset;
  $reverseUserPredictions = $arr["REV_USER_PREDS"] ?? $reverseUserPredictions;
  $usersPerPage = $arr["USERS_PER_PAGE"] ?? $usersPerPage;
  if ($usersPerPage < 1) {
    $usersPerPage = 10;
  }
  $maxAdminUsers = $arr["MAX_ADMIN_USERS"] ?? $maxAdminUsers;
  //$isauto = $arr["ISAUTO"];
  $NumMultFixts = $arr["NUMMULTFIXTS"] ?? $NumMultFixts;
  $ViewUserPredictions = $arr["VIEWUSERPREDS"] ?? $ViewUserPredictions;
  $Use24Hr = $arr["USE24HR"] ?? $Use24Hr;
  $UploadIcons = $arr["UPLOADICONS"] ?? $UploadIcons;
  $MaxIconFileSize = $arr["ICONSIZE"] ?? $MaxIconFileSize;
  $PasswordEncryption = $arr["PASSWORDENCRYPT"] ?? $PasswordEncryption;
  $LockedGame = $arr["LOCKEDGAME"] ?? $LockedGame;
  $autoPredict = $arr["AUTO_PREDICT"] ?? $autoPredict;

  CloseConnection($link);

  function RenderConfigControl($param, $value) {
    $name = htmlspecialchars($param, ENT_QUOTES, "UTF-8");
    $escapedValue = htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
    $booleanOptions = array("MULT_USERS", "USE24HR", "REV_USER_PREDS", "USE_MESSAGING", "HIDE_0_PREDS", "VIEWUSERPREDS", "UPLOADICONS", "PASSWORDENCRYPT", "LOCKEDGAME", "AUTO_PREDICT");
    if (in_array($param, $booleanOptions, TRUE)) {
      $selectedTrue = strtoupper((string)$value) === "TRUE" ? " selected" : "";
      $selectedFalse = strtoupper((string)$value) === "TRUE" ? "" : " selected";
      return "<select name=\"$name\"><option value=\"TRUE\"$selectedTrue>TRUE</option><option value=\"FALSE\"$selectedFalse>FALSE</option></select>";
    }
    if ($param === "DEFAULT_LANGUAGE") {
      $options = "";
      foreach (glob(__DIR__."/lang/lang.*.php") as $languagePath) {
        $language = basename($languagePath);
        $selected = $language === $value ? " selected" : "";
        $label = substr($language, 5, -4);
        $options .= "<option value=\"".htmlspecialchars($language, ENT_QUOTES, "UTF-8")."\"$selected>".htmlspecialchars($label, ENT_QUOTES, "UTF-8")."</option>";
      }
      return "<select name=\"$name\">$options</select>";
    }
    if ($param === "ADMIN_SIGNATURE") {
      return "<textarea name=\"$name\" rows=\"4\" cols=\"40\">$escapedValue</textarea>";
    }
    if ($param === "ADMIN_EMAIL") {
      return "<input type=\"email\" name=\"$name\" value=\"$escapedValue\">";
    }
    if ($param === "HOME_PAGE_URL" || $param === "CHATROOM_URL") {
      return "<input type=\"url\" name=\"$name\" value=\"$escapedValue\">";
    }
    if (in_array($param, array("TZ_OFFSET", "USERS_PER_PAGE", "MAX_ADMIN_USERS", "NUMMULTFIXTS", "ICONSIZE", "CORR_HOME_SCORE", "CORR_AWAY_SCORE", "CORR_MARGIN", "CORR_RESULT", "CORR_SCORE"), TRUE)) {
      return "<input type=\"number\" name=\"$name\" value=\"$escapedValue\" step=\"any\">";
    }
    return "<input type=\"text\" name=\"$name\" value=\"$escapedValue\">";
  }
?>
