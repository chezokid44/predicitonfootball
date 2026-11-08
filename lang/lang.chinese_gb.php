<?php 
/*********************************************************
 * Author: HE YAN BING(SZARTS) (c) 2003
 * Date  : 2003-06-23
 * File  : lang.chinese_gb.php
 * Desc  : Chinese Simplified language file
 * E-mail: szarts@vip.sina.com
 ********************************************************/

 /////////////////////////////////////////////////////////
 // Character set required
 /////////////////////////////////////////////////////////
 $charset = "gb2312";

 /////////////////////////////////////////////////////////
 // Menu variables
 /////////////////////////////////////////////////////////

$Login_txt = '用户登录';
$Username_txt = '用户名称：';
$Password_txt = '登录密码：';
$Logon = '登录';
$New_User = '用户注册';
$Forgot_password = '忘记密码';

$Home = '网站导航';

$Menu = '网站服务';

$Prediction_Table = '会员排行';
$Fixtures_Results = '查看比分';
$My_Predictions = '预测比赛';
$My_Profile = '用户信息';
$Logout = '退出登录';

$Help = '帮助信息';
$Email_Us = '联系我们';

$Admin_Menu = '管理中心';
$Enter_Fixture = '赛程管理';
$Enter_Result = '比赛结果';
$Show_Users = '会员列表';
$Recalculate_Standings = '计算奖金';
$Email_All_Users  = '邮件列表';
$View_Log = '查看纪录';

 /////////////////////////////////////////////////////////
 // Create new user variables
 /////////////////////////////////////////////////////////

$Cookie_read_test_error = '无法读取新会员的cookie信息';
$Create_New_User = '新用户注册';
$User_ID_Required = '用户名称必填';
$User_ID_too_long = '用户名称太长，必须小于32个字符';
$Passwords_is_required = '登录密码必填';
$Passwords_do_not_match = '密码不匹配';
$Password_too_long = '登录密码太长，必须小于32个字符';
$Email_address_is_required = '邮件地址必填';
$Email_too_long = '邮件地址太长，必须小于32个字符';
$Email_address_is_not_valid = '邮件地址无效';
$User_Info = '用户信息';
$User_ID = '用户名称：';
$User_Name = 'User Name';
$Select_user_ID = '请选择一个用户名称 <font color="red">（必填）</font>';
$Password_length = '登录密码必须大于5位';
$Repeat_password = '登录密码确认';
$Email_Address_txt = '邮件地址：';
$Enter_valid_email_address = '请输入有效的邮件地址';
$Icon = '用户头像：';
$Select_icon = '注册后请在你的用户信息中修改';
$Create = '注册';

// CreateUser.php

$Unable_to_register_txt = "无法注册用户 $username : $User";
$Unable_to_register_3_attempts_txt = "无法注册用户 $User";
$Successfully_registered = '用户 '.$username.' 注册成功！';
$Username_already_exists = '用户名称已经存在，请重新选择一个用户名称';
$One_user_per_email = '邮件地址已经被使用';
$Username_required = '用户名称必须填写';
$Password_required = '用户密码必须填写';
$Email_Answer_txt = "你好：".$username.",\n\n欢迎您加入 ".$PredictionLeagueTitle."。您已经成功注册成为我们的会员，请仔细检查你的注册信息是否正确。如果你需要修改你的用户信息，可以登录到我们的网站，然后在[用户信息]中进行修改。\n\n用户名称：".$username."\n用户密码：".$password."\n邮件地址：".$email."\n\n您现在可以登录我们的网站，并享受我们提供的所有服务了！\n\n ".$adminSignature;
$Email_Subject_txt = "注册信息：".$PredictionLeagueTitle;

 /////////////////////////////////////////////////////////
 // Change user variables
 /////////////////////////////////////////////////////////
$My_Profile_for = '用户信息：';
$change_icon = '修改';
$Join_Date = '注册日期';
$Change_Profile = '修改信息';
$Change_Password = '修改密码';
$Old_Password_txt = 'Old Password';
$Password_Again = '密码确认';
$Change_Password_button = '修改密码';

// UserSelectIcon.php

$Click_on_the_Icon = '请点击头像图片选择你喜欢的用户头像';
$Profile_Icon_Selection = '用户头像选项';
$Language = "粂ē";


 /////////////////////////////////////////////////////////
 // Help file variables
 /////////////////////////////////////////////////////////

$Prediction_League_Help_topic = '帮助信息';
$Creating_a_new_user = '如何注册新用户';

$Creating_a_new_user_txt = '参与竞猜和下注前您必须先注册成为我们的会员，你可以[<b><a href="CreateNewUser.php" blank="_blank">按此</a></b>]注册新用户。<br><br>注意：你的浏览器必须能支持！<br><br>注册过程：<br><br>1。选择一个您喜欢的用户名称，用户名称可以使用中文或英文，但是必须没有被其他用户使用。<br><br>2。输入你的登录密码并进行密码确认。注册成功后，您的密码可以在您的用户信息选项里进行修改。<br><br>3。输入一个有效的邮件地址，注册成功后，系统将会发送注册信息到您的邮件地址。';

$Scoring_txt = '竞猜和下注的说明';

$Scoring_txt_txt = '系统如何计算奖金：
          <ul>
          <li>猜对正确比赛比分得：'.$corrScore.'分<br>例如：你竞猜的比分为3-1，如何比赛最后的结果为3-1，那么你可以获得3分。
          <li>猜对正确比赛结果得：'.$corrResult.'分<br>例如：你竞猜的比分为1-1，如何比赛最后的结果为2-3，那么你可以获得1分。
          </ul>
          进球获得奖金的说明：
          <ul>
          <li>所有猜对比赛比分的会员将可以获得额外的奖金。<br>例如：您竞猜主队的进球为2，如果主队最后的那进球为2，那么你可以获得2分。
		  <li>所有猜错比赛比分得会员将自动被扣除奖金。<br>例如：您竞猜主队的进球为2，如果主队最后的那进球为3，那么您将被扣除1分。</ul>';


// Version 1.0 Additions
 /////////////////////////////////////////////////////////
 // Messaging variables
 /////////////////////////////////////////////////////////
$Del = "删除";
$Delete = "删除";
$From = "发件人";
$To = "收件人";
$Date = "日期";
$Subject = "主题";
$My_Messages = "私人短信";
$My_Messages_For = "私人短信：";
$My_Predictions_For = "预测下注：";
$Send_Msg = "发送短信";
$Create_Msg = "建立短信";
$Send_Msg_Button = "发送短信";
$Message_txt = "短信内容";
$RE = "Re:";
$REM = "Rem";  // For removing messages
$Remove_All_Deleted = "删除所有短信";
$View_All_Messages = "查看所有短信";
$Status_Col = "状态";

$Config_League = '系统设置';
$Predictions = 'Predictions';
$Predict = 'Predict';
$Next_Match = "下一比赛";
$Prediction_League_Standings = 'Prediction League Standings';
$No_Matches_Scheduled = '没有找到匹配数据';
$New_Message = "新短信";
$Subject_txt = "短信主题";

// v1.03 addition
$Months = array('01'=>"1月",
             '02'=>"2月",
             '03'=>"3月",
             '04'=>"4月",
             '05'=>"5月",
             '06'=>"6月",
             '07'=>"7月",
             '08'=>"8月",
             '09'=>"9月",
             '10'=>"10月",
             '11'=>"11月",
             '12'=>"12月");
$Set_Defaults = "默认系统设置变量";

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
