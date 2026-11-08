<?php
/*********************************************************
 * Author: T Ljungström (c) 2004
 * Date  : 29.01.2004
 * File  : lang.php
 * Desc  : Swedish language file
 ********************************************************/

 /////////////////////////////////////////////////////////
 // Menu variables
 /////////////////////////////////////////////////////////

$Login_txt = 'Logga in';
$Username_txt = 'Anv&auml;ndarnamn';
$Password_txt = 'L&ouml;senord';
$Logon = 'Logga in';
$New_User = 'Ny anv&auml;ndare';
$Forgot_password = 'Gl&ouml;mt l&ouml;senord';

$Home = 'Hem';

$Menu = 'Meny';

$Prediction_Table = 'Topplista';
$Fixtures_Results = 'Spelstop/Resultat';
$My_Predictions = 'Mina tips';
$My_Profile = 'Min profil';
$Logout = 'Logga ut';

$Help = 'Hj&auml;lp';
$Email_Us = 'E-posta oss';

$Admin_Menu = 'Admin Meny';
$Enter_Fixture = 'L&auml;gg till match';
$Enter_Result = 'L&auml;gg in resultat';
$Show_Users = 'Visa anv&auml;ndare';
$Recalculate_Standings = 'R&auml;kna ut st&auml;llningen';
$Email_All_Users  = 'E-posta alla anv&auml;ndare ';
$View_Log = 'Visa logg';

 /////////////////////////////////////////////////////////
 // Create new user variables
 /////////////////////////////////////////////////////////

$Cookie_read_test_error = 'Kunde inte l&auml;sa cookie n&auml;r anv&auml;ndaren skapades';
$Create_New_User = 'Skapa ny nyanv&auml;ndare';
$User_ID_Required = 'Anv&auml;ndar-ID &auml;r obligatoriskt';
$User_ID_too_long = 'Anv&auml;ndar-ID f&ouml;r l&aringngt. Max 32 tecken.';
$Passwords_is_required = 'L&ouml;senord &auml;r obligatoriskt';
$Passwords_do_not_match = 'L&ouml;senordet st&auml;mmer inte';
$Password_too_long = 'L&ouml;senordet f&ouml;r langt. Max 32 tecken.';
$Email_address_is_required = 'Epostadress &auml;r obligatoriskt';
$Email_too_long = 'Epostadressen &auml;r f&ouml;r l&aringng. Max 60 tecken.';
$Email_address_is_not_valid = 'Epostadressen &auml;r inte giltig';
$User_Info = 'Anv&auml;ndarinfo';
$User_ID = 'Anv&auml;ndar-ID';
$User_Name = 'Anv&auml;ndarnamn';
$Select_user_ID = 'V&auml;lj ett anv&auml;ndar-ID.';
$Password_length = 'L&ouml;senordet ska vara minst fem (5) tecken.';
$Repeat_password = 'Bekr&auml;fta l&ouml;senord.';
$Email_Address_txt = 'Epost-adress';
$Enter_valid_email_address = 'Fyll i din giltiga epostadress.';
$Icon = 'Ikon';
$Select_icon = 'V&auml;lj en ikon som representerar din anv&auml;ndare.';
$Create = 'Skapa anv&auml;ndare';

// CreateUser.php

$Unable_to_register_txt = "Kunde inte registrera anv&auml;ndarvariabel vid skapandet av $username : $User";
$Unable_to_register_3_attempts_txt = "Kunde inte registrera anv&auml;ndarvariabel vid skapandet av $username, misslyckades efter tre f&ouml;rs&ouml;k: $User";
$Successfully_registered = 'Anv&auml;ndarnamnet '.$username.' &auml;r registrerat';
$Username_already_exists = 'Anv&auml;ndarnamnet excisterar redan, v&auml;lj ett nytt anv&auml;ndarnamn';
$One_user_per_email = 'Bara en anv&auml;ndare per epostadress ($email) &auml;r till&aringten.';
$Username_required = 'Anv&auml;ndarnamn &auml;r obligatoriskt, v&auml;lj ett anv&auml;ndarnamn';
$Password_required = 'L&ouml;senord &auml;r obligatoriskt, v&auml;lj ett l&ouml;senord';
$Email_Answer_txt = "Hej ".$username.",\n\nV&auml;lkommen till ".$PredictionLeagueTitle.". Tack f&ouml;r att du deltar i Prediction Leauge.\n\nKontrollera att dina uppgifter &auml;r korrekta. Om du beh&ouml;ver &auml;ndra n&aringgot, logga in och modifiera din profil.\n\nL&ouml;senord = ".$password."\nEpost = ".$email."\n\nLycka till\n ".$adminSignature;
$Email_Subject_txt = "V&auml;lkommen till $PredictionLeagueTitle";

 /////////////////////////////////////////////////////////
 // Change user variables
 /////////////////////////////////////////////////////////
$My_Profile_for = 'Min profil f&ouml;r';
$change_icon = '&Auml;ndra ikon';
$Join_Date = 'Anv&auml;ndare registrerad';
$Change_Profile = '&Auml;ndra profil';
$Change_Password = '&Auml;ndra l&ouml;senord';
$Old_Password_txt = 'Gamalt l&ouml;senord';
$Password_Again = 'En g&aringng till';
$Change_Password_button = '&Auml;ndra l&ouml;senord';

// UserSelectIcon.php

$Click_on_the_Icon = 'Klicka p&aring ikonen du vill anv&auml;nda';
$Profile_Icon_Selection = 'Val av profil-ikon';
$Language = "Språk";

 /////////////////////////////////////////////////////////
 // Help file variables
 /////////////////////////////////////////////////////////

$Prediction_League_Help_topic = 'Hj&auml;lp';
$Creating_a_new_user = 'Skapa en ny anv&auml;ndare';

$Creating_a_new_user_txt = '          F&ouml;r att kunna deltaga i Prediction Leauge m&aringste du registrera dig som anv&auml;ndare. Detta g&ouml;r du gemon att klicka p&aring l&auml;nken i menyn till v&auml;nster. Observera att din webl&auml;sare m&aringste acceptera cookies. Om cookies &auml;r avst&auml;ngda kommer du att bli vidarebefordrad till denna sida.
          <br> <br>
          V&auml;lj ett anv&auml;ndarnamn f&ouml;r att identifiera dig i Prediction Leauge. Detta kan vara vad som helst bara det inte &auml;r upptaget av en annan anv&auml;ndare.
          <br> <br>
          Fyll i l&ouml;senordet (tv&aring g&aringnger). Detta f&ouml;r att ingen ska kunna &auml;ndra dina tips.
          <br> <br>
          Du <b>m&aringste</b> uppge en giltig epostadress f&ouml;r att kunna delta i Prediction Leauge. Eposten anv&auml;nds bland annat f&ouml;r att kunna skicka ditt l&ouml;senord om du skulle ha gl&ouml;mt det.
<br> <br>
Man kan tippa alla matcher redan f&ouml;rsta dagen, eller s&aring kan man v&auml;nta till innan matchstart. <b>Du kan &auml;ndra ditt tips fram till matchstart</b>.';


$Cookies_txt = 'Cookies';

$Cookies_txt_txt = '          Din webl&auml;sare <b>m&aringste</b> acceptera cookies om du vill delta i Prediction Leauge.
          <br><br>
          Vanliga problem n&auml;r cookies &auml;r avst&auml;ngda &auml;r bland annat:
          <ul>
          <li>Du ser denna sida n&auml;r du f&ouml;rs&ouml;ker logga in.
          <li>Du blir ombedd att uppge anv&auml;ndarnamn n&auml;r du f&ouml;rs&ouml;ker att registrera tips.
          <li>Ikonen syns inte till v&auml;nster p&aring sk&auml;rmen.
          <li>Du f&ouml;rs&ouml;kte registrera ett anv&auml;ndarnamn, men blev vidarebefordrad till denna sidan. &Auml;ndra inst&auml;llningarna i din webl&auml;sare s&aring att den accepterar cookies.
          </ul>
          F&ouml;r att &auml;ndra inst&auml;llningarna f&ouml;r cookies i Internet Explorer 6, g&aring till Tools->Internet Options. V&auml;lj "Privacy tab". &Auml;ndra till "Medium" eller l&auml;gre. Tryck OK. N&aringgra anv&auml;ndare har upplevt problem med medium-inst&auml;llningar. Du m&aringste d&aring v&auml;lja en l&auml;gre s&auml;kerhet.<br>
F&ouml;r att &auml;ndra inst&auml;llningarna f&ouml;r cookies i Netscape 6, g&aring til Edit->Preferences. V&auml;lj "Privacy & tab" och v&auml;lj cookies. V&auml;lj "Enable Cookies" f&ouml;r "originating site only" eller g&ouml;r dem tillg&auml;ngliga f&ouml;r alla. Tryck OK.
<br>';

$Scoring_txt = 'Po&auml;ngf&ouml;rdelning';

$Scoring_txt_txt = '          Po&auml;ngen delas ut enligt f&ouml;ljande regler:
          <ul>
          <li>3 po&auml;ng for korrekt m&aringlresultat, t.ex. du tippar 3-1 och resultatet blir 3-1.
          <li>1 po&auml;ng for korrekt resultat. Du tippar 1-1, resultatet blir 2-2.
          </ul>
          M&aringlskillnad
          <ul>
          <li>F&ouml;r korrekt antal m&aringl f&ouml;r var och ett av lagen f&aringr du det antal som +mål. Tippar du att laget g&ouml;r 2 m&aringl och laget g&ouml;r 2 f&aringr du +2 i m&aringlskillnad.
          <li>F&ouml;r varje m&aringl du tippar fel mister du samma antal i m&aringlskillnaden. Tippar du att laget g&ouml;r 3 m&aringl och de g&ouml;r 2 f&aringr du -1 i m&aringlskillnad.
          </ul>';


// Version 1.0 Additions
 /////////////////////////////////////////////////////////
 // Messaging variables
 /////////////////////////////////////////////////////////
$Del = "Del";
$Delete = "Radera";
$From = "Fr&aringn";
$To = "Till";
$Date = "Datum";
$Subject = "&Auml;mne";
$My_Messages = "Mina meddelanden";
$My_Messages_For = "Mina meddelanden med";
$My_Predictions_For = "Mina tips med";
$Send_Msg = "Skicka meddelande";
$Create_Msg = "Skapa meddelande";
$Send_Msg_Button = "Skicka";
$Message_txt = "Meddelande";
$RE = "Re:";
$REM = "Kasta";  // For removing messages
$Remove_All_Deleted = "Kasta alla raderade meddelanden";
$View_All_Messages = "Se alla meddelanden";
$Status_Col = "Status";

$Config_League = 'Inst&auml;llningar ';
$Predictions = 'Tips';
$Predict= 'Tippa';
$Next_Match = "N&auml;sta Match";
$Prediction_League_Standings = 'Topplista';
$No_Matches_Scheduled = 'Inga schemalagda matcher';
$New_Message = "Nytt meddelande";
$Subject_txt = "&Auml;mne";

// v1.03 addition
$Months = array('01'=>"Jan",
             '02'=>"Feb",
             '03'=>"Mar",
             '04'=>"Apr",
             '05'=>"Maj",
             '06'=>"Jun",
             '07'=>"Jul",
             '08'=>"Aug",
             '09'=>"Sep",
             '10'=>"Okt",
             '11'=>"Nov",
             '12'=>"Dec");
$Set_Defaults = "Standard v&auml;rden";

// v1.06 additions
$FixturesResults = "Spelstop/Resultat";
$Away = "Bortalag"; // Away team title
$P = "S";  // Games played
$W = "V";  // Games won
$D = "O";  // Games drawn
$L = "F";  // Games lost
$F = "H";  // Goals for
$A = "B";  // Goals against
$GD = "MS";  // Goals difference
$POS = "Pos";  // Position
$PTS = "Png";  // Points
$Result = "Resultat";
$Prediction_Stats = "Tipsstatistik ";
$Download_Dbase = "Backupdatabas";
$Lang_Report_Bug = "Rapportera fel";

// v1.07 additions
$Lang_Enable_Auto_Predictions = "Autotips";
$Lang_Enable = "P&aring";
$Lang_Enter_Outstanding_Results = "Tilldela bel&ouml;ning f&ouml;r prestation";
$Lang_Enter_Multiple_Fixtures = "Ange flera spelstop";

// v1.08 additions
$Predictions_For = "Vald anv&auml;ndare";
$Game_Time = "Matchstart";

// v1.09 additions
$New_Message_Subject = "Du har ett nytt meddelande p&aring $PredictionLeagueTitle";
$New_Message_Body = "Du har ett nytt meddelande p&aring $PredictionLeagueTitle. Du kan l&auml;sa meddelandet h&auml;r http://$PLHome/";
$Games_Menu = "V&auml;lj kategori"; // translated in the meaning that you can choose NFL/NHL/???
$Upload_Icon = "Ladda Ikon";
$Previous = "F&ouml;reg&aringende";
$Next = "N&auml;sta";
$Upload_Instructions = "Anv&auml;nd bara bilder med dimensioner som &auml;r l&auml;mpade att visas som ikoner, tack. Storleken p&aring filen &auml;r begr&auml;nsad till $MaxIconFileSize Kb";
?>
