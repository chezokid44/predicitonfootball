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
$Username_txt = 'Brugernavn';
$Password_txt = 'Password';
$Logon = 'Logon';
$New_User = 'Ny Bruger';
$Forgot_password = 'Glemt password';

$Home = 'Home';

$Menu = 'Menu';

$Prediction_Table = 'Tips Tabel';
$Fixtures_Results = 'Kampe/Resultater';
$My_Predictions = 'Mine Resultat Tips';
$My_Profile = 'Min Profil';
$Logout = 'Log Ud';

$Help = 'Info og regler';
$Email_Us = 'Email Webmaster';

$Admin_Menu = 'Admin Menu';
$Enter_Fixture = 'Tilføj Kamp';
$Enter_Result = 'Tilføj Resultat';
$Show_Users = 'Vis Deltagere';
$Recalculate_Standings = 'Udregn Stillingen';
$Email_All_Users  = 'Send Email Til Alle Deltagere ';
$View_Log = 'Vis Log';

 /////////////////////////////////////////////////////////
 // Create new user variables
 /////////////////////////////////////////////////////////

$Cookie_read_test_error = 'Kan ikke åbne test cookie når ny bruger oprettes';
$Create_New_User = 'Tilføj Ny Deltager';
$User_ID_Required = 'Bruger ID mangler';
$User_ID_too_long = 'Bruger ID er for langt. Må højst være 32 bogstaver.';
$Passwords_is_required = 'Password mangler';
$Passwords_do_not_match = 'Passwords er ikke de samme';
$Password_too_long = 'Password er for langt. Må højst være 32 bogstaver.';
$Email_address_is_required = 'Email addresse mangler';
$Email_too_long = 'email adresse er for langt. Må højst være 60 bogstaver.';
$Email_address_is_not_valid = 'Email addresse er ikke gyldig';
$User_Info = 'Bruger Info';
$User_ID = 'Bruger ID';
$User_Name = 'Bruger Name';
$Select_user_ID = 'Vælg bruger ID.';
$Password_length = 'Password skal være på mindst 5 bogstaver.';
$Repeat_password = 'Gentag password.';
$Email_Address_txt = 'Email Addresse';
$Enter_valid_email_address = 'Indtast gyldig email adresse.';
$Icon = 'Ikon';
$Select_icon = 'Vælg bruger ikon.';
$Create = 'Tilmeld';

// CreateUser.php

$Unable_to_register_txt = "Ikke i stand til at oprette $username : $User";
$Unable_to_register_3_attempts_txt = "Ikke istand til at oprette bruger variabel efter 3 forsøg: $User";
$Successfully_registered = $username.' registeret!';
$Username_already_exists = 'Brugernavn eksisterer allerede. vælg venligst et andet.';
$One_user_per_email = 'Kun 1 bruger pr. email ($email) addresse tilladt.';
$Username_required = 'Brugernavn påkrævet, vælg venligst et navn';
$Password_required = 'Password required, please choose a password';
$Email_Answer_txt = "Hej ".$username.",\n\nvelkommen til ".$PredictionLeagueTitle.". Tak for din tilmelding\n\nCheck venligst om dine oplysninger er korrekte. Hvis du vil ændre noget, så log ind og lav ændringer under din profil.\n\nPassword = ".$password."\nEmail = ".$email."\n\nHeld og Lykke\n ".$adminSignature;
$Email_Subject_txt = "Velkommen til $PredictionLeagueTitle";

 /////////////////////////////////////////////////////////
 // Change user variables
 /////////////////////////////////////////////////////////
$My_Profile_for = 'Min Profil for';
$change_icon = 'rediger';
$Join_Date = 'Dato tilmeldt';
$Change_Profile = 'Rediger Profil';
$Change_Password = 'Rediger Password';
$Old_Password_txt = 'Old Password';
$Password_Again = 'Password igen';
$Change_Password_button = 'Rediger password';

// UserSelectIcon.php

$Click_on_the_Icon = 'Klik på det ikon du vil bruge.';
$Profile_Icon_Selection = 'Vælg ikon for din profil';
$Language = "Language";


 /////////////////////////////////////////////////////////
 // Help file variables
 /////////////////////////////////////////////////////////

$Prediction_League_Help_topic = 'Prediction League Help';
$Creating_a_new_user = 'HI4 tipsliga';

$Creating_a_new_user_txt = 'Dette er HI 4. seniors tipsliga. Formålet med denne liga, er at tippe de korrekte resultater af vores egne kampe og Superliga kampe. Vinderen
er selvfølgelig den som har flest point, når vi har spillet vores sidste kamp en gang til efteråret. Alle er velkomne til at deltage. 
<br><br>
Du skal registrere en ny profil med brugernavn og password i denne tipsliga, selvom du er oprettet som bruger på www.hi4.dk. Så hvad venter du på? Tilmeld dig med det samme!
<br><br>
HUSK! cookies skal være tilsluttet i din browser!

          ';


$Scoring_txt = 'Points';

$Scoring_txt_txt = 'Du optjener points på følgende måde:
          <ul>
          <li>3 points for en korrekt score (du tipper 3-1 og kampen bliver 3-1.)
          <li>1 Point for et korrekt 1,X,2 (du tipper 1-1 og kampen bliver 2-2.)<br><br>
<b>Målforskel bruges til at afgøre stillingen ved pointlighed:</b><br>         
          <li>For hver korrekt antal mål pr. hold du tipper, ligeså mange mål for får du (du tipper at holdet scorer 2 mål og de scorer 2 mål, får du 2 mål for).
          <li>For hver ikke korrekt antal mål pr. hold du tipper, ligeså mange mål imod får du (Du tipper at holdet scorer 3 mål og de scorer kun 2, får du 1 mål imod).

<br><br>
<ul>
<li>Ordforklaring i resultat tabellen:<br><br>
<li>Bruger = Brugernavn<br>
<li>Vu = Antal af korrekte måltips<br>
<li>Ua = Antal af korrekte uafgjorte tip<br>
<li>Ta = Antal af ikke-korrekte tip<br>
<li>Fo = Antal mål for<br>
<li>Im = Antal mål imod<br>
<li>MåF = Målforskel<br>
<li>Pts = Points<br>
</ul>


';

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
