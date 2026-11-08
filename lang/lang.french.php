<?php
/*********************************************************
* Author: Gonzague Lepine (c) 2003
* Date  : 16.02.2003
* File  : lang.php
* Desc  : French language file
********************************************************/

/////////////////////////////////////////////////////////
// Character set required
/////////////////////////////////////////////////////////
$charset = "ISO8859-1";

/////////////////////////////////////////////////////////
// Menu variables
/////////////////////////////////////////////////////////
$Login_txt = 'Connexion';
$Username_txt = 'Pseudo';
$Password_txt = 'Mot de passe';
$Logon = 'Connexion';
$New_User = 'Nouvel utilisateur';
$Forgot_password = 'Mot de passe oublié';
$Home = 'Accueil';
$Menu = 'Menu';
$Prediction_Table = 'Tableau des pronostiques';
$Fixtures_Results = 'Matchs/Résultats';
$My_Predictions = 'Mes pronostiques';
$My_Profile = 'Mon profil';
$Logout = 'Sortir';
$Help = 'Aide';
$Email_Us = 'Nous contacter';
$Admin_Menu = 'Administration';
$Enter_Fixture = 'Entrer un match';
$Enter_Result = 'Entrer un résultat';
$Show_Users = 'Montrer les utilisateurs';
$Recalculate_Standings = 'Recalculer le classement';
$Email_All_Users  = 'Email de masse';
$View_Log = 'View Log';
/////////////////////////////////////////////////////////
// Create new user variables
/////////////////////////////////////////////////////////
$Cookie_read_test_error = 'Incapable de lire le cookie d essai en créant le 
nouvel utilisateur';
$Create_New_User = 'Créez un nouvel utilisateur';
$User_ID_Required = 'Veuillez entrer un pseudo';
$User_ID_too_long = 'Veuillez choisir un pseudo de moins de 32 caractères.';
$Passwords_is_required = 'Veuillez entrer un mot de passe';
$Passwords_do_not_match = 'Vos mots de passes sont différents.';
$Password_too_long = 'Veuillez choisir un mot de passe de moins de 32 
caractères.';
$Email_address_is_required = 'Veuillez entrer une adresse électronique.';
$Email_too_long = 'Veuillez entrer une adresse électronique comportant au 
maximum 60 caractères.';
$Email_address_is_not_valid = 'Veuillez entrer une adresse électronique 
valide.';
$User_Info = 'Profil utilisateur';
$User_ID = 'Pseudo';
$Select_user_ID = 'Veuillez choisir un pseudo.';
$Password_length = 'Votre mot de passe devra comporter au moins 5 
caractères.';
$Repeat_password = 'Veuillez retaper votre mot de passe';
$Email_Address_txt = 'Courriel';
$Enter_valid_email_address = 'Veuillez entrer une adresse électronique 
valide.';
$Icon = 'Icone';
$Select_icon = 'Choisissez une icone pour vous représenter.';
$Create = 'Créer';
// CreateUser.php
$Unable_to_register_txt = "Incapable d'enregistrer les variables de 
l'utilisateur pendant la création de $username : $User";
$Unable_to_register_3_attempts_txt = "Impossible d enregistrer les variables 
utilisateur pour $username échoué après 3 tentatives: $User";
$Successfully_registered = 'Création de votre profil '.$username.' avec 
succès';
$Username_already_exists = 'Cet utilisateur existe déjà, merci de choisir un 
autre pseudo';
$One_user_per_email = 'Un seul utilisateur par adresse email ($email) est 
autorisé.';
$Username_required = 'Pseudo requis, merci de choisir un nom';
$Password_required = 'Mot de passe requis, merci de choisir un mot de 
passe';
$Email_Answer_txt = "Bonjour ".$username.",\n\nBienvenue sur 
".$PredictionLeagueTitle.". Merci de nous rejoindre.\n\nVeuillez vérifier si 
vos détails sont corrects. Si vous désirez modifier votre profil, 
connectez-vous et modifiez votre profil.\n\nMot de passe = 
".$password."\nAdresse Email = ".$email."\n\nBonne Chance\n 
".$adminSignature;
$Email_Subject_txt = "Bienvenue sur $PredictionLeagueTitle";
/////////////////////////////////////////////////////////
// Change user variables
/////////////////////////////////////////////////////////
$My_Profile_for = 'Mon Profil :';
$change_icon = 'Changer';
$Join_Date = 'Date d inscription';
$Change_Profile = 'Changer votre profil';
$Change_Password = 'Changer de mot de passe';
$Old_Password_txt = 'Ancien mot de passe';
$Password_Again = 'Retaper le mot de passe';
$Change_Password_button = 'Changer de mot de passe';
// UserSelectIcon.php
$Click_on_the_Icon = 'Cliquez sur l icône que vous désirez employer';
$Profile_Icon_Selection = 'Sélection';
$Language = "Langue";
/////////////////////////////////////////////////////////
// Help file variables
/////////////////////////////////////////////////////////
$Prediction_League_Help_topic = 'Aide aux pronostiques';
$Creating_a_new_user = 'CREER UN NOUVEL UTILISATEUR';
$Creating_a_new_user_txt = 'Pour pouvoir participer aux pronostiques, vous 
devez vous créer un profil utilisateur. Pour ce faire, choisissez le lien 
<b>Nouvel utilisateur</b> dans la colonne de droite.  Veuillez vous assurer 
que les cookies sont acceptés par votre navigateur. Si ils ne le sont pas, 
vous serez redirigés sur cette page.
<br> <br>
Pour une simplification, veuillez choisir un même pseudo que sur le forum.
<br> <br>
Entrez un mot de passe (deux fois). Ceci afin de s assurer que personne ne 
pourra modifier vos pronostiques.
<br> <br>
Vous <b>devez</b> entrer un courriel valide afin de participer au concours 
et ce afin de recevoir votre mot de passe si vous l oubliez.';
$Cookies_txt = 'COOKIES';
$Cookies_txt_txt = 'Les Cookies <b>DOIVENT</b> être autorisés pour 
participer au concours.
<br><br>
Les problèmes courants dans l utilisation des cookies sont :
<ul>
<li> Redirection vers cette page lors de connexions.
<li> Que l on vous demande de vous identifier à nouveau lors de l entrée d 
une prévision.
<li> Icône non indiquée dans la gauche de l écran.
<li> Vous avez essayé de créer un nouvel utilisateur et avez été réorientés 
à cette page. Vous devez autoriser les cookies sur votre navigateur.
</ul>
Pour permettre des cookies dans Internet Explorer 6, allez dans le menu 
Outils >> Option Internet.  Choisissez l onglet confidentialité.  Placez la 
confidentialité à moyenne, basse.  Cliquez sur OK.  Quelques utilisateurs 
ont éprouvé des problèmes avec l arrangement moyen, vous pouvez devoir 
passer à 0.
<br>
Pour permettre des cookies dans Netscape 6, allez dans le menu Editer >> 
Préférence.  Choisissez l onglet confidentialité, puis cookies. Autorisez 
les cookies pour l URL des prédictions.  Cliquez sur OK.<br>';
$Scoring_txt = 'POINTS';
$Scoring_txt_txt = 'Les points sont distribués comme suit:
<ul>
<li>3 Points pour un score parfait. (p.e. Votre pronostique est 3-1 et le 
resultat est 3-1).
<li>1 Point pour un résultat correct. (p.e. Votre pronostique est 1-1 et le 
resultat est 2-2).
</ul>
Différentiel de buts
<ul>
<li>Pour chaque nombre correct de buts par équipe pronostiqué, vous recevez 
ce nombre de buts en points. (p.e. vous pronostiquez 2 buts et l équipe 
marque 2 fois, vous obtenez + 2).
<li>Pour chaque noimbre incorrect de buts par équipe pronostiqué, vous 
perdez la différence de buts en points. (p.e. vous pronostiquez 3 buts et l 
équipe marque 2 fois, vous obtenez - 1).
</ul>';


// Version 1.0 Additions
/////////////////////////////////////////////////////////
// Messaging variables
/////////////////////////////////////////////////////////
$Del = "Sup";
$Delete = "Supprimer";
$From = "De";
$To = "A";
$Date = "Date";
$Subject = "Sujet";
$My_Messages = "Mes Messages";
$My_Messages_For = "Mes messages pour";
$My_Predictions_For = "Mes pronostiques pour";
$Send_Msg = "[msg] ";
$Create_Msg = "Créer un message";
$Send_Msg_Button = "Envoyer un message";
$Message_txt = "Message";
$RE = "Re:";
$REM = "Sup";  // For removing messages
$Remove_All_Deleted = "Vider tous les messages supprimés";
$View_All_Messages = "Voir tous les messages";
$Status_Col = "Statut";

$Config_League = 'Configuration';
$Predictions = 'Pronostiques';
$Predict = 'Prédire';
$Next_Match = "Prochain Match";
$Prediction_League_Standings = 'Classement du concours de pronostiques';
$No_Matches_Scheduled = 'Pas de match prévu';
$New_Message = "Nouveau message";
$Subject_txt = "Sujet";
$User_Name = 'Pseudo';

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
$P = "J";  // Games played
$W = "G";  // Games won
$D = "N";  // Games drawn
$L = "P";  // Games lost
$F = "BP";  // Goals for
$A = "BC";  // Goals against
$GD = "GD";  // Goals difference
$POS = "Pos";  // Position
$PTS = "Pts";  // Points
$Result = "Result";
$Prediction_Stats = "Statistiques ";
$Download_Dbase = "Sauvegarder la base de donnée";
$Lang_Report_Bug = "Rapporter un bug";

// v1.07 additions
$Lang_Enable_Auto_Predictions = "Prédictions automatiques";
$Lang_Enable = "Activer";
$Lang_Enter_Outstanding_Results = "Entrer un résultat exceptionnel";
$Lang_Enter_Multiple_Fixtures = "Entrer des matchs multiples";

// v1.08 additions
$Predictions_For = "Prédictions pour";
$Game_Time = "Heure du match";

// v1.09 additions
$New_Message_Subject = "Vous avez reçu un nouveau message de 
$PredictionLeagueTitle";
$New_Message_Body = "Vous avez un nouveau message sur 
$PredictionLeagueTitle. Vous pouvez visionner votre message ici: 
http://$PLHome/";
$Games_Menu = "Selectionner une partie";
$Upload_Icon = "Uploader votre icone";
$Previous = "Suivant";
$Next = "Précédent";
$Upload_Instructions = "Essayer SVP de garder des dimensions raisonnables 
pour votre image afin de ne pas nuire a l affichage. La taille de votre 
image est limitée a $MaxIconFileSize Kb";
?>

