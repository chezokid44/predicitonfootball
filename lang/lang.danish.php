<?php 
/*********************************************************
 * Author: Kasper Osnes (c) 2003
 * Date  : 24.03.2003
 * File  : lang.danish.php
 * Desc  : Danish language file
 ********************************************************/

 /////////////////////////////////////////////////////////
 // Character set required
 /////////////////////////////////////////////////////////
 $charset = "iso8859-1";

 /////////////////////////////////////////////////////////
 // Menu variables
 /////////////////////////////////////////////////////////

$Login_txt = 'Login';
$Username_txt = 'Brugernavn';
$Password_txt = 'Adgangskode';
$Logon = 'Log på';
$New_User = 'Ny Bruger';
$Forgot_password = 'Glemt Adgangskode';

$Home = 'Hjem';

$Menu = 'Menu';

$Prediction_Table = 'Prediction Tabel';
$Fixtures_Results = 'Fixtures/Resultater';
$My_Predictions = 'Mine Predictions';
$My_Profile = 'Min Profil';
$Logout = 'Log af';

$Help = 'Hjælp';
$Email_Us = 'Email Os';

$Admin_Menu = 'Admin Menu';
$Enter_Fixture = 'Indtast Fixture';
$Enter_Result = 'Indtast Resultat';
$Show_Users = 'Vis Brugere';
$Recalculate_Standings = 'Beregn Placeringer';
$Email_All_Users  = 'Email Alle Brugere';
$View_Log = 'Vis Log';

 /////////////////////////////////////////////////////////
 // Create new user variables
 /////////////////////////////////////////////////////////

$Cookie_read_test_error = 'Kunne ikke læse test cookie ved oprettelse af ny bruger';
$Create_New_User = 'Opret Ny Bruger';
$User_ID_Required = 'Bruger ID er krævet';
$User_ID_too_long = 'Bruger ID er for langt. Skal være mindre end 32 tegn';
$Passwords_is_required = 'Adgangskode er krævet';
$Passwords_do_not_match = 'Adgangskoderne er ikke ens';
$Password_too_long = 'Adgangskoden er for lang. Skal være mindre end 32 tegn';
$Email_address_is_required = 'Email adresse er krævet';
$Email_too_long = 'Email adressen er for lang. Skal være mindre end 60 tegn';
$Email_address_is_not_valid = 'Email adressen er ugyldig';
$User_Info = 'Bruger Info';
$User_ID = 'Bruger ID';
$User_Name = 'Bruger Name';
$Select_user_ID = 'Vælg et bruger ID.';
$Password_length = 'Adgangskoden skal være på mindst 5 tegn.';
$Repeat_password = 'Gentag adgangskode.';
$Email_Address_txt = 'Email Adresse';
$Enter_valid_email_address = 'Indtast en gyldig email adresse.';
$Icon = 'Ikon';
$Select_icon = 'Vælg et ikon der skal repræsentere din bruger.';
$Create = 'Opret';

// CreateUser.php

$Unable_to_register_txt = "Kunne ikke registrere bruger variable ved oprettelse for $username : $User";
$Unable_to_register_3_attempts_txt = "Kunne ikke registrere bruger variable ved oprettelse for $username fejlede efter 3 forsøg: $User";
$Successfully_registered = 'Succesfuld registrering '.$username.' under oprettelse';
$Username_already_exists = 'Brugernavnet eksisterer allerede, vælg venligst et andet navn';
$One_user_per_email = 'Kun en bruger per mail adresse. ($email) adresse tilladt.';
$Username_required = 'Brugernavn er krævet, vælg venligst et navn';
$Password_required = 'Adgangskode er krævet, vælg venligst en adgangskode';
$Email_Answer_txt = "Hej ".$username.",\n\nVelkommen til ".$PredictionLeagueTitle.". Tak for din deltagelse.\n\nKontroller venligst at dine detajler er korrekte. Hvis du har brug for at ændre noget så log ind i din profil.\n\nAdgangskode = ".$password."\nEmail = ".$email."\n\nHeld og Lykke\n ".$adminSignature;
$Email_Subject_txt = "Velkommen til $PredictionLeagueTitle";

 /////////////////////////////////////////////////////////
 // Change user variables
 /////////////////////////////////////////////////////////
$My_Profile_for = 'Min Profil for';
$change_icon = 'rediger';
$Join_Date = 'Dato Oprettet';
$Change_Profile = 'Rediger Profil';
$Change_Password = 'Skift Adgangskode';
$Old_Password_txt = 'Old Password';
$Password_Again = 'Igen';
$Change_Password_button = 'Skift Adgangskode';

// UserSelectIcon.php

$Click_on_the_Icon = 'Klik på det ikon du ønsker at bruge';
$Profile_Icon_Selection = 'Profil Ikon';
$Language = "Language";


 /////////////////////////////////////////////////////////
 // Help file variables
 /////////////////////////////////////////////////////////

$Prediction_League_Help_topic = 'Prediction League Hjælp';
$Creating_a_new_user = 'Opret en ny bruger';

$Creating_a_new_user_txt = '          For at kunne deltage i Prediction League skal du oprette en profil. Denne bliver oprettet ved at vælge <b>Ny Bruger</b> linket fra højre side. Sørg venligst for at cookies er aktiveret inden du begynder. Hvis cookies er deaktiveret bliver du ført til denne side igen.
          <br> <br>
          Vælg et Bruger ID for at identifikere dig selv. Dette kan være lige hvad du ønsker så længe det ikke er taget af en anden bruger.
          <br> <br>
          Indtast en adgangskode (to gange). Dette er for at sikre at ngen anden end dig selv kan logge på som dig.
          <br> <br>
          Du <b>skal</b> indtaste en gyldig email adresse for at deltage i prediction league. Denne bliver brugt til at sende din adgangskode ud til dig i tilfælde af du glemmer den.';

$Cookies_txt = 'Cookies';

$Cookies_txt_txt = '          Cookies <b>skal</b> være aktiveret for at bruge Prediction League.
          <br><br>
          De mest almindelige problemer der opstår når cookies ikke er aktiveret:
          <ul>
          <li>Denne side bliver vist når du logger på.
          <li>Du bliver spurgt efter en Bruger når du forsøger at komme på.
          <li>Ikon bliver ikke vist i venstre side af skærmen.
          <li>Du forsøgte at oprette en bruger men blev ført til denne side. Du skal aktiverer cookies i din browser.
          </ul>
          For at aktivere cookies i Internet Explorer 6, følg menu linien Værktøjer->Internet Indstillinger. Vælg Privacy fanebladet. Indstil privacy indstillingen til Medium eller lavere. Tryk OK. Nogle brugere har oplevet problemer med medium indstillingen. Du er måske nødsaget til at sætte den lavere.<br>
          For at aktivere cookies i Netscape 6, følg menu linien Edit->Preferences. Vælg Privacy & fanebladet, vælg så cookies. Vælg Enable Cookies for originating site only, eller enable all cookies. Tryk OK.<br>';

$Scoring_txt = 'Scoring';

$Scoring_txt_txt = '          Point bliver fordelt som følgende:
          <ul>
          <li>3 Points for en korrekt score. f.eks. Du forudsiger 3-1 og resultater bliver 3-1.
          <li>1 Point for et korrekt resultat. Du forudsiger 1-1, scoren bliver 2-2.
          </ul>
          Mål Difference
          <ul>
          <li>For hvert korrekt holds mål du scorer får du det nummer af +goals. f.eks. Du forudsiger 2 og holdet scorer 2, du får +2 goals.
          <li>For hvert forkert nummer af mål du forudsiger per hold taber du den difference i mål. f.eks. Du forudsiger 3 og holdet scorer 2, du får -1 mål.
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
