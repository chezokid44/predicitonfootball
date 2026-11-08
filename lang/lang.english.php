<?php 
/*********************************************************
 * Author: Tuomo Aura (c) 2002
 * Date  : 21.11.2002
 * File  : lang.php
 * Desc  : English language file
 ********************************************************/

 /////////////////////////////////////////////////////////
 // Character set required
 /////////////////////////////////////////////////////////
 $charset = "iso8859-1";

 /////////////////////////////////////////////////////////
 // Menu variables
 /////////////////////////////////////////////////////////

$Login_txt = 'Login';
$Username_txt = 'Username';
$Password_txt = 'Password';
$Logon = 'Logon';
$New_User = 'New User';
$Forgot_password = 'Forgot password';

$Home = 'Home';

$Menu = 'Menu';

$Prediction_Table = 'Prediction Table';
$Fixtures_Results = 'Fixtures/Results';
$My_Predictions = 'My Predictions';
$My_Profile = 'My Profile';
$Logout = 'Logout';

$Help = 'Help';
$Email_Us = 'Email Us';

$Admin_Menu = 'Admin Menu';
$Enter_Fixture = 'Enter Fixture';
$Enter_Result = 'Enter Result';
$Show_Users = 'Show Users';
$Recalculate_Standings = 'Recalculate Standings';
$Email_All_Users  = 'Email All Users ';
$View_Log = 'View Log';

 /////////////////////////////////////////////////////////
 // Create new user variables
 /////////////////////////////////////////////////////////

$Cookie_read_test_error = 'Not able to read test cookie when creating new user';
$Create_New_User = 'Create New User';
$User_ID_Required = 'User ID Required';
$User_ID_too_long = 'User ID too long. Must be less than 32 characters.';
$Passwords_is_required = 'Passwords is required';
$Passwords_do_not_match = 'Passwords do not match';
$Password_too_long = 'Password too long. Must be less than 32 characters.';
$Email_address_is_required = 'Email address is required';
$Email_too_long = 'Email too long. Must be less than 60 characters.';
$Email_address_is_not_valid = 'Email address is not valid';
$User_Info = 'User Info';
$User_ID = 'User ID';
$User_Name = 'User Name';
$Select_user_ID = 'Select a user ID.';
$Password_length = 'Password must be at least 5 characters long.';
$Repeat_password = 'Repeat password.';
$Email_Address_txt = 'Email Address';
$Enter_valid_email_address = 'Enter your valid email address.';
$Icon = 'Icon';
$Select_icon = 'Select the icon to represent your user.';
$Create = 'Create';

// CreateUser.php

$Unable_to_register_txt = "Unable to register User variable during creation for $username : $User";
$Unable_to_register_3_attempts_txt = "Unable to register User variable during creation for $username failed after 3 attempts: $User";
$Successfully_registered = 'Successfully registered '.$username.' during creation';
$Username_already_exists = 'Username already exists, please choose another name';
$One_user_per_email = 'Only one user per email ($email) address allowed.';
$Username_required = 'Username required, please choose a name';
$Password_required = 'Password required, please choose a password';
$Email_Answer_txt = "Hi ".$username.",\n\nWelcome to the ".$PredictionLeagueTitle.". Thank you for joining us.\n\nPlease check your details are correct. If you need to change anything please log in and modify your profile.\n\nPassword = ".$password."\nEmail = ".$email."\n\nGood Luck\n ".$adminSignature;
$Email_Subject_txt = "Welcome to the ".$PredictionLeagueTitle;

 /////////////////////////////////////////////////////////
 // Change user variables
 /////////////////////////////////////////////////////////
$My_Profile_for = 'My Profile for';
$change_icon = 'change';
$Join_Date = 'Join Date';
$Change_Profile = 'Change Profile';
$Change_Password = 'Change Password';
$Old_Password_txt = 'Old Password';
$Password_Again = 'Again';
$Change_Password_button = 'Change Password';

// UserSelectIcon.php

$Click_on_the_Icon = 'Click on the Icon you want to use';
$Profile_Icon_Selection = 'Profile Icon Selection';
$Language = "Language";

 /////////////////////////////////////////////////////////
 // Help file variables
 /////////////////////////////////////////////////////////

$Prediction_League_Help_topic = 'Prediction League Help';
$Creating_a_new_user = 'Creating a new user';

$Creating_a_new_user_txt = '          To be able to participate in the Prediction League you need to create a user profile. This is created by selecting the <b>New User</b> link from the right hand column.
          <br> <br>
          Select a User ID to identify yourself in the prediction league. This can be anything you like as long as it is not already taken by another user.
          <br> <br>
          Enter a password (twice). This is to ensure no-one else can change your predictions.
          <br> <br>
          You <b>must</b> enter a valid email address to join the prediction league. This is used to have your password mailed to should you forget it.';

$Scoring_txt = 'Scoring';

$Scoring_txt_txt = '          Points are awared as follows:
          <ul>
          <li>'.$corrScore.' Points for a correct score. i.e. you predict 3-1 and the result is 3-1.
          <li>'.$corrResult.' Point for a correct result. You predict 1-1, the score is 2-2.
          </ul>
          Goal Difference
          <ul>
          <li>For each correct teams number of goals you score, you receive that number of +goals. e.g. you predicit 2 and the team scores 2, you get +2 goals.
          <li>For each incorrect number of goals you predict per team, you lose the difference in goals. e.g. you predict 3 and the team scores 2, you get -1 goals.
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
$Predict= 'Predict';
$Next_Match = "Next Match";
$Prediction_League_Standings = 'Prediction League Standings';
$No_Matches_Scheduled = 'No matches scheduled';
$New_Message = "New message";
$Subject_txt = "Subject";

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
