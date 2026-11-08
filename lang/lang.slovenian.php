<?php 
/*********************************************************
 * Author: Ales Veber (c) 2003
 * Date  : 10.06.2003
 * File  : lang.php
 * Desc  : English language file
 ********************************************************/

 /////////////////////////////////////////////////////////
 // Menu variables
 /////////////////////////////////////////////////////////

$Login_txt = 'Vstop';
$Username_txt = 'Upor. ime';
$Password_txt = 'Geslo';
$Logon = 'Vstopi';
$New_User = 'Nov èlan';
$Forgot_password = 'Si pozabil geslo?!';

$Home = 'Domov';

$Menu = 'Meni';

$Prediction_Table = 'Napovedna tabela';
$Fixtures_Results = 'Tekme/Rezultati';
$My_Predictions = 'Moje napovedi';
$My_Profile = 'Moj profil';
$Logout = 'Izhod';

$Help = 'Na pomoè!';
$Email_Us = 'E-mail';

$Admin_Menu = 'Admin meni';
$Enter_Fixture = 'Vnesi tekmo';
$Enter_Result = 'Vnesi rezultat';
$Show_Users = 'Pokaži èlane';
$Recalculate_Standings = 'Preraèunaj vrstni red';
$Email_All_Users  = 'Email vsem èlanom';
$View_Log = 'Poglej zapis';

 /////////////////////////////////////////////////////////
 // Create new user variables
 /////////////////////////////////////////////////////////

$Cookie_read_test_error = 'Žal je nemogoèe prebrati testni cookie, ko kreiraš novega èlana!';
$Create_New_User = 'Ustvari novega èlana';
$User_ID_Required = 'Zahtevana je èlanska ID';
$User_ID_too_long = 'Èlanski ID je predolg. Vsebovati mora manj kot 32 èrk.';
$Passwords_is_required = 'Potrebno je geslo!';
$Passwords_do_not_match = 'Geslo se ne ujema!';
$Password_too_long = 'Geslo je predolgo. Vsebovati mora manj kot 32 èrk.';
$Email_address_is_required = 'Potreben je e-mail naslov!';
$Email_too_long = 'E-mail naslov je predolg!. Vsebovati mora manj kot 60 èrk.';
$Email_address_is_not_valid = 'E-mail naslov ni toèen!';
$User_Info = 'Info o èlanu';
$User_ID = 'Èlanski ID';
$User_Name = 'User Name';
$Select_user_ID = 'Izberi èlanski ID.';
$Password_length = 'Geslo mora vsebovati vsaj 5 èrk.';
$Repeat_password = 'Ponovi geslo.';
$Email_Address_txt = 'E-mail naslov';
$Enter_valid_email_address = 'Vnesi tvoj veljavni e-mail naslov!.';
$Icon = 'Ikona';
$Select_icon = 'Izberi ikono, ki bo predstavljala tvojega èlana.';
$Create = 'Ustvari';

// CreateUser.php

$Unable_to_register_txt = "Ne morem registrirati uporabniške nastavitve med ustvarjanjem $username : $User";
$Unable_to_register_3_attempts_txt = "Ne morem registrirati uporabniške nastavitve med ustvarjanjem $username failed after 3 attempts: $User";
$Successfully_registered = 'Uspešno registriral '.$username.' during creation';
$Username_already_exists = 'Èlansko ime je že izbrano! Prosim izberi kakšno drugo ime.';
$One_user_per_email = 'Samo en uporabnik na en e-mail ($email) naslov.';
$Username_required = 'Potrebno je èlansko ime! Prosim izberi svoje èlansko ime.';
$Password_required = 'Potrebno je geslo! Prosim izberi svoje geslo.';
$Email_Answer_txt = "Pozdravljen/a ".$username.",\n\nDobrodošel/la v ".$PredictionLeagueTitle.". Hvala, ker si se nam pridružil/a.\n\nProsim preveri pravilnost tvojih podatkov. Èe želiš karkoli spremeniti se prosim vpiši in uredi tvoj profil.\n\nGeslo = ".$password."\nEmail = ".$email."\n\nVeliko sreèe in zabave!\n ".$adminSignature;
$Email_Subject_txt = "Dobrodošel/la v $PredictionLeagueTitle";

 /////////////////////////////////////////////////////////
 // Change user variables
 /////////////////////////////////////////////////////////
$My_Profile_for = 'Moj profil za';
$change_icon = 'spremeni';
$Join_Date = 'Datum pridružitve';
$Change_Profile = 'Spremeni profil';
$Change_Password = 'Spremeni geslo';
$Old_Password_txt = 'Old Password';
$Password_Again = 'Ponovno';
$Change_Password_button = 'Spremeni geslo';

// UserSelectIcon.php

$Click_on_the_Icon = 'Klikni na ikono, ki jo želiš uporabiti';
$Profile_Icon_Selection = 'Izbor ikon za tvoj profil';
$Language = "Language";

 /////////////////////////////////////////////////////////
 // Help file variables
 /////////////////////////////////////////////////////////

$Prediction_League_Help_topic = 'Prediction League - pomoè';
$Creating_a_new_user = 'Ustvarjanje novega èlana';

$Creating_a_new_user_txt = '          Za tvoje sodelovanje v Prediction League moraš najprej ustvariti uporabniško ali èlansko ime. To narediš s pritiskom na povezavo <b>Nov èlan</b> v desnem stolpcu. Preprièaj se da so cookies (piškotki) vklopljeni v tvojem iskalniku. Èe so cookies (piškotki) izklopljeni, boš pristal na tej strani.
          <br> <br>
          Izberi èlansko ID za tvoj prikaz v napovedni ligi. To je lahko kar koli želi le da tega ne uporablja že kakšen drug èlan.
          <br> <br>
          Vnesi geslo (dvakrat). To je za prepreèitev spreminjanja tvojih napovedi s strani kakšne druge osebe.
          <br> <br>
          <b>Moraš</b> vnesti veljaven email naslov za pridružitev k napovedni ligi. V primeru da pozabiš geslo, ti pošljemo le-tega na tvoj email naslov.';

$Cookies_txt = 'Cookies';

$Cookies_txt_txt = '          Cookies (piškotki) <b>morajo</b> biti vklopljeni za sodelovanje v Prediction League.
          <br><br>
          Problemi, ki se pojavijo, èe ne uporabljaš Cookiev (piškotkov):
          <ul>
          <li>Pri poizkusu vstopa vidiš to stran.
          <li>Ko želiš vnesti napoved si vprašan za èlansko ime.
          <li>Na levem delu ekrana ne vidiš ikone.
          <li>Ko želiš ustvariti novega èlana se preseliš na to stran. Vklopiti moraš cookies (piškotke) v tvojem iskalniku.
          </ul>
          Za vklop cookies (piškotkov) v Internet Explorerju 6, pojdi v meni (orodja) Tools->Internet Options. Izberi Privacy tab. Nastavi na Medium ali lower. Pritisni OK. Nekateri uporabniki se sreèujejo s problemi v nastavitvah medium. Morda moraš nastaviti še na nižji nivo.<br>
          Za vklop cookies (piškotkov) v Netscape 6, pojdi v meni Edit->Preferences. Izberi Privacy & tab, nato izberi cookies. Izberi Enable Cookies za samo to strany, ali pa vklopi vse piškotke (cookies). Pritisni OK.<br>';

$Scoring_txt = 'Toèkovanje';

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
