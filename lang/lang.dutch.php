<?php 
/*********************************************************
 * Author: Cambier Wout (c) 2004 
 * Date  : 06.01.2004
 * File  : lang.php
 * Ver.  : 1.5  (smaller size)
 * Desc  : Dutch language file
  ********************************************************/

 /////////////////////////////////////////////////////////
 // Character set required
 /////////////////////////////////////////////////////////
 $charset = "ISO8859-1";

 /////////////////////////////////////////////////////////
 // Menu variables
 /////////////////////////////////////////////////////////

$Login_txt = 'Login';
$Username_txt = 'Gebruikersnaam';
$Password_txt = 'Wachtwoord';
$Logon = 'Inloggen';
$New_User = 'Nieuwe Gebruiker';
$Forgot_Password = 'Wachtwoord vergeten';

$Home = 'Links';

$Menu = 'Menu';

$Prediction_Table = 'Pronostiek';
$Fixtures_Results = 'Wedstrijden/Uitslagen';
$My_Predictions = 'Mijn voorspellingen';
$My_Profile = 'Mijn profiel';
$Logout = 'Uitloggen';

$Help = 'Help';
$Email_Us = 'Mail de beheerder';

$Admin_Menu = 'Beheerder Menu';
$Enter_Fixture = 'Wedstrijd Toevoegen';
$Enter_Result = 'Uitslag Toevoegen';
$Show_Users = 'Toon Gebruikers';
$Recalculate_Standings = 'Vernieuw Pronostiek';
$Email_All_Users  = 'Mail alle gebruikers ';
$View_Log = 'Toon log';

 /////////////////////////////////////////////////////////
 // Create new user variables
 /////////////////////////////////////////////////////////

$Cookie_read_test_error = 'Het is niet mogelijk een test cookie te lezen bij het creeëren van een nieuwe gebruiker';
$Create_New_User = 'Maak nieuwe gebruiker aan';
$User_ID_Required = 'Gebruikersnaam vereist';
$User_ID_too_long = 'Gebruikersnaam te lang. Maximaal 32 tekens.';
$Passwords_is_required = 'Wachtwoord is vereist';
$Passwords_do_not_match = 'Wachtwoord komt niet overeen';
$Password_too_long = 'Wachtwoord is te lang. Maximaal 32 tekens.';
$Email_address_is_required = 'Email is vereist';
$Email_too_long = 'Email is te lang. Maximaal 60 tekens.';
$Email_address_is_not_valid = 'Email adres is niet geldig';
$User_Info = 'Gebruikers info';
$User_ID = 'Gebruikersnaam';
$User_Name = 'Gebruikersnaam';
$Select_user_ID = 'Selecteer een gebruikersnaam.';
$Password_length = 'Wachtwoord moet minimaal 5 tekens lang zijn.';
$Repeat_password = 'Herhaal Wachtwoord.';
$Email_Address_txt = 'Email Adres';
$Enter_valid_email_address = 'Vul een geldig email adres in.';
$Icon = 'Avatar';
$Select_icon = 'Selecteer de avatar om je gebruiker te vertegenwoordigen.';
$Create = 'Maak aan';

// CreateUser.php

$Unable_to_register_txt = "Er is een fout opgetreden tijdens het creeëren van $username : $User";
$Unable_to_register_3_attempts_txt = "Er is een fout opgetreden tijdens het creeëren van $username bij 3 keer proberen: $User";
$Successfully_registered = $username.' is met succes aangemaakt';
$Username_already_exists = 'Gebruikersnaam bestaat al, gebruik een andere naam';
$One_user_per_email = 'Maximaal een gebruiker per e-mail ($email) adres toegestaan.';
$Username_required = 'Gebruikersnaam vereist, kies een naam';
$Password_required = 'Wachtwoord vereist, kies een wachtwoord';
$Email_Answer_txt = "Hallo ".$username.",\n\nWelkom bij ".$PredictionLeagueTitle.". Bedankt voor je registratie.\n\nVergeet niet je gegevens te controleren. Indien er iets gewijzigd moet worden, log dan in en wijzig het in je eigen profiel.\n\nWachtwoord = ".$password."\nEmail = ".$email."\n\nVeel Geluk\n ".$adminSignature;
$Email_Subject_txt = "Welkom bij $PredictionLeagueTitle";

 /////////////////////////////////////////////////////////
 // Change user variables
 /////////////////////////////////////////////////////////
$My_Profile_for = 'Mijn profiel';
$change_icon = 'verander avatar';
$Join_Date = 'Registratie datum';
$Change_Profile = 'Verander profiel';
$Change_Password = 'Verander Wachtwoord';
$Old_Password_txt = 'Oud Wachtwoord';
$Password_Again = 'Wachtwoord Nogmaals Invoeren';
$Change_Password_button = 'Verander Wachtwoord';

// UserSelectIcon.php

$Click_on_the_Icon = 'Klik op de Avatar dat je wilt gebruiken';
$Profile_Icon_Selection = 'Avatar selectie voor je Profiel';
$Language = "Taal";

 /////////////////////////////////////////////////////////
 // Help file variables
 /////////////////////////////////////////////////////////

$Prediction_League_Help_topic = 'Prediction League Help';
$Creating_a_new_user = 'Een nieuwe gebruiker aanmaken';

$Creating_a_new_user_txt = '          Om in staat te zijn om mee te doen in de Voorspellings competitie zul je een gebruikersprofiel moeten creeëren. Deze kan worden aangemaakt door de <b>Nieuwe gebruiker</b> link te selecteren uit de rechter kolom. Zorg er wel voor dat je browser cookies accepteerd. Als cookies uitgeschakeld zijn wordt je verwezen naar deze pagina.
          <br> <br>
          Selecteer een Gebruikers ID om jezelf te identificeren in de voorspellings competitie. Deze naam kan een willekeurige naam zijn, zolang deze nog niet is gebruikt door een andere gebruiker.
          <br> <br>
          Voer een wachtwoord in (twee maal). Dit is om er zeker van te zijn dat niemand je voorspellingen kan wijzigen.
          <br> <br>
          Je <b>moet</b> overigens een geldig e-mail adres hebben om de aan voorspellings competitie mee te mogen doen. Dit is zodat we je wachtwoord naar je op kunnen sturen, mocht je het vergeten.';

$Scoring_txt = 'Score';

$Scoring_txt_txt = '          Punten worden als volgt toegekend:
          <ul>
          <li>'.$corrScore.' Punten voor iedere correcte score. Bijvoorbeeld je voorspelt 3-1 en de uitslag is inderdaad 3-1.
          <li>'.$corrResult.' Punt voor een goed resultaat. Je voorspelt 1-1 en de uitslag is 2-2.
          </ul>
          Doelpunten verschil
          <ul>
          <li>Voor ieder hoeveelheid aan team goals je maakt, ontvang je het nummer +doelpunten. Bijvoorbeeld, je voorspelt 2 doelpunten en het team scoort 2 doelpunten. Je ontvangt dan +2 goals.
          <li>Voor ieder aantal incorrecte doelpunten die je voorspelt voor een team, verlies je het verschil in doelpunten. Bijvoorbeeld, je voorspelt 3 doelpunten en het team scoort 2 doelpunten. Je ontvangt dan -1 goals.
          </ul>';


// Version 1.0 Additions
 /////////////////////////////////////////////////////////
 // Messaging variables
 /////////////////////////////////////////////////////////
$Del = "Verwijderen";
$Delete = "Verwijderen";
$From = "Van";
$To = "Naar";
$Date = "Datum";
$Subject = "Onderwerp";
$My_Messages = "Mijn berichten";
$My_Messages_For = "Mijn berichten voor";
$My_Predictions_For = "Mijn Pronostiek voor";
$Send_Msg = "[bericht] ";
$Create_Msg = "Maak een bericht";
$Send_Msg_Button = "Verstuur bericht";
$Message_txt = "Bericht";
$RE = "Re:";
$REM = "Rem";  // For removing messages
$Remove_All_Deleted = "Verwijder alle berichten";
$View_All_Messages = "Bekijk alle berichten";
$Status_Col = "Status";

$Config_League = 'Configureer je Pronostiek';
$Predictions = 'Voorspellingen';
$Predict = 'Voorspel';
$Next_Match = "Volgende match";
$Prediction_League_Standings = 'pronostiek tussenstand';
$No_Matches_Scheduled = 'Geen matchen voorzien';
$New_Message = "Nieuw bericht";
$Subject_txt = "Onderwerp";

// v1.03 additions
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
$FixturesResults = "Wedstrijden/Resultaten";
$Away = "Uit"; // Naam van de andere ploeg
$P = "Wedstrijden";  // Wedstrijden gespeeld
$W = "Gewonnen";  // Wedstrijden gewonnen
//$D = "Gelijk";  // Wedstrijden gelijk
$L = "Verloren";  // Wedstrijden verloren
//$F = "F";  // Goalen voor
//$A = "A";  // Goalen tegen
//$GD = "GD";  // Goalenverschil
$POS = "#";  // Positie
$PTS = "Punten";  // Punten
$Result = "Resultaat";
$Prediction_Stats = "Statistieken ";
$Download_Dbase = "Maak een BackUp";
$Lang_Report_Bug = "Verstuur Foutmelding";

// v1.07 additions
$Lang_Enable_Auto_Predictions = "Automatische voorspellingen";
$Lang_Enable = "Aanzetten";
$Lang_Enter_Outstanding_Results = "Voer de resultaten in";
$Lang_Enter_Multiple_Fixtures = "Voer meerdere wedstrijden in";

// v1.08 additions
$Predictions_For = "Voorspellingen voor";
$Game_Time = "SpelTijd";

// v1.09 additions
$New_Message_Subject = "U hebt een nieuw bericht ontvangen op $PredictionLeagueTitle";
$New_Message_Body = "U hebt een nieuw bericht ontvangen op $PredictionLeagueTitle. U kan hier het bericht bekijken http://$PLHome/";
$Games_Menu = "Select game";
$Upload_Icon = "Upload Avatar";
$Previous = "vorige";
$Next = "volgende";
$Upload_Instructions = "Gelieve te proberen om de afmetingen van het beeld aan een grootte te
houden geschikt voor vertoning. Uw afbeeldingsgrootte is gelimiteerd tot $MaxIconFileSize Kb";
?>
