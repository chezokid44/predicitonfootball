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

$Login_txt = 'Prisijungimas';
$Username_txt = 'Vartotojo vardas';
$Password_txt = 'Slaptazodis';
$Logon = 'Pirmyn';
$New_User = 'Naujas vartotojas';
$Forgot_password = 'Pamirsai slaptazodi?';

$Home = 'Namai';

$Menu = 'Meniu';

$Prediction_Table = 'Spejimu lentele';
$Fixtures_Results = 'Spejimai/rezultatai';
$My_Predictions = 'Mano spejimai';
$My_Profile = 'Mano profilis';
$Logout = 'Atsijungti';

$Help = 'Pagalba';
$Email_Us = 'Parasyk mums';

$Admin_Menu = 'Admino meniu';
$Enter_Fixture = 'Ivesti ivyki';
$Enter_Result = 'Ivesti rezultata';
$Show_Users = 'ROdyti vartotojus';
$Recalculate_Standings = 'Perskaiciuoti lentele';
$Email_All_Users  = 'Parasyk visiems ';
$View_Log = 'Perziureti loga';

 /////////////////////////////////////////////////////////
 // Create new user variables
 /////////////////////////////////////////////////////////

$Cookie_read_test_error = 'Negalima perskaityti sausainuku';
$Create_New_User = 'Sukurti nauja vartotoja';
$User_ID_Required = 'Vartotojo ID butinas';
$User_ID_too_long = 'Per ilgas ID. Turi but 32 raidziu ilgio.';
$Passwords_is_required = 'Slaptazodis butinas';
$Passwords_do_not_match = 'Nesutampa slaptazodziai';
$Password_too_long = 'Per ilgas slaptazodis. Gali buti tik 32 raidziu.';
$Email_address_is_required = 'El. pastas butinas.';
$Email_too_long = 'Pastas per ilgas. Gali buti tik 32 simboliai.';
$Email_address_is_not_valid = 'Blogas el. pastas';
$User_Info = 'Vartotojo informacija';
$User_ID = 'Vartotojo ID';
$User_Name = 'Vartotojo vardas';
$Select_user_ID = 'Pasirink vartotojo ID.';
$Password_length = 'Per trumpas vartotojo vardas. Min 5 simboliai.';
$Repeat_password = 'Parkatoti slaptazodi.';
$Email_Address_txt = 'El. pastas';
$Enter_valid_email_address = 'Ivesk tikra el. pasta.';
$Icon = 'Ikona';
$Select_icon = 'Pasirink ikona.';
$Create = 'Sukurti';

// CreateUser.php

$Unable_to_register_txt = "Negalima sukurti vartotjo vardo $username : $User";
$Unable_to_register_3_attempts_txt = "Negalima uzregistruoti vartojo vardo";
$Successfully_registered = 'Sukurta '.$username.' per kurima';
$Username_already_exists = 'Toks vartotojo vardas jau uzregistruotas';
$One_user_per_email = 'Tik vienas meilas galioja per juseri ($email) .';
$Username_required = 'Vartotjo vardas butinas';
$Password_required = 'Slaptazodis butinas';
$Email_Answer_txt = "Labas ".$username.",\n\nsveikas atvykes i ".$PredictionLeagueTitle.". Aciu, kad prisijunge.\n\nPasitink ar viska gerai suvedei.\n\nSlaptazodis = ".$password."\nEl.pastas = ".$email."\n\nSekmes\n ".$adminSignature;
$Email_Subject_txt = "Sveikas atvykes i ".$PredictionLeagueTitle;

 /////////////////////////////////////////////////////////
 // Change user variables
 /////////////////////////////////////////////////////////
$My_Profile_for = 'Mano profilis';
$change_icon = 'pakeisti';
$Join_Date = 'Pasijungimo data';
$Change_Profile = 'Redaguoti profili';
$Change_Password = 'Paskeisi slaptazodi';
$Old_Password_txt = 'Senas slaptazodis';
$Password_Again = 'Pakartoti';
$Change_Password_button = 'Pakeisti slaptazodi';

// UserSelectIcon.php

$Click_on_the_Icon = 'Paspausk ant ikonos, kuria nori naudoti';
$Profile_Icon_Selection = 'Profilio ikona pasirinkta';
$Language = "Kalba";

 /////////////////////////////////////////////////////////
 // Help file variables
 /////////////////////////////////////////////////////////

$Prediction_League_Help_topic = 'Spejimu lygos pagalba';
$Creating_a_new_user = 'Naujo vartotojo kurimas';

$Creating_a_new_user_txt = '          Jei nori dalyvauti spejimu lygoje privalai uzhsiregistruoti vartotojo varda paspausdamas <b>Naujas vartotojas</b> nuoroda is desinio stulpelio.
          <br> <br>
          Pasirinkite savo ID, kad galetumete dalyvauti. Jis gali buti belekoks, svarbu, kad nebutu jau paimtas kito vartotojo.
          <br> <br>
          Iveskite slaptazodi (du kartus). Tai butina, norint dalyvauti spejimu lygoje.
          <br> <br>
          Tu <b>privalai</b> ivesti tikra el. pasta, kad galetum dalyvauti spejimu lygoje. Tai naudoja tam, kad bus atsiustas slaptazodis i meila, kad nepamirstum.';

$Scoring_txt = 'Taskai';

$Scoring_txt_txt = '          Taskus galima gauti uz:
          <ul>
          <li>'.$corrScore.' Teisingai atspeta rezultata.
          <li>'.$corrResult.' Atspeta nugaletoja.
          </ul>
          Ivarciu skirtumas
          <ul>
          <li>Jei atspeji kiek imusa viena ar kita komada tai tu gauni tiek tasku kiek jie imuse.
          <li>Uz kiekviena neatspeta ivarti tu gauni minus tiek ivarciu.
          </ul>';

// Version 1.0 Additions
 /////////////////////////////////////////////////////////
 // Messaging variables
 /////////////////////////////////////////////////////////
$Del = "Trin";
$Delete = "Trinti";
$From = "Nuo";
$To = "Kam";
$Date = "Data";
$Subject = "Subjektas";
$My_Messages = "Mano zinutes";
$My_Messages_For = "Zinute kam";
$My_Predictions_For = "Statymai kam";
$Send_Msg = "Siusti zinute";
$Create_Msg = "Sukurti zinute";
$Send_Msg_Button = "Siusti zinute";
$Message_txt = "Zinute";
$RE = "Ats:";
$REM = "Trin";  // For removing messages
$Remove_All_Deleted = "Ismesti visas istrintas zinutes";
$View_All_Messages = "Perziureti visas zinutes";
$Status_Col = "Statusas";

$Config_League = 'Konfiguruoti lyga';
$Predictions = 'Spejimai';
$Predict= 'Speti';
$Next_Match = "Kitos varzybos";
$Prediction_League_Standings = 'Spejimo lygos lentele';
$No_Matches_Scheduled = 'Nera varzybu';
$New_Message = "Nauja zinute";
$Subject_txt = "Subjektas";

// v1.03 addition
$Months = array('01'=>"Sau",
             '02'=>"Vas",
             '03'=>"Kov",
             '04'=>"Bal",
             '05'=>"Geg",
             '06'=>"Bir",
             '07'=>"Lie",
             '08'=>"Rugp",
             '09'=>"Rugs",
             '10'=>"Spa",
             '11'=>"Lap",
             '12'=>"Gruo");
$Set_Defaults = "Defaultiniai duomenys";

// v1.06 additions
$FixturesResults = "Spejimai/rezultatai";
$Away = "Sveciai"; // Away team title
$P = "Z";  // Games played
$W = "P";  // Games won
$D = "L";  // Games drawn
$L = "Pr";  // Games lost
$F = "In";  // Goals for
$A = "Is";  // Goals against
$GD = "Sk";  // Goals difference
$POS = "Poz";  // Position
$PTS = "Task";  // Points
$Result = "Rezultatas";
$Prediction_Stats = "Spejimu statistika ";
$Download_Dbase = "Padaryti atsargine kopija";
$Lang_Report_Bug = "Pranesti apie bugus";

// v1.07 additions
$Lang_Enable_Auto_Predictions = "Automatinis spsejimas";
$Lang_Enable = "Ijungtas";
$Lang_Enter_Outstanding_Results = "Ivesti sekancius rezultatus";
$Lang_Enter_Multiple_Fixtures = "Ivesti visus rezultatus";

// v1.08 additions
$Predictions_For = "Speti ka";
$Game_Time = "Laiko zadimas";

// v1.09 additions
$New_Message_Subject = "Tu gavai nauja zinute i $PredictionLeagueTitle";
$New_Message_Body = "Tu gavai nauja zinute i $PredictionLeagueTitle. Ja gali perziureti cia http://$PLHome/";
$Games_Menu = "Pasirink rungtynes";
$Upload_Icon = "Ideti ikona";
$Previous = "Atgal";
$Next = "Toliau";
$Upload_Instructions = "Per didelis failas, todel negalima jo ideti tarp ikonu. Failo dydis apribotas iki $MaxIconFileSize Kb";
?>
