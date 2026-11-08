<?php 
/*********************************************************
 * Author: Tim Chan Yew Kwong (c) 2003
 * Date  : 14.03.2003
 * File  : lang.php
 * Desc  : Indonesia language file
 ********************************************************/

 /////////////////////////////////////////////////////////
 // Character set required
 /////////////////////////////////////////////////////////
 $charset = "ISO8859-1";

 /////////////////////////////////////////////////////////
 // Menu variables
 /////////////////////////////////////////////////////////

$Login_txt = 'Masuk';
$Username_txt = 'Username';
$Password_txt = 'Password';
$Logon = 'Masuk';
$New_User = 'Anggota Baru';
$Forgot_password = 'Lupa password';

$Home = 'Home';

$Menu = 'Menu';

$Prediction_Table = 'Tabel Prediksi';
$Fixtures_Results = 'Hasil/Akhir';
$My_Predictions = 'Prediksi Saya ';
$My_Profile = 'Profil Saya';
$Logout = 'Logout';

$Help = 'Petunjuk';
$Email_Us = 'Hubungi Kami';

$Admin_Menu = 'Menu Admin';
$Enter_Fixture = 'Masukan Prediksi';
$Enter_Result = 'Masukan Hasil';
$Show_Users = 'Daftar Anggota';
$Recalculate_Standings = 'Perhitungan Kembali Posisi';
$Email_All_Users  = 'E-mail kepada semua user ';
$View_Log = 'Lihat Log';

 /////////////////////////////////////////////////////////
 // Create new user variables
 /////////////////////////////////////////////////////////

$Cookie_read_test_error = 'Cookie tidak bisa dibaca saat proses pendaftaran anggota baru';
$Create_New_User = 'Isi Anggota Baru';
$User_ID_Required = 'Masukkan User ID Anda';
$User_ID_too_long = 'User ID terlalu panjang. Harus kurang dari 32 karakter.';
$Passwords_is_required = 'Password harus di isi';
$Passwords_do_not_match = 'Password Tidak Ditemukan';
$Password_too_long = 'Password terlalu panjang. Harus kurang dari 32 karakter.';
$Email_address_is_required = 'e-mail address harus diisi';
$Email_too_long = 'E-mail terlalu panjang. Harus kurang dari 60 karakter.';
$Email_address_is_not_valid = 'E-mail address tidak berlaku';
$User_Info = 'Informasi Anggota';
$User_ID = 'User ID';
$User_Name = 'User Nama';
$Select_user_ID = 'Pilih User ID.';
$Password_length = 'Password minimal 5 karakter.';
$Repeat_password = 'Ulangi Password.';
$Email_Address_txt = 'Alamat E-mail';
$Enter_valid_email_address = 'Masukan e-mail address anda yang masih berlaku.';
$Icon = 'Ikon';
$Select_icon = 'Pilih ikon yang mewakili diri anda.';
$Create = 'Buat';

// CreateUser.php

$Unable_to_register_txt = "Pendaftaran menggunakan user $username : $User";
$Unable_to_register_3_attempts_txt = "Pendaftaran menggunakan user $username tidak dapat di proses 3 sukses: $User";
$Successfully_registered = 'Pendaftaran untuk '.$username.' sukses';
$Username_already_exists = 'Nama User anda sudah terpakai, silahkan pilih nama lainnya';
$One_user_per_email = 'Hanya diijinkan satu user per alamat e-mail ($email).';
$Username_required = 'Dibutuhkan Nama User, silahkan pilih sebuah nama';
$Password_required = 'Dibutuhkan Password, silahkan pilih sebuah password';
$Email_Answer_txt = "Hi".$username.",\n\nSelamat datang di ".$PredictionLeagueTitle.". Terima kasih untuk bergabung dengan kami. Silahkan periksa apakah data yang anda masukan telah benar. Jika anda ingin meng edit data yang telah anda masukan silahkan login kembali dan ganti profil anda.\n\nPassword = ".$password."\nEmail = ".$email."\n\nGood Luck\n ".$adminSignature;
$Email_Subject_txt = "Selamat datang di ".$PredictionLeagueTitle;

 /////////////////////////////////////////////////////////
 // Change user variables
 /////////////////////////////////////////////////////////
$My_Profile_for = 'Informasi Profil Saya';
$change_icon = 'Ganti';
$Join_Date = 'Tanggal Bergabung';
$Change_Profile = 'Ganti Profil';
$Change_Password = 'Ganti Password';
$Old_Password_txt = 'Password yang sekarang';
$Password_Again = 'Sekali Lagi';
$Change_Password_button = 'Ganti Password';

// UserSelectIcon.php

$Click_on_the_Icon = 'Klik pada ikon yang akan anda gunakan';
$Profile_Icon_Selection = 'Pilihan profil ikon';
$Language = "Bahasa";

 /////////////////////////////////////////////////////////
 // Help file variables
 /////////////////////////////////////////////////////////

$Prediction_League_Help_topic = 'Liga Prediksi Petunjuk';
$Creating_a_new_user = 'Daftar anggota baru';

$Creating_a_new_user_txt = ' Untuk berpartisipasi di dalam Liga Prediksi anda harus membuat sebuah profil. Profil ini dibuat dengan memilih link <b>Anggota Baru</b> yang berada di kolom sebelah kanan. Pastikan setting cookies di browser dalam keadaan aktif. Jika cookies tidak aktif, anda akan di teruskan ke halaman ini.     <br><br>Pilih sebuah User ID untuk mengidentifikasi diri anda pada tim prediksi. Anda dapat memilih nama user apa saja sejauh nama tersebut belum digunakan oleh user yang lainnya.<br><br>Masukkan password (dua kali). Ini akan meyakinkan anda bahwa tidak ada orang lain yang akan mengganti prediksi anda <br><br>Anda <b>harus</b> memasukan alamat e-mail anda yang masih berlaku untuk bergabung pada Liga Prediksi. E-mail ini akan dipergunakan jika anda lupa password anda. ';

$Scoring_txt = 'Score';

$Scoring_txt_txt = '          Point akan diberikan sebagai berikut :
          <ul>
          <li>'.$corrScore.' point untuk prediksi score yang benar, contoh : prediksi Anda 3-1 dan hasilnya adalah 3-1
          <li>'.$corrResult.' point untuk prediksi score yang seimbang, contoh prediksi Anda 1-1 dan hasil pertandingan adalah 2-2
          </ul>
          Selisih Goal
          <ul>
          <li>Untuk setiap jumlah gol yang benar, Anda mendapatkan jumlah +Goals, misalnya prediksi Anda 2 goal dan hasil dari tim tersebut 2, maka Anda akan mendapatkan tambahan +2 goals
          <li>Untuk setiap hasil prediksi yang salah, anda akan kehilangan selisih goal, misalnya prediksi anda sebuah tim scorenya 3, tetapi hasilnya 2, anda mendapatkan -1 goal 
          </ul>';

// Version 1.0 Additions
 /////////////////////////////////////////////////////////
 // Messaging variables
 /////////////////////////////////////////////////////////
$Del = "hapus";
$Delete = "Hapus";
$From = "Dari";
$To = "Untuk";
$Date = "Tanggal";
$Subject = "Judul";
$My_Messages = "Pesan Saya";
$My_Messages_For = "Pesan Saya Untuk";
$My_Predictions_For = "Prediksiku";
$Send_Msg = "Kirim Pesan";
$Create_Msg = "Membuat Pesan";
$Send_Msg_Button = "Kirim Pesan";
$Message_txt = "Pesan";
$RE = "Perihal:";
$REM = "Hapus Pesan";  // For removing messages
$Remove_All_Deleted = "Hapus permanent semua pesan";
$View_All_Messages = "Lihat Semua Pesan";
$Status_Col = "Status";

$Config_League = 'Konfigurasi Liga';
$Predictions = 'Prediksi';
$Predict = 'Prediksi';
$Next_Match = "Pertandingan Berikutnya";
$Prediction_League_Standings = 'Prediksi Hasil Pertandingan';
$No_Matches_Scheduled = 'Tidak ditemukan jadwal pertandinga';
$New_Message = "Pesan baru";
$Subject_txt = "Judul";

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
