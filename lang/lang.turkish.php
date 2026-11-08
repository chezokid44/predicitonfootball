<?php 
/*********************************************************
 * Author: Tolga Vural (c) 2003
 * Date  : 25.03.2003
 * File  : lang.php
 * Desc  : Turkish language file
 ********************************************************/

 /////////////////////////////////////////////////////////
 // Character set required
 /////////////////////////////////////////////////////////
 $charset = "ISO_8859-9";

 /////////////////////////////////////////////////////////
 // Menu variables
 /////////////////////////////////////////////////////////

$Login_txt = 'Giriþ';
$Username_txt = 'Kullanýcý Adý';
$Password_txt = 'Þifre';
$Logon = 'Baðlý';
$New_User = 'Yeni Kullanýcý';
$Forgot_password = 'Þifremi unuttum';

$Home = 'Ana Sayfa';

$Menu = 'Menü';

$Prediction_Table = 'Tahmin Tablosu';
$Fixtures_Results = 'Fikstür/Sonuçlar';
$My_Predictions = 'Tahminlerim';
$My_Profile = 'Profilim';
$Logout = 'Çýkýþ';

$Help = 'Yardým';
$Email_Us = 'Bize Yazýn';

$Admin_Menu = 'Admin Menü';
$Enter_Fixture = 'Fikstür girin';
$Enter_Result = 'Sonuç girin';
$Show_Users = 'Kullanýcýlarý göster';
$Recalculate_Standings = 'Sýralamalarý hesapla';
$Email_All_Users  = 'Tüm kullanýcýlara email';
$View_Log = 'Loglarý aç';

 /////////////////////////////////////////////////////////
 // Create new user variables
 /////////////////////////////////////////////////////////

$Cookie_read_test_error = 'Yeni kullanýcý yaratýlýrken test çerezi okunamadý';
$Create_New_User = 'Yeni Kullanýcý Yarat';
$User_ID_Required = 'Kullanýcý adý gereklidir';
$User_ID_too_long = 'Kullanýcý adý çok uzun. 32 karakterden az olmalýdýr.';
$Passwords_is_required = 'Þifre gereklidir';
$Passwords_do_not_match = 'Þifreler uyuþmuyor';
$Password_too_long = 'Þifre çok uzun. 32 karakterden az olmalýdýr.';
$Email_address_is_required = 'Email adresi gereklidir';
$Email_too_long = 'Email çok uzun. 60 karakterden az olmalýdýr.';
$Email_address_is_not_valid = 'Email adresi geçerli deðil';
$User_Info = 'Kullanýcý Bilgisi';
$User_ID = 'Kullanýcý adý';
$User_Name = 'User Name';
$Select_user_ID = 'Kullanýcý adý seçiniz';
$Password_length = 'Þifre en az 5 karakter olmalýdýr.';
$Repeat_password = 'Þifreyi Tekrarlayýnýz';
$Email_Address_txt = 'Email Adresi';
$Enter_valid_email_address = 'Geçerli Email adresinizi giriniz.';
$Icon = 'Ýkon';
$Select_icon = 'Kullanýcýnýzý belirtmek için istediðiniz ikonu seçiniz';
$Create = 'Yarat';

// CreateUser.php

$Unable_to_register_txt = "Kullanýcý yaratýlamamýþtýr.$username : $User";
$Unable_to_register_3_attempts_txt = "^3 deneme sonucunda kullanýcý $username yaratýlamamýþtýr.: $User";
$Successfully_registered = $username.' baþarý ile yaratýlmýþtýr.';
$Username_already_exists = 'Kullanýcý adý mevcut, Lütfen baþka bir ad seçiniz.';
$One_user_per_email = 'Her email($email) için sadece bir kullanýcý kayýt olabilir.';
$Username_required = 'Kullanýcý adý gereklidir, Lütfen bir ad seçiniz';
$Password_required = 'Þifre gereklidir, Lütfen bir þifre seçiniz';
$Email_Answer_txt = "Merhaba ".$username.",\n\nHoþgeldiniz ".$PredictionLeagueTitle.". Bize katýldýðýnýz için Teþekkürler.\n\nLütfen detaylarýnýzýn doðruluðunu kontrol ediniz. Herhangi bir deðiþiklik yapmak isterseniz lütfen giriþ yapýp profilinizi düzenleyiniz.\n\nÞifre = ".$password."\nEmail = ".$email."\n\nÝyi Þanslar\n ".$adminSignature;
$Email_Subject_txt = " $PredictionLeagueTitle Hoþgeldiniz!";

 /////////////////////////////////////////////////////////
 // Change user variables
 /////////////////////////////////////////////////////////
$My_Profile_for = 'Profilim';
$change_icon = 'Deðiþtir';
$Join_Date = 'Kayýt Tarihi';
$Change_Profile = 'Profil deðiþtir';
$Change_Password = 'Þifre deðiþtir';
$Old_Password_txt = 'Old Password';
$Password_Again = 'Tekrar';
$Change_Password_button = 'Þifre deðiþtir';

// UserSelectIcon.php

$Click_on_the_Icon = 'Kullanmak istediðiniz ikonu týklayýnýz';
$Profile_Icon_Selection = 'Profil Ýkon Seçimi';
$Language = "Language";


 /////////////////////////////////////////////////////////
 // Help file variables
 /////////////////////////////////////////////////////////

$Prediction_League_Help_topic = 'Tahmin Ligi Yardým';
$Creating_a_new_user = 'Yeni Kullanýcý Yaratmak';

$Creating_a_new_user_txt = '          Tahmin ligine katýlabilmek için bir kullanýcý profili yaratmanýz gereklidir. Bu iþlemi Sol kolondaki <b>Yeni Kullanýcý</b> linkini kullanarak yapabilirsiniz. Lütfen çerezlerin onaylý olduðundan emin olunuz. Eðer çerezler onaylanmamýþsa bu sayfaya yönlendirileceksiniz.
          <br> <br>
          Tahmin liginde kendinizi tanýtmak için bir Kullanýcý adý seçiniz. Bu ad baþka bir kullanýcý tarafýndan daha önce alýnmadýðý sürece hoþunuza giden herhangi birþey olabilir.
          <br> <br>
          Þifre girin (iki kez). Bu baþka bir kimsenin tahminlerinizi deðiþtirmemesi için gereklidir.
          <br> <br>
          Tahmin ligine katýlabilmeniz için geçerli bir email adresi vermeniz <b>zorunludur</b>. Bu email þifrenizi unutmanýz durumunda size tekrar gönderebilmemiz için kullanýlacaktýr.';

$Scoring_txt = 'Puanlar';

$Scoring_txt_txt = '          Puanlandýrma þu þekildedir:
          <ul>
          <li>Doðru skor için 3 Puan. Örn. Tahmininiz 3-1 ve sonuç 3-1.
          <li>Doðru tahmin için 1 Puan. Örn. Tahmininiz 1-1 ve sonuç 2-2.
          </ul>
          Averaj
          <ul>
          <li>Takýmlarýn atacaðý gol sayýsýný doðru tahmin ettiðinizde, o gol sayýsý +gol olarak yazýlýr. Örn. Tahmininiz bir takým için 2 gol ve o takým 2 gol attý ise, +2 gol yazýlýr.
          <li>Takýmlarýn atacaðý gol sayýsýný yanlýþ tahmin etmeniz durumunda, gol farký -gol olarak yazýlýr. Örn. Tahmininiz bir takým için 3 gol ve o takým 2 gol attý ise, -1 gol yazýlýr.
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
