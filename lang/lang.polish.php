<?php 
/*********************************************************
 * Author: Kioiko + Guzik (c) 2003
 * Date  : 05.09.2003
 * File  : lang.php
 * Desc  : Polish language file
 ********************************************************/

 /////////////////////////////////////////////////////////
 // Character set required
 /////////////////////////////////////////////////////////
 $charset = "iso8859-2";

 /////////////////////////////////////////////////////////
 // Menu variables
 /////////////////////////////////////////////////////////

$Login_txt = 'Logowanie';
$Username_txt = 'U¿ytkownik';
$Password_txt = 'Has³o';
$Logon = 'Loguj';
$New_User = 'Nowy u¿ytkownik';
$Forgot_password = 'Zapomnia³em has³a';

$Home = 'U siebie';

$Menu = 'Menu';

$Prediction_Table = 'Tabela graczy';
$Fixtures_Results = 'Terminarz/Rezultaty';
$My_Predictions = 'Moje typowanie';
$My_Profile = 'Mój profil';
$Logout = 'Wyloguj';

$Help = 'Pomoc';
$Email_Us = 'Email do admina';

$Admin_Menu = 'Admin Menu';
$Enter_Fixture = 'Wpisz przysz³e mecze';
$Enter_Result = 'Wpisz rezultaty';
$Show_Users = 'Zobacz u¿ytkowników';
$Recalculate_Standings = 'Policz punkty';
$Email_All_Users  = 'Wy¶lij mail do wszystkich';
$View_Log = 'Zobacz logowania';

 /////////////////////////////////////////////////////////
 // Create new user variables
 /////////////////////////////////////////////////////////

$Cookie_read_test_error = 'Nie mo¿na zapisaæ cookies. Patrz Pomoc';
$Create_New_User = 'Nowy u¿ytkownik';
$User_ID_Required = 'U¿ytkownik';
$User_ID_too_long = 'Nazwa u¿ytkownika jest za d³uga. Max 32 znaki.';
$Passwords_is_required = 'Has³o jest wymagane';
$Passwords_do_not_match = 'Z³e has³o';
$Password_too_long = 'Has³o jest za d³ugie. Max 32 znaki.';
$Email_address_is_required = 'Adres mailu jest wymagany.';
$Email_too_long = 'Email jest za d³ugi. Mniej ni¿ 60 znaków.';
$Email_address_is_not_valid = 'Email jest nie poprawny';
$User_Info = 'Info o u¿ytkowniku';
$User_ID = 'U¿ytkownik';
$User_Name = 'Gracz';
$Select_user_ID = 'Wpisz swój pseudonim.';
$Password_length = 'Has³o musi byæ d³u¿sze ni¿ 5 znaków.';
$Repeat_password = 'Powtórz has³o.';
$Email_Address_txt = 'Email ';
$Enter_valid_email_address = 'Wpisz prawdziwy mail.';
$Icon = 'Ikonka';
$Select_icon = 'Wybierz ikonke.';
$Create = 'Utwórz';

// CreateUser.php

$Unable_to_register_txt = "Niemo¿na zarejestrowaæ u¿ytkownika $username : $User";
$Unable_to_register_3_attempts_txt = "Nie mo¿na stworzyæ nowego ID dla $username , nieudane 3 próby: $User";
$Successfully_registered = 'Rejestracja zakoñczona '.$username.' during creation';
$Username_already_exists = 'U¿ytkownik o takim pseudonimie ju¿ istnieje';
$One_user_per_email = 'Kto¶ ju¿ poda³ ten adres: ($email) .';
$Username_required = 'Nazwa u¿ytkownika wymagana, proszê uzupe³niæ.';
$Password_required = 'Has³o jest wymagane, proszê uzupe³niæ';
$Email_Answer_txt = "Czesc ".$username.",\n\nwitamy w ".$PredictionLeagueTitle.". Dziekujemy ze dolaczyles do nas.\n\nSprawdz poprawnosc danych. Jesli chcesz cos zmienic zaloguj sie i dokonaj korekty.\n\nHas³o = ".$password."\nEmail = ".$email."\n\nPowodzenia !!!\n ".$adminSignature;
$Email_Subject_txt = "Witamy w ".$PredictionLeagueTitle;

 /////////////////////////////////////////////////////////
 // Change user variables
 /////////////////////////////////////////////////////////
$My_Profile_for = 'Profil dla';
$change_icon = 'Zmieñ';
$Join_Date = 'Do³±czy³';
$Change_Profile = 'Zmieñ profil';
$Change_Password = 'Zmieñ has³o';
$Old_Password_txt = 'Stare has³o';
$Password_Again = 'Ponownie';
$Change_Password_button = 'Zmieñ has³o';

// UserSelectIcon.php

$Click_on_the_Icon = 'Kliknij na ikonke aby zmieniæ';
$Profile_Icon_Selection = 'Ikonki';
$Language = "Jêzyk";

 /////////////////////////////////////////////////////////
 // Help file variables
 /////////////////////////////////////////////////////////

$Prediction_League_Help_topic = 'Pomoc';
$Creating_a_new_user = 'Rejestracja';

$Creating_a_new_user_txt = '          Je¶li chcesz zabawiæ siê w typowanie meczy, musisz siê zarejestrowaæ. Mo¿esz to zrobiæ klikaj±c <b>Nowy u¿ytkownik</b> w menu po lewej. Sprawd¼ najpierw czy twoja przegl±darka obs³uguje "cookies" (ciasteczka :P). Je¶li nie bêdzie obs³ugiwaæ zostaniesz przeniesiony na t± strone.
          <br> <br>
          <b>Musisz</b> podaæ prawdziwy adres email, na wypadek jakby¶ zapomnia³ has³a.';

$Cookies_txt = 'Cookies';

$Cookies_txt_txt = '          Cookies <b>musz±</b> byæ obs³ugiwane przez twoj± przegl±darkê !!!
          <br><br>
          Je¶li twoja przegl±darka stron WWW nie obs³uguje cookies, wtedy:
          <ul>
          <li>Zobaczysz t± strone próbuj±c siê zalogowaæ.
          <li>Bêdziesz pytany o zalogowanie siê ponownie przy próbie typowania wyniku.
          <li>Próbuj±c siê zarejestrowaæ zostaniesz przeniesiony tu.
          </ul>
          Sposób w³±czenia cookies w:<BR>
<B>Internet Explorer 6</B> kliknij menu Narzêdzia->Opcje internetowe... Wybierz zak³adke Prywatno¶æ. Ustaw wska¼nik na ¦redni lub Niski. Kliknij OK.<br>
<b>Netscape 6</B> kliknij menu Edycja->Preferencje. Wybierz zak³adke Prywatno¶æ i Zabezpieczenia, i kliknij na Ciasteczka. Zaznacz Akceptuj ciasteczka. Kliknij OK.<br>';

$Scoring_txt = 'Punktacja';

$Scoring_txt_txt = '          Punkty zdobywa siê za:
          <ul>
          <li>3 punkty za trafiony wynik. np. typujesz 3-1 a wynik meczu jest taki sam 3-1.
          <li>1 punkt za trafiony rezultat. np. typujesz 1-1 a wynik meczu jest 2-2.
          </ul>
          Bramki
          <ul>
          <li>Je¶li zgadniesz ilo¶æ bramek w meczu, dostajesz tyle samo goli. np. typujesz 2 i zespó³ zdobywa 2 bramki, otrzymujesz +2 gole.
          <li>Je¶li nie zgadniesz ilo¶æ bramek w meczu, odejmuje Ci sie ró¿nica bramek. np typujesz 3 a zespó³ zdobywa 2, tracisz -1 gol.
          </ul>';

// Version 1.0 Additions
 /////////////////////////////////////////////////////////
 // Messaging variables
 /////////////////////////////////////////////////////////
$Del = "Usuñ";
$Delete = "Usuñ";
$From = "Od";
$To = "Do";
$Date = "Data";
$Subject = "Temat";
$My_Messages = "Wiadomo¶æ";
$My_Messages_For = "Wiadomo¶æ od";
$My_Predictions_For = "Typowanie od";
$Send_Msg = "Wy¶lij wiadomo¶æ";
$Create_Msg = "Utwórz wiadomo¶æ";
$Send_Msg_Button = "Wy¶lij wiadomo¶æ";
$Message_txt = "wiadomo¶æ";
$RE = "Odp:";
$REM = "Prze";  // For removing messages
$Remove_All_Deleted = "Przenie¶ wszystkie usuniête wiadomo¶ci";
$View_All_Messages = "Zobacz wszystkie wiadomo¶ci";
$Status_Col = "Status";

$Config_League = 'Ustawienia Ligi';
$Predictions = 'Typowanie';
$Predict= 'Typuj';
$Next_Match = "Nastêpny mecz";
$Prediction_League_Standings = 'Tabela graczy';
$No_Matches_Scheduled = 'Brak meczy';
$New_Message = "Nowa wiadomo¶æ";
$Subject_txt = "Temat";

// v1.03 addition
$Months = array('01'=>"Sty",
             '02'=>"Lut",
             '03'=>"Mar",
             '04'=>"Kwi",
             '05'=>"Maj",
             '06'=>"Cze",
             '07'=>"Lip",
             '08'=>"Sie",
             '09'=>"Wrz",
             '10'=>"Paz",
             '11'=>"Lis",
             '12'=>"Gru");
$Set_Defaults = "Domy¶lne zasady";

// v1.06 additions
$FixturesResults = "Kalendarz/Rezultaty";
$Away = "Wyjazd"; // Away team title
$P = "M";  // Games played
$W = "W";  // Games won
$D = "R";  // Games drawn
$L = "P";  // Games lost
$F = "F";  // Goals for
$A = "A";  // Goals against
$GD = "RB";  // Goals difference
$POS = "Poz";  // Position
$PTS = "Pkt";  // Points
$Result = "Rezultat";
$Prediction_Stats = "Statystyki typowania ";
$Download_Dbase = "Kopia bazy danych";
$Lang_Report_Bug = "Info o b³êdach";

// v1.07 additions
$Lang_Enable_Auto_Predictions = "Automatyczne typowanie";
$Lang_Enable = "W³±czone";
$Lang_Enter_Outstanding_Results = "Zobacz wybrane wyniki";
$Lang_Enter_Multiple_Fixtures = "Zobacz wszystkie wyniki";

// v1.08 additions
$Predictions_For = "Typowanie dla";
$Game_Time = "Aktualny czas";

// v1.09 additions
$New_Message_Subject = "Otrzyma³e¶ now± wiadomo¶æ na $PredictionLeagueTitle";
$New_Message_Subject = "Masz now± wiadomo¶c na $PredictionLeagueTitle. Mo¿esz obejrzeæ j± tutaj  http://$PLHome/";
$Games_Menu = "Wybierz grê";
$Upload_Icon = "Prze¶lij ikonkê";
$Previous = "Poprz";
$Next = "Nast";
$Upload_Instructions = "Wielko¶c obrazka powinna byæ odpowiednia do wy¶wietlenia na stronie www. Rozmiar pliku jest ograniczony do $MaxIconFileSize Kb";
?>
