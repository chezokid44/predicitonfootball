<?php
/*********************************************************
 * Author: John Astill (c)
 * Date  : 20th March 2003
 * File  : configvalues.php
 *********************************************************/
  $link = OpenConnection();
  $query = "select * from $dbaseConfigData where lid='$leagueID'";

  $result = mysql_query($query) 
    or die("Unable to read config data: $query<br>\n".mysql_error());
  if ($result != FALSE) {
    $arr = array();
    while ($line = mysql_fetch_array($result)) {
      $param = $line["param"];
      $val = $line["value"];
      $arr["$param"] = $val;
    }

    // There is a tight cohesion between the coding and the contents of the config
    // database.
    $hideNoPredictions = $arr["HIDE_0_PREDS"];
    $useMessaging = $arr["USE_MESSAGING"];
    $corrHomeScore = $arr["CORR_HOME_SCORE"];
    $corrAwayScore = $arr["CORR_AWAY_SCORE"];
    $corrMargin = $arr["CORR_MARGIN"];
    $corrResult = $arr["CORR_RESULT"];
    $corrScore = $arr["CORR_SCORE"];
    $PredictionLeagueTitle = $arr["PRED_LEAGUE_TITLE"];
    $defaulticon = $arr["DEF_ICON"];
    $adminEmailAddr = isset($arr["ADMIN_EMAIL"]) ? $arr["ADMIN_EMAIL"] : $adminEmailAddr;
    $adminSignature = isset($arr["ADMIN_SIGNATURE"]) ? $arr["ADMIN_SIGNATURE"] : $adminSignature;
    $chatRoomURL = isset($arr["CHATROOM_URL"]) ? $arr["CHATROOM_URL"] : $chatRoomURL;
    $logfile = isset($arr["LOGFILE"]) ? $arr["LOGFILE"] : $logfile;
    $languageCandidate = isset($arr["DEFAULT_LANGUAGE"]) ? basename($arr["DEFAULT_LANGUAGE"]) : $languageFile;
    if (preg_match('/\Alang\.[A-Za-z0-9_-]+\.php\z/', $languageCandidate) && is_file(__DIR__."/lang/".$languageCandidate)) {
      $languageFile = $languageCandidate;
    }
    $homePage = $arr["HOME_PAGE_URL"];
    $homePageTitle = $arr["HOME_PAGE_TITLE"];
    $allowMultipleUserPerEmail = $arr["MULT_USERS"];
    $timezoneOffset = $arr["TZ_OFFSET"];
    $reverseUserPredictions = $arr["REV_USER_PREDS"];
    $usersPerPage = $arr["USERS_PER_PAGE"];
    if ($usersPerPage < 1) {
      $usersPerPage = 10;
    }
    $maxAdminUsers = $arr["MAX_ADMIN_USERS"];
    //$isauto = $arr["ISAUTO"];
    $NumMultFixts = $arr["NUMMULTFIXTS"];
    $ViewUserPredictions = $arr["VIEWUSERPREDS"];
    $Use24Hr = $arr["USE24HR"];
    $UploadIcons = $arr["UPLOADICONS"];
    $MaxIconFileSize = $arr["ICONSIZE"];
    $PasswordEncryption = $arr["PASSWORDENCRYPT"];
    $LockedGame = $arr["LOCKEDGAME"];
    $autoPredict = isset($arr["AUTO_PREDICT"]) ? $arr["AUTO_PREDICT"] : $autoPredict;
  }

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
