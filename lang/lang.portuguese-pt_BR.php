<?php 
/*********************************************************
 * Author: Joel Lazzari 2004
 * Date  : 09.01.2004
 * File  : lang.portuguese-pt_BR.php
 * Desc  : Portuguese (Brazil) language file
 ********************************************************/

 /////////////////////////////////////////////////////////
 // Caracter requerido
 /////////////////////////////////////////////////////////
 $charset = "iso8859-1";

 /////////////////////////////////////////////////////////
 // Menu - variáveis
 /////////////////////////////////////////////////////////

$Login_txt = 'Login';
$Username_txt = 'Usuário';
$Password_txt = 'Senha';
$Logon = 'Entrar';
$New_User = 'Novo Usuário';
$Forgot_password = 'Esqueceu-se da senha?';

$Home = 'Página Principal';

$Menu = 'Menu';

$Prediction_Table = 'Apostas';
$Fixtures_Results = 'Jogos/Resultados';
$My_Predictions = 'Minhas apostas';
$My_Profile = 'Meus dados';
$Logout = 'Sair';

$Help = 'Ajuda';
$Email_Us = 'Envie-nos um email';

$Admin_Menu = 'Menu Administrativo';
$Enter_Fixture = 'Entrar jogo';
$Enter_Result = 'Entrar resultados';
$Show_Users = 'Mostrar usuários';
$Recalculate_Standings = 'Recalcular Classificação';
$Email_All_Users  = 'Email para todos os usuários ';
$View_Log = 'Ver log';

 /////////////////////////////////////////////////////////
 // Criar novo usuário - variáveis
 /////////////////////////////////////////////////////////

$Cookie_read_test_error = 'Não está habilitado a leitura de cookies (teste) quando o usuário vai ser criado';
$Create_New_User = 'Criar novo usuário';
$User_ID_Required = 'ID do usuário requerido';
$User_ID_too_long = 'ID muito grande. O máximo permitido é 32 caracteres.';
$Passwords_is_required = 'Uma senha é necessária';
$Passwords_do_not_match = 'As duas senhas digitadas não conferem';
$Password_too_long = 'A senha digitada é muito grande. O máximo permitido é 32 caracteres.';
$Email_address_is_required = 'Um endereço de email é necessário';
$Email_too_long = 'O endereço de email é muito grande. O máximo permitido é 60 caracteres.';
$Email_address_is_not_valid = 'Endereço de email inválido';
$User_Info = 'Informação do usuário';
$User_ID = 'ID do usuário';
$User_Name = 'Nome do usuário';
$Select_user_ID = 'Selecionar ID do usuário.';
$Password_length = 'A senha deve conter no mínimo 5 caracteres.';
$Repeat_password = 'Repita a senha.';
$Email_Address_txt = 'Endereço de Email';
$Enter_valid_email_address = 'Coloque seu e-mail (válido).';
$Icon = 'Icone';
$Select_icon = 'Seleccione um ícone que o representará.';
$Create = 'Criar';

// CreateUser.php

$Unable_to_register_txt = "Não foi possível registar a variável do usuário durante a criação de  $username : $User";
$Unable_to_register_3_attempts_txt = "Não foi possível registar a variável do usuário durante a criação de  $username : $User $username falhado após 3 tentativas: $User";
$Successfully_registered = 'Registro efetuado com sucesso '.$username.' durante criação';
$Username_already_exists = 'O nome de usuário escolhido já existe, por favor escolha outro';
$One_user_per_email = 'Apenas um usuário por email ($email) já existe.';
$Username_required = 'Usuário requerido, por favor escolha um';
$Password_required = 'Senha requerida, por favor escolha uma';
$Email_Answer_txt = "Olá ".$username.",\n\nBem vindo ".$PredictionLeagueTitle.". Obrigado por se juntar a nós.\n\nPor favor verifique se os seus detalhes estão corretos. Se precisa alterar alguma informação por favor clique em.\n\nSenha = ".$password."\nEmail = ".$email."\n\nBoa sorte\n ".$adminSignature;
$Email_Subject_txt = "Bem vindo ".$PredictionLeagueTitle;

 /////////////////////////////////////////////////////////
 // Mudar variáveis do usuário
 /////////////////////////////////////////////////////////
$My_Profile_for = 'Minhas informações para';
$change_icon = 'mudar';
$Join_Date = 'desde';
$Change_Profile = 'Mudar informações';
$Change_Password = 'Mudar senha';
$Old_Password_txt = 'Senha antiga';
$Password_Again = 'Outra vez';
$Change_Password_button = 'Mudar senha';

// UserSelectIcon.php

$Click_on_the_Icon = 'Clique no icone que quer utilizar';
$Profile_Icon_Selection = 'Menu de ícones';
$Language = "Idioma";

 /////////////////////////////////////////////////////////
 // Arquivo de ajuda - variáveis
 /////////////////////////////////////////////////////////

$Prediction_League_Help_topic = 'Prediction League Ajuda';
$Creating_a_new_user = 'Criar um novo usuário';

$Creating_a_new_user_txt = '          Para poder fazer apostas e palpitar é necessário o usuário estar registrado. Clique no link <b>Novo Usuário</b> na coluna da esquerda.
          <br> <br>
          Selecione um ID de usuário para você se indentificar na prediction league. Pode ser qualquer apelido, desde que não tenha nenhum usuário registrado com o mesmo.
          <br> <br>
          Entre com a senha (duas vezes). Ela será necessária para realizar suas apostas e mudar suas informações.
          <br> <br>
          Você <b>deve</b> colocar um e-mail válido para participar da prediction league. Ele será utilizado para enviar a senha se porventura ela seja perdida.';

$Scoring_txt = 'Pontuação';

$Scoring_txt_txt = '          São adicionados pontos na sua classificação quando:
          <ul>
          <li>'.$corrScore.' Pontos para um resultado correto. Exemplo: você palpitou para 3-1 e o resultado foi 3-1. Será acrescido 3 pontos.
          <li>'.$corrResult.' ponto para o resultado correto - nas diferenças. Você palpitou 1-1, o resultado do jogo foi de 2-2. Será acreacido 1 ponto.
          </ul>
          Diferença de Gols
          <ul>
          <li>Para cada resultado correto por jogo que acerte é colocado na sua classificação de gols marcados o número de gols, por exemplo : você palpitou que uma equipe ia marcar dois gols e o time marcou dois gols. Nesse caso são acrescentados os dois gols aos seus gols marcados.
          <li>Para cada resultado incorreto por jogo é colocado na sua classificação de gols sofridos o número de gols, por exemplo : você palpitou que uma equipe ia marcar dois gols e o time marcou três gols. Nesse caso é acrescentado um gols sofrido a sua classificação uma vez que a diferença entre o seu palpite e o resultado foi de um gol.
          </ul>';

// Version 1.0 Additions
 /////////////////////////////////////////////////////////
 // Variáveis das mensagens
 /////////////////////////////////////////////////////////
$Del = "Apague";
$Delete = "Delete";
$From = "De";
$To = "Para";
$Date = "Data";
$Subject = "Assunto";
$My_Messages = "Minhas mensagens";
$My_Messages_For = "Minhas mensagens para";
$My_Predictions_For = "Minhas Predictions para";
$Send_Msg = "Enviar mensagem";
$Create_Msg = "Criar mensagem";
$Send_Msg_Button = "Enviar mensagem";
$Message_txt = "Mensagem";
$RE = "Re:";
$REM = "Rem";  // Para remover mensagens
$Remove_All_Deleted = "Remover todas as msgs deletadas";
$View_All_Messages = "Ver todas as mensagens";
$Status_Col = "Status";

$Config_League = 'Configurar Liga';
$Predictions = 'Predição';
$Predict= 'Predizer';
$Next_Match = "Próxima competição";
$Prediction_League_Standings = 'Considerações sobre a Prediction League';
$No_Matches_Scheduled = 'Nenhuma programação encontrada';
$New_Message = "Nova mensagem";
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
$Set_Defaults = "Valores padrões de configuração";

// v1.06 additions
$FixturesResults = "Jogos/Resultados";
$Away = "de fora"; // Título do time de fora
$P = "P";  // Partidas jogadas
$W = "V";  // Vitórias
$D = "D";  // Jogos empatados
$L = "JP";  // Jogos perdidos
$F = "GF";  // Gols a favor
$A = "GC";  // Gols contra
$GD = "DG";  // Diferença de Gols
$POS = "Pos";  // Posição
$PTS = "Pts";  // Pontos
$Result = "Resultado";
$Prediction_Stats = "Prediction Statísticas ";
$Download_Dbase = "Backup Banco de dados";
$Lang_Report_Bug = "Reportar falha";

// v1.07 additions
$Lang_Enable_Auto_Predictions = "Predictions automáticas";
$Lang_Enable = "Desativado";
$Lang_Enter_Outstanding_Results = "Entrar com resultados";
$Lang_Enter_Multiple_Fixtures = "Entrar com múltiplas competições";

// v1.08 additions
$Predictions_For = "Predictions para";
$Game_Time = "Tempo de jogo";

// v1.09 additions
$New_Message_Subject = "Você recebeu uma nova mensagem em $PredictionLeagueTitle";
$New_Message_Body = "Você tem uma nova mensagem em $PredictionLeagueTitle. Você pode ver a mensagem em http://$PLHome/";
$Games_Menu = "Selecionar jogo";
$Upload_Icon = "Enviar Ícone";
$Previous = "Anterior";
$Next = "Próximo";
$Upload_Instructions = "Por favor tente guardar o tamanho e as dimensões da imagem para mostrar no visor. Seu arquivo de imagem tem um tamanho limitado para $MaxIconFileSize Kb";

// V1.11 additions
$League_Statistics = "Estatísticas";

?>
