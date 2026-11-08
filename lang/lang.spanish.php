<?php 
/*********************************************************
 * Author: Tuomo Aura (c) 2002
 * Date  : 21.11.2002
 * File  : lang.php
 * Desc  : Spanish language file
 ********************************************************/

 /////////////////////////////////////////////////////////
 // Character set required
 /////////////////////////////////////////////////////////
 $charset = "iso8859-1";

 /////////////////////////////////////////////////////////
 // Menu variables
 /////////////////////////////////////////////////////////

$Login_txt = 'Login';
$Username_txt = 'Usuario';
$Password_txt = 'Clave';
$Logon = 'Entrar';
$New_User = 'Nuevo Usuario';
$Forgot_password = 'Olvido la password?';

$Home = 'Inicio';

$Menu = 'Menu';

$Prediction_Table = 'Tabla de Predicciones';
$Fixtures_Results = 'Partidos/Resultados';
$My_Predictions = 'Mis Predicciones';
$My_Profile = 'Mi Perfil';
$Logout = 'Logout';

$Help = 'Ayuda';
$Email_Us = 'Contactenos';

$Admin_Menu = 'Menu de Administración';
$Enter_Fixture = 'Introducir Partido';
$Enter_Result = 'Introducir Resultado';
$Show_Users = 'Mostrar Usuarios';
$Recalculate_Standings = 'Recalcular Clasif.';
$Email_All_Users  = 'Correo a todos los Usuarios ';
$View_Log = 'Ver el Log';

 /////////////////////////////////////////////////////////
 // Create new user variables
 /////////////////////////////////////////////////////////

$Cookie_read_test_error = 'No es posible leer la cookie de prueba al crear un nuevo usuario';
$Create_New_User = 'Crear nuevo Usuario';
$User_ID_Required = 'Requerido el ID de Usuario';
$User_ID_too_long = 'ID del Usuario es demasiado largo. Debe tener menos de 32 caracteres.';
$Passwords_is_required = 'Los campos Passwords son obligatorios';
$Passwords_do_not_match = 'Las Passwords no coinciden';
$Password_too_long = 'La Password es demasiado larga. Debe tener menos de 32 caracteres.';
$Email_address_is_required = 'El campo Email es obligatorio';
$Email_too_long = 'El Email es demasiado largo. Debe tener menos de 60 caracteres.';
$Email_address_is_not_valid = 'La dirección de correo no es válida';
$User_Info = 'Información de Usuario';
$User_ID = 'ID de Usuario';
$User_Name = 'Nombre de Usuario';
$Select_user_ID = 'Seleccione un ID de Usuario.';
$Password_length = 'La Password debe tener al menos cinco caracteres.';
$Repeat_password = 'Repita la password.';
$Email_Address_txt = 'Dirección Email';
$Enter_valid_email_address = 'Introduzca una dirección de correo válida.';
$Icon = 'Icono';
$Select_icon = 'Seleccione un Icono para representar a su Usuario.';
$Create = 'Crear';

// CreateUser.php

$Unable_to_register_txt = "Imposible registrar la variable Usuario durante la creación de $username : $User";
$Unable_to_register_3_attempts_txt = "Imposible registrar la variable Usuario durante la creación de $username ha fallado después de 3 intentos: $User";
$Successfully_registered = 'Usuario '.$username.' registrado correctamente';
$Username_already_exists = 'El nombre de Usuario existe, por favor elija otro nombre';
$One_user_per_email = 'Permitido solo un usuario por dirección de correo ($email).';
$Username_required = 'Nombre de usuario obligatorio, por favor elija un nombre';
$Password_required = 'Password obligatoria, por favor seleccione una password';
$Email_Answer_txt = "Hola ".$username.",\n\nBienvenido a ".$PredictionLeagueTitle.". Gracias por unirse a nosotros.\n\nPor favor compruebe que sus datos son correctos. Si necesita cambiar algo, por favor validese y modifique su perfil.\n\nPassword = ".$password."\nEmail = ".$email."\n\nBuena Suerte\n ".$adminSignature;
$Email_Subject_txt = "Bienvenido a ".$PredictionLeagueTitle;

 /////////////////////////////////////////////////////////
 // Change user variables
 /////////////////////////////////////////////////////////
$My_Profile_for = 'Mi perfil para';
$change_icon = 'cambiar';
$Join_Date = 'Fecha de Alta';
$Change_Profile = 'Cambiar Perfil';
$Change_Password = 'Cambiar Password';
$Old_Password_txt = 'Passwod Antigua';
$Password_Again = 'De nuevo';
$Change_Password_button = 'Cambiar Password';

// UserSelectIcon.php

$Click_on_the_Icon = 'Pulse en el Icono que quiera usar';
$Profile_Icon_Selection = 'Selección del icono de Perfil';
$Language = "Idioma";

 /////////////////////////////////////////////////////////
 // Help file variables
 /////////////////////////////////////////////////////////

$Prediction_League_Help_topic = 'Ayuda de Prediction League';
$Creating_a_new_user = 'Creando un nuevo usuario';

$Creating_a_new_user_txt = '          Para poder participar en Prediction League necesita crear un perfil de usuario. Se crea seleccionando <b>Nuevo Usuario</b> enlace de la columna derecha. Por favor aseguresé de que las cookies estén activadas en su navegador. Si las cookies estan desactivadas, será redireccionado a esta página.
          <br> <br>
          Seleccione un ID de usuario para identificarse en la league. Este ID puede ser cualquier cosa que quiera y que no esté siendo utilizado por otro usuario.
          <br> <br>
          Introduzca password (dos veces). Esto es para asegurarse de que nadie pueda cambiar sus predicciones.
          <br> <br>
          <b>Debe</b> introducir una dirección de correo válida para unirse a la liga. Esta dirección se usará para enviar su password en el caso de que la olvide.';

$Scoring_txt = 'Puntuación';

$Scoring_txt_txt = '          Los puntos se dan de esta forma:
          <ul>
          <li>'.$corrScore.' Puntos por un marcador correcto. Ej. predijo 3-1 y el resultado es 3-1.
          <li>'.$corrResult.' Punto por el resultado correcto. Predijo 1-1, y el resultado es 2-2.
          </ul>
          Diferencia de goles
          <ul>
          <li>Por cada numero de goles correcto en cualquier equipo recibirá ese número de goles a favor. Ej. predijo 2 y el equipo marca 2, obtiene +2 goles (a favor).
          <li>Por cada numero de goles correcto en cualquier equipo perderá la diferencia en goles. Ej. predijo 3 y el equipo marca 2, obtiene -1 goles (en contra).
          </ul>';

// Version 1.0 Additions
 /////////////////////////////////////////////////////////
 // Messaging variables
 /////////////////////////////////////////////////////////
$Del = "Borr";
$Delete = "Borrar";
$From = "De";
$To = "Para";
$Date = "Fecha";
$Subject = "Asunto";
$My_Messages = "Mis Mensajes";
$My_Messages_For = "Mis Mensajes para";
$My_Predictions_For = "Mis Predicciones para";
$Send_Msg = "Enviar Mensaje";
$Create_Msg = "Crear Mensaje";
$Send_Msg_Button = "Enviar Mensaje";
$Message_txt = "Mensaje";
$RE = "Re:";
$REM = "Elim";  // For removing messages
$Remove_All_Deleted = "Eliminar todos los mensajes borrados";
$View_All_Messages = "Ver todos los mensajes";
$Status_Col = "Estado";

$Config_League = 'Configurar Liga';
$Predictions = 'Predicciones';
$Predict= 'Predecir';
$Next_Match = "Siguiente Partido";
$Prediction_League_Standings = 'Clasificación de Prediction League';
$No_Matches_Scheduled = 'No existen fechas de partidos';
$New_Message = "Nuevo Mensaje";
$Subject_txt = "Asunto";

// v1.03 addition
$Months = array('01'=>"Ene",
             '02'=>"Feb",
             '03'=>"Mar",
             '04'=>"Abr",
             '05'=>"May",
             '06'=>"Jun",
             '07'=>"Jul",
             '08'=>"Ago",
             '09'=>"Sep",
             '10'=>"Oct",
             '11'=>"Nov",
             '12'=>"Dic");
$Set_Defaults = "Valores de configuración por defecto";

// v1.06 additions
$FixturesResults = "Partidos/Resultados";
$Away = "Visitante"; // Away team title
$P = "J";  // Games played
$W = "G";  // Games won
$D = "E";  // Games drawn
$L = "P";  // Games lost
$F = "F";  // Goals for
$A = "C";  // Goals against
$GD = "DG";  // Goals difference
$POS = "Pos";  // Position
$PTS = "Pts";  // Points
$Result = "Resultado";
$Prediction_Stats = "Estadísticas de Predicciones ";
$Download_Dbase = "Backup BDatos";
$Lang_Report_Bug = "Enviar Error(Bug)";

// v1.07 additions
$Lang_Enable_Auto_Predictions = "Predicciones Automáticas";
$Lang_Enable = "Activar";
$Lang_Enter_Outstanding_Results = "Introducir Resultados Excepcionales";
$Lang_Enter_Multiple_Fixtures = "Introducir Múltiples Partidos";

// v1.08 additions
$Predictions_For = "Predicciones Para";
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
