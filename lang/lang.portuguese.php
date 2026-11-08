<?php 
/*********************************************************
 * Author: Apostas (c) 2002
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

$Login_txt = 'Login';
$Username_txt = 'Username';
$Password_txt = 'Password';
$Logon = 'Entrar';
$New_User = 'Novo utilizador';
$Forgot_password = 'Esqueceu-se da password?';

$Home = 'Página principal';

$Menu = 'Menu';

$Prediction_Table = 'Apostas';
$Fixtures_Results = 'Jogos/Resultados';
$My_Predictions = 'As minhas apostas';
$My_Profile = 'A minha informação';
$Logout = 'Sair';

$Help = 'Ajuda';
$Email_Us = 'Envie-nos um email';

$Admin_Menu = 'Admin Menu';
$Enter_Fixture = 'Entrar jogo';
$Enter_Result = 'Entrar resultados';
$Show_Users = 'Mostrar utilizadores';
$Recalculate_Standings = 'Recalcilar classificação';
$Email_All_Users  = 'Email para todos os utilizadores ';
$View_Log = 'Ver estado';

 /////////////////////////////////////////////////////////
 // Create new user variables
 /////////////////////////////////////////////////////////

$Cookie_read_test_error = 'Não é possível criar novo utilizador';
$Create_New_User = 'Criar novo utilizador';
$User_ID_Required = 'ID de utilizador requerido';
$User_ID_too_long = 'O seu ID é logo demais.O máximo permitido é de 32 caracteres.';
$Passwords_is_required = 'É requerida uma password';
$Passwords_do_not_match = 'As duas passwords escritas não combinam';
$Password_too_long = 'A password é muito longa.. O máximo permitido é de 32 caracteres.';
$Email_address_is_required = 'è requerido um email';
$Email_too_long = 'O email é grande demais. Pode conter no máximo 60 caracteres.';
$Email_address_is_not_valid = 'O endereço de email não é vádido';
$User_Info = 'Informação do utilizador';
$User_ID = 'Id do utilizador';
$Select_user_ID = 'Selecionar o ID utilizador.';
$Password_length = 'A password deve conter no mínimo 5 caracteres.';
$Repeat_password = 'Repetir password.';
$Email_Address_txt = 'Email';
$Enter_valid_email_address = 'Coloque o seu email.';
$Icon = 'Icone';
$Select_icon = 'Seleccione um ícone que o representará.';
$Create = 'Criar';

// CreateUser.php

$Unable_to_register_txt = "Incapaz de registar a variável do utilizador durante a criação para  $username : $User";
$Unable_to_register_3_attempts_txt = "Incapaz de registar a variável do utilizador durante a criação para  $username falhado após 3 tentativas: $User";
$Successfully_registered = 'Registado com sucesso '.$username;
$Username_already_exists = 'O nome escolhido já existe, por favor escolha outro';
$One_user_per_email = 'Apenas um utilizador por email ($email) já existe.';
$Username_required = 'Nome de utilizador é requerido, por favor escolha um';
$Password_required = 'A password é requerida, por favor escolha uma';
$Email_Answer_txt = "Olá ".$username.",\n\nBem vindo ao ".$PredictionLeagueTitle.". Obrigado por se juntar a nós.\n\nPor favor verifique se os seus detalhes estão correctos. Se precisa de mudar alguma informação por favor clique em : A MINHA INFORMAÇÃO.\n\nPassword = ".$password."\nEmail = ".$email."\n\nBoa sorte e boas apostas\n ".$adminSignature;
$Email_Subject_txt = "Bem vindo $PredictionLeagueTitle";

 /////////////////////////////////////////////////////////
 // Change user variables
 /////////////////////////////////////////////////////////
$My_Profile_for = 'Os detalhes informativos para';
$change_icon = 'mudar';
$Join_Date = 'desde';
$Change_Profile = 'Mudar informações';
$Change_Password = 'Mudar password';
$Old_Password_txt = 'Old Password';
$Password_Again = 'Outra vez';
$Change_Password_button = 'Mudar password';

// UserSelectIcon.php

$Click_on_the_Icon = 'Clique no icone que pretende utilizar';
$Profile_Icon_Selection = 'Menú de icones';
$Language = "Language";


 /////////////////////////////////////////////////////////
 // Help file variables
 /////////////////////////////////////////////////////////

$Prediction_League_Help_topic = 'PALPITES DESPORTO PT  Ajuda';
$Creating_a_new_user = 'Criar um novo utilizador';

$Creating_a_new_user_txt = '          Para poder participar nos palpites necessita estar registado. Para isso clique em <b>Novo utilizador</b> . Por favor verifique se os cookies são permitidos pelo seu browser. Se tiver esta opção desactivada não será possível visualizar esta página.
          <br> <br>
          Seleccione um nome que o identificará nos PALPITES DESPORTO PT. Pode escolher o nome que entender desde que este não esteja a ser utilizado por outro utilizador.
          Enter a password (twice). This is to ensure no-one else can change your predictions.
          <br> <br>
          Você <b>tem</b> de colocar um email válido para se registar nos PALPITES  DESPORTO PT. Dessa forma caso se esqueça enviaremos a sua password para o email que nos indicar.';



$Scoring_txt = 'Sistema de pontuação';

$Scoring_txt_txt = '          São adicionados pontos à sua classificação nos seguintes casos:
          <ul>
          <li>3 Pontos se o resultado é correcto. Exemplo : Você palpitou para o jogo 3-1 e o resultado final do jogo foi 3-1.
          <li>1 Ponto se acerta no resultado (V-E-D). Exemplo : Você palpitou 1-1 e o resultado final do jogo foi 2-2.
          </ul>
          Diferença de golos
          <ul>
          <li>Por cada resultado correcto por equipa que acerte é colocado na sua classificação de golos marcados o número de golos, por exemplo : você palpitou que uma equipa ia marcar dois golos e a equipa marcou dois golos. Nesse caso são acrescentados os dois golos aos seus golos marcados.
          <li>Por cada resultado incorrecto por equipa é colocado na sua classificação de golos sofridos o número de golos, por exemplo : você palpitou que uma equipa ia marcar dois golos e a equipa marcou tres golos. Nesse caso é acrescentado um golos sofrido á sua classificação uma vez que a diferença entre o seu palpite e o resultados foi de um golo.
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
$Send_Msg = "[msg] ";
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
$User_Name = 'User Name';

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
