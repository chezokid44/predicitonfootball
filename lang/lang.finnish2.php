<?php 
/*********************************************************
 * Author: Tuomo Aura (c) 2002
 * Date  : 21.11.2002
 * File  : lang.php
 * Desc  : Finnish language file
 * Edit  : Kimmo Helle (c) 6.8.2003
 ********************************************************/

 /////////////////////////////////////////////////////////
 // Character set required
 /////////////////////////////////////////////////////////
 $charset = "ISO8859-1";

 /////////////////////////////////////////////////////////
 // Menu variables
 /////////////////////////////////////////////////////////

$Login_txt = 'Kirjaudu';
$Username_txt = 'K‰ytt‰j‰tunnus';
$Password_txt = 'Salasana';
$Logon = 'Sis‰‰n';
$New_User = 'Uusi k‰ytt‰j‰';
$Forgot_password = 'Unohtunut salasana';

$Home = 'Koti';

$Menu = 'Valikko';
$Prediction_Table = 'Arvaustaulukko';
$Fixtures_Results = 'Ottelut/tulokset';
$My_Predictions = 'Omat arvaukset';
$My_Profile = 'Oma profiili';
$Logout = 'Kirjaudu ulos';

$Help = 'Ohje';
$Email_Us = 'E-mail';

$Admin_Menu = 'Admin Valikko';
$Enter_Fixture = 'Lis‰‰ ottelu';
$Enter_Result = 'Lis‰‰ tulos';
$Show_Users = 'N‰yt‰ k‰ytt‰j‰';
$Recalculate_Standings = 'P‰ivit‰ tulostaulukko';
$Email_All_Users  = 'Mailaa kaikille k‰yt. ';
$View_Log = 'Katso lokia';

 /////////////////////////////////////////////////////////
 // Create new user variables
 /////////////////////////////////////////////////////////

$Cookie_read_test_error = 'J‰rjestelm‰ ei pystynyt lukemaan testi ev‰stett‰ uutta k‰ytt‰j‰‰ luodessa.';
$Create_New_User = 'Luo uusi k‰ytt‰j‰';
$User_ID_Required = 'K‰ytt‰j‰tunnus vaadittu';
$User_ID_too_long = 'K‰ytt‰j‰tunnus liian pitk‰. Se voi olla korkeintaan 32 merkki‰.';
$Passwords_is_required = 'Salasana vaadittu';
$Passwords_do_not_match = 'Salasanat eiv‰t t‰sm‰‰';
$Password_too_long = 'Salasana liian pitk‰. Se voi olla korkeintaan 32 merkki‰.';
$Email_address_is_required = 'S‰hkˆposti vaadittu';
$Email_too_long = 'S‰hkˆpostiosoite liian pitk‰. Se voi olla korkeintaan 60 merkki‰.';
$Email_address_is_not_valid = 'S‰hkˆpostiosoite ei ole oikeaa muotoa.';
$User_Info = 'K‰ytt‰j‰tiedot';
$User_ID = 'K‰ytt‰j‰tunnus';
$Select_user_ID = 'Valitse k‰ytt‰j‰tunnus.';
$Password_length = 'Salasanan t‰ytyy olla v‰hint‰‰n 5 merkki‰ pitk‰.';
$Repeat_password = 'Toista salasana.';
$Email_Address_txt = 'S‰hkˆposti';
$Enter_valid_email_address = 'Syˆt‰ toimiva s‰hkˆpostiosoiteesi.';
$Icon = 'Ikoni';
$Select_icon = 'Valitse k‰ytt‰j‰tunnustasi vastaava ikoni';
$Create = 'Luo';

// CreateUser.php

$Unable_to_register_txt = "J‰rjestelm‰ ei pysty rekisterˆim‰‰n k‰ytt‰j‰ muuttujaa luonnin yhteydess‰ tunnuksille: $username : $User";
$Unable_to_register_3_attempts_txt = "J‰rjestelm‰ ei pysty rekisterˆim‰‰n k‰ytt‰j‰ muuttujaa luonnin yhteydess‰ tunnuksille: $username ep‰onnistui kolmen yrityksen j‰lkeen: $User";
$Successfully_registered = 'K‰ytt‰j‰tunnus $username luotu';
$Username_already_exists = 'Tunnus $username on jo olemassa. Valitse toinen';
$One_user_per_email = 'Vain yksi k‰ytt‰j‰ per s‰hkˆpostiosoite ($email) sallittu.';
$Username_required = 'K‰ytt‰j‰tunnus pakollinen, valitse k‰ytt‰j‰tunnus';
$Password_required = 'Salasana pakollinen, valitse salasana';
$Email_Answer_txt = "Terve ".$username.",\n\nTervetuloa ".$PredictionLeagueTitle.". Kiitos liittymisest‰si.\n\nTarkista, ett‰ tietosi ovat oikein. Jos on jotain korjattavaa, niin kirjaudu sis‰‰n ja muokkaa profiiliasi.\n\nSalasana = ".$password."\nS‰hkˆposti = ".$email."\n\nOnnea veikkauksiin!\n ".$adminSgnature;
$Email_Subject_txt = "Tervetuloa: $PredictionLeagueTitle";

 /////////////////////////////////////////////////////////
 // Change user variables
 /////////////////////////////////////////////////////////

$My_Profile_for = 'Profiilini';
$change_icon = 'muuta';
$Join_Date = 'Liittymisp‰iv‰';
$Change_Profile = 'Muuta profiilia';
$Change_Password = 'Muuta salasanaa';
$Password_Again = 'Uudestaan';
$Change_Password_button = 'Muuta salasana';

// UserSelectIcon.php

$Click_on_the_Icon = 'Klikkaa ikonia, jota haluat k‰ytt‰‰';
$Profile_Icon_Selection = 'Ikoni valikoima';


 /////////////////////////////////////////////////////////
 // Help file variables
 /////////////////////////////////////////////////////////

$Prediction_League_Help_topic = 'Arvausliigan ohjeet';
$Creating_a_new_user = 'Uuden k‰ytt‰j‰n luonti';

$Creating_a_new_user_txt = '          Osallistuaksei arvausliigaan, sinun t‰ytyy luoda uusi profiili. T‰m‰ onnistuu valitsemalla <b>Uusi k‰ytt‰j‰</b> linkin oikean puoleisesta valikosta. Varmista, ett‰ ev‰steet (cookies) on p‰‰ll‰ selaimessasi. Mik‰li ev‰steet ovat poissa p‰‰lt‰, sinut uudelleen ohjataan t‰lle sivulle.
          <br> <br>
          Valitse itsellesi oma k‰ytt‰j‰tunnus. Se voi olla mik‰ tahansa, kunhan jollain toisella k‰ytt‰j‰ll‰ ei ole samaa tunnusta.
          <br> <br>
          Valitse salasani (kahdesti). T‰m‰ on t‰rke‰‰, mik‰li et halua jonkun toisen muuttavan arvauksiasi.
          <br> <br>
          Sinun <b>t‰ytyy</b> laittaa voimassa oleva s‰hkˆpostiosoite liitty‰ksesi arvausliigaan. T‰t‰ k‰ytet‰‰n mm. salasanasi postittamiseen, mik‰li unohdat sen.';

$Cookies_txt = 'Cookies';

$Cookies_txt_txt = '          Ev‰steet <b>t‰ytyy</b> olla p‰‰ll‰, jotta voit osallistua arvausliigaan.
          <br><br>
          Yleisimpi‰ ongelmia, kun ev‰steet eiv‰t ole p‰‰ll‰:
          <ul>
          <li>T‰m‰n sivun n‰kyminen, kun yrit‰t kirjautua sis‰‰n.
          <li>Arvausta syˆtt‰ess‰si sinulta kysyt‰‰n k‰ytt‰j‰tunnusta.
          <li>Ikoni ei n‰y vasemmassa reunassa.
          <li>Yritit luoda k‰ytt‰j‰tunnuksen ja jouduit t‰lle sivulle. Sinun t‰ytyy laittaa ev‰steet p‰‰lle.
          </ul>
          Laittaaksesi ev‰steet p‰‰lle Internet Explorer 6:ssa, seuraa "reitti‰" Tyˆkalut->Internet-asetukset (Tools->Internet Options). Valitse Tietosuoja-valikko (Privacy tab). Laita tasoksi normaali tai pieni (medium / lower). Klikkaa OK. Joillakin k‰ytt‰jill‰ on ollut vaikeuksia normaalin tason kanssa, joten sinunkin saattaa t‰yty‰ laittaa tasoksi pieni.<br>
          Laittaaksesi ev‰steet p‰‰lle Netscape 6:ssa, seuraa "reitti‰" Muokkaa->Asetukset (Edit->Preferences). Valitse Tietosuoja-valikko (Privacy & tab) ja valitse ev‰steet (cookies). Valitse vapauta ev‰steet (Enable Cookies) vain t‰lle sivulle tai kaikille sivuille. Klikkaa OK.<br>';

$Scoring_txt = 'Pisteytys';

$Scoring_txt_txt = '          Pisteit‰ annetaan seuraavasti:
          <ul>
          <li>3 pistett‰ oikeasta lopputuloksesta. Esim. arvaat lopputulokseksi 3-1 ja lopputulos on 3-1.
          <li>1 piste oikeasta tuloksesta. Esim. arvaat 1-1 ja tulos on 2-2.
          </ul>
          Maaliero
          <ul>
          <li>Jokaisesta oikeasta maalim‰‰r‰st‰ saat sen m‰‰r‰n +maaleja. Esim. Arvaat joukkueelle 2 maalia ja joukkue tekee kaksi maalia, sin‰ saat +2 maalia.
          <li>Jokaisesta v‰‰r‰st‰ maalim‰‰r‰st‰ saat sen m‰‰r‰n -maaleja. Esim. Arvaat joukkueelle 3 maalia ja joukkue tekee kaksi maalia, sin‰ saat -1 maalia.
          </ul>';

// Version 1.0 Additions
 /////////////////////////////////////////////////////////
 // Messaging variables
 /////////////////////////////////////////////////////////
$Del = "Poista";
$Delete = "Poista";
$From = "Kenelt‰";
$To = "Kenelle";
$Date = "P‰iv‰m‰‰r‰";
$Subject = "Aihe";
$My_Messages = "Omat viestit";
$My_Messages_For = "Viestini";
$My_Predictions_For = "Arvaukseni";
$Send_Msg = "[viesti] ";
$Create_Msg = "Luo viesti";
$Send_Msg_Button = "L‰het‰ viesti";
$Message_txt = "Viesti";
$RE = "Vs:";
$REM = "Poista";  // For removing messages
$Remove_All_Deleted = "H‰vit‰ kaikki poistetut viestit";
$View_All_Messages = "N‰yt‰ kaikki viestit";
$Status_Col = "Tila";

$Config_League = 'Konfiguroi liigaa';
$Predictions = 'Arvaukset';
$Predict = 'Arvaa';
$Next_Match = "Seuraava ottelu";
$Prediction_League_Standings = 'Arvausliigan sarjataulukko';
$No_Matches_Scheduled = 'Ei tulevia otteluita';
$New_Message = "Uusi viesti";
$Subject_txt = "Aihe";
$User_Name = 'K‰ytt‰j‰nimi';
  

// v1.03 addition
$Months = array('01'=>"Tam",
             '02'=>"Hel",
             '03'=>"Maa",
             '04'=>"Huh",
             '05'=>"Tou",
             '06'=>"Kes",
             '07'=>"Hei",
             '08'=>"Elo",
             '09'=>"Syy",
             '10'=>"Lok",
             '11'=>"Mar",
             '12'=>"jou");
$Set_Defaults = "Default config values";

// v1.06 additions
$FixturesResults = "Ottelut/tulokset";
$Away = "Vieras"; // Away team title
$P = "O";  // Games played
$W = "V";  // Games won
$D = "T";  // Games drawn
$L = "H";  // Games lost
$F = "TM";  // Goals for
$A = "PM";  // Goals against
$GD = "+/-";  // Goals difference
$POS = "Sij";  // Position
$PTS = "Pis";  // Points
$Result = "Tulos";
$Prediction_Stats = "Arvaus tilastot";
$Download_Dbase = "Lataa backup";
$Lang_Report_Bug = "Ilmoita virheest‰";

// v1.07 additions
$Lang_Enable_Auto_Predictions = "Automaattiset arvaukset";
$Lang_Enable = "P‰‰ll‰";
$Lang_Enter_Outstanding_Results = "Lis‰‰ tuloksia";
$Lang_Enter_Multiple_Fixtures = "Lis‰‰ useita otteluita";

// v1.08 additions
$Predictions_For = "Arvaukset";
$Game_Time = "Otteluaika";

// v1.09 additions
$New_Message_Subject = "Olet saanut uuden $PredictionLeagueTitle viestin";
$New_Message_Subject = "Sinulla on uusi $PredictionLeagueTitle viesti. Voit lukea viestin t‰‰ll‰ http://$PLHome/";
$Games_Menu = "Valitse ottelu";
$Upload_Icon = "Lataa ikoni";
$Previous = "Edel.";
$Next = "Seur.";
$Upload_Instructions = "Yrit‰ pit‰‰ kuvan koko kohtuullisen pienen‰. Tiedoston koko on rajoitettu $MaxIconFileSize kilotavuun.";
?>

