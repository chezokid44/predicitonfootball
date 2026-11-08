<?php 
/*********************************************************
 * Author: Tom Thomson (c) 2003
 * Date  : 15.03.2003
 * File  : lang.php
 * Desc  : Scots Dialect spoken in lowland Scotland language file
 ********************************************************/

 /////////////////////////////////////////////////////////
 // Character set required
 /////////////////////////////////////////////////////////
 $charset = "ISO8859-1";

 /////////////////////////////////////////////////////////
 // Menu variables
 /////////////////////////////////////////////////////////

$Login_txt = 'Login';
$Username_txt = 'Tae-name';
$Password_txt = 'Password';
$Logon = 'Logon';
$New_User = 'New User';
$Forgot_password = 'Forgat password';

$Home = 'Hame';

$Menu = 'Menu';

$Prediction_Table = 'Spae Buird';
$Fixtures_Results = 'Fixtures/Ootcomes';
$My_Predictions = 'Ma Spaes';
$My_Profile = 'Ma Profile';
$Logout = 'Logoot';

$Help = 'Help';
$Email_Us = 'Email Us';

$Admin_Menu = 'Admin Menu';
$Enter_Fixture = 'Merk up Fixture';
$Enter_Result = 'Merk up Ootcome';
$Show_Users = 'Kythe Users';
$Recalculate_Standings = 'Recalculate Standings';
$Email_All_Users  = 'Email aw Users ';
$View_Log = 'Kythe Log';

 /////////////////////////////////////////////////////////
 // Create new user variables
 /////////////////////////////////////////////////////////

$Cookie_read_test_error = 'Naw able tae read test cookie whan creating new user';
$Create_New_User = 'Create New User';
$User_ID_Required = 'User ID Required';
$User_ID_too_long = 'User ID twa lang. Hus tae be less bi 32 characters.';
$Passwords_is_required = 'Passwords is required';
$Passwords_do_not_match = 'Passwords dinnae marrae';
$Password_too_long = 'Password twa lang. Hus tae be less bi 32 characters.';
$Email_address_is_required = 'Email address is required';
$Email_too_long = 'Email twa lang. Hus tae be less bi 60 characters.';
$Email_address_is_not_valid = 'Email address is unricht';
$User_Info = 'User Info';
$User_ID = 'User ID';
$User_Name = 'User Name';
$Select_user_ID = 'Wale a user ID.';
$Password_length = 'Password Hus tae be at least 5 characters lang.';
$Repeat_password = 'Say ower yer password.';
$Email_Address_txt = 'Email Address';
$Enter_valid_email_address = 'Merk up yer richt email address.';
$Icon = 'Icon';
$Select_icon = 'Wale the icon tae represent yer tae name.';
$Create = 'Create';

// CreateUser.php

$Unable_to_register_txt = "Downa tae register User variable during creation fur $username : $User";
$Unable_to_register_3_attempts_txt = "Unable tae register User variable during creation fur $username failed efter 3 attempts: $User";
$Successfully_registered = 'Successfully registered '.$username.' during creation';
$Username_already_exists = 'Tae-name awreadies exists, please chuise anither tae-name';
$One_user_per_email = 'Only wan user per email ($email) address allowed.';
$Username_required = 'Tae-name required, please chuise a tae name';
$Password_required = 'Password required, please chuise a password';
$Email_Answer_txt = "Hi ".$username.",\n\nFair faw tae the ".$PredictionLeagueTitle.". Thank ye fur jyneing us.\n\nPlease check yer parteeculars are richt. If ye need tae chynge onything please log in and modify yer profile.\n\nPassword = ".$password."\nEmail = ".$email."\n\nGuid Sonse\n ".$adminSignature;
$Email_Subject_txt = "Fair Faw tae the $PredictionLeagueTitle";

 /////////////////////////////////////////////////////////
 // Change user variables
 /////////////////////////////////////////////////////////
$My_Profile_for = 'Ma Profile fur';
$change_icon = 'chynge';
$Join_Date = 'Jyne Date';
$Change_Profile = 'Chynge Profile';
$Change_Password = 'Chynge Password';
$Old_Password_txt = 'Old Password';
$Password_Again = 'Again';
$Change_Password_button = 'Chynge Password';

// UserSelectIcon.php

$Click_on_the_Icon = 'Click oan the Icon ye wannae uise';
$Profile_Icon_Selection = 'Profile Icon Walin';
$Language = "Language";

 /////////////////////////////////////////////////////////
 // Help file variables
 /////////////////////////////////////////////////////////

$Prediction_League_Help_topic = 'Spae League Help';
$Creating_a_new_user = 'Creating a new user';

$Creating_a_new_user_txt = '          Tae be able tae participate in the Spae League ye need tae create a user profile. This is created by selecting the <b>New User</b> link fae the richt haund column. Please mak siccar that cookies are enabled fur yer browser. If cookies are disabled, ye will be forwarded tae this page.
          <br> <br>
          Wale a Tae-name tae identify yersel in the spae league. This can be onything ye like as lang as it isnae awreadies taen by anither user.
          <br> <br>
          Merk up a password (twa times). This is tae mak siccar naebodie else can chynge yer spaes.
          <br> <br>
          Ye <b>huv tae</b> merk up  a valid email address tae jyne the spae league. This is used tae hae yer password mailed tae shoud ye forgit it.';

$Scoring_txt = 'Scoring';

$Scoring_txt_txt = '          Points ur awarded as follaes:
          <ul>
          <li>3 Points fur a richt score. i.e. ye spae 3-1 and the ootcome is 3-1.
          <li>1 Point fur a correct ootcome. Ye spae 1-1, the score is 2-2.
          </ul>
          Goal Difference
          <ul>
          <li>Fur ilk richt teams nummer of goals ye score, ye git that nummer o +goals. e.g. ye spae 2 and the team scores 2, ye git +2 goals.
          <li>Fur ilk wrang nummer o goals ye spae per team, ye tyne the difference in goals. e.g. ye spae 3 and the team scores 2, ye git -1 goals.
          </ul>';


// Version 1.0 Additions
 /////////////////////////////////////////////////////////
 // Messaging variables
 /////////////////////////////////////////////////////////
$Del = "Del";
$Delete = "Delete";
$From = "From";
$To = "To";
$Date = "Date";
$Subject = "Subject";
$My_Messages = "My Messages";
$My_Messages_For = "My Messages for";
$My_Predictions_For = "My Predictions for";
$Send_Msg = "Send Message";
$Create_Msg = "Create message";
$Send_Msg_Button = "Send message";
$Message_txt = "Message";
$RE = "Re:";
$REM = "Rem";  // For removing messages
$Remove_All_Deleted = "Remove all deleted msgs";
$View_All_Messages = "View All Messages";
$Status_Col = "Status";

$Config_League = 'Configure League';
$Predictions = 'Predictions';
$Predict = 'Predict';
$Next_Match = "Next Match";
$Prediction_League_Standings = 'Prediction League Standings';
$No_Matches_Scheduled = 'No matches scheduled';
$New_Message = "New message";
$Subject_txt = "Subject";
$User_Name = 'User Name';

// v1.03 addition
$Months = array('01'=>"Jan",
             '02'=>"Feb",
             '03'=>"Mar",
             '04'=>"Apr",
             '05'=>"May",
             '06'=>"Jun",
             '07'=>"Jul",
             '08'=>"Aug",
             '09'=>"Sep",
             '10'=>"Oct",
             '11'=>"Nov",
             '12'=>"Dec");
$Set_Defaults = "Default config values";

// v1.06 additions
$FixturesResults = "Fixtures/Results";
$Away = "Away"; // Away team title
$P = "P";  // Games played
$W = "W";  // Games won
$D = "D";  // Games drawn
$L = "L";  // Games lost
$F = "F";  // Goals for
$A = "A";  // Goals against
$GD = "GD";  // Goals difference
$POS = "Pos";  // Position
$PTS = "Pts";  // Points
$Result = "Result";
$Prediction_Stats = "Prediction Stats ";
$Download_Dbase = "Backup DBase";
$Lang_Report_Bug = "Report Bug";

// v1.07 additions
$Lang_Enable_Auto_Predictions = "Automatic Predictions";
$Lang_Enable = "Enable";
$Lang_Enter_Outstanding_Results = "Enter Outstanding Results";
$Lang_Enter_Multiple_Fixtures = "Enter Multiple Fixtures";

// v1.08 additions
$Predictions_For = "Predictions for";
$Game_Time = "Game Time";

// v1.09 additions
$New_Message_Subject = "You have received a new message on $PredictionLeagueTitle";
$New_Message_Body = "You have a new message on $PredictionLeagueTitle. You can view the message here http://$PLHome/";
$Games_Menu = "Select game";
$Upload_Icon = "Upload Icon";
$Previous = "Prev";
$Next = "Next";
$Upload_Instructions = "Please try to keep the dimensions of the image to a size suitable for display. Your image file size is limited to $MaxIconFileSize Kb";
?>
