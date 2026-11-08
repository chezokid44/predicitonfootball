<?php 
/*********************************************************
 * Author: Octavian Susnea
 * Date  : 25.08.2003
 * File  : lang.php
 * Desc  : Romanian language file
 ********************************************************/

 /////////////////////////////////////////////////////////
 // Character set required
 /////////////////////////////////////////////////////////
 $charset = "iso8859-1";

 /////////////////////////////////////////////////////////
 // Menu variables
 /////////////////////////////////////////////////////////

$Login_txt = 'Login';
$Username_txt = 'Nume de utilizator';
$Password_txt = 'Parola';
$Logon = 'Login';
$New_User = 'Utilizator Nou';
$Forgot_password = 'Am uitat parola!';

$Home = 'HOME';

$Menu = 'Menu';

$Prediction_Table = 'Tabela de pronostic';
$Fixtures_Results = 'Meci';
$My_Predictions = 'Pronosticul Meu';
$My_Profile = 'Profilul Meu';
$Logout = 'Logout';

$Help = 'Ajutor';
$Email_Us = 'Email';

$Admin_Menu = 'Menu Administrator';
$Enter_Fixture = 'Adauga Meci';
$Enter_Result = 'Adauga Rezultat';
$Show_Users = 'Arata Utilizatori';
$Recalculate_Standings = 'Actualizeaza Clasamentul';
$Email_All_Users  = 'Email toti utilizatori';
$View_Log = 'Vizioneaza logurile';


 /////////////////////////////////////////////////////////
 // Create new user variables
 /////////////////////////////////////////////////////////

$Cookie_read_test_error = 'Imposibil de a testa cookie-ul.';
$Create_New_User = 'Adauga Utilizator Nou';
$User_ID_Required = 'Nume utilizator necessar';
$User_ID_too_long = 'Nume utilizator prea lung. Nu are voie sa fie mai lung decat 32 de caractere.';
$Passwords_is_required = 'Parola necessara';
$Passwords_do_not_match = 'Parola nu este identica';
$Password_too_long = 'Parola prea lunga. Trebuie sa fie mai mica decit 20 de caractere.';
$Email_address_is_required = 'Email necessar';
$Email_too_long = 'Email prea lung. Nu are voie sa fie mai lung decit 50 de caractere.';
$Email_address_is_not_valid = 'Email fals';
$User_Info = 'Informatie Utilizator';
$User_ID = 'Nume Utilizator';
$User_Name = 'User Name';
$Select_user_ID = 'Selecteaza un nume de utilizator.';
$Password_length = 'Parola trebuie sa fie cel puntin lunga de 5 caractere';
$Repeat_password = 'Repeta parola';
$Email_Address_txt = 'Email';
$Enter_valid_email_address = 'Introduceti un email correct.';
$Icon = 'Icoana';
$Select_icon = 'Selecteaza icoana.';
$Create = 'Executa';

// CreateUser.php

$Unable_to_register_txt = "Registrare ne reusita $username : $User";
$Unable_to_register_3_attempts_txt = "Registrare ne reusita $username dupa 3 incercari: $User";
$Successfully_registered = 'Registrare succes, $username adaugat';
$Username_already_exists = 'Utilizator exista deja, alegeti altul';
$One_user_per_email = 'Nu mai mult de un utilizator pe adresa email';
$Username_required = 'Nume Utilizator obligator';
$Password_required = 'Parola obligatoare';
$Email_Answer_txt = "Salut ".$username.",\n\nbun venit la ".$PredictionLeagueTitle.". Multumim ca esti de-al nostru.\n\nUita-te bine daca totul este bine scris, daca nu, puteti scimba\n\nParola = ".$password."\nEmail = ".$email."\n\nHai sa ai NOROC MULT!\n ".$adminSignature;
$Email_Subject_txt = "Bine t-am gasit la $PredictionLeagueTitle";

 /////////////////////////////////////////////////////////
 // Change user variables
 /////////////////////////////////////////////////////////
$My_Profile_for = 'Profilul meu';
$change_icon = 'Scimba';
$Join_Date = 'Data de inregistrare';
$Change_Profile = 'Scimba profil';
$Change_Password = 'Scimba parola';
$Old_Password_txt = 'Old Password';
$Password_Again = 'Repeta';
$Change_Password_button = 'Scimba parola';

// UserSelectIcon.php

$Click_on_the_Icon = 'Click pe icoana care o doresti';
$Profile_Icon_Selection = 'Profil icoana selectat';
$Language = "Language";

 /////////////////////////////////////////////////////////
 // Help file variables
 /////////////////////////////////////////////////////////

$Prediction_League_Help_topic = 'Prediction League Ajutor';
$Creating_a_new_user = 'A crea un nou utilizator';

$Creating_a_new_user_txt = '          Pentru a participa trebuie sa creiezi un nume de utilizator. Asta se face prin <b>Utilizator Nou</b> in coloana de dreapta. Verifica ca cookies sint acesate. Daca nu, o sa fiti trimis la aceasta pagina!
          <br> <br>
          Selecteaze un Nume utilizator pentru a va defini in liga. Poate fi ori ce, de o data ce nu e deja luat.
          <br> <br>
          Baga parola de doa ori. Pentru a fi sigur ca nimeni nu poate sa va scimbe parola.
          <br> <br>
          Tu <b>trebuie</b> sa bagi o adresa valida. In caz daca iti uiti parola.';

$Cookies_txt = 'Cookies';

$Cookies_txt_txt = '          Cookies <b>trebuie</b> sa fie acesate.
          <br><br>
          Probleme daca joci fara COOKIES:
          <ul>
          <li>Vezi aceasta pagina cind vrei sa login.
          <li>Nu poti baga pronosticul.
          <li>Icoana ne aratata.
          <li>Ai incercat sa te inregistrezi, ai ajuns la aceasta pagina. Trebui sa activezi Cookies.
          </ul>
          Pentru a activa Cookies in Internet Explorer 6, acesta: Tools->Internet Options. Selecteaza Privacy tab. Baga Lower. Preseaza OK. Uni au probleme cu medium. <br>
          In Netscape 6, menu: Edit->Preferences. Selecteaza Privacy & tab, dupa aia cookies. Selecteaza activeaza Cookies for the originating site numai, sau toti cookies. Apasa OK.<br>';

$Scoring_txt = 'Marca puncte';

$Scoring_txt_txt = '          Punctele acordate:
          <ul>
          <li>3 Puncte pentru scor correct. Exemplu tu zici 3-1, resultatul este 3-1.
          <li>1 Punct pentru resultat correct. Tu zici 0-1, scorul este 0-2.
          </ul>
          Diferenta de golurii
          <ul>
          <li>Pentru fiecare goal zis bine la o echipa, primesti +gol. Exeplu: tu zici 2 si echipa da 2, tu ai +2 goluri in diferenta.
          <li>Pentru fiecare goal zis prost la o echipa, primesti -gol. Exeplu: tu zici 2 si echipa da 0, tu ai (MINUS) -2 goluri in diferenta.
          </ul>';

// Version 1.0 Additions
 /////////////////////////////////////////////////////////
 // Messaging variables
 /////////////////////////////////////////////////////////
$Del = "Del";
$Delete = "Sterge";
$From = "De la";
$To = "Pentru";
$Date = "Data";
$Subject = "Subiect";
$My_Messages = "Mesajele mele";
$My_Messages_For = "Mesajele mele pentru";
$My_Predictions_For = "Pronosticurile mele pentru";
$Send_Msg = "Trimite mesaj";
$Create_Msg = "Creaza mesaj";
$Send_Msg_Button = "Trimite mesaj";
$Message_txt = "Mesaj";
$RE = "RE:";
$REM = "ReS";  // For removing messages
$Remove_All_Deleted = "Sterge toate mesajele deletate";
$View_All_Messages = "Vedere de toate mesajele";
$Status_Col = "Statut";

$Config_League = 'Configureaza liga';
$Predictions = 'Pronostic';
$Predict= 'Da pronostic';
$Next_Match = "Meciul urmator";
$Prediction_League_Standings = 'Clasament Liga Pronostic';
$No_Matches_Scheduled = 'Nici un meci prevazut';
$New_Message = "Mesaj nou";
$Subject_txt = "Subiect";

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
             '10'=>"Oct",
             '11'=>"Nov",
             '12'=>"Dec");
$Set_Defaults = "Valori prin default";

// v1.06 additions
$FixturesResults = "Resultate/Meciuri prevazute";
$Away = "Deplasare"; //
$P = "J";  // Games played
$W = "C";  // Games won
$D = "E";  // Games drawn
$L = "P";  // Games lost
$F = "P";  // Goals for
$A = "I";  // Goals against
$GD = "DG";  // Goals difference
$POS = "Pos";  // Position
$PTS = "Pun";  // Points
$Result = "Resultat";
$Prediction_Stats = "Statistice de Pronostic";
$Download_Dbase = "Backup Basa de Date";
$Lang_Report_Bug = "Reporta Bug";

// v1.07 additions
$Lang_Enable_Auto_Predictions = "Pronostic Automatic";
$Lang_Enable = "Ales";
$Lang_Enter_Outstanding_Results = "Inserteaza resultatele ramase";
$Lang_Enter_Multiple_Fixtures = "Inserteaza mai multe resultate";

// v1.08 additions
$Predictions_For = "Pronosticurii pentru";
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
