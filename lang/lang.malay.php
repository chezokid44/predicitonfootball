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
 $charset = "ISO8859-1";

 /////////////////////////////////////////////////////////
 // Menu variables
 /////////////////////////////////////////////////////////

$Login_txt = 'Masuk';
$Username_txt = 'Ahli';
$Password_txt = 'Kata Laluan';
$Logon = 'Masuk';
$New_User = 'Ahli Baru';
$Forgot_password = 'Lupa Password?';

$Home = 'Home';

$Menu = 'Menu';

$Prediction_Table = 'Jadual Jangkaan';
$Fixtures_Results = 'Keputusan Perlawanan';
$My_Predictions = 'Jangkaan Saya';
$My_Profile = 'Profil Saya';
$Logout = 'Logout';

$Help = 'Panduan';
$Email_Us = 'Hubungi Kami';

$Admin_Menu = 'Menu Admin';
$Enter_Fixture = 'Masukkan Jangkaan';
$Enter_Result = 'Masukkan Keputusan';
$Show_Users = 'Senarai Ahli';
$Recalculate_Standings = 'Kira Semula Kedudukan';
$Email_All_Users  = 'E-Mel Kepada Semua Ahli';
$View_Log = 'Lihat Log';

 /////////////////////////////////////////////////////////
 // Create new user variables
 /////////////////////////////////////////////////////////

$Cookie_read_test_error = 'COOKIE tidak dapat dibaca ketika membuat ahli baru';
$Create_New_User = 'Buatkan Ahli Baru';
$User_ID_Required = 'Masukkan ID ahli anda';
$User_ID_too_long = 'ID ahli terlalu panjang. Hendaklah kurang daripada 32 abjad.';
$Passwords_is_required = 'Masukkan Kata Laluan';
$Passwords_do_not_match = 'Kata Laluan tidak sepadan';
$Password_too_long = 'Kata Laluan terlalu panjang. Hendaklah kurang daripada 32 abjad.';
$Email_address_is_required = 'Masukkan E-Mel';
$Email_too_long = 'E-Mel terlalu panjang. Hendaklah kurang daripada 60 abjad.';
$Email_address_is_not_valid = 'Alamat E-Mel tidak sah.';
$User_Info = 'Maklumat Ahli';
$User_ID = 'ID Ahli';
$User_Name = 'Nama Ahli';
$Select_user_ID = 'Sila Pilih ID Keahlian.';
$Password_length = 'Kata Laluan hendalah tidak kurang daripada 5 abjad.';
$Repeat_password = 'Ulangkan Kata Laluan.';
$Email_Address_txt = 'Alamat E-Mel';
$Enter_valid_email_address = 'Masukkan E-Mel anda yang sah.';
$Icon = 'Ikon';
$Select_icon = 'Pilih Ikon sebagai tanda keahlian anda.';
$Create = 'Buat';

// CreateUser.php

$Unable_to_register_txt = "Tidak dapat mendaftar keahlian ketika proses untuk $username : $User";
$Unable_to_register_3_attempts_txt = "Tidak dapat mendaftar keahlian ketika proses untuk $username setelah 3 kali percubaan gagal: $User";
$Successfully_registered = 'Berjaya mendaftar '.$username.' ketika proses';
$Username_already_exists = 'Nama keahlian telah dipakai, sila pilih Nama lain';
$One_user_per_email = 'Hanya 1 ahli dibenarkan per alamat E-MEL ($email).';
$Username_required = 'Masukkan nama keahlian, sila pilih satu nama';
$Password_required = 'Masukkan Kata Laluan, sila pilih satu Kata Laluan';
$Email_Answer_txt = "Hi ".$username.",\n\nSelamat Datang Ke ".$PredictionLeagueTitle.". Terima Kasih Kerana Sudi Menyertai Kami.\n\nSila Pastikan Maklumat Anda Adalah Betul. Sekiranya anda ingin mengubah maklumat anda sila Masuk.\n\nKata Laluan = ".$password."\nE-Mel = ".$email."\n\nSemoga Berjaya\n ".$adminSignature;
$Email_Subject_txt = "Welcome to the ".$PredictionLeagueTitle;

 /////////////////////////////////////////////////////////
 // Change user variables
 /////////////////////////////////////////////////////////
$My_Profile_for = 'Maklumat Saya Untuk';
$change_icon = 'Tukar';
$Join_Date = 'Tarikh Penyertaan';
$Change_Profile = 'Ubah Profil';
$Change_Password = 'Ubah Kata Laluan';
$Old_Password_txt = 'Kata Laluan Lama';
$Password_Again = 'Sekali Lagi';
$Change_Password_button = 'Ubah Kata Laluan';

// UserSelectIcon.php

$Click_on_the_Icon = 'Klik Pada Ikon Yang Ingin Anda Gunakan';
$Profile_Icon_Selection = 'Pilihan Profil Ikon';
$Language = "Bahasa";

 /////////////////////////////////////////////////////////
 // Help file variables
 /////////////////////////////////////////////////////////

$Prediction_League_Help_topic = 'Panduan Liga Jangkaan';
$Creating_a_new_user = 'Membuat Ahli Baru';

$Creating_a_new_user_txt = '          Untuk menyertai Liga Jangkaan ini anda perlulah membuat satu profil keahlian, dengan hanya mengklik pada link <b>Ahli Baru</b> di sebelah kanan anda. Sila pastikan khidmad COOKIE digunakan oleh BROWSER anda. Sekiranya anda tidak menggunakan COOKIE, anda akan terus dibawa ke halaman ini.
          <br> <br>
          Pilih satu ID KEAHLIAN sebagai tanda diri anda didalam liga ini. Anda boleh pilih nama kesukaan anda asalkan ianya tidak diambil oleh Ahli lain terlebih dahulu.
          <br> <br>
          Masukkan kata laluan (dua kali). Ini adalah untuk memastikan tiada Ahli lain yang boleh mengubah jangkaan anda.
          <br> <br>
          Anda mestilah<b>must</b> memasukkan alamat E-MEL yang sah untuk menyertai LIGA JANGKAAN ini. Ianya digunakan adalah untuk menghantar Kata Laluan sekiranya anda terlupa Kata Laluan anda.';

$Scoring_txt = 'Pemarkahan';

$Scoring_txt_txt = '          Markah diberikan bedasarkan:
          <ul>
          <li>'.$corrScore.' Markah untuk jaringan yang betul. contoh: Anda menjangkakan 3-1 dan keputusan adalah 3-1.
          <li>'.$corrResult.' Markah untuk keputusan yang betul. Anda jangkakan 1-1, dan jaringan adalah 2-2.
          </ul>
          Perbezaan GOL
          <ul>
          <li>Untuk setiap GOL yang betul, anda akan menerima +gol. contoh: Anda jangkakan 2 dan jaringan pasukan adalah 2, anda akan mendapat +2 gol.
          <li>Untuk setiap kesalahan jangkaan gol setiap pasukan, anda akan kehilang perbezaan gol. contoh: Anda jangkakan 3 dan jaringan pasukan tersebut adalah 2, anda mendapat -1 gol.
          </ul>';

// Version 1.0 Additions
 /////////////////////////////////////////////////////////
 // Messaging variables
 /////////////////////////////////////////////////////////
$Del = "Padam";
$Delete = "Padam";
$From = "Daripada";
$To = "Kepada";
$Date = "Tarikh";
$Subject = "Tajuk";
$My_Messages = "Pesanan Untuk Saya";
$My_Messages_For = "Pesanan Saya Untuk";
$My_Predictions_For = "Jangkaan Saya Untuk";
$Send_Msg = "Hantar Pesanan";
$Create_Msg = "Buat Pesanan";
$Send_Msg_Button = "Hantar Pesanan";
$Message_txt = "Pesanan";
$RE = "Balasan:";
$REM = "Padam";  // For removing messages
$Remove_All_Deleted = "Padam kesemua pesanan yang telah dipadam";
$View_All_Messages = "Lihat Kesemua Pesanan";
$Status_Col = "Status";

$Config_League = 'Kongfigurasi Liga';
$Predictions = 'Jangkaan';
$Predict = 'Jangkaan';
$Next_Match = "Perlawanan Seterunya";
$Prediction_League_Standings = 'Kedudukan Liga Jangkaan';
$No_Matches_Scheduled = 'Tiada Perlawanan';
$New_Message = "Pesanan Baru";
$Subject_txt = "Tajuk";

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
