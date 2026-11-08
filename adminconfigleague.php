<?php
/*********************************************************
 * Author: John Astill (c)
 * Date  : March 20th 2003
 * File  : adminconfigleague.php
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
function GetCurrentConfig() {
  global $dbaseConfigData, $leagueID;

  $link = OpenConnection();

  $query = "select * from $dbaseConfigData where lid='$leagueID' order by grp";
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
  echo "<input type=\"HIDDEN\" name=\"REFERER\" value=\"adminconfigleague.php\">\n";
  echo "<input type=\"SUBMIT\" name=\"Change\" value=\"Change\">\n";
  echo "<input type=\"SUBMIT\" name=\"Default\" value=\"Default\">\n";
  echo "<input type=\"RESET\" name=\"Reset\" value=\"Reset\">\n";
  echo "</form>\n";

  CloseConnection($link);
}


renderPage("admin", array(
  "title" => "Prediction Football Configuration",
  "heading" => "Configure game parameters [LeagueID $leagueID]",
  "content" => "admin-configuration.php",
  "show_prize" => FALSE
));
