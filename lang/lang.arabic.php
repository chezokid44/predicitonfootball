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
 $charset = "windows-1256";

 /////////////////////////////////////////////////////////
 // Menu variables
 /////////////////////////////////////////////////////////

$Login_txt = 'ַבÊ׃ּםב';
$Username_txt = 'ַ׃ד ַבÚײז';
$Password_txt = 'ßבדֹ ַב׃ׁ';
$Logon = 'ֿ־זב';
$New_User = 'Úײז ּֿםֿ';
$Forgot_password = 'ַ׃ÊַּׁÚ ßבדֹ ַב׃ׁ';

$Home = 'ַבֶׁם׃םֹ';

$Menu = 'ַבÞֶַדֹ';

$Prediction_Table = 'ּֿזב ַבÊזÞÚַÊ';
$Fixtures_Results = 'ַבדַָׁםַÊ ז ַבהÊֶַּ';
$My_Predictions = 'ÊזÞÚַÊם';
$My_Profile = 'ַבדבÝ ַבװ־ױם';
$Logout = '־ׁזּ';

$Help = 'ד׃ַÚֹֿ';
$Email_Us = 'ַׁ׃בהַ';

$Admin_Menu = 'Þֶַדֹ ַבדֿםׁ';
$Enter_Fixture = 'ÊָֻםÊ ַבדַָׁםַֹ';
$Enter_Result = 'ÊָֻםÊ ַבהÊםֹּ';
$Show_Users = 'דװַוֹֿ ַבֳÚײֱַ';
$Recalculate_Standings = 'ַבÊָֻםÊ ַבהוֶַם';
$Email_All_Users  = 'דַׁ׃בֹ ַבֳÚײֱַ ';
$View_Log = 'דװַוֹֿ ַבÚדבםַÊ';

 /////////////////////////////////////////////////////////
 // Create new user variables
 /////////////////////////////////////////////////////////

$Cookie_read_test_error = 'בַ ם׃Ê״םÚ Þֱַֹׁ ßÚßֹ ַבַ־Êַָׁ Úהֿ ַהװֱַ Úײז ּֿםֿ';
$Create_New_User = 'ַהװֱַ Úײז ּֿםֿ';
$User_ID_Required = 'ַ׃ד ַבד׃Ê־ֿד ד״בזָ';
$User_ID_too_long = 'ַ׃ד ַבÚײז ״זםב ַּֿ םָּ ַה םßזה ַÞב דה 32 ֽׁÝַ';
$Passwords_is_required = 'ַבßבדֹ ַב׃ׁםֹ ד״בזָֹ';
$Passwords_do_not_match = 'ַבßבדֹ ַב׃ׁםֹ Ûםׁ ױַבֹֽ';
$Password_too_long = 'ַבßבדֹ ַב׃ׁםֹ ״זםבֹ ַּֿ םָּ ַה Êßזה ַÞב דה 32 ֽׁÝַ';
$Email_address_is_required = 'ַבָׁםֿ ַבַבßÊׁזהם ד״בזָ';
$Email_too_long = 'ַבָׁםֿ ַבַבßÊׁזהם ״זםב ַּֿ םָּ ַה םßזה ַÞב דה 60 ֽׁÝַ';
$Email_address_is_not_valid = 'ַבָׁםֿ ַבַבßÊׁזהם Ûםׁ װׁÚם';
$User_Info = 'ָםַהַÊ ַבÚײז';
$User_ID = 'ַ׃ד ַבÚײז';
$User_Name = 'ַבַ׃ד';
$Select_user_ID = '.ַ־Êׁ ַ׃ד ַבֿ־זב';
$Password_length = '.ַבßבדֹ ַב׃ׁםֹ םָּ ַה בַ ÊÞב Úה 5 ֳֽׁÝ';
$Repeat_password = '.ֵÚַֹֿ ßבדֹ ַב׃ׁ';
$Email_Address_txt = 'ַבָׁםֿ ַבַבßÊׁזהם';
$Enter_valid_email_address = '.ַֿ־ב Úהזַה ָׁםֿß ַבַבßÊׁזהם ַבױֽםֽ';
$Icon = 'ַבױזֹׁ';
$Select_icon = '.םדßהß דה ÊÛםםׁ ַבױזֹׁ ַבַּהָםֹ דה דבÝß ַבװ־ױם';
$Create = 'ַבÊֽֿםֻ';

// CreateUser.php

$Unable_to_register_txt = "Ûםׁ Þַֿׁ ֳה Ê׃ּרב דÊÛםרׁ ַבד׃Ê־ֿד ֳֻהֱַ ַב־בÞ בַ׃ד ַבד׃Ê־ֿד  $username : $User";
$Unable_to_register_3_attempts_txt = "Ûםׁ Þַֿׁ ֳה Ê׃ּרב דÊÛםרׁ ַבד׃Ê־ֿד ֳֻהֱַ ַב־בÞ בַ׃ד ַבד׃Ê־ֿד  $username ַבÝװב ָÚֿ 3 דַֽזבַÊ : ד׃Ê־ֿד : $User";
$Successfully_registered = 'Êד ַבÊ׃ּםב ָהַּֽ '.$username.' ַָבÚדבםֹ';
$Username_already_exists = 'ַבַ׃ד דזּזֿ ד׃ָÞַ , ֱַּׁ ַ־Êׁ ַ׃ד ֲ־ׁ';
$One_user_per_email = 'ÝÞ״ Úײז זַֽֿ ַָבָׁםֿ ַבַבßÊׁזהם ($email) ַ׃דֽ בבÚהזַה.';
$Username_required = 'ַ׃ד Úײז ד״בזָ , ֱַּׁ ַ־Êׁ ַ׃ד';
$Password_required = 'ßבדֹ ַב׃ׁ ד״בזָֹ , ֱַּׁ ַ־Êׁ ßבדֹ ׃ׁ';
$Email_Answer_txt = "דַָֽׁ ".$username.",\n\nַובַ ז׃ובַ ָß Ýם ".$PredictionLeagueTitle.". װßַׁ בß בÊ׃ּםבß דÚהַ.\n\nPדה Ýײבß ַּׁÚ ָםַהַÊß ּםַֿ . ֵ׀ַ ַֽÊּÊ בÊÛםםׁ ָםַהַÊß ַבװ־ױםֹ ֱַּׁ ׃ּב ֿ־זב ַָבדזÞÚ זÚֿב ָָםַהַÊß.\n\nßבדֹ ַב׃ׁ = ".$password."\nַבָׁםֿ ַבַבßÊׁזהם = ".$email."\n\nֽÙ דזÝÞ\n ".$adminSignature;
$Email_Subject_txt = "ַובַ ז׃ובַ ָß Ýם ".$PredictionLeagueTitle;

 /////////////////////////////////////////////////////////
 // Change user variables
 /////////////////////////////////////////////////////////
$My_Profile_for = 'דבÝם ַבװ־ױם ַבל';
$change_icon = 'ÊÛםםׁ';
$Join_Date = 'Êַׁם־ ַבÊ׃ּםב';
$Change_Profile = 'ÊÛםםׁ ַבױזֹׁ';
$Change_Password = 'ÊÛםםׁ ַבßבדֹ ַב׃ׁםֹ';
$Old_Password_txt = 'ַבßבדֹ ַב׃ׁםֹ ַבÞֿםדֹ';
$Password_Again = 'דֹׁ ֳ־ׁל';
$Change_Password_button = 'Ûםׁ ַבßבדֹ ַב׃ׁםֹ';

// UserSelectIcon.php

$Click_on_the_Icon = 'ַײÛ״ Úבל ַבױזֹׁ ַבÊם Êזֿ ַ׃Ê־ַֿדוַ ָדבÝß';
$Profile_Icon_Selection = 'ַ־Êםַׁ ַםÞזהֹ ַבױזֹׁ';
$Language = "ַבבÛֹ";

 /////////////////////////////////////////////////////////
 // Help file variables
 /////////////////////////////////////////////////////////

$Prediction_League_Help_topic = 'ד׃ַÚֹֿ ÊזÞÚַÊ ַבדַָׁםַÊ';
$Creating_a_new_user = 'ַהװֱַ Úײז ּֿםֿ';

$Creating_a_new_user_txt = '          בÊßזה Þַַֿׁ Úבל ַבדװַׁßֹ ָÊזÞÚַÊ ַבֿזׁם ÊֽÊַּ
בַהװֱַ ַ׃ד Úײז ּֿםֿ . ז׀בß ַָ־Êםַׁ ַָׁ״ <big><strong>Úײז
ּֿםֿ</strong></big> בַהװֱַ ַבֽ׃ַָ
          <br> <br>
          ַ־Êׁ ַ׃ד Úײזםֹ בבÊÚׁםÝ Úה הÝ׃ß Ýם ֿזׁם ַבÊזÞÚַÊ . ַָדßַהß ַ־Êםַׁ ֳם ַ׃ד ÊÝײבו ״ַבדַ בד ם׃ָÞß ֳֽֿ ָּֽׂו
          <br> <br>
          ֳֿ־ב ßבדֹ ַב׃ׁ (דׁÊםה) בײדַה ַה בַ ֳֽֿ ם׃Ê״םÚ ֳה םÛםׁ ָÊזÞÚַÊß בַֽÞַ
          <br> <br>
          ַהÊָו <big><strong>םָּ</strong></big> ֳה Êֿ־ב Úהזַה ָׁםֿß
ַבױֽםֽ בבÊ׃ּםב ָÊזÞÚַÊ ַבֿזׁם בםÊ׃הל בהַ ַׁ׃ַב
ßבדֹ ַב׃ׁ ַָֽבֹ ה׃םַהוַ';

$Scoring_txt = 'ַבהÊֶַּ';

$Scoring_txt_txt = '          ״ׁםÞֹ ֽ׃ַָ ַבהÞַ״:
          <ul>
          <li>'.$corrScore.' הÞַ״ בֳוַֿÝ ַבדַַָֹׁ ַבױֽםֹֽ. ַ׀ַ ÊזÞÚÊ ַבהÊםֹּ 3-1 זßַהÊ ַבהÊםֹּ ַבÝÚבםֹ 3-1
          <li>'.$corrResult.' הÞ״ֹ בבהÊםֹּ ַבױֽםֹֽ. ַהÊ ÊזÞÚÊ 1-1 , זַבהÊםֹּ ַבÝÚבםֹ 2-2.
          </ul>
          Êדםםׂ ַבֳוַֿÝ
          <ul>
          <li>בßב הÊםֹּ ױֽםֹֽ ָÚֿֿ ַבַוַֿÝ Êֽױב Úבל הÝ׃ ַבׁÞד ַָבַוַֿÝ , דַֻב ַהÊ ÊזÞÚÊ 2 זַבהÊםֹּ ַבÝÚבםֹ 2 Êֽױב Úבל +2
          <li>ßב ÊזÞÚ ־״ֳ ַָבהÊםֹּ Ê־׃ׁ ַבÝַׁÞ ָםהוד , דַֻב ַהÊ ÊזÞÚÊ 3 זַבהÊםֹּ ַבÝÚבםֹ 2 םױָֽ בֿםß -1
          </ul>';

// Version 1.0 Additions
 /////////////////////////////////////////////////////////
 // Messaging variables
 /////////////////////////////////////////////////////////
$Del = "ֵבÛֱַ";
$Delete = "ֵבÛֱַ";
$From = "דה";
$To = "ַבל";
$Date = "ַבÊַׁם־";
$Subject = "ַבדזײזÚ";
$My_Messages = "ׁ׃ֶַבם";
$My_Messages_For = "ׁ׃ֶַבם ַבל";
$My_Predictions_For = "ÊזÞÚַÊם ַבל";
$Send_Msg = "ֵׁ׃ב ַבׁ׃ַבֹ";
$Create_Msg = "ַהװֱַ ַבׁ׃ַבֹ";
$Send_Msg_Button = "ֵׁ׃ב ַבׁ׃ַבֹ";
$Message_txt = "ַבׁ׃ַבֹ";
$RE = "Re:";
$REM = "ַהÞב";  // For removing messages
$Remove_All_Deleted = "ַהÞב ßב ַבׁ׃ֶַב ַבדבÛַֹ";
$View_All_Messages = "דװַוֹֿ ßב ַבׁ׃ֶַב";
$Status_Col = "ַבזײÚ";

$Config_League = 'זÝרÞ ָםה ַבֿזׁם';
$Predictions = 'ַבÊזÞÚַÊ';
$Predict= 'ÊזÞÚ';
$Next_Match = "ַבדַַָֹׁ ַבÞַֿדֹ";
$Prediction_League_Standings = 'ּֿזב ÊזÞÚַÊ ַבֿזׁם';
$No_Matches_Scheduled = 'בַ Êזּֿ דַַָֹׁ דÞֹׁׁ';
$New_Message = "ׁ׃ַבֹ ּֿםֹֿ";
$Subject_txt = "ַבדזײזÚ";

// v1.03 addition
$Months = array('01'=>"םהַםׁ",
             '02'=>"Ýַָׁםׁ",
             '03'=>"דַׁ׃",
             '04'=>"ַָׁםב",
             '05'=>"דַםז",
             '06'=>"םזהםז",
             '07'=>"םזבםז",
             '08'=>"ֳÛ׃״׃",
             '09'=>"׃ָÊדָׁ",
             '10'=>"ַßÊזָׁ",
             '11'=>"הזÝדָׁ",
             '12'=>"ֿם׃דָׁ");
$Set_Defaults = "ַודַב Êװßםב ַבÊÞםםד";

// v1.06 additions
$FixturesResults = "ַבדַָׁםַÊ ז ַבהÊֶַּ";
$Away = "ַבײםÝ"; // Away team title
$P = "בÚָ";  // Games played
$W = "Ýזׂ";  // Games won
$D = "ÊÚַֿב";  // Games drawn
$L = "־׃ַֹׁ";  // Games lost
$F = "בו";  // Goals for
$A = "Úבםו";  // Goals against
$GD = "ַבÝַׁÞ";  // Goals difference
$POS = "Ê";  // Position
$PTS = "ַבהÞ״";  // Points
$Result = "ַבהÊםֹּ";
$Prediction_Stats = "ַֽױֱַַÊ ַבÊזÞÚַÊ ";
$Download_Dbase = "ה׃־ ÞַÚֹֿ ַבָםַהַÊ";
$Lang_Report_Bug = "ַבÊÞׁםׁ";

// v1.07 additions
$Lang_Enable_Auto_Predictions = "ַבÊזÞÚַÊ ַבÊבÞֶַםֹ";
$Lang_Enable = "דßרה ַבבÛֹ";
$Lang_Enter_Outstanding_Results = "ֳֿ־ב ַבהÊֶַּ ַבדדםֹׂ";
$Lang_Enter_Multiple_Fixtures = "ֳֿ־ב ַבדַָׁםַÊ ַבֳ׃ַ׃םֹ";

// v1.08 additions
$Predictions_For = "ַבÊזÞÚַÊ ַבל";
$Game_Time = "זÞÊ ַבדַַָֹׁ";

// v1.09 additions
$New_Message_Subject = "בÞֿ ÊבÞםÊ ׁ׃ַבֹ ּֿםֹֿ Úבל $PredictionLeagueTitle";
$New_Message_Body = "בÞֿ ÊבÞםÊ ׁ׃ַבֹ ּֿםֹֿ Úבל $PredictionLeagueTitle. Ê׃Ê״םÚ דװַוֹֿ ַבׁ׃ַבֹ Úבל ו׀ַ ַבÚהזַה http://$PLHome/";
$Games_Menu = "ַ־Êׁ ַבדַַָֹׁ";
$Upload_Icon = "ֽדב ַםÞזהֹ";
$Previous = "ַב׃ַָÞ";
$Next = "ַבÊַבם";
$Upload_Instructions = "דה Ýײבß ַֽזב ֵָÞֱַ ֳָÚַֿ ַבױרזֹׁ ֵבל ּֽד דהַ׃ָֹנ בבÚׁײ . ּֽד דבÝר ױזׁÊß דֽֿרֿ ֵבל $MaxIconFileSize Kb";
?>
