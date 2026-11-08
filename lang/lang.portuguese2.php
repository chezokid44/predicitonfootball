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

$Login_txt = 'Entrar';
$Username_txt = 'Username';
$Password_txt = 'Password';
$Logon = 'Logon';
$New_User = 'Novo utilizador';
$Forgot_password = 'Esqueceu-se da password?';

$Home = 'Página principal';

$Menu = 'Menu';

$Prediction_Table = 'Tabela Prediction';
$Fixtures_Results = 'Jogos/Resultados';
$My_Predictions = 'As minhas Predictions';
$My_Profile = 'A minha informação';
$Logout = 'Saír';

$Help = 'Ajuda';
$Email_Us = 'Envie-nos um email';

$Admin_Menu = 'Admin Menu';
$Enter_Fixture = 'Colocar Jogos';
$Enter_Result = 'Colocar Resultados';
$Show_Users = 'Mostrar Utilizadores';
$Recalculate_Standings = 'Recalcular Classificação';
$Email_All_Users  = 'Email para todos os utilizadores ';
$View_Log = 'Ver estado';

 /////////////////////////////////////////////////////////
 // Create new user variables
 /////////////////////////////////////////////////////////

$Cookie_read_test_error = 'Not able to read test cookie when creating new user';
$Create_New_User = 'Criar novo utilizador';
$User_ID_Required = 'ID de utilizador necessário';
$User_ID_too_long = 'O seu ID é longo demais. O máximo permitido é de 32 caracteres.';
$Passwords_is_required = 'É necessário uma password';
$Passwords_do_not_match = 'As passwords escritas não combinam';
$Password_too_long = 'A password é muito longa. O máximo permitido é de 32 caracteres.';
$Email_address_is_required = 'É necessário um email';
$Email_too_long = 'O email é grande demais. O máximo permitido é de 60 caracteres.';
$Email_address_is_not_valid = 'O endereço de email não é vádido';
$User_Info = 'Informação do utilizador';
$User_ID = 'ID do utilizador';
$User_Name = 'Nome do utilizador';
$Select_user_ID = 'Seleccione o ID do utilizador.';
$Password_length = 'A password deve conter no mínimo 5 caracteres.';
$Repeat_password = 'Repetir password.';
$Email_Address_txt = 'Endereço de Email';
$Enter_valid_email_address = 'Coloque o seu verdadeiro email.';
$Icon = 'Icone';
$Select_icon = 'Seleccione um ícone que o representará.';
$Create = 'Criar';

// CreateUser.php

$Unable_to_register_txt = "Incapaz de registar a variável do jogador durante a criação para  $username : $User";
$Unable_to_register_3_attempts_txt = "Incapaz de registar a variável do jogador durante a criação para  $username falhado após 3 tentativas: $User";
$Successfully_registered = 'Registado com sucesso $username durante a criação';
$Username_already_exists = 'O nome escolhido já existe, por favor escolha outro';
$One_user_per_email = 'Apenas um jogador por email ($email) é permitido.';
$Username_required = 'É necessário o nome de utilizador , por favor escolha um';
$Password_required = 'É necessário uma password, por favor escolha uma';
$Email_Answer_txt = "Olá ".$username.",\n\nBem vindo ao ".$PredictionLeagueTitle.". Obrigado por se juntar a nós.\n\nPor favor verifique se os seus detalhes estão correctos. Se precisa de mudar alguma informação por favor faça Login e modifique o seu perfil.\n\nPassword = ".$password."\nEmail = ".$email."\n\nBoa sorte\n ".$adminSignature;
$Email_Subject_txt = "Bem vindo ao ".$PredictionLeagueTitle;

 /////////////////////////////////////////////////////////
 // Change user variables
 /////////////////////////////////////////////////////////
$My_Profile_for = 'O me perfil for';
$change_icon = 'mudar';
$Join_Date = 'Data de entrada';
$Change_Profile = 'Mudar informações';
$Change_Password = 'Mudar Password';
$Old_Password_txt = 'Password Antiga';
$Password_Again = 'Outra vez';
$Change_Password_button = 'Mudar password';

// UserSelectIcon.php

$Click_on_the_Icon = 'Clique no icone que pretende utilizar';
$Profile_Icon_Selection = 'Selecção de Icons de Perfil';
$Language = "Língua";

 /////////////////////////////////////////////////////////
 // Help file variables
 /////////////////////////////////////////////////////////

$Prediction_League_Help_topic = 'Prediction League Ajuda';
$Creating_a_new_user = 'Criar um novo utilizador';

$Creating_a_new_user_txt = '          Para poder participar na Prediction league você necessita criar um perfil de utilizador. Isto é criado seleccionando o link de <b>Novo Utilizador/b> na coluna do lado direito. 
          <br> <br>
          Seleccione um ID de Utilizador você mesmo na prediction league. Este pode ser qualquer coisa que você gosta, a menos que outro utilizador já tenha escolhido para si.
          <br> <br>
          Colocar a Password (duplicada). Isto é para garantir que mais niguém pode modificar as suas predictions.
          <br> <br>
          Você <b>deve</b> colocar um email válido para poder entrar na prediction league. Isto é usado para enviarmos a sua password para o caso de se esquecer.';

$Scoring_txt = 'Marcador';

$Scoring_txt_txt = '          Os pontos são dados da seguinte forma:
          <ul>
          <li>'.$corrScore.' Pontos para um resultado correcto. Exe:. Você apostou 3-1 e o resultado foi 3-1.
          <li>'.$corrResult.' Ponto para um resultado igual. Exe:. Você apostou 1-1, e o resultado foi 2-2.
          </ul>
          Diferença de Golos
          <ul>
          <li>Para cada número correto de golos que você aposta por equipa, recebe como golos marcados. Exe:. Você apostou 2 e a equipa marcou 2, você recebe +2 golos.
          <li>Para cada número incorreto de golos que você aposta por equipa, perde na diferença de golos. Exe:. Você apostou 3 e a equipa marcou 2, você recebe -1 golos.
          </ul>';

// Version 1.0 Additions
 /////////////////////////////////////////////////////////
 // Messaging variables
 /////////////////////////////////////////////////////////
$Del = "Apagar";
$Delete = "Apagar";
$From = "De";
$To = "Para";
$Date = "Data";
$Subject = "Assunto";
$My_Messages = "A Minhas Mensagens";
$My_Messages_For = "A Minhas Mensagens para";
$My_Predictions_For = "A Minhas Predictions para";
$Send_Msg = "Enviar mensagem";
$Create_Msg = "Criar mensagem";
$Send_Msg_Button = "Enviar mensagem";
$Message_txt = "Mensagem";
$RE = "Re:";
$REM = "Rem";  // For removing messages
$Remove_All_Deleted = "Remover todas as msgs apagadas";
$View_All_Messages = "Ver todas as Mensagem";
$Status_Col = "Estado";

$Config_League = 'Configurar Liga';
$Predictions = 'Predictions';
$Predict= 'Predict';
$Next_Match = "Jogo Seguinte";
$Prediction_League_Standings = 'Prediction League Posições';
$No_Matches_Scheduled = 'Sem jogos agendados';
$New_Message = "Nova Mensagem";
$Subject_txt = "Assunto";

// v1.03 addition
$Months = array('01'=>"Jan",
             '02'=>"Fev",
             '03'=>"Mar",
             '04'=>"Abr",
             '05'=>"Mai",
             '06'=>"Jun",
             '07'=>"Jul",
             '08'=>"Ago",
             '09'=>"Set",
             '10'=>"Out",
             '11'=>"Nov",
             '12'=>"Dez");
$Set_Defaults = "Valores de configuração pré-definidos";

// v1.06 additions
$FixturesResults = "Fixtures/Resultados";
$Away = "Fora"; // Away team title
$P = "P";  // Games played
$W = "V";  // Games won
$D = "E";  // Games drawn
$L = "D";  // Games lost
$F = "GM";  // Goals for
$A = "GS";  // Goals against
$GD = "GD";  // Goals difference
$POS = "Pos";  // Position
$PTS = "Pts";  // Points
$Result = "Resultado";
$Prediction_Stats = "Prediction Estatística ";
$Download_Dbase = "Backup DBase";
$Lang_Report_Bug = "Avisar Erro";

// v1.07 additions
$Lang_Enable_Auto_Predictions = "Predictions Automáticas";
$Lang_Enable = "Permitir";
$Lang_Enter_Outstanding_Results = "Colocar resultados dominantes";
$Lang_Enter_Multiple_Fixtures = "Colocar Multiplas posições";

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
