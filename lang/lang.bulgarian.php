<?php 
/*********************************************************
 * Author: Tuomo Aura (c) 2002
 * Date  : 24.08.2003
 * File  : lang.php
 * Desc  : Bulgarian language file
 ********************************************************/

 /////////////////////////////////////////////////////////
 // Character set required
 /////////////////////////////////////////////////////////
 $charset = "Windows-1251";

 /////////////////////////////////////////////////////////
 // Menu variables
 /////////////////////////////////////////////////////////

$Login_txt = 'Вход';
$Username_txt = 'Потребител';
$Password_txt = 'Парола';
$Logon = 'Влез';
$New_User = 'Нов потребител';
$Forgot_password = 'Забравена парола';

$Home = 'Начало';

$Menu = 'Меню';

$Prediction_Table = 'Таблица с прогнози';
$Fixtures_Results = 'Програма/Резултати';
$My_Predictions = 'Моите прогнози';
$My_Profile = 'Моят профил';
$Logout = 'Изход';

$Help = 'Помощ';
$Email_Us = 'Прати ни Email';

$Admin_Menu = 'Админ меню';
$Enter_Fixture = 'Въведи мач';
$Enter_Result = 'Въведи резултат';
$Show_Users = 'Покажи потребителите';
$Recalculate_Standings = 'Преизчисли класирането';
$Email_All_Users  = 'Прати Email на всички';
$View_Log = 'Виж регистъра';

 /////////////////////////////////////////////////////////
 // Create new user variables
 /////////////////////////////////////////////////////////

$Cookie_read_test_error = 'Не може да прочете пробните cookie, когато създава нов потребител';
$Create_New_User = 'Създаване на нов потребител';
$User_ID_Required = 'Изисква ID на потребителя';
$User_ID_too_long = 'ID на потребителя е много дълго. Трябва да бъде до 32 символа.';
$Passwords_is_required = 'Изисква се парола';
$Passwords_do_not_match = 'Паролите не съвпадат';
$Password_too_long = 'Паролата е твърде дълга. Трябва да бъде до 32 символа.';
$Email_address_is_required = 'Изисква се Email адрес';
$Email_too_long = 'Email е твърде дълъг. Трябва да бъде до 60 символа.';
$Email_address_is_not_valid = 'Email адресът не е валиден';
$User_Info = 'Инфо за потребителя';
$User_ID = 'ID на потребителя';
$User_Name = 'Име на потребителя';
$Select_user_ID = 'Избери ID на потребителя.';
$Password_length = 'Паролата трябва да бъде минимум 5 символа.';
$Repeat_password = 'Повтори паролата.';
$Email_Address_txt = 'Email адрес';
$Enter_valid_email_address = 'Въведи свой валиден email адрес.';
$Icon = 'Емблема';
$Select_icon = 'Избери емблема, която да те представлява.';
$Create = 'Създай';

// CreateUser.php

$Unable_to_register_txt = "Не може да регистрира данните за потребителя при създаването на $username : $User";
$Unable_to_register_3_attempts_txt = "Не може да регистрира данните за потребителя при създаването на $username . Направи 3 неуспешни опита: $User";
$Successfully_registered = 'Успешно регистрира $username по време на създаването на потребителя';
$Username_already_exists = 'Потребителят вече съществува, моля изберете друго име';
$One_user_per_email = 'Само един потребител за email ($email) адрес е позволен.';
$Username_required = 'Изисква се потребителско име, моля изберете си име';
$Password_required = 'Изисква се парола, моля изберете парола';
$Email_Answer_txt = "Здравейте ".$username.",\n\nДобре дошъл в ".$PredictionLeagueTitle.". Благодарим ви, че се присъединихте към нас.\n\nМоля проверете дали данните ви са верни. Ако трябва да промените нещо, моля влезте и редактирайте своя профил.\n\nПарола = ".$password."\nEmail = ".$email."\n\nУспех\n ".$adminSignature;
$Email_Subject_txt = "Добре дошъл в ".$PredictionLeagueTitle;

 /////////////////////////////////////////////////////////
 // Change user variables
 /////////////////////////////////////////////////////////
$My_Profile_for = 'Моят профил за';
$change_icon = 'промяна';
$Join_Date = 'Дата на включване';
$Change_Profile = 'Промени профила';
$Change_Password = 'Промени паролата';
$Old_Password_txt = 'Старата парола';
$Password_Again = 'Отново';
$Change_Password_button = 'Промени паролата';

// UserSelectIcon.php

$Click_on_the_Icon = 'Натиснете върху емблемата, която искате да ползвате';
$Profile_Icon_Selection = 'Избор на емблема за профила';
$Language = "Език";

 /////////////////////////////////////////////////////////
 // Help file variables
 /////////////////////////////////////////////////////////

$Prediction_League_Help_topic = 'Помощ за играта';
$Creating_a_new_user = 'Създаване на нов потребител';

$Creating_a_new_user_txt = '          За да можете да участвате в играта за прогнозиране на резултати трябва да създадете свой потребителски профил. Той се създава като изберете връзката <b>Нов потребител</b> от менюто в дясната колонка.
          <br> <br>
          Изберете си потребителско ID, за да се идентифицирате с него в играта. Това може да бъде всичко, което пожелаете, стига да не е вече заето от друг потребител.
          <br> <br>
          Въведете паролата (два пъти). Това гарантира, че никой друг освен вас няма да може да променя прогнозите ви.
          <br> <br>
          Вие <b>трябва</b> да въведете свой валиден email адрес, за да се включите в играта. Той ще бъде използван, за да ви изпратим паролата ако я забравите.';

$Scoring_txt = 'Точки';

$Scoring_txt_txt = '          Points are awared as follows:
          <ul>
          <li>'.$corrScore.' точки се дават за точно познат резултат, напр. вие прогнозирате 3-1 и резултатът е 3-1.
          <li>'.$corrResult.' точки се дават за познат изход от мача, напр. вие прогнозирате 1-1, а резултатът е 2-2.
          </ul>
          Голова разлика
          <ul>
          <li>За всеки точно прогнозиран брой отбелязани голове от даден отбор, вие получавате в актива си толкова голове, напр. вие прогнозирате отбелязването на 2 гола и отборът вкарва 2 гола, вие получавате +2 гола.
          <li>За всеки неточно прогнозиран брой отбелязани голове от даден отбор, вие губите разликата в головете, напр. вие прогнозирате 3 гола, а отборът вкарва 2 гола, вие получавате -1 гол.
          <li>Това правило важи за головете отбелязани и от двата отбора - домакин и гост.
          </ul>';

// Version 1.0 Additions
 /////////////////////////////////////////////////////////
 // Messaging variables
 /////////////////////////////////////////////////////////
$Del = "Трий";
$Delete = "Изтрий";
$From = "От";
$To = "До";
$Date = "Дата";
$Subject = "Тема";
$My_Messages = "Моите съобщения";
$My_Messages_For = "Моите съобщения за";
$My_Predictions_For = "Моите прогнози за";
$Send_Msg = "Изпрати съобщение";
$Create_Msg = "Създай съобщение";
$Send_Msg_Button = "Изпрати съобщение";
$Message_txt = "Съобщение";
$RE = "Отг.:";
$REM = "Прем.";  // For removing messages
$Remove_All_Deleted = "Премахни всички изтрити съобщения";
$View_All_Messages = "Виж всички съобщения";
$Status_Col = "Статус";

$Config_League = 'Конфигуриране на лигата';
$Predictions = 'Прогнози';
$Predict= 'Прогнозирай';
$Next_Match = "Следващ мач";
$Prediction_League_Standings = 'Класиране в лигата';
$No_Matches_Scheduled = 'Няма мачове за прогнозиране';
$New_Message = "Ново съобщение";
$Subject_txt = "Тема";

// v1.03 addition
$Months = array('01'=>"Яну",
             '02'=>"Фев",
             '03'=>"Мар",
             '04'=>"Апр",
             '05'=>"Май",
             '06'=>"Юни",
             '07'=>"Юли",
             '08'=>"Авг",
             '09'=>"Сеп",
             '10'=>"Окт",
             '11'=>"Ное",
             '12'=>"Дек");
$Set_Defaults = "Настройки по подразбиране";

// v1.06 additions
$FixturesResults = "Програма/Резултати";
$Away = "Гост"; // Away team title
$P = "М";  // Games played
$W = "П";  // Games won
$D = "Р";  // Games drawn
$L = "З";  // Games lost
$F = "за";  // Goals for
$A = "с/у";  // Goals against
$GD = "Г.Р.";  // Goals difference
$POS = "Поз.";  // Position
$PTS = "Т.";  // Points
$Result = "Резултат";
$Prediction_Stats = "Статистика на прогнозите ";
$Download_Dbase = "Архив на базата";
$Lang_Report_Bug = "Съобщи за грешка";

// v1.07 additions
$Lang_Enable_Auto_Predictions = "Автоматични прогнози";
$Lang_Enable = "Разреши";
$Lang_Enter_Outstanding_Results = "Въведи останали резултати";
$Lang_Enter_Multiple_Fixtures = "Въведи няколко мача";

// v1.08 additions
$Predictions_For = "Прогнози за";
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
