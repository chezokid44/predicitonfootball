<?php 
/*********************************************************
 * Author: Peter Gyarmati (c) 2004
 * Date  : 06.02.2004
 * File  : lang.php
 * Desc  : Hungarian language file
 * Mail  : gyap@freestart.hu
 ********************************************************/

 /////////////////////////////////////////////////////////
 // Character set required
 /////////////////////////////////////////////////////////
 $charset = "iso8859-1";

 /////////////////////////////////////////////////////////
 // Menu variables
 /////////////////////////////////////////////////////////

$Login_txt = 'Login';
$Username_txt = 'Felhasználói név';
$Password_txt = 'Jelszó';
$Logon = 'Logon';
$New_User = 'Új felhasználó';
$Forgot_password = 'Elfelejtettem a jelszót';

$Home = 'Home';

$Menu = 'Menü';

$Prediction_Table = 'Bajnokság állása';
$Fixtures_Results = 'Idõpontok/Eredmények';
$My_Predictions = 'Tippjeim';
$My_Profile = 'Profilom';
$Logout = 'Logout';

$Help = 'Segítség';
$Email_Us = 'Email küldés';

$Admin_Menu = 'Admin Menü';
$Enter_Fixture = 'Tipp bevitel';
$Enter_Result = 'Eredmény bevitel';
$Show_Users = 'Mutasd a felhasználókat';
$Recalculate_Standings = 'Állás újraszámítása';
$Email_All_Users  = 'Email Mindenkinek ';
$View_Log = 'Log megtekintése';

 /////////////////////////////////////////////////////////
 // Create new user variables
 /////////////////////////////////////////////////////////

$Cookie_read_test_error = 'Nem olvasható a teszt COOKIE!';
$Create_New_User = 'Új felhasználó készítése';
$User_ID_Required = 'Felhasználói azonosító szükséges!';
$User_ID_too_long = 'Felhasnálói azonosító túl hosszú (Max. 32 karakter).';
$Passwords_is_required = 'Jelszavak szükségesek';
$Passwords_do_not_match = 'Nem megfelelõ jelszó!';
$Password_too_long = 'Jelszó túl hosszú (Max. 32 karakter)!';
$Email_address_is_required = 'Email címe szükséges!';
$Email_too_long = 'Email cím túl hosszú (Max. 60 karakter).'
$Email_address_is_not_valid = 'Email címe nem érvényes!';
$User_Info = 'Felhasználói Info';
$User_ID = 'Felhasználó azonosító';
$Select_user_ID = 'Válassz egy felhasználói azonosítót!';
$Password_length = 'Jelszó legalább 5 karakter hosszú legyen!';
$Repeat_password = 'Jelszó megismétlése.';
$Email_Address_txt = 'Email Cím';
$Enter_valid_email_address = 'Add meg az érvényes emailedet.';
$Icon = 'Ikon';
$Select_icon = 'Válassz hozzád illõ ikont!';
$Create = 'Elkészít';

// CreateUser.php

$Unable_to_register_txt = "Regisztráció sikertelen $username : $User";
$Unable_to_register_3_attempts_txt = "Regisztráció sikertelen $username 3 próbálkozás után: $User";
$Successfully_registered = "Sikeres regisztráció $username ";
$Username_already_exists = 'A név már létezik, válassz másikat!';
$One_user_per_email = 'Csak 1 felhasználó per email ($email) cím lehetséges.';
$Username_required = 'Felhasználói név szükséges!';
$Password_required = 'Jelszó szükséges!';
$Email_Answer_txt = "Hello ".$username.",\n\nÜdvözöllek".$PredictionLeagueTitle.". Köszönjük, hogy minket választottál.\n\nEllenõrizd az adataidat!. \n\nJelszó = ".$password."\nEmail = ".$email."\n\nSok sikert!\n ".$adminSignature;
$Email_Subject_txt = "Üdvözöllek a $PredictionLeagueTitle";

 /////////////////////////////////////////////////////////
 // Change user variables
 /////////////////////////////////////////////////////////
$My_Profile_for = 'A profilom';
$change_icon = 'Változtat';
$Join_Date = 'Csatlakozás idõpontja';
$Change_Profile = 'Profil változtatása';
$Change_Password = 'Jelszó változtatása';
$Password_Again = 'Újra';
$Change_Password_button = 'Jelszó változtatás';

// UserSelectIcon.php

$Click_on_the_Icon = 'Változtasd meg az ikonodat!';
$Profile_Icon_Selection = 'Profil Ikon Választás';
$Language = "Nyelv";

 /////////////////////////////////////////////////////////
 // Help file variables
 /////////////////////////////////////////////////////////

$Prediction_League_Help_topic = 'Segítség';
$Creating_a_new_user = 'Új felhasználó készítése';

$Creating_a_new_user_txt = '          Ahhoz, hogy részt vehess a játékban, szükséges egy felhasználót regisztrálnod. Ennek elkészítéséhez válaszd <b>Új felhasználó</b> linket a bal oldali oszlopból. Kérlek nézd meg, hogy a COOKIE-k engedélyezve vannak az Internet Explorerben vagy más böngészoben. Ha nem, akkor a rendszer átirányít erre az oldalra.
          <br> <br>
          Válassz egy felhasználói nevet, ami azonosít Téged ebben a játékban. Ez bármi lehet, amit szeretnél, csak még ne használja más is ezt a nevet.
          <br> <br>
          Add meg a jelszót (kétszer). Ez azért szükséges, hogy senki más ne változtathassa meg a tippjeidet.
          <br> <br>
          Neked <b>meg kell adnod</b> egy érvényes e-mail címet, hogy csatlakozhass a játékhoz. Ez azért fontos, hogy e-mailben el tudjuk küldeni a jelszavad és adataid, hogy soha sem felejtsd el.';

$Scoring_txt = 'Pontozás';

$Scoring_txt_txt = '          A pontozási rendszer a következo:
          <ul>
          <li>3 Pont jár a korrekt (számszeru) góleredmény eltalálásáért (teli találat). Pl. a tipped 3-1 és az eredmény 3-1.
          <li>1 Pont jár a korrekt eredmény eltalálásáért. Pl. a tipped 1-1, és az eredmény 2-2 vagy a tipped 2-1, és az eredmény 1-0.
          </ul>
          Gól különbség
          <ul>
          <li>Minden egyes csapat gólszámért, amit eltaláltál, megkapod a gólók számát +gól. Pl. Tippelsz 2 gólt az egyik csapatnak és az 2 szerez, akkor kapsz +2 gólt.
          <li>Minden egyes csapat gólszámért, amit rosszul találtál el, elveszíted a különbséget gólokban. Pl. Tippelsz 3 gólt az egyik csapatnak és az 2 gólt szerez, akkor kapsz -1 gólt.
          </ul>';

// Version 1.0 Additions
 /////////////////////////////////////////////////////////
 // Messaging variables
 /////////////////////////////////////////////////////////
$Del = "Töröl";
$Delete = "Töröl";
$From = "-tól";
$To = "-nak";
$Date = "Dátum";
$Subject = "Tárgy";
$My_Messages = "Üzeneteim";
$My_Messages_For = "Üzeneteim másnak";
$My_Predictions_For = "Tippjeim másnak";
$Send_Msg = "Üzenet küldése";
$Create_Msg = "Üzenet készítése";
$Send_Msg_Button = "Send message";
$Message_txt = "Üzenet";
$RE = "Re:";
$REM = "Eltávolít";  // For removing messages
$Remove_All_Deleted = "Eltávolítani az összes törölt üzenetet";
$View_All_Messages = "Mutasd az összes üzenetet";
$Status_Col = "Státusz";

$Config_League = 'Liga konfigurálás';
$Predictions = 'Tippek';
$Predict= 'Tipp';
$Next_Match = "Következõ meccs";
$Prediction_League_Standings = 'Prediction League bajnokság állása';
$No_Matches_Scheduled = 'Nincs meccs tervezve';
$New_Message = "Új üzenet";
$Subject_txt = "tárgy";

// v1.03 addition
$Months = array('01'=>"Jan",
             '02'=>"Feb",
             '03'=>"Már",
             '04'=>"Ápr",
             '05'=>"Máj",
             '06'=>"Jún",
             '07'=>"Júl",
             '08'=>"Aug",
             '09'=>"Szept",
             '10'=>"Okt",
             '11'=>"Nov",
             '12'=>"Dec");
$Set_Defaults = "Alap konfigurációs érték";

// v1.06 additions
$FixturesResults = "Idõpontok/Eredmények";
$Away = "Vendég"; // Away team title
$P = "J";  // Games played
$W = "N";  // Games won
$D = "D";  // Games drawn
$L = "V";  // Games lost
$F = "F";  // Goals for
$A = "A";  // Goals against
$GD = "GK";  // Goals difference
$POS = "Pos";  // Position
$PTS = "Pnt";  // Points
$Result = "Eredmény";
$Prediction_Stats = "Tipp Statisztika ";
$Download_Dbase = "Adatbázis mentése";
$Lang_Report_Bug = "Hiba (Bug) jelentés";

// v1.07 additions
$Lang_Enable_Auto_Predictions = "Automatikus tippek";
$Lang_Enable = "Elérhetõ";
$Lang_Enter_Outstanding_Results = "Elmaradt eredmények bevitele";
$Lang_Enter_Multiple_Fixtures = "Több idõpont bevitele";

// v1.08 additions
$Predictions_For = "Tipp a";
$Game_Time = "Játék Idõ";

// v1.09 additions
$New_Message_Subject = "Elfogadtál egy üzenetet a $PredictionLeagueTitle -tól.";
$New_Message_Body = "Új üzeneted van a $PredictionLeagueTitle -ban. Megtekintheted az üzeneted itt: http://$PLHome/";
$Games_Menu = "Válassz meccset";
$Upload_Icon = "Upload Ikon";
$Previous = "Elõzõ";
$Next = "következõ";
$Upload_Instructions = "Kérem, hogy a kép méretei megfelelõek legyenek a megjelenítéshez! A Te kép fájl méreted maximum: $MaxIconFileSize Kb";

// V1.11 additions
$League_Statistics = "Statisztikák";
$My_Missing_Predictions = 'A hiányzó tippjeim';

?>
