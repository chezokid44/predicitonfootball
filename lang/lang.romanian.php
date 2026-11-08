<?php 
/*********************************************************
 * Author: Adrian Bargaoanu - DINAMO ONLINE (c) 2003
 * Date  : 12.08.2003
 * File  : lang.romanian.php
 * Desc  : Romanian language file
 ********************************************************/

 /////////////////////////////////////////////////////////
 // Menu variables
 /////////////////////////////////////////////////////////

$Login_txt = 'Login';
$Username_txt = 'Utilizator';
$Password_txt = 'Parola';
$Logon = 'Logon';
$New_User = 'Utilizator nou';
$Forgot_password = 'Parola uitata';

$Home = 'Acasa';

$Menu = 'Meniu';

$Prediction_Table = 'Clasament';
$Fixtures_Results = 'Meciuri/Rezultate';
$My_Predictions = 'Pronosticurile mele';
$My_Profile = 'Profilul meu';
$Logout = 'Iesi din cont';

$Help = 'Ajutor';
$Email_Us = 'Scrie-ne';

$Admin_Menu = 'Administrare Menu';
$Enter_Fixture = 'Introducere meci';
$Enter_Result = 'Introducere rezultat';
$Show_Users = 'Arata utilizatorii';
$Recalculate_Standings = 'Recalculeaza clasament';
$Email_All_Users  = 'Scriele tuturor utilizatorilor ';
$View_Log = 'Vezi logari';

 /////////////////////////////////////////////////////////
 // Create new user variables
 /////////////////////////////////////////////////////////

$Cookie_read_test_error = 'Nu poti citi test cookie cind creezi un nou utilizator';
$Create_New_User = 'Creaza un nou utilizator';
$User_ID_Required = 'Cerere ID utilizatori';
$User_ID_too_long = 'ID-ul utilizatorului prea lung. Trebuie sa aiba cel putint 32 de caractere.';
$Passwords_is_required = 'Cerere parola';
$Passwords_do_not_match = 'Parola nu se potriveste';
$Password_too_long = 'Parola prea lunga. Trebuie sa aiba cel putin 32 de caractere.';
$Email_address_is_required = 'Cerere adresa email';
$Email_too_long = 'Adresa de email prea lunga. Trebuie sa aiba cel putin 60 de caractere.';
$Email_address_is_not_valid = 'Adresa de email nu este valida';
$User_Info = 'Informatii utilizator';
$User_ID = 'ID utilizator';
$User_Name = 'User Name';
$Select_user_ID = 'Alege ID utilizator.';
$Password_length = 'Parola trebuie sa aiba cel putin 5 caractere.';
$Repeat_password = 'Repeta parola.';
$Email_Address_txt = 'Adresa de email';
$Enter_valid_email_address = 'Introdu o adreasa de email valida.';
$Icon = 'Iconita';
$Select_icon = 'Alege o icoanita care sa reprezinte un utilizator.';
$Create = 'Creaza';

// CreateUser.php

$Unable_to_register_txt = "Unable to register User variable during creation for $username : $User";
$Unable_to_register_3_attempts_txt = "Unable to register User variable during creation for $username failed after 3 attempts: $User";
$Successfully_registered = 'Successfully registered '.$username.' during creation';
$Username_already_exists = 'Numele de utilizator deja exista, te rugam alege alt nume';
$One_user_per_email = 'Un singur utilizator permis pentru adresa ($email).';
$Username_required = 'Nume utilizator necesar, te rugam alege un nume';
$Password_required = 'Parola necesara, te rugam alege o parola';
$Email_Answer_txt = "Salut ".$username.",\n\nBine ai venit la 'LigaSuporterilor'. Iti multumim ca te-ai inregistrat.\n\nTe rugam verifica daca datele tale sunt corecte. Daca e nevoie sa schimbi ceva logheaza-te si schimba profilul.\n\nParola = ".$password."\nEmail = ".$email."\n\nSucces\n ".$adminSignature;
$Email_Subject_txt = "Bine ai venit la LigaSuporterilor";

 /////////////////////////////////////////////////////////
 // Change user variables
 /////////////////////////////////////////////////////////
$My_Profile_for = 'Profilul meu pentru';
$change_icon = 'Schimba';
$Join_Date = 'Alege data';
$Change_Profile = 'Schimba profilul';
$Change_Password = 'Schimba parola';
$Old_Password_txt = 'Old Password';
$Password_Again = 'Inca odata';
$Change_Password_button = 'Schimba parola';

// UserSelectIcon.php

$Click_on_the_Icon = 'Da click pe iconita care vrei sa o folosesti';
$Profile_Icon_Selection = 'Selectare iconita';
$Language = "Language";

 /////////////////////////////////////////////////////////
 // Help file variables
 /////////////////////////////////////////////////////////

$Prediction_League_Help_topic = 'Liga Suporterilor Ajutor';
$Creating_a_new_user = 'Creaza un nou user';

$Creating_a_new_user_txt = '          Ca sa poti participa la Liga Suporterilor trebuie sa creezi un user profile. Acesta se creeaza selectind <b>User nou</b> din coloana din stinga. Verifica daca cookies sunt enabled pentru browserul tau. Daca cookies sunt disabled, va trebui sa te intorci la aceasta pagina.
          <br> <br>
          Alege un User ID pentru a te identifica in Liga Suporterilor. Poate fi orice cuvint atita timp cit nu este luat de un alt user.
          <br> <br>
          Introdu parola (de doua ori). Asa poti fi sigur ca nimeni nu iti va schimba pronosticurile.
          <br> <br>
          <b>Trebuie</b> sa introduci o adresa valida de email pentru a putea sa te inregistrezi in Liga Suporterilor. O vom folosi pentru a-ti trimite parola astfel incit sa nu o uiti.';

$Cookies_txt = 'Cookies';

$Cookies_txt_txt = '          Cookies <b>trebuie</b> trebuie sa fie activate pentru a putea utiliza Liga Suporterilor.
          <br><br>
          Problemele cele mai dese care apar cind nu folosesti cookies cuprind:
          <ul>
          <li>Vezi aceasta pagina cind incerci sa te loghezi.
          <li>Esti intrebat de User cind incerci sa introduci un pronostic.
          <li>Icoana nu apare in stinga ecranului.
          <li>Ai incercat sa creezi un nou user si esti redirectionat catre aceasta pagina. Trebuie sa pui enable la cookies in browserul tau.
          </ul>
          Ca sa pui cookies enable in Internet Explorer 6, urmeaza in Meniu urmatoarea cale Tools->Internet Options. Selecteaza din meniul de sus Privacy. Selecteaza Privacy la Medium sau lower. Apasa OK. Unii useri au avut probleme cu setarea medium. E posibil sa fie necesar sa selectezi lower.<br>
          Ca sa pui cookies enable in Netscape 6, urmeaza in meniu calea Edit->Preferences. Selecteaza Privacy & tab, apoi selecteaza cookies. Selecteaza Enable Cookies pentru originating site only, sau enable pentru toate cookies. Apasa OK.<br>';

$Scoring_txt = 'Punctajul';

$Scoring_txt_txt = '          Punctele sunt acordate dupa cum urmeaza:
          <ul>
          <li>3 puncte pentru scorul corect. Ex: ai pronosticat 3-1 si scorul a fost 3-1.
          <li>1 punct pentru rezultatul corect. Ex: ai pronosticat 1-1, si rezultatul a fost 2-2.
          </ul>
          Diferenta de goluri
          <ul>
          <li>Pentru fiecare gol marcat corect de echipa ta primesti diferenta de goluri. Ex: daca pronosticul tau este de 2 goluri, iar echipa inscrie 2 goluri primesti +2 goluri.
          <li>Pentru fiecare gol incorect inscris de echipa, pierzi diferenta in goluri. Ex: pronostic 3, iar echipa inscrie 2 goluri, primesti -1 gol.
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
