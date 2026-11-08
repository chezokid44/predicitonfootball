<?php 
error_reporting(E_ALL & ~E_NOTICE);
/*********************************************************
 * Author: John Astill (c)
 * Date  : 10th December 2001
 * File  : systemvars.php
 * Desc  : Global data definitions. This is where the 
 *       : prediction league is configured for the 
 *       : specific installation.
 ********************************************************/
require "userclass.php";
require "logfunctions.php";
require "error.php";

//////////////////////////////////////////
// System variable and configuration
//////////////////////////////////////////
//////////////////////////////////////////
// Constants
//////////////////////////////////////////
define("VERSION","1.13");

/////////////////////////////////////////////////
// Default points for scoring, this can be 
// set in the config screen.
/////////////////////////////////////////////////
$corrHomeScore = 1;
$corrAwayScore = 1;
$corrMargin = 1;
$corrResult = 1;
$corrScore = 3;

//////////////////////////////////////////////////////////////
// Modify the values from here to where you are told to stop.
// The numbers match those in the installation steps in
// the file INSTALL.TXT.
//////////////////////////////////////////////////////////////
//////////////////////////////////////////
// 1.Prediction League Title
// The title of the Prediction League.
// Change the value between the "" to 
// give the prediction league the title
// of your choice
//////////////////////////////////////////
$PredictionLeagueTitle = "Prediction Football.com";

//////////////////////////////////////////////////////////////
// 2. Header Row
// This is the header to be displayed in 
//all the pages. It can contain HTML code.
//////////////////////////////////////////////////////////////
$HeaderRow = "<table cellpadding=\"0\" cellspacing=\"0\"><tr><td align=\"center\" class=\"TBLHEAD\"><font class=\"TBLHEAD\"><img border=\"0\" src=\"banner.jpg\"></font></td></tr></table>";

//////////////////////////////////////////////////////////////
// 3. Database hostname
// Database hostname
// The fqdn of the host containing the
// database
//////////////////////////////////////////////////////////////
$dbaseHost = "localhost";

//////////////////////////////////////////////////////////////
// 4. Base Directory Name
// The directory storing the prediction
// league files
//////////////////////////////////////////////////////////////
$baseDirName = "PredictionLeague";

//////////////////////////////////////////////////////////////
// 5. Username
// User name
// The username to be used for logging
// into the database
//////////////////////////////////////////////////////////////
$dbaseUsername = "username";

//////////////////////////////////////////////////////////////
// 6. Password
// Password to be used for logging into
// the database
//////////////////////////////////////////////////////////////
$dbasePassword = "password";

//////////////////////////////////////////////////////////////
// 7. Database Name.
// This is the name of the database. This *MUST* be the same
// name as the name you used when creating the database.
//////////////////////////////////////////////////////////////
$dbaseName = "predictionleague111";

//////////////////////////////////////////////////////////////
// 8. The email address of the administrator. Set this to your 
// own address
//////////////////////////////////////////////////////////////
$adminEmailAddr = "";
$defaultAdminEmailAddr = $adminEmailAddr;

//////////////////////////////////////////////////////////////
// 9. The signature of the admin to use at the end of the 
//    email welcoming the user. This can be a simple name,
//    or something more complex.
//////////////////////////////////////////////////////////////
$adminSignature = "\nhttp://www.predictionfootball.com/";
$defaultAdminSignature = $adminSignature;

//////////////////////////////////////////////////////////////
// 10. The default icon to use for a new user. The icons are
// displayed when the user is logged on. If you have an icon
// named default.gif, you can leave this as default.gif.
//////////////////////////////////////////////////////////////
$defaulticon = "default.gif";

//////////////////////////////////////////////////////////////
// 11. The URL of the associated chat room.
// This link can be used to point to chatroom, or discussion
// area you may have for your prediction league.
// If this is empty, the menu link is not shown.
//////////////////////////////////////////////////////////////
$chatRoomURL = "";
$defaultChatRoomURL = $chatRoomURL;

//////////////////////////////////////////////////////////////
// 12. The URL of the associated home page
// Add the URL of your home page. This is shown in the menu.
//////////////////////////////////////////////////////////////
$homePage = "http://www.predictionfootball.com";
$homePageTitle = "Prediction Football";

//////////////////////////////////////////////////////////////
// 13. The name of the log file. 
// "" disables the logfile functionality.
//////////////////////////////////////////////////////////////
$logfile = "";
$defaultLogfile = $logfile;

//////////////////////////////////////////////////////////////
// 14. To allow more than one user per email address set
//     this variable to TRUE .
//     e.g. $allowMultipleUserPerEmail = "TRUE";
//////////////////////////////////////////////////////////////
$allowMultipleUserPerEmail = "FALSE";

//////////////////////////////////////////////////////////////
// 15. If your server is in a different timezone than the
//     country in which the games are played then enter the 
//     difference in hours here.
//     This does not allow for differences when daylight 
//     savings times are encountered.
//     e.g. Server is in Wash DC, USA and league in UK. Then
//     $timezoneOffset = -5.
//////////////////////////////////////////////////////////////
$timezoneOffset = 0;

//////////////////////////////////////////////////////////////
// 16. Set this flag to true to show the fixtures in reverse
//     date order in ShowMyPredictions. Setting this to
//     FALSE will display the fixtures in date order, first
//     date first.
//////////////////////////////////////////////////////////////
$reverseUserPredictions = "TRUE";

//////////////////////////////////////////////////////////////
// 17. Change this flag to define which language file to use.
//     Language files must be in subfolder lang.
//     Default value = "lang.english.php"
//////////////////////////////////////////////////////////////
$languageFile = "lang.english.php";
$defaultLanguageFile = $languageFile;

//////////////////////////////////////////////////////////////
// 18. Change this flag to enable auto predictions.
//     Auto predictions allow the user to have the scripts
//     make predictions for games where they have not made
//     there own prediction.
//////////////////////////////////////////////////////////////
$autoPredict = "FALSE";
$defaultAutoPredict = $autoPredict;

//////////////////////////////////////////////////////////////
// 19. Use the 24 hour clock
//////////////////////////////////////////////////////////////
$Use24Hr = "TRUE";

//////////////////////////////////////////////////////////////
// 20. Allow users to upload icons.
// Setting the icon directory is an absolute value. This 
// will have a path something along the lines of 
// "".
// The MaxIconFileSize in in Kb. This for a maximum 50Kb
// image the value is set to 50.
//////////////////////////////////////////////////////////////
$UploadIcons = "TRUE";
$MaxIconFileSize = 50;
$IconDirectory = "";

//////////////////////////////////////////////////////////////
// 21. Encrypt passwords.
// This variable determines whether encryption is enabled.
// The value is also configured in the config screen.
// If you previously chose not to encrypt passwords then you
// need to make sure that the users use the forgot password 
// feature to get their new passwords.
//////////////////////////////////////////////////////////////
$PasswordEncryption = "TRUE";

/*************************************************************
**************************************************************
* The following values should not be modified unless you
* REALLY know what you are doing.
**************************************************************
**************************************************************/
/*************************************************************
// Data Tables
// The following is where you define the names of your
// database tables.
*************************************************************/
/*************************************************************
// This allows the default directory for session files
// to be changed. This should only be changed if you really
// know what you are doing.
// This should only need to be changed if you are having
// problems with sessions on your server.
// The directory you choose must exist and be writeable
// by the server.
*************************************************************/
$sessionFileDir = "";

/*************************************************************
// The League ID. Future support for multiple leagues.
*************************************************************/
$leagueID = "0";

/*************************************************************
// The name of the table to be used for the configuration Data.
// This value *MUST* be the same as the value defined when 
// creating the tables.
*************************************************************/
$dbaseConfigData = "plconfigdata";

/*************************************************************
// The name of the table to be used for the User Data.
// This value *MUST* be the same as the value defined when 
// creating the tables.
*************************************************************/
$dbaseUserData = "pluserdata";

/*************************************************************
// The name of the table to be used for the Prediction Data.
// This value *MUST* be the same as the value defined when 
// creating the tables.
*************************************************************/
$dbasePredictionData = "plpredictiondata";

/*************************************************************
// The name of the table to be used for the Match Data. This
// value *MUST* be the same as the value defined when creating
// the tables.
*************************************************************/
$dbaseMatchData = "plmatchdata";

/*************************************************************
// The name of the table to be used for the user messaging.
// This value *MUST* be the same as the value defined when 
// creating the tables.
*************************************************************/
$dbaseMsgData = "plmsgdata";

/*************************************************************
// The name of the table to be used for the current standings.
// This value *MUST* be the same as the value defined when 
// creating the tables.
*************************************************************/
$dbaseStandings = "plstandings";

/*************************************************************
** The home page for the Prediction League
*************************************************************/
$PLHome = "http://www.predictionfootball.com/";

/*************************************************************
** Should the users predictions be visible
*************************************************************/
$ViewUserPredictions = "TRUE";

/*************************************************************
** The number of fixtures to show when making multiple
** fixtures.
*************************************************************/
$NumMultFixts = 12;

/*************************************************************
** The number of users to display on each page of the 
** prediction league. This is the default value. Each user
** can select their own.
*************************************************************/
$usersPerPage = 80;

//////////////////////////////////////////////////////////////
// 22. LockGame
// This variable allows the game to be locked so that only
// power users can create users.
//////////////////////////////////////////////////////////////
$LockedGame = "FALSE";

/////////////////////////////////////////////////
// Storage lengths
/////////////////////////////////////////////////
$userlen = 32; // Storage length for the username.
$passlen = 40; // Storage length for the password.
$fnamelen = 128; // Storage length for any filenames (or URLs).
$teamlen = 30; // Storage length for team names.
$emaillen = 60; // Storage length for email addresses.

/*************************************************************
** The maximum allowed number of admin users.
** If this value is increased it is essential that 
** the user is created as this could present a security 
** hole.
*************************************************************/
$maxAdminUsers = 1;

/////////////////////////////////////////////////////////
// Character set required. The default character set.
// This can be overridden in the language files.
/////////////////////////////////////////////////////////
$charset = "ISO8859-1";

/*************************************************************
** Maintain the logged in users data.
*************************************************************/
$HTTP_POST_VARS = $_POST;
$HTTP_GET_VARS = $_GET;
$HTTP_SERVER_VARS = $_SERVER;
$HTTP_REFERER = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "";
$HTTP_HOST = isset($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"] : "";
$REMOTE_ADDR = isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : "";
$REMOTE_PORT = isset($_SERVER["REMOTE_PORT"]) ? $_SERVER["REMOTE_PORT"] : "";

require "sessiondata.php";
require "dbasefunctions.php";

?>
