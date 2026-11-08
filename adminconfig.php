<?php
/*********************************************************
 * Author: John Astill (c)
 * Date  : 3rd January 2003
 * File  : adminconfig.php
 *********************************************************/
require "systemvars.php";
require "configvalues.php";
require "security.php";
require "sortfunctions.php";
require "templates/render.php";


// By now the admin user should be logged in.
if ($User->loggedIn == FALSE) {
  echo "Only an Admin User can perform this function";
  exit;
}
if (!CheckAdmin($User->usertype)) {
  echo "Only an Admin User can perform this function";
  exit;
}

/*************************************************
 * Function to display the current config values
 * as a form.
 *************************************************/
function GetCurrentConfig() {
  global $dbaseConfigData;

  $link = OpenConnection();

  $query = "select * from $dbaseConfigData order by grp";
  $result = mysql_query($query)
    or die("Unable to read config data: $query");

  echo "<form action=\"setconfigparams.php\" method=\"POST\">\n";
  echo "<table>\n";
  while ($line = mysql_fetch_array($result)) {
    $grp = $line["grp"];
    $param = $line["param"];
    $descr = $line["descr"];
    $value = $line["value"];
    $ro = $line["ro"];

    echo "<tr class=\"TBLROW\">\n";
    echo "<td class=\"TBLROW\">\n";
    echo "<font class=\"TBLROW\">\n";
    echo htmlspecialchars($descr, ENT_QUOTES, "UTF-8")."\n";
    echo "</font>\n";
    echo "</td>\n";
    echo "<td class=\"TBLROW\">\n";
    echo "<font class=\"TBLROW\">\n";
    if ($ro == 'Y') {
      echo htmlspecialchars($value, ENT_QUOTES, "UTF-8")."\n";
    } else {
      echo RenderConfigControl($param, $value)."\n";
    }
    echo "</font>\n";
    echo "</td>\n";
    echo "</tr>\n";
  }
  echo "</table>\n";
  echo "<input type=\"HIDDEN\" name=\"REFERER\" value=\"adminconfig.php\">\n";
  echo "<input type=\"SUBMIT\" name=\"Change\" value=\"Change\">\n";
  echo "<input type=\"RESET\" name=\"Reset\" value=\"Reset\">\n";
  echo "</form>\n";

  CloseConnection($link);
}


$page = array(
  "title" => "Prediction Football Configuration",
  "heading" => "Configure game parameters [LeagueID $leagueID]",
  "content" => "admin-configuration.php",
  "setup_menu" => TRUE,
  "right_rail" => FALSE
);
renderPage("admin", $page);
