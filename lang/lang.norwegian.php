<?php 
/*********************************************************
 * Author: Jarle Svanes (c) 2003
 * Date  : 27.01.2003
 * File  : lang.php
 * Desc  : Norwegian language file
 ********************************************************/

 /////////////////////////////////////////////////////////
 // Character set required
 /////////////////////////////////////////////////////////
 $charset = "ISO8859-1";

 /////////////////////////////////////////////////////////
 // Menu variables
 /////////////////////////////////////////////////////////

$Login_txt = 'Logg inn';
$Username_txt = 'Brukernavn';
$Password_txt = 'Passord';
$Logon = 'Logg inn';
$New_User = 'Ny bruker';
$Forgot_password = 'Glemt passord';

$Home = 'Hjem';

$Menu = 'Meny';

$Prediction_Table = 'Tipstabell';
$Fixtures_Results = 'Terminliste/resultat';
$My_Predictions = 'Mine tips';
$My_Profile = 'Min profil';
$Logout = 'Logg ut';

$Help = 'Hjelp';
$Email_Us = 'Send oss en epost';

$Admin_Menu = 'Admin Meny';
$Enter_Fixture = 'Legg til kamp';
$Enter_Result = 'Legg inn resultat';
$Show_Users = 'Vis brukere';
$Recalculate_Standings = 'Rekalkuler stillingen';
$Email_All_Users  = 'Send epost til alle brukere ';
$View_Log = 'Se logg';

 /////////////////////////////////////////////////////////
 // Create new user variables
 /////////////////////////////////////////////////////////

$Cookie_read_test_error = 'Kan ikke lese test cookie når bruker ble laget';
$Create_New_User = 'Lag ny bruker';
$User_ID_Required = 'Bruker ID obligatorisk';
$User_ID_too_long = 'Bruker ID for lang. Må være mindre enn 32 tegn.';
$Passwords_is_required = 'Passord er obligatorisk';
$Passwords_do_not_match = 'Passord stemmer ikke';
$Password_too_long = 'Passord for langt. Må være mindre enn 32 tegn.';
$Email_address_is_required = 'Epostadresse er obligatorisk';
$Email_too_long = 'Epostadresse for lang. Må være mindre enn 60 tegn.';
$Email_address_is_not_valid = 'Epostadresse er ikke gyldig';
$User_Info = 'Brukerinformasjon';
$User_ID = 'Bruker ID';
$Select_user_ID = 'Velg en bruker ID.';
$Password_length = 'Passord må være minst 5 tegn langt.';
$Repeat_password = 'Gjenta passord.';
$Email_Address_txt = 'Epost adresse';
$Enter_valid_email_address = 'Skriv inn din gyldige epostadresse.';
$Icon = 'Ikon';
$Select_icon = 'Velg et ikon som representerer din bruker.';
$Create = 'Lag bruker';

// CreateUser.php

$Unable_to_register_txt = "Kunne ikke registrere brukervariabel ved opprettelse av $username : $User";
$Unable_to_register_3_attempts_txt = "Kunne ikke registrere brukervariabel ved opprettelse av $username feilet etter 3 forsøk: $User";
$Successfully_registered = 'Brukernavnet $username er registrert';
$Username_already_exists = 'Brukernavn eksisterer allerede, vennligst velg et nytt navn';
$One_user_per_email = 'Kun en bruker per epost ($email) adresse er lovlig.';
$Username_required = 'Brukernavn obligatorisk, vennligst velg et navn';
$Password_required = 'Passord obligatorisk, vennligst velg et passord';
$Email_Answer_txt = "Hei ".$username.",\n\nVelkommen til ".$PredictionLeagueTitle.". Takk for at du deltar i Prediction Leauge.\n\nVennligst sjekk at dine detaljer er korrekte. Hvis du trenger å endre noe, vennligst logg inn og modifiser din profil.\n\nPassord = ".$password."\nEpost = ".$email."\n\nLykke til\n ".$adminSignature;
$Email_Subject_txt = "Velkommen til $PredictionLeagueTitle";

 /////////////////////////////////////////////////////////
 // Change user variables
 /////////////////////////////////////////////////////////
$My_Profile_for = 'Min profil for';
$change_icon = 'endre';
$Join_Date = 'Bruker registrert';
$Change_Profile = 'Endre profil';
$Change_Password = 'Endre passord';
$Old_Password_txt = 'Old Password';
$Password_Again = 'En gang til';
$Change_Password_button = 'Endre passord';

// UserSelectIcon.php

$Click_on_the_Icon = 'Klikk på ikonet du vil bruke';
$Profile_Icon_Selection = 'Valg av profil-ikon';
$Language = "Language";


 /////////////////////////////////////////////////////////
 // Help file variables
 /////////////////////////////////////////////////////////

$Prediction_League_Help_topic = 'Hjelp';
$Creating_a_new_user = 'Lag en ny bruker';

$Creating_a_new_user_txt = '          For å kunne delta i denne Prediction Leauge må du lage deg din egen brukerprofil. Dette gjør du ved å velge lenken fra kolonnen til venstre. Vennligst se til at det er åpent for bruk av cookies i din nettleser. Hvis cookies er slått av vil du bli sendt videre til denne siden.
          <br> <br>
          Velg brukernavn for å identifisere deg selv i Prediction Leauge. Dette kan være hva og så langt du vil så lenge det ikke er tatt av en annen bruker.
          <br> <br>
          Skriv inn passordet (to ganger). Dette er for å sikre at ingen kommer seg inn for å endre dine tips.
          <br> <br>
          Du <b>må</b> skrive inn en gyldig epostadresse for å delta i Prediction Leauge. Dette blir brukt for å sende deg ditt passord om du skulle glemme dette.
<br> <br>
Man kan allerede fra første dag legge inn tips for alle kampene om man ønsker det, eller man kan vente til kampstart. <b>Husk at tipsene kan endres av brukerne helt fram til kampstart</b>.';

$Cookies_txt = 'Cookies';

$Cookies_txt_txt = '          Cookies <b>må</b> være tilgjengelig om du vil benytte Prediction Leauge.
          <br><br>
          Vanlige problemer når cookies ikke er i bruk er f.eks:
          <ul>
          <li>Du ser denne siden når du forsøker å logge deg inn.
          <li>Du blir bedt om brukernavn når du forsøker å legge inn et tips.
          <li>Ikonet vises ikke til venstre i skjermen.
          <li>Du forsøkte å registrere ditt brukernavn, men ble sendt videre til denne siden. Du må åpne for bruk av cookies i din nettleser.
          </ul>
          For å gjøre cookies tilgjengelig i Internet Explorer 6, gå til Tools->Internet Options. Velg "Privacy tab". Sett denne til "Medium" eller lavere. Trykk OK. Noen brukere har opplevd problemer med medium innstillinger. Du må da velge en lavere sikkerhet.<br>
For å gjøre cookies tilgjengelig i Netscape 6, gå til Edit->Preferences. Velg "Privacy & tab" og velg cookies. Velg "Enable Cookies" for "originating site only" eller gjør dem tilgjengelig for alle. Trykk OK.
<br>';

$Scoring_txt = 'Poengfordeling';

$Scoring_txt_txt = '          Poengene blir tildelt etter følgende regler:
          <ul>
          <li>3 poeng for korrekt målscore, f,eks. du tipper 3-1 og resultatet blir 3-1.
          <li>1 poeng for korrekt resultat. Du tipper 1-1, resultatet blir 2-2.
          </ul>
          Målforskjell
          <ul>
          <li>For korrekt antall mål for hvert av lagene får du det antallet som +mål. Tipper du at laget lager 2 mål og laget lager 2 får du +2 i målforskjell.
          <li>For hvert mål du tipper feil mister du det samme antallet i målforskjellen. Tipper du at laget lager 3 mål og de lager 2 får du -1 i målforskjell.
          </ul>';


// Version 1.0 Additions
 /////////////////////////////////////////////////////////
 // Messaging variables
 /////////////////////////////////////////////////////////
$Del = "Sle";
$Delete = "Slett";
$From = "Fra";
$To = "Til";
$Date = "Dato";
$Subject = "Emne";
$My_Messages = "Mine meldinger";
$My_Messages_For = "Mine meldinger for";
$My_Predictions_For = "Mine tips for";
$Send_Msg = "[mel] ";
$Create_Msg = "Skriv melding";
$Send_Msg_Button = "Send melding";
$Message_txt = "Melding";
$RE = "Sv:";
$REM = "Fje";  // For removing messages
$Remove_All_Deleted = "Fjern alle slettede meldinger";
$View_All_Messages = "Se alle meldinger";
$Status_Col = "Status";

$Config_League = 'Konfigurer liga';
$Predictions = 'Tips';
$Predict = 'Tipp';
$Next_Match = "Neste kamp";
$Prediction_League_Standings = 'Prediction League tabell';
$No_Matches_Scheduled = 'Ingen oppsatte kamper';
$New_Message = "Ny melding";
$Subject_txt = "Emne";
$User_Name = 'Brukernavn';

// v1.03 addition
$Months = array('01'=>"Jan",
             '02'=>"Feb",
             '03'=>"Mar",
             '04'=>"Apr",
             '05'=>"Mai",
             '06'=>"Jun",
             '07'=>"Jul",
             '08'=>"Aug",
             '09'=>"Sep",
             '10'=>"Okt",
             '11'=>"Nov",
             '12'=>"Des");
$Set_Defaults = "Standardinnstillinger";

// v1.06 additions
$FixturesResults = "Kamper/Resultat";
$Away = "Borte"; // Away team title
$P = "S";  // Games played
$W = "V";  // Games won
$D = "U";  // Games drawn
$L = "T";  // Games lost
$F = "F";  // Goals for
$A = "M";  // Goals against
$GD = "MFD";  // Goals difference
$POS = "Pos";  // Position
$PTS = "Png";  // Points
$Result = "Resultat";
$Prediction_Stats = "Prediction Stats ";
$Download_Dbase = "Backup DBase";
$Lang_Report_Bug = "Rapporter bugs";

// v1.07 additions
$Lang_Enable_Auto_Predictions = "Automatiske tips";
$Lang_Enable = "Legg til";
$Lang_Enter_Outstanding_Results = "Legg til utestående resultater";
$Lang_Enter_Multiple_Fixtures = "Legg til flere kamper";

// v1.08 additions
$Predictions_For = "Tips for";
$Game_Time = "Klokke";

// v1.09 additions
$New_Message_Subject = "You have received a new message on $PredictionLeagueTitle";
$New_Message_Body = "You have a new message on $PredictionLeagueTitle. You can view the message here http://$PLHome/";
$Games_Menu = "Select game";
$Upload_Icon = "Upload Icon";
$Previous = "Prev";
$Next = "Next";
$Upload_Instructions = "Please try to keep the dimensions of the image to a size suitable for display. Your image file size is limited to $MaxIconFileSize Kb";
?>
