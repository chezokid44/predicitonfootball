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

$Login_txt = 'Logio i mewn';
$Username_txt = 'Enw defnyddiwr';
$Password_txt = 'Cyfrinair';
$Logon = 'Logio ymlaen';
$New_User = 'Defnyddiwr newydd';
$Forgot_password = 'Wedi anghofio fy nghyfrinair';

$Home = 'Cartref';

$Menu = 'Dewislen';

$Prediction_Table = 'Tabl Dyfalu';
$Fixtures_Results = 'Gemau / Canlyniadau';
$My_Predictions = 'Yr hyn rwy\'n ei ragweld';
$My_Profile = 'Fy mhroffil';
$Logout = 'Logio allan';

$Help = 'Help';
$Email_Us = 'Ebostiwch ni';

$Admin_Menu = 'Dewislen Weinyddol';
$Enter_Fixture = 'Nodwch y gêm';
$Enter_Result = 'Nodwch y canlyniad';
$Show_Users = 'Dangoswch defnyddwyr';
$Recalculate_Standings = 'Ailgyfrif y safleoedd';
$Email_All_Users  = 'Ebostio holl ddefnyddwyr ';
$View_Log = 'Edrych ar y Log';

 /////////////////////////////////////////////////////////
 // Create new user variables
 /////////////////////////////////////////////////////////

$Cookie_read_test_error = 'Methu â darllen y \'cookie\' prawf wrth greu defnyddiwr newydd';
$Create_New_User = 'Creu defnyddiwr newydd';
$User_ID_Required = 'Angen ID defnyddiwr';
$User_ID_too_long = 'ID Defnyddiwr yn rhy hir.  Rhaid iddo fod yn llai na 32 o lythrennau.';
$Passwords_is_required = 'Mae angen Cyfrinair arnoch';
$Passwords_do_not_match = 'Nid yw eich cyfrinair yn gweddu';
$Password_too_long = 'Mae eich cyfrinair yn rhy hir.  Rhaid iddo fod yn llai na 32 o lythrennau.';
$Email_address_is_required = 'Mae angen cyfeiriad Ebost';
$Email_too_long = 'Ebost yn rhy hir.  Rhaid iddo fod yn llai na 60 o lythrennau.';
$Email_address_is_not_valid = 'Nid yw eich cyfeiriad Ebost yn ddilys';
$User_Info = 'Gwybodaeth Defnyddiwr';
$User_ID = 'ID Defnyddiwr';
$User_Name = 'Enw defnyddiwr';
$Select_user_ID = 'Dewiswch ID defnyddiwr';
$Password_length = 'Bydd yn rhaid i\'r cyfrinair fod o leiaf 5 lythyren o hyd.';
$Repeat_password = 'Ailnodwch eich cyfrinair.';
$Email_Address_txt = 'Cyfeiriad Ebost';
$Enter_valid_email_address = 'Nodwch eich cyfeiriad ebost dilys.';
$Icon = 'Icon';
$Select_icon = 'Dewiswch yr Icon fydd yn eich cynrychioli fel defnyddiwr.';
$Create = 'Creu';

// CreateUser.php

$Unable_to_register_txt = "Methu â chofrestru Newidyn Defnyddiwr ar gyfer $username : $User";
$Unable_to_register_3_attempts_txt = "Methu â chofrestru Newidyn Defnyddiwr wrth geisio creu $username Wedi methu ar ôl rhoi 3 cynnig arni : $User";
$Successfully_registered = 'Cofrestrwyd yn llwyddiannus '.$username.' wrth gael ei greu';
$Username_already_exists = 'Y mae\'r enw defnyddiwr yma\'n bod eisoes, a fyddech cystal â dewis un arall';
$One_user_per_email = 'Caniateir un defnyddiwr yn unig ($email) ar gyfer un cyfeiriad ebost.';
$Username_required = 'Mae angen enw defnyddiwr arnoch, a fyddech cystal â dewis enw';
$Password_required = 'Mae angen cyfrinair arnoch, a fyddech cystal â dewis cyfrinair';
$Email_Answer_txt = "Helo ".$username.",\n\nCroeso i ".$PredictionLeagueTitle.". Diolch am ymuno â ni.\n\nA fyddech cystal â sicrhau bod eich manylion yn gywir. Os bydd angen i chi newid unrhyw beth, logiwch i mewn a diwygio eich proffil.\n\nCyfrinair = ".$password."\nEbost = ".$email."\n\nPob lwc\n ".$adminSignature;
$Email_Subject_txt = "Croeso i ".$PredictionLeagueTitle;

 /////////////////////////////////////////////////////////
 // Change user variables
 /////////////////////////////////////////////////////////
$My_Profile_for = 'My Mhroffil ar gyfer';
$change_icon = 'newid';
$Join_Date = 'Dyddiad ymuno';
$Change_Profile = 'Newid proffil';
$Change_Password = 'Newid cyfrinair';
$Old_Password_txt = 'Hen gyfrinair';
$Password_Again = 'Eto';
$Change_Password_button = 'Newid cyfrinair';

// UserSelectIcon.php

$Click_on_the_Icon = 'Cliciwch ar yr Icon yr hoffech chi ei ddefnyddio';
$Profile_Icon_Selection = 'Proffil Dewis Icon';
$Language = "Iaith";

 /////////////////////////////////////////////////////////
 // Help file variables
 /////////////////////////////////////////////////////////

$Prediction_League_Help_topic = 'Help Cynghrair Dyfalu';
$Creating_a_new_user = 'Creu defnyddiwr newydd';

$Creating_a_new_user_txt = '          Er mwyn cymryd rhan yn y Cynghrair Dyfalu bydd angen i chi greu proffil defnyddiwr.  Caiff hyn ei greu gan ddewis y ddolen gyswllt <b>Defnyddiwr newydd</b> yn y golofn ar y dde.
          <br> <br>
          Dewiswch ID Defnyddiwr er mwyn i chi fedru gweld eich hun yn y Cynghrair Dyfalu. Gall hwn fod yn unrhyw beth yr hoffech chi ei ddefnyddio cyn belled nad oes defnyddiwr yn ei ddefnyddio. 
          <br> <br>
          Nodwch eich cyfrinair (ddwy waith). Y mae hyn i sicrhau na all unrhyw un arall newid yr hyn rydych chi\'n dyfalu y bydd y canlyniadau.
          <br> <br>
          <b>Rhaid i chi</b> nodi cyfeiriad ebost dilys er mwyn ymuno â\'r Cynghrair Dyfalu. Defnyddir yr ebost yma i anfon eich cyfrinair atoch petaech chi\'n ei anghofio.';

$Scoring_txt = 'Sgorio';

$Scoring_txt_txt = '          Rhoddir pwyntiau fel a ganlyn:
          <ul>
          <li>'.$corrScore.' Pwyntiau am sgôr cywir. h.y. rydych chi\'n dyfalu y bydd y canlyniad yn 3-1 ac y mae\'r canlyniad yn 3-1.
          <li>'.$corrResult.' Pwynt am ganlyniad cywir. Rydych chi\'n rhagweld 1-1, ac y mae\'r sgôr yn 2-2.
          </ul>
          Gwahaniaeth gôl
          <ul>
          <li>Ar gyfer y nifer cywir o goliau fesul tîm rydych chi\'n eu sgorio, rydych chi\'n derbyn y nifer hynny o +goliau . e.e. os bu i chi\'n dyfalu y caiff 2 gôl ei sgorio ac y mae\'r tim yn sgorio 2 gôl, rydych felly\'n cael +2 gôl.
          <li>Ar gyfer y nifer anghywir o goliau rydych chi\'n dyfalu fesul tîm,  rydych chi\'n colli\'r gwahaniaeth yn nifer y goliau e.e. os bu i chi\'n dyfalu y caiff 3 gôl eu sgorio ac y mae\'r tîm yn sgorio 2, rydych chi\'n cael -1 gol.
          </ul>';

// Version 1.0 Additions
 /////////////////////////////////////////////////////////
 // Messaging variables
 /////////////////////////////////////////////////////////
$Del = "Dileu";
$Delete = "Dileu";
$From = "O";
$To = "I";
$Date = "Dyddiad";
$Subject = "Pwnc";
$My_Messages = "Fy negeseuon";
$My_Messages_For = "Fy negeseuon ar gyfer";
$My_Predictions_For = "Yr hyn rwy'n dyfalu sy'n mynd i ddigwydd ar gyfer";
$Send_Msg = "Anfon neges";
$Create_Msg = "Creu neges";
$Send_Msg_Button = "Anfon neges";
$Message_txt = "Neges";
$RE = "Yngln â:";
$REM = "Gwaredu";  // For removing messages
$Remove_All_Deleted = "Gwaredu holl negeseuon a ddilëwyd";
$View_All_Messages = "Edrych ar yr holl negeseuon";
$Status_Col = "Statws";

$Config_League = 'Ffurfio\'r Cynghrair';
$Predictions = 'Yr hyn sy\'n cael ei ddyfalu';
$Predict= 'Dyfalu';
$Next_Match = "Gêm nesaf ";
$Prediction_League_Standings = 'Sefyllfa o fewn y Cynghrair Dyfalu';
$No_Matches_Scheduled = 'Sefyllfa o fewn y Cynghrair Dyfalu';
$New_Message = "Neges newydd";
$Subject_txt = "Pwnc";

// v1.03 addition
$Months = array('01'=>"Ionawr",
             '02'=>"Chwefror",
             '03'=>"Mawrth",
             '04'=>"Ebrill",
             '05'=>"Mai",
             '06'=>"Mehefin",
             '07'=>"Gorfennaf",
             '08'=>"Awst",
             '09'=>"Medi",
             '10'=>"Hydref",
             '11'=>"Tachwedd",
             '12'=>"Rhagfyr");
$Set_Defaults = "Ffurfio gwerthodd gwreiddiol";

// v1.06 additions
$FixturesResults = "Gemau/Canlyniadau";
$Away = "I ffwrdd"; // Away team title
$P = "P";  // Games played
$W = "W";  // Games won
$D = "D";  // Games drawn
$L = "L";  // Games lost
$F = "F";  // Goals for
$A = "A";  // Goals against
$GD = "GD";  // Goals difference
$POS = "Sefyllfa";  // Position
$PTS = "Pwyntiau";  // Points
$Result = "Canlyniad";
$Prediction_Stats = "Stategau Dyfalu ";
$Download_Dbase = "Cronfa ddata Wrth gefn";
$Lang_Report_Bug = "Reportio Byg";

// v1.07 additions
$Lang_Enable_Auto_Predictions = "Dyfalu Awtomatig";
$Lang_Enable = "Galluogi";
$Lang_Enter_Outstanding_Results = "Nodwch y canlyniadau sy'n weddill";
$Lang_Enter_Multiple_Fixtures = "Nodi amryw gemau ar yr un pryd";

// v1.08 additions
$Predictions_For = "Yr sydd wedi ei ddyfalu ar gyfer";
$Game_Time = "Amser y gêm";

// v1.09 additions
$New_Message_Subject = "Rydych chi wedi derbyn neges newydd ar $PredictionLeagueTitle";
$New_Message_Body = "Mae gennych neges newydd ar $PredictionLeagueTitle. Gallwch edrych ar y neges yma http://$PLHome/";
$Games_Menu = "Dewiswch gêm";
$Upload_Icon = "Llwytho i fyny Icon";
$Previous = "Blaenorol";
$Next = "Nesaf";
$Upload_Instructions = "A fyddech cystal â cheisio sicrhau fod y ddelwedd o faint priodol i'w harddangos.  Y mae maint ffeil eich delwedd yn gyfyngedig i $MaxIconFileSize Kb";

// V1.11 additions
$League_Statistics = "Ystadegau";
$My_Missing_Predictions = 'Yr hyn rwyf wedi ei ddyfalu sydd ar goll';
$Division = "Adran";

?>
