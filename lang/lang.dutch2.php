<?php
/*********************************************************
* Auteur      : Jeroen Coolsaet, vertaling van Tuomo Aura (c) 2002
* Datum       : 18.07.2003
* Bestand     : lang.dutch.php
* Beschrijving: Bestand voor de nederlandse taal
********************************************************/

/////////////////////////////////////////////////////////
// Character set required (Karakter set vereist)
/////////////////////////////////////////////////////////
$charset = "iso8859-1";

/////////////////////////////////////////////////////////
// Menu variables (Menu Variabelen)
/////////////////////////////////////////////////////////

$Login_txt = 'Log In';
$Username_txt = 'Gebruikersnaam';
$Password_txt = 'Paswoord';
$Logon = 'Log Uit';
$New_User = 'Nieuwe gebruiker';
$Forgot_password = 'Paswoord vergeten';

$Home = 'Home';

$Menu = 'Menu';

$Prediction_Table = 'Voorspellingstabel';
$Fixtures_Results = 'Wedstrijden/Uitslagen';
$My_Predictions = 'Mijn voorspellingen';
$My_Profile = 'Mijn profiel';
$Logout = 'Log Uit';

$Help = 'Help';
$Email_Us = 'Email Ons';

$Admin_Menu = 'Admin Menu';
$Enter_Fixture = 'Toevoegen Wedstrijd';
$Enter_Result = 'Toevoegen Resultaat';
$Show_Users = 'Toon Gebruikers';
$Recalculate_Standings = 'Herbereken Klassement';
$Email_All_Users  = 'Email Alle Gebruikers';
$View_Log = 'Toon Log';

/////////////////////////////////////////////////////////
// Create new user variables (variabelen voor nieuwe gebruikers)
/////////////////////////////////////////////////////////

$Cookie_read_test_error = 'Het is niet mogelijk een test cookie te lezen bij 
het creeëren van een nieuwe gebruiker';
$Create_New_User = 'Maak een nieuwe gebruiker aan';
$User_ID_Required = 'Gebruikers ID is vereist';
$User_ID_too_long = 'Gebruikers ID is te lang. Maximaal 32 karakters.';
$Passwords_is_required = 'Wachtwoord is vereist';
$Passwords_do_not_match = 'Wachtwoord komt niet overeen';
$Password_too_long = 'Wachtwoord is te lang. Maximaal 32 karakters.';
$Email_address_is_required = 'Email is vereist';
$Email_too_long = 'Email is te lang. Maximaal 60 karakters.';
$Email_address_is_not_valid = 'Email adres is niet geldig';
$User_Info = 'Gebruikers info';
$User_ID = 'Gebruikers ID';
$User_Name = 'User Name';
$Select_user_ID = 'Selecteer een gebruikers ID.';
$Password_length = 'Wachtwoord moet minimaal 5 characters lang zijn.';
$Repeat_Password = 'Herhaal Wachtwoord.';
$Email_Address_txt = 'Email Adres';
$Enter_valid_email_address = 'Vul een geldig email adres in.';
$Icon = 'Avatar';
$Select_icon = 'Selecteer de avatar om je gebruiker te vertegenwoordigen.';
$Create = 'Maak';


// CreateUser.php

$Successfully_registered = $username.' is met succes aangemaakt';
$Username_already_exists = 'Gebruikersnaam bestaat al, gebruik een andere 
naam';
$One_user_per_email = 'Maximaal een gebruiker per e-mail ($email) adres 
toegestaan.';
$Username_required = 'Gebruikersnaam is vereist, kies een naam';
$Password_required = 'Wachtwoord is vereist, kies een wachtwoord';
$Email_Answer_txt = "Hallo ".$username.",\n\nWelkom bij 
".$PredictionLeagueTitle.". Bedankt voor je registratie.\n\nVergeet niet je 
gegevens te controleren. Indien er iets gewijzigd moet worden, log dan in en 
wijzig het in je eigen profiel.\n\nWachtwoord = ".$password."\nEmail = 
".$email."\n\nVeel Geluk\n ".$adminSignature;
$Email_Subject_txt = "Welkom bij ".$PredictionLeagueTitle;

// CreateUser.php


/////////////////////////////////////////////////////////
// Change user variables (variabelen voor de gebruikersdatabase)
/////////////////////////////////////////////////////////
$My_Profile_for = 'Mijn Profiel voor';
$change_icon = 'verander';
$Join_Date = 'Registratie Datum';
$Change_Profile = 'Verander Profiel';
$Change_Password = 'Verander Paswoord';
$Old_Password_txt = 'Oud Paswoord';
$Password_Again = 'Opnieuw';
$Change_Password_button = 'Verander Paswoord';

// UserSelectIcon.php

$Click_on_the_Icon = 'Klik op de avatar die je wil gebruiken';
$Profile_Icon_Selection = 'Avatar Selectie';
$Language = "Taal";

/////////////////////////////////////////////////////////
// Help file variables (Variabelen voor het helpbestand)
/////////////////////////////////////////////////////////

$Prediction_League_Help_topic = 'Prediction League Help';
$Creating_a_new_user = 'Een nieuwe gebruiker aanmaken';

$Creating_a_new_user_txt = '          Om in staat te zijn om mee te doen in 
de Voorspellings competitie zul je een gebruikersprofiel moeten creeëren. 
Deze kan worden aangemaakt door de <b>Nieuwe gebruiker</b> link te 
selecteren uit de rechter kolom.
          <br> <br>
          Selecteer een Gebruikers ID om jezelf te identificeren in de 
voorspellingscompetitie. Deze naam kan een willekeurige naam zijn, zolang 
deze nog niet is gebruikt door een andere gebruiker.
          <br> <br>
          Voer een wachtwoord in (twee maal). Dit is om er zeker van te zijn 
dat niemand je voorspellingen kan wijzigen.
          <br> <br>
          Je <b>moet</b> overigens een geldig e-mail adres hebben om aan de 
voorspellingscompetitie mee te mogen doen. Dit is opdat we je wachtwoord 
naar je op kunnen sturen, mocht je het vergeten.';

$Scoring_txt = 'Score';

$Scoring_txt_txt = '          Punten worden als volgt toegekend:

          <ul>
          <li>'.$corrScore.' Punten voor een correcte score. Bijvoorbeeld je 
voorspelt 3-1 en de uitslag is ook 3-1.
          <li>'.$corrResult.' Punten voor een correct resultaat. Je 
voorspelt 1-1, de score is 2-2.
          </ul>
          Doelpunten verschil
          <ul>
          <li>Voor ieder hoeveelheid aan team goals je maakt, ontvang je het 
nummer +doelpunten. Bijvoorbeeld, je voorspelt 2 doelpunten en het team 
scoort 2 doelpunten. Je ontvangt dan +2 goals.
          <li>Voor ieder aantal incorrecte doelpunten die je voorspelt voor 
een team, verlies je het verschil in doelpunten. Bijvoorbeeld, je voorspelt 
3 doelpunten en het team scoort 2 doelpunten. Je ontvangt dan -1 goals.
          </ul>';

// Version 1.0 Additions (Versie 1.0 Toevoegingen)
/////////////////////////////////////////////////////////
// Messaging variables (Bericht Variabelen)
/////////////////////////////////////////////////////////
$Del = "Verwijder";
$Delete = "Verwijder";
$From = "Van";
$To = "Tot";
$Date = "Datum";
$Subject = "Onderwerp";
$My_Messages = "Mijn Berichten";
$My_Messages_For = "Mijn Berichten voor";
$My_Predictions_For = "Mijn Voorspellingen voor";
$Send_Msg = "Verzend Bericht";
$Create_Msg = "Nieuw Bericht";
$Send_Msg_Button = "Verzend Bericht";
$Message_txt = "Bericht";
$RE = "Aw:";  // Although "Re:" is often used to, but "Aw" is the correct dutch reply abbreviation
$REM = "Verwijder";  // For removing messages
$Remove_All_Deleted = "Verwijder alle gemarkeerde berichten";
$View_All_Messages = "Alle Berichten";
$Status_Col = "Status";

$Config_League = 'Configure League';
$Predictions = 'Voorspellingen';
$Predict= 'Voorspel';
$Next_Match = "Volgende Wedstrijd";
$Prediction_League_Standings = 'Prediction League Tabel';
$No_Matches_Scheduled = 'Geen wedstrijden gepland';
$New_Message = "Nieuw Bericht";
$Subject_txt = "Onderwerp";

// v1.03 addition (v1.03 toevoegingen)
$Months = array('01'=>"Jan",
             '02'=>"Feb",
             '03'=>"Mar",
             '04'=>"Apr",
             '05'=>"Mei",
             '06'=>"Jun",
             '07'=>"Jul",
             '08'=>"Aug",
             '09'=>"Sep",
             '10'=>"Okt",
             '11'=>"Nov",
             '12'=>"Dec");
$Set_Defaults = "Standaard configuratie waarden";

// v1.06 additions (v1.06 toevoegingen)
$FixturesResults = "Wedstrijden/Resultaten";
$Away = "Uit"; // Titel van Uitploeg
$P = "A";  // Aantal Wedstrijden
$W = "W";  // Wedstrijden Gewonnen
$D = "G";  // Wedstrijden Gelijkgespeeld
$L = "V";  // Wedstrijden Verloren
$F = "F";  // Doelpunten Voor
$A = "A";  // Doelpunten Tegen
$GD = "DS";  // Doelpunten Saldo
$POS = "Pos";  // Positie
$PTS = "Pnt";  // Punten
$Result = "Resultaat";
$Prediction_Stats = "Statistieken";
$Download_Dbase = "Neem een backup van de database";
$Lang_Report_Bug = "Rapporteer een bug";

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
