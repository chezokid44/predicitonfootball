<?php 
/*********************************************************
 * Author: Hilmar Jan Hansen (c) 2003
 * Date  : 26.03.2003
 * File  : lang.php
 * Desc  : Faroese language file
 ********************************************************/

 /////////////////////////////////////////////////////////
 // Character set required
 /////////////////////////////////////////////////////////
 $charset = "ISO8859-1";

 /////////////////////////////////////////////////////////
 // Menu variables
 /////////////////////////////////////////////////////////

$Login_txt = 'Logga á';
$Username_txt = 'Brúkaranavn';
$Password_txt = 'Loyniorð';
$Logon = 'Logga á';
$New_User = 'Nýggjur brúkari';
$Forgot_password = 'Gloymt loyniorð';

$Home = 'Heim';

$Menu = 'Valmøguleikar';

$Prediction_Table = 'Tipstabell';
$Fixtures_Results = 'Dystarskrá og -úrslit';
$My_Predictions = 'Míni tips';
$My_Profile = 'Mín profilur';
$Logout = 'Logga av';

$Help = 'Hjálp';
$Email_Us = 'Send okkum teldubræv';

$Admin_Menu = 'Umsiting';
$Enter_Fixture = 'Legg dyst inn';
$Enter_Result = 'Legg úrslit inn';
$Show_Users = 'Vís brúkarar';
$Recalculate_Standings = 'Dagfør støðuna';
$Email_All_Users  = 'Send øllum brúkarum teldubræv ';
$View_Log = 'Loggur';

 /////////////////////////////////////////////////////////
 // Create new user variables
 /////////////////////////////////////////////////////////

$Cookie_read_test_error = 'Kann ikki lesa royndar-cookie, tá brúkarin varð meldaður til';
$Create_New_User = 'Stovna nýggjan brúkara';
$User_ID_Required = 'Brúkara-ID krevst';
$User_ID_too_long = 'Brúkara-ID er ov langt. Má vera minni enn 32 tekn.';
$Passwords_is_required = 'Loyniorð krevst';
$Passwords_do_not_match = 'Loyniorðið er skeivt';
$Password_too_long = 'Loyniorðið er ov langt. Má vera minni enn 32 tekn.';
$Email_address_is_required = 'Teldupostadressa krevst';
$Email_too_long = 'Teldupostadressan er ov long. Má vera minni enn 60 tekn.';
$Email_address_is_not_valid = 'Teldupostadressan er ógildug';
$User_Info = 'Brúkaraupplýsingar';
$User_ID = 'Brúkara-ID';
$User_Name = 'User Name';
$Select_user_ID = 'Vel eitt brúkara-ID.';
$Password_length = 'Loyniorðið má í minsta lagi vera 5 tekn.';
$Repeat_password = 'Endurtak loyniorðið.';
$Email_Address_txt = 'Teldupostadressa';
$Enter_valid_email_address = 'Skriva tína gyldugu teldupostadressu.';
$Icon = 'Ikonur';
$Select_icon = 'Vel eina ikon, sum umboðar tín brúkara.';
$Create = 'Stovna brúkara';

// CreateUser.php

$Unable_to_register_txt = "Fekk ikki skrásett brúkara-variabul, tá tú stovnaði $username : $User";
$Unable_to_register_3_attempts_txt = "Fekk ikki skrásett brúkara-variabul, tá tú stovnaði $username. Miseydnaðist eftir 3 royndir: $User";
$Successfully_registered = 'Brúkaranavnið '.$username.' er skrásett';
$Username_already_exists = 'Brúkaranavnið er longu upptikið. Vinarliga, vel annað navn';
$One_user_per_email = 'Tað er bert loyvt at hava ein brúkara fyri hvørja teldupostadressu ($email).';
$Username_required = 'Brúkaranavn krevst. Vinarliga vel eitt navn';
$Password_required = 'Loyniorð krevst. Vinarliga vel eitt loyniorð';
$Email_Answer_txt = "Hey, ".$username."!\n\nVælkomin til ".$PredictionLeagueTitle.". Takk fyri, at tú vilt vera við í Prediction Leauge.\n\nVinarliga, eftirkanna, at tínar brúkaraupplýsingar eru rættar. Um onkur broyting krevst, vinarliga logga á og broyt tín profil.\n\nLoyniorð = ".$password."\nTeldupostur = ".$email."\n\nGóða eydnu!\n ".$adminSignature;
$Email_Subject_txt = "Vælkomin til $PredictionLeagueTitle";

 /////////////////////////////////////////////////////////
 // Change user variables
 /////////////////////////////////////////////////////////
$My_Profile_for = 'Mín profilur fyri';
$change_icon = 'Broyt';
$Join_Date = 'Brúkari skrásettur';
$Change_Profile = 'Broyt profil';
$Change_Password = 'Broyt loyniorð';
$Old_Password_txt = 'Old Password';
$Password_Again = 'Umaftur';
$Change_Password_button = 'Broyt loyniorð';

// UserSelectIcon.php

$Click_on_the_Icon = 'Trýst á ikonuna, tú vilt brúka';
$Profile_Icon_Selection = 'Vel profil-ikon';
$Language = "Language";


 /////////////////////////////////////////////////////////
 // Help file variables
 /////////////////////////////////////////////////////////

$Prediction_League_Help_topic = 'Hjálp';
$Creating_a_new_user = 'Stovna nýggjan brúkara';

$Creating_a_new_user_txt = '          Fyri at vera við í hesi Prediction Leauge, mást tú stovna tín egna brúkaraprofil. Hetta verður gjørt frá slóðini í teiginum vinstrumegin. Vinarliga, kanna eftir, um tú kanst brúka cookies í tínum browsara. Ber tað ikki til, verður tú sendur víðari til hesa síðuna.
          <br> <br>
          Vel brúkaranavn fyri at kunna vera við í Prediction Leauge. Tú velur sjálvur, hvat og hvussu langt tað skal vera, bara eingin annar brúkari frammanundan hevur valt sama navnið.
          <br> <br>
          Skriva loyniorðið (tvær ferðir). Hetta er fyri at tryggja, at eingin kann sleppa inn at broyta tíni tips.
          <br> <br>
          Tú <b>mást</b> skriva eina gylduga teldupostadressu fyri at vera við í Prediction Leauge. Hetta er neyðugt, so tað skal bera til at senda tær loyniorðið, um tú skuldi gloymt tað.
<br> <br>
Longu fyrsta dagin ber til at leggja inn tips fyri allar dystirnar, um tú ynskir tað. Eisini ber til at bíða, til dystirnir byrja. <b>Minst til, at tipsini kunnu broytast heilt fram til dysturin byrjar</b>.';

$Scoring_txt = 'Stigabýti';

$Scoring_txt_txt = '          Stigini verða latin eftir hesum reglum:
          <ul>
          <li>3 stig fyri rætt úrslit. Dømi: Tú tippar 3-1, og úrslitið verður 3-1.
          <li>1 stig fyri rættan mun. Dømi: Tú tippar 1-1, og úrslitið verður 2-2.
          </ul>
          Málmunur
          <ul>
          <li>Fyri rætt tal á málum fyri hvørt av liðunum, fært tú tað talið sum +mál. Tippar tú eitt lið at fáa 2 mál, og liðið fær 2 mál, fært tú +2 í málmuni.
          <li>Fyri hvørt skeivt tippað mál missir tú sama tal í málmuni. Tippar tú eitt lið at fáa 2 mál, og liðið fær 2 mál, fært tú -1 í málmuni.
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
