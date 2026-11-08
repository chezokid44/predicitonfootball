<?php 
/*********************************************************
 * Author: José Carvalho - fcporto.ws (c) 2004
 * Date  : 20.08.2003
 * File  : lang.php
 * Desc  : Portuguese language file
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
$Forgot_password = 'Recuperar password';

$Home = 'Página principal';

$Menu = 'Menu';

$Prediction_Table = 'Apostas';
$Fixtures_Results = 'Jogos/Resultados';
$My_Predictions = 'As minhas apostas';
$My_Predictions_For = 'Apostas de';
$My_Profile = 'O meu perfil';
$Logout = 'Sair';

$Help = 'Ajuda';
$Email_Us = 'Envie-nos um email';

$Admin_Menu = 'Admin Menu';
$Enter_Fixture = 'Introduzir um jogo';
$Enter_Result = 'Listar resultados';
$Show_Users = 'Listar utilizadores';
$Recalculate_Standings = 'Recalcular classificação';
$Email_All_Users  = 'Email aos utilizadores';
$View_Log = 'Ver estado';

 /////////////////////////////////////////////////////////
 // Create new user variables
 /////////////////////////////////////////////////////////

$Cookie_read_test_error = 'Não é possível criar novo utilizador.';
$Create_New_User = 'Criar novo utilizador';
$User_ID_Required = 'ID de utilizador requerido.';
$User_ID_too_long = 'O seu ID está incorrecto. O máximo permitido é de 32 caracteres.';
$Passwords_is_required = 'É requerida uma password.';
$Passwords_do_not_match = 'As passwords introduzidas não coincidem!';
$Password_too_long = 'Password incorrecta. O máximo permitido é de 32 caracteres.';
$Email_address_is_required = 'é requerido um email.';
$Email_too_long = 'A mensagem so pode conter no máximo 60 caracteres.';
$Email_address_is_not_valid = 'O endereço de email não é vádido.';
$User_Info = 'Informação do utilizador';
$User_ID = 'Id do utilizador';
$Select_user_ID = 'Seleccionar o ID do utilizador.';
$Password_length = 'A password deve conter no mínimo 5 caracteres.';
$Repeat_password = 'Confirme a password.';
$Email_Address_txt = 'Email';
$Enter_valid_email_address = 'Introduza o seu email.';
$Icon = 'Ícone';
$Select_icon = 'Ícone que o representará. Poderá alterá-lo posteriormente.';
$Create = 'Criar';

// CreateUser.php

$Unable_to_register_txt = "Não foi possível registar o utilizador - $username : $User";
$Unable_to_register_3_attempts_txt = "Não foi possível registar o utilizador $username falhado após 3 tentativas: $User";
$Successfully_registered = '$username registado com sucesso';
$Username_already_exists = 'Utilizador já existe. Por favor escolha outro';
$One_user_per_email = 'Só é permitido um utilizador por email ($email).';
$Username_required = 'Nome de utilizador é requerido, por favor escolha um';
$Password_required = 'A password é requerida, por favor escolha uma';
$Email_Answer_txt = "Olá ".$username.",\n\nBem-vindo ao ".$PredictionLeagueTitle."!\n\nPor favor verifique se os seus detalhes estão correctos. Se precisa de alterar alguma informação por favor clique em O MEU PERFIL.\n\nPassword = ".$password."\nEmail = ".$email."\n\nBoa sorte e boas apostas!\n ".$adminSgnature;
$Email_Subject_txt = "Bem-vindo à $PredictionLeagueTitle";

 /////////////////////////////////////////////////////////
 // Change user variables
 /////////////////////////////////////////////////////////
$My_Profile_for = 'O perfil para';
$change_icon = 'Alterar';
$Join_Date = 'desde';
$Change_Profile = 'Alterar perfil';
$Change_Password = 'Alterar password';
$Password_Again = 'Confirme a password';
$Change_Password_button = 'Alterar password';

// UserSelectIcon.php

$Click_on_the_Icon = 'Clique no ícone que pretende utilizar';
$Profile_Icon_Selection = 'Menu de ícones';


 /////////////////////////////////////////////////////////
 // Help file variables
 /////////////////////////////////////////////////////////

$Prediction_League_Help_topic = 'Liga dos Dragões - Ajuda';
$Creating_a_new_user = 'Novo utilizador';

$Creating_a_new_user_txt = 'Para poder participar na Liga tem de estar registado. Clique em <b>Novo utilizador</b>.
          <br><br>
          Seleccione um <b>Id do utilizador</b> que o identificará na Liga dos Dragões (até 32 caracteres). 
          Introduza uma <b>password</b> (no mínimo 5 caracteres), que terá de introduzir 2 vezes, para efeitos de confirmação. Esta protecção permitirá que nenhum outro utilizador possa alterar as suas apostas. 
          <br><br>
          É <b>obrigatória</b> a introdução de um <b>email válido</b>. Receberá na sua caixa de correio a confirmação do registo.<br><br>Caso se esqueca da sua password, clique em <b>Recuperar password</b>. A sua password será enviada para o email por si indicado.
          <br><br>Nota importante: Por favor verifique se o seu browser permite a utilização de cookies. Se tiver esta opção desactivada não será possível visualizar esta página.
	  <br><br><b>Opções do menu do sistema:</b><br><br>
          Apostas - visualização da classificação da Liga dos Dragões. A actualização da classificação é efectuada pelo administrador do sistema, assim como será da responsabilidade do administrador calendarizar jogos e introduzir resultados;<br><br>
	  Jogos/Resultados - Jogos que se encontram calendarizados para apostas;<br><br>
          As minhas apostas - é nesta opção que poderá realizar as suas apostas. Só as poderá
          realizar até à hora do jogo;<br><br>
          O meu perfil - nesta opção poderá realizar modificações ao seu perfil de utilizador;<br><br>
          Sair - para sair do sistema;<br><br>
          Ajuda - acesso à página de ajuda da Liga dos Dragões;<br><br>
          Envie-nos um email - para contactar o Portal dos Dragões sobre questões relacionadas com o sistema.';

$Scoring_txt = 'Sistema de pontuação';

$Scoring_txt_txt = 'São adicionados pontos à sua classificação nos seguintes casos:
          <ul>
          <li>3 Pontos se o resultado é correcto. Exemplo : apostou em 3-1 para um dado jogo, e este termina com o resultado de 3-1.
          <li>1 Ponto se acerta no resultado (V-E-D). Exemplo : apostou em 1-1 para um dado jogo, e este termina com o resultado de 2-2.
          </ul>
          Como é calculada a diferença de golos?
          <ul>
          <li>Cada resultado correcto por equipa é reflectido na sua classificação de golos marcados. Exemplo : apostou que uma equipa marcava 2 golos, e essa equipa marcou 2 golos. Nesse caso são acrescentados 2 golos aos seus golos marcados.
          <br><br>
	  <li>Cada resultado incorrecto por equipa é reflectido na sua classificação de golos sofridos. Exemplo : apostou que uma equipa marcava 2 golos, e a equipa marcou 3 golos. Nesse caso é acrescentado 1 golo à sua classificação de golos sofridos, uma vez que a diferença entre a sua aposta e o resultado foi de 1 golo.
          </ul>';

// Version 1.0 Additions
 /////////////////////////////////////////////////////////
 // Messaging variables
 /////////////////////////////////////////////////////////
$Del = "Del";
$Delete = "Apagar";
$From = "De";
$To = "Para";
$Date = "Data";
$Subject = "Assunto";
$My_Messages = "As minhas mensagens";
$My_Messages_For = "Mensagens para";
$My_Predictions_For = "Apostas de";
$Send_Msg = "[msg] ";
$Create_Msg = "Criar mensagem";
$Send_Msg_Button = "Enviar mensagem";
$Message_txt = "Mensagem";
$RE = "Re:";
$REM = "Rem";  // For removing messages
$Remove_All_Deleted = "Remove todas as msgs apagadas";
$View_All_Messages = "Todas as mensagens";
$Status_Col = "Estado";

$Config_League = 'Configurar Liga';
$Predictions = 'Apostas';
$Predict = 'Apostar';
$Next_Match = "Próximo jogo";
$Prediction_League_Standings = 'Classificação da Liga dos Dragões';
$No_Matches_Scheduled = 'Não existem jogos calendarizados';
$New_Message = "Nova mensagem";
$Subject_txt = "Assunto";
$User_Name = 'User Name';

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
$Set_Defaults = "Configuração por defeito";

// v1.06 additions
$FixturesResults = "Jogos/Resultados";
$Away = "Fora"; // Away team title
$P = "J";  // Games played
$W = "V";  // Games won
$D = "E";  // Games drawn
$L = "D";  // Games lost
$F = "GM";  // Goals for
$A = "GS";  // Goals against
$GD = "DIF";  // Goals difference
$POS = "Pos";  // Position
$PTS = "Pts";  // Points
$Result = "Resultado";
$Prediction_Stats = "Estatísticas de apostas";
$Download_Dbase = "Backup DBase";
$Lang_Report_Bug = "Reportar Bugs";

// v1.07 additions
$Lang_Enable_Auto_Predictions = "Apostas automáticas";
$Lang_Enable = "Activar";
$Lang_Enter_Outstanding_Results = "Introduzir resultados";
$Lang_Enter_Multiple_Fixtures = "Introduzir jogos";

// v1.08 additions
$Predictions_For = "Apostas";
$Game_Time = "Hora";

// v1.09 additions
$New_Message_Subject = "Nova mensagem - $PredictionLeagueTitle";
$New_Message_Subject = "Tem uma nova mensagem - $PredictionLeagueTitle. Pode visualizar a mensagem em http://$PLHome/";
$Games_Menu = "Seleccione jogo";
$Upload_Icon = "Upload Icone";
$Previous = "Ant.";
$Next = "Próx.";
$Upload_Instructions = "O tamanho da imagem está limitado a $MaxIconFileSize Kb";
?>
