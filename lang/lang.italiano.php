<?php 
/*********************************************************
 * Author: Tuomo Aura (c) 2002
 * Date  : 21.11.2002
 * File  : lang.php
 * Desc  : Italian language file
 ********************************************************/

 /////////////////////////////////////////////////////////
 // Character set required
 /////////////////////////////////////////////////////////
 $charset = "iso8859-1";

 /////////////////////////////////////////////////////////
 // Menu variables
 /////////////////////////////////////////////////////////

$Login_txt = 'Login';
$Username_txt = 'Nome Utente';
$Password_txt = 'Password';
$Logon = 'Logon';
$New_User = 'Nuovo Conto';
$Forgot_password = 'Password dimenticata';

$Home = 'Home';

$Menu = 'Menu';

$Prediction_Table = 'Classifica generale';
$Fixtures_Results = 'Agenda/Risultati';
$My_Predictions = 'Le mie predizioni';
$My_Profile = 'Il mio profilo';
$Logout = 'Log out';

$Help = 'Aiuto';
$Email_Us = 'Contattaci';

$Admin_Menu = 'Admin Menu';
$Enter_Fixture = 'Immettere una partita';
$Enter_Result = 'Immettere un risultato';
$Show_Users = 'Mostrare gli utilizzatori';
$Recalculate_Standings = 'Ricalcolare la classifica';
$Email_All_Users  = 'Email tutti gli utilizzatori ';
$View_Log = 'Vedere il Log';

 /////////////////////////////////////////////////////////
 // Create new user variables
 /////////////////////////////////////////////////////////

$Cookie_read_test_error = 'Non è possibile leggere il test cookie quando viene creato il nuovo utilizzatore';
$Create_New_User = 'Crea un nuovo utilizzatore';
$User_ID_Required = "ID dell'utilizzatore è richiesta";
$User_ID_too_long = 'ID di utilizzatore troppo lunga.  32 caratteri al massimo.';
$Passwords_is_required = 'La Password è richiesta';
$Passwords_do_not_match = 'La Password non funziona';
$Password_too_long = 'Password troppo lunga. 32 caratteri al massimo.';
$Email_address_is_required = "L'indirizzo Email è richiesta";
$Email_too_long = 'Email troppo lunga. 60 caratteri al massimo.';
$Email_address_is_not_valid = "L'indirizzo Email non è valido";
$User_Info = "Info sull'utilizzatore";
$User_ID = "ID dell'utilizzatore";
$User_Name = "Nome dell'utilizzatore";
$Select_user_ID = "Seleziona un' ID di utilizzatore.";
$Password_length = 'La Password deve contenere al minimo 5 caratteri.';
$Repeat_password = 'Ripetere la password.';
$Email_Address_txt = 'Indirizzo Email';
$Enter_valid_email_address = 'Immette il tuo valido indirizzo email.';
$Icon = 'Icona';
$Select_icon = "Seleziona l'icona che rapprensenterà il tuo personaggio.";
$Create = 'Creare';

$Unable_to_register_txt = "Impossibilità di registrazione della variabile utilizzatore durante la creazione dell' $username : $User";
$Unable_to_register_3_attempts_txt = "Impossibilità di registrazione della variabile utilizzatore durante la creazione dell' $username fallì dopo 3 tentativi: $User";
$Successfully_registered = '$username è stato registrato con successo durante la creazione';
$Username_already_exists = "L'Username scelto esiste già, seleziona un altro nome per favore";
$One_user_per_email = "Solo un utilizzatore per indirizzo email ($email) è accettato.";
$Username_required = "L'Username è richiesto, seleziona un nome per favore";
$Password_required = "La Password è richiesta, seleziona una password per favore";
$Email_Answer_txt = "Ciao ".$username.",\n\nBenvenuti su ".$PredictionLeagueTitle.". Grazie del tuo interesse\n\nVerifica per favore se le tue informazioni sono corrette. Se vuoi fare delle modificazioni, log in e modifica il tuo profilo.\n\nPassword = ".$password."\nEmail = ".$email."\n\nBuona Fortuna\n ".$adminSignature;
$Email_Subject_txt = "Benvenuti su".$PredictionLeagueTitle;

 /////////////////////////////////////////////////////////
 // Change user variables
 /////////////////////////////////////////////////////////
$My_Profile_for = 'Il mio profilo per';
$change_icon = 'modificare';
$Join_Date = 'Data di registrazione';
$Change_Profile = 'Modificare il profilo';
$Change_Password = 'Modificare la Password';
$Old_Password_txt = 'La Password attuale';
$Password_Again = 'Di nuovo';
$Change_Password_button = 'Modificare la Password';

// UserSelectIcon.php

$Click_on_the_Icon = "Seleziona l'icona che vuoi utilizzare";
$Profile_Icon_Selection = "Selezione dell'icona per il profilo";
$Language = "Lingua";

 /////////////////////////////////////////////////////////
 // Help file variables
 /////////////////////////////////////////////////////////

$Prediction_League_Help_topic = 'Aiuto';
$Creating_a_new_user = 'Creare un nuovo utilizzatore';

$Creating_a_new_user_txt = "          Per poter partecipare ai pronostici, deve esser creato un profilo di utilizzatore. Per questo, seleziona il link <b>Nuovo Utilizzatore</b> nel menu di destra. I cookies devono essere accettati dal tuo browser. Se non lo sono, sarai ridiretto su questa pagina.
          <br> <br>
          Seleziona un'ID di utilizzatore per identificarti nel gioco. Puoi scegliere qualsiasi ID, se non è già utilizzata da un altro membro.
          <br> <br>
          Entra una password (due volte), così nessuno potrà cambiare le tue predizioni.
          <br> <br>
          Devi entrare un indirizzo email valido per poter giocare. E' necessario per inviarti la tua password nel caso in cui la dimenticheresti.";

$Scoring_txt = 'Scoring';

$Scoring_txt_txt = '          I punti sono distribuiti in quel modo:
          <ul>
          <li>'.$corrScore.' Punti per un punteggio giusto. p.esempio. predisci 3-1 ed il risultato è 3-1.
          <li>'.$corrResult.' Punto per un risultato giusto. Predisci 1-1, ed il punteggio è 2-2.
          </ul>
          Differenza di goal
          <ul>
          <li>Per ogni corretto numero di goals di ogni team che predisci, ricevi quel numero di +goals. Per esempio, predisci 2 ed il team segna 2, ricevi +2 goals.
          <li>Per ogni falso numero di goals di ogni team che predisci, perdi la differenza in goals. Per esempio, predisci 3 ed il team segna 2, ricevi -1 goals.
          </ul>';

// Version 1.0 Additions
 /////////////////////////////////////////////////////////
 // Messaging variables
 /////////////////////////////////////////////////////////
$Del = "Sop";
$Delete = "Sopprimere";
$From = "Da";
$To = "A";
$Date = "Data";
$Subject = "Soggetto";
$My_Messages = "I miei messaggi";
$My_Messages_For = "I miei messaggi per";
$My_Predictions_For = "Le mie predizioni per";
$Send_Msg = "Inviare il messaggio";
$Create_Msg = "Creare un messaggio";
$Send_Msg_Button = "Inviare il messaggio";
$Message_txt = "Messaggio";
$RE = "Re:";
$REM = "Rem";  // Per sopprimere i messagi
$Remove_All_Deleted = "Sopprimere tutti i messaggi cancellati";
$View_All_Messages = "Vedere tutti i messaggi";
$Status_Col = "Stato";

$Config_League = 'Configurare la Lega';
$Predictions = 'Predizioni';
$Predict= 'Pronostica';
$Next_Match = "Prossima partita";
$Prediction_League_Standings = 'Classifiche';
$No_Matches_Scheduled = 'Nessuna partita al calendario';
$New_Message = "Nuovo messaggio";
$Subject_txt = "Soggetto";

// v1.03 addition
$Months = array('01'=>"Gen",
             '02'=>"Feb",
             '03'=>"Mar",
             '04'=>"Apr",
             '05'=>"Mag",
             '06'=>"Giu",
             '07'=>"Lug",
             '08'=>"Ago",
             '09'=>"Set",
             '10'=>"Ott",
             '11'=>"Nov",
             '12'=>"Dic");
$Set_Defaults = "Default config values";

// v1.06 additions
$FixturesResults = "Agenda/Risultati";
$Away = "Away"; // Squadra in spostamento
$P = "P";  // Partite giocate
$W = "W";  // Partite vinte
$D = "D";  // Pari
$L = "L";  // Partite perse
$F = "F";  // Goals segnati
$A = "A";  // Goals incassati
$GD = "GD";  // Differenza di Goals 
$POS = "Pos";  // Posizione
$PTS = "Pts";  // Punti
$Result = "Risultato";
$Prediction_Stats = "Statistiche";
$Download_Dbase = "Backup DBase";
$Lang_Report_Bug = "Segnalare un Bug";

// v1.07 additions
$Lang_Enable_Auto_Predictions = "Predizioni automatiche";
$Lang_Enable = "Autorizzare";
$Lang_Enter_Outstanding_Results = "Entra risultati in sospeso";
$Lang_Enter_Multiple_Fixtures = "Entra molti eventi";

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
