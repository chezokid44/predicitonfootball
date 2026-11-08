<?php 
/*********************************************************
 * Author: Stanley@UCBB (c) 2003
 * Date  : 20.11.2003
 * File  : lang.chinese_big5.php
 * Desc  : Chinese (Traditional) language file
 ********************************************************/

 /////////////////////////////////////////////////////////
 // Character set required
 /////////////////////////////////////////////////////////
 $charset = "big5";

 /////////////////////////////////////////////////////////
 // Menu variables
 /////////////////////////////////////////////////////////

$Login_txt = '登入';
$Username_txt = '用戶名稱';
$Password_txt = '密碼';
$Logon = '登入';
$New_User = '新用戶';
$Forgot_password = '忘記密碼';

$Home = '首頁';

$Menu = '目錄';

$Prediction_Table = '玩家積分表';
$Fixtures_Results = '賽程/賽果';
$My_Predictions = '預測賽果';
$My_Profile = '帳戶資料';
$Logout = '登出';

$Help = '幫助';
$Email_Us = '聯絡我們';

$Admin_Menu = '管理目錄';
$Enter_Fixture = '輸入賽程';
$Enter_Result = '輸入賽果';
$Show_Users = '用戶列表';
$Recalculate_Standings = '更新排名';
$Email_All_Users  = '電郵用戶';
$View_Log = '查看記錄';

 /////////////////////////////////////////////////////////
 // Create new user variables
 /////////////////////////////////////////////////////////

$Cookie_read_test_error = '新用戶未能成功通過Cookie測試';
$Create_New_User = '新增用戶';
$User_ID_Required = '必須填寫登入名稱';
$User_ID_too_long = '登入名稱太長，不能超過三十二個字符';
$Passwords_is_required = '必須填寫密碼';
$Passwords_do_not_match = '密碼不相同';
$Password_too_long = '密碼太長，不能超過三十二個字符';
$Email_address_is_required = '必須填寫電郵地址';
$Email_too_long = '電郵地址太長，不能超過六十個字符';
$Email_address_is_not_valid = '電郵地址無效';
$User_Info = '用戶資料';
$User_ID = '登入名稱';
$User_Name = '用戶稱號';
$Select_user_ID = '選擇一個登入名稱';
$Password_length = '密碼最少為五個字符';
$Repeat_password = '確認密碼';
$Email_Address_txt = '電郵地址';
$Enter_valid_email_address = '請輸入有效的郵件地址';
$Icon = '用戶圖像';
$Select_icon = '請選擇代表你的圖像';
$Create = '註冊';

// CreateUser.php

$Unable_to_register_txt = '無法註冊用戶$username：'.$User;
$Unable_to_register_3_attempts_txt = "註冊失敗，三次嘗試均未能成功註冊$username：$User";
$Successfully_registered = $username.' 註冊成功 ';
$Username_already_exists = '用戶稱號已被選用，請選擇其他用戶稱號';
$One_user_per_email = '每個電郵地址($email)只能登記一名用戶';
$Username_required = '必須填寫用戶稱號，請選擇用戶稱號';
$Password_required = '必須填寫密碼，請選擇密碼';
$Email_Answer_txt = $username.'你好,\n\n歡迎您加入 '.$PredictionLeagueTitle.'。\n\n請確定你的資料正確。如果你需要更改任何資料，請於登入後到〔帳戶資料〕中進行修改。\n\n密碼 = '.$password.'\n電郵 = '.$email.'\n\n祝你好運\n '.$adminSignature;
$Email_Subject_txt = '歡迎來到 '.$PredictionLeagueTitle;

 /////////////////////////////////////////////////////////
 // Change user variables
 /////////////////////////////////////////////////////////
$My_Profile_for = '帳戶資料：';
$change_icon = '修改';
$Join_Date = '註冊日期';
$Change_Profile = '更改帳戶資料';
$Change_Password = '更改密碼';
$Old_Password_txt = '舊密碼';
$Password_Again = '確認密碼';
$Change_Password_button = '更改密碼';

// UserSelectIcon.php

$Click_on_the_Icon = '請點擊頭像圖片選擇你喜歡的用戶頭像';
$Profile_Icon_Selection = '用戶頭像選項';
$Language = '語言';

 /////////////////////////////////////////////////////////
 // Help file variables
 /////////////////////////////////////////////////////////

$Prediction_League_Help_topic = '幫助說明';
$Creating_a_new_user = '如何註冊新用戶';

$Creating_a_new_user_txt = '參與競猜和下注前您必須先註冊成為我們的會員，你可以[<b><a href=\"CreateNewUser.php\" blank=\"_blank\">按此</a></b>]註冊新用戶。
          <br> <br>
          選擇一個登入名稱作為你在本估波遊戲中的身份。你可隨意選擇任何你喜愛的名稱(中文或英文)，只要該名稱未備其他人使用。
          <br> <br>
          輸入一個密碼(兩次)，這是用來確保其他人不能更改你的個人資料。
          <br> <br>
          你<b>必須</b>輸入一個有效的電郵地址方可參加本估波遊戲。若你忘記你的密碼，該電郵地址將會用作重發密碼之用。';

$Scoring_txt = '積分計算方法';

$Scoring_txt_txt = '積分以下列規則計算﹕
          <ul>
          <li>'.$corrScore.' 正確比數得分。  例﹕你的競猜為3-1而最終賽果為3-1。
          <li>'.$corrResult.' 正確賽果得分。  例﹕你的競猜為1-1而最終賽果為2-2。
          </ul>
          得失球
          <ul>
          <li>每個猜對的球隊入球數﹐你可獲得該入球數之得球。例﹕你競猜2而該球隊入球數為2﹐你的得失球會加2。
          <li>每個猜錯的球隊入球數﹐你會增加該入球數差別之失球。例﹕你競猜3而該球隊入球數為2﹐你的得失球會減1。
          </ul>';

// Version 1.0 Additions
 /////////////////////////////////////////////////////////
 // Messaging variables
 /////////////////////////////////////////////////////////
$Del = '刪除';
$Delete = '刪除';
$From = '寄件者';
$To = '收件者';
$Date = '日期';
$Subject = '主旨';
$My_Messages = '我的訊息';
$My_Messages_For = '我的訊息：';
$My_Predictions_For = '預測賽果：';
$Send_Msg = '發送訊息';
$Create_Msg = '新增訊息';
$Send_Msg_Button = '發送訊息';
$Message_txt = '訊息';
$RE = '回覆﹕';
$REM = '清空';  // For removing messages
$Remove_All_Deleted = '清空所有已刪除訊息';
$View_All_Messages = '查看所有訊息';
$Status_Col = '狀態';

$Config_League = '設定聯賽';
$Predictions = '競猜之賽果';
$Predict= '競猜';
$Next_Match = '下場賽事';
$Prediction_League_Standings = '賽果競猜聯賽積分表';
$No_Matches_Scheduled = '未有賽事安排';
$New_Message = '新訊息';
$Subject_txt = '主旨';

// v1.03 addition
$Months = array('01'=>'一月',
             '02'=>'二月',
             '03'=>'三月',
             '04'=>'四月',
             '05'=>'五月',
             '06'=>'六月',
             '07'=>'七月',
             '08'=>'八月',
             '09'=>'九月',
             '10'=>'十月',
             '11'=>'十一月',
             '12'=>'十二月');
$Set_Defaults = '預設設定值';

// v1.06 additions
$FixturesResults = '賽程/賽果';
$Away = '客隊'; // Away team title
$P = '賽';  // Games played
$W = 'W勝';  // Games won
$D = '和';  // Games drawn
$L = '負';  // Games lost
$F = '得球';  // Goals for
$A = '失球';  // Goals against
$GD = '得失球差';  // Goals difference
$POS = '位置';  // Position
$PTS = '得分';  // Points
$Result = '賽果';
$Prediction_Stats = '競猜統計資料 ';
$Download_Dbase = '備份資料庫';
$Lang_Report_Bug = '報告程式錯誤';

// v1.07 additions
$Lang_Enable_Auto_Predictions = '自動預測賽果';
$Lang_Enable = '許可';
$Lang_Enter_Outstanding_Results = '輸入球賽結果';
$Lang_Enter_Multiple_Fixtures = '輸入多項賽程';

// v1.08 additions
$Predictions_For = '預測';
$Game_Time = '開賽時間';

// v1.09 additions
$New_Message_Subject = '你收到一個來自 '.$PredictionLeagueTitle.' 的訊息';
$New_Message_Body = '你收到一個來自 '.$PredictionLeagueTitle.' 的訊息. 你可以按此查看 http://'.$PLHome.'/';
$Games_Menu = '選擇遊戲';
$Upload_Icon = '上載圖示';
$Previous = '上一個';
$Next = '下一個';
$Upload_Instructions = '請確保圖像大小為合適之尺寸，上限為 '.$MaxIconFileSize.' Kb';
?>
