<?php
/*********************************************************
 * Author: Juergen Rudolph & Chan YK (c) 2003
 * Date  : 14.03.2003
 * File  : lang.php
 * Desc  : Deutsch language file
 ********************************************************/

 /////////////////////////////////////////////////////////
 // Character set required
 /////////////////////////////////////////////////////////
 $charset = "iso8859-1";

 /////////////////////////////////////////////////////////
 // Menu variables
 /////////////////////////////////////////////////////////

$Login_txt = 'Einloggen';
$Username_txt = 'Username';
$Password_txt = 'Passwort';
$Logon = 'Login';
$New_User = 'Neuer User';
$Forgot_password = 'Passwort vergessen';

$Home = 'Home';

$Menu = 'Menü';

$Prediction_Table = 'Tabelle';
$Fixtures_Results = 'Spiele/Ergebnisse';
$My_Predictions = "Meine Tipp's";
$My_Profile = 'Mein Profil';
$Logout = 'Ausloggen';

$Help = 'Hilfe';
$Email_Us = 'E-Mail';

$Admin_Menu = 'Admin-Menu';
$Enter_Fixture = 'Paarung eingeben';
$Enter_Result = 'Ergebnisse eingeben';
$Show_Users = 'User eingeben';
$Recalculate_Standings = 'Tabelle errechnen';
$Email_All_Users  = 'E-Mail an User ';
$View_Log = 'Log ansehen';

 /////////////////////////////////////////////////////////
 // Create new user variables
 /////////////////////////////////////////////////////////

$Cookie_read_test_error = 'Nicht in der Lage, Test-Cookies zu lesen beim Kreieren eines neuen Users';
$Create_New_User = 'Neuer User kreieren';
$User_ID_Required = 'User-Kennwort notwendig';
$User_ID_too_long = 'User-Kennwort ist zu lang. Bitte weniger als 32 Zeichen verwenden.';
$Passwords_is_required = 'Passwort ist notwendig';
$Passwords_do_not_match = 'Passwort ist inkorrekt';
$Password_too_long = 'Passwort ist zu lang. Es muss weniger als 32 Zeichen lang sein.';
$Email_address_is_required = 'Bitte E-Mail-Adresse angeben';
$Email_too_long = 'E-mail-Adresse ist zu lang. Bitte weniger als 60 Zeichen verwenden.';
$Email_address_is_not_valid = 'E-mail-Adresse ist inkorrekt.';
$User_Info = 'User-Info';
$User_ID = 'User-Kennwort';
$User_Name = 'Username';
$Select_user_ID = 'Bitte wählen Sie ein User-Kennwort';
$Password_length = 'Passwort muss mindestens 5 Zeichen lang sein.';
$Repeat_password = 'Bitte Passwort wiederholen.';
$Email_Address_txt = 'E-Mail-Adresse';
$Enter_valid_email_address = 'Bitte geben Sie Ihre gültige E-mail-Adresse an.';
$Icon = 'Symbol';
$Select_icon = 'Bitte wählen Sie ein Symbol.';
$Create = 'Kreieren';

// CreateUser.php

$Unable_to_register_txt = "Nicht in der Lage, den User zu registrieren während der Kreierung von $username : $User";
$Unable_to_register_3_attempts_txt = "Nicht in der Lage, den User zu registrieren während der Kreierung des $username nach 3 Fehlversuchen: $User";
$Successfully_registered = 'Registrierung von '.$username.' war erfolgreich';
$Username_already_exists = 'Der Username existiert bereits, bitte wählen Sie einen anderen Namen';
$One_user_per_email = 'Nur ein User per E-mail-Adresse ($email) ist erlaubt.';
$Username_required = 'Username nötig, bitte wählen Sie einen Namen';
$Password_required = 'Passwort nötig, bitte wählen Sie ein Passwort';
$Email_Answer_txt = "Hallo ".$username.",\n\nWillkommen… ".$PredictionLeagueTitle.". Vielen Dank für Ihre Mitgliedschaft. Bitte prüfen Sie, ob alle Details korrekt sind. Falls Sie etwas ändern wollen, loggen Sie sich bitte ein und verändern Sie Ihr Profil.\n\nPassword = ".$password."\nEmail = ".$email."\n\nViel Glück\n ".$adminSignature;
$Email_Subject_txt = "Willkommen… ".$PredictionLeagueTitle;

 /////////////////////////////////////////////////////////
 // Change user variables
 /////////////////////////////////////////////////////////
$My_Profile_for = 'Mein Profil für';
$change_icon = 'ändern';
$Join_Date = 'Mitgliedschaftsdatum';
$Change_Profile = 'Profil ändern';
$Change_Password = 'Passwort ändern';
$Old_Password_txt = 'Derzeitiges Passwort';
$Password_Again = 'Nochmal';
$Change_Password_button = 'Passwort ändern';

// UserSelectIcon.php

$Click_on_the_Icon = 'Klicken Sie auf das Symbol, das Sie verwenden möchten';
$Profile_Icon_Selection = 'Profil Symbol Auswahl ';
$Language = "Sprache";

 /////////////////////////////////////////////////////////
 // Help file variables
 /////////////////////////////////////////////////////////

$Prediction_League_Help_topic = 'Vorhersage Liga Hilfe';
$Creating_a_new_user = 'Kreieren eines neuen Users';

$Creating_a_new_user_txt = '         Um in der Vorhersage-Liga teilnehmen zu können, müssen Sie ein Userprofil kreieren. Sie können ein Userprofil kreieren, indem Sie auf <b>Neuer User</b> in der rechten Spalte klicken. Bitte haben Sie Cookies eingeschaltet. Falls Sie keine Cookies erlauben, werden Sie wieder auf diese Seite gelangen.

          <br> <br>
          Wählen Sie einen Usernamen, um sich in der Vorhersage-Liga zu identifizieren. Sie haben freie Wahl, aber der Name darf noch nicht von einem anderen Mitglied verwendet werden.
          <br> <br>
         Geben Sie Ihr Passwort ein (zweimal). Dies geschieht um sicherzugehen, dass niemand Ihre Vorhersagen verändern kann.
          <br> <br>
         Sie <b>müssen</b> eine gültige E-mail-Adresse eingeben, um Mitglied der Vorhersage-Liga werden zu können. Sie bekommen ein Passwort zugesandt für den Fall, dass Sie es vergessen.';

$Scoring_txt = 'Tore schießen';

$Scoring_txt_txt = '          Punkte werden wie folgt vergeben:
          <ul>
          <li>'.$corrScore.'  Punkte für ein völlig korrektes Ergebnis, d.h. Sie sagen 3-1 voraus, und das Ergebnis ist 3-1.
          <li>'.$corrResult.' Punkt für eine korrekte Tendenz. Z.B.: Sie sagen 1-1 voraus, und das Ergebnis ist 2-2.
          </ul>
         Toredifferenz
          <ul>
          <li>Für alle korrekten Tore einer Mannschaft bekommen Sie die Anzahl an +-Toren, z.B. Sie sagen 2 voraus und Ihr Team schießt 2 Tore, Sie bekommen +2 Tore.
          <li>Für jede inkorrekte Anzahl von Toren, die Sie per Mannschaft vorhersagen, verlieren Sie die Tordifferenz. Z.B. Sie sagen 3 Tore voraus, und das Team schießt 2 Tore, Sie haben -1 Tor.
          </ul>';

// Version 1.0 Additions
 /////////////////////////////////////////////////////////
 // Messaging variables
 /////////////////////////////////////////////////////////
$Del = "Löschen";
$Delete = "Löschen";
$From = "Von";
$To = "An";
$Date = "Datum";
$Subject = "Betreff";
$My_Messages = "Meine Nachrichten";
$My_Messages_For = "Meine Nachrichten für";
$My_Predictions_For = "Meine Vorhersagen für";
$Send_Msg = "Nachricht senden";
$Create_Msg = "Neue Nachricht";
$Send_Msg_Button = "Nachricht senden";
$Message_txt = "Nachricht";
$RE = "Betreff:";
$REM = "Löschen";  // For removing messages
$Remove_All_Deleted = "Alle gelöschten Nachrichten entfernen";
$View_All_Messages = "Alle Nachrichten";
$Status_Col = "Status";

$Config_League = 'Konfiguration';
$Predictions = 'Vorhersagen';
$Predict = 'vorhersagen';
$Next_Match = "Nächstes Spiel";
$Prediction_League_Standings = 'Tabelle';
$No_Matches_Scheduled = 'Keine Spiele geplant';
$New_Message = "Neue Nachricht";
$Subject_txt = "Betreff";

// v1.03 addition
$Months = array('01'=>"Jan",
             '02'=>"Feb",
             '03'=>"Mär",
             '04'=>"Apr",
             '05'=>"Mai",
             '06'=>"Jun",
             '07'=>"Jul",
             '08'=>"Aug",
             '09'=>"Sep",
             '10'=>"Okt",
             '11'=>"Nov",
             '12'=>"Dez");
$Set_Defaults = "Standard Konfigurationswerte";

// v1.06 additions
$FixturesResults = "Spiele/Ergebnisse";
$Away = "Auswärts"; // Away team title
$P = "SP";  // Games played
$W = "S";  // Games won
$D = "U";  // Games drawn
$L = "N";  // Games lost
$F = "+T";  // Goals for
$A = "-T";  // Goals against
$GD = "TD";  // Goals difference
$POS = "Pos";  // Position
$PTS = "Pkt";  // Points
$Result = "Ergebnis";
$Prediction_Stats = "Vorhersage Statistiken ";
$Download_Dbase = "Datenbank Backup";
$Lang_Report_Bug = "Fehler melden";

// v1.07 additions
$Lang_Enable_Auto_Predictions = "Automatische Vorhersagen";
$Lang_Enable = "Aktivieren";
$Lang_Enter_Outstanding_Results = "Ausstehende Ergebnisse";
$Lang_Enter_Multiple_Fixtures = "Mehrere Spiele";

// v1.08 additions
$Predictions_For = "Vorhersagen für";
$Game_Time = "Spielzeit";

// v1.09 additions
$New_Message_Subject = "$PredictionLeagueTitle: Sie haben eine neue Nachricht erhalten";
$New_Message_Body = "Sie haben eine neue Nachricht bei $PredictionLeagueTitle erhalten. Sie können die Nachricht auf $baseDirName einsehen.";
$Games_Menu = "Spiel auswählen";
$Upload_Icon = "Symbol hochladen";
$Previous = "zurück";
$Next = "vor";
$Upload_Instructions = "Bitte halten Sie die Größe des Bildes unter der maximalen Größe von $MaxIconFileSize Kb, um das Bild korrekt anzuzeigen.";

// V1.11 additions
$League_Statistics = "Statistiken";
$My_Missing_Predictions = 'Meine fehlenden Vorhersagen';
$Division = "Liga";
$Txt_No_Messages = "Keine Nachrichten";
$Txt_Icon_Change = "Um das Symbol zu ändern klicken Sie bitte auf das Symbol im linken Bereich.";
$Txt_Bonus_Points = "Bonuspunkte";
$Txt_Change_Icon = "Symbol ändern";
$Txt_User_Type = "Usertyp";
$Txt_Last_Login = "Letzter Login";
$Txt_Last_Ip = "Letzte IP Adresse";
$Txt_All_Ip = "Alle IP Adressen";
$Txt_User_Created = "User kreiert";

?>