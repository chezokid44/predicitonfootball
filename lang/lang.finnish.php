<?php 
/*********************************************************
 * Author: Juuso Heinisuo (c) 2004
 * Date  : 25.01.2004
 * File  : lang.php
 * Desc  : Finnish language file
 ********************************************************/

 /////////////////////////////////////////////////////////
 // Character set required
 /////////////////////////////////////////////////////////
 $charset = "ISO8859-1";

 /////////////////////////////////////////////////////////
 // Menu variables
 /////////////////////////////////////////////////////////

$Login_txt = 'Kirjaudu';
$Username_txt = 'Tunnus';
$Password_txt = 'Salasana';
$Logon = 'Sisään';
$New_User = 'Uusi käyttäjä';
$Forgot_password = 'Unohtunut salasana';

$Home = 'Koti';

$Menu = 'Valikko';
$Prediction_Table = 'Veikkaustaulukko';
$Fixtures_Results = 'Ottelut/tulokset';
$My_Predictions = 'Omat veikkaukset';
$My_Profile = 'Oma profiili';
$Logout = 'Kirjaudu ulos';

$Help = 'Ohje';
$Email_Us = 'Mailaa meille';

$Admin_Menu = 'Admin Valikko';
$Enter_Fixture = 'Lisää ottelu';
$Enter_Result = 'Lisää tulos';
$Show_Users = 'Näytä käyttäjä';
$Recalculate_Standings = 'Päivitä tulostaulukko';
$Email_All_Users  = 'Lähetä sähköposti kaikkille käyttäjille ';
$View_Log = 'Katso lokia';

 /////////////////////////////////////////////////////////
 // Create new user variables
 /////////////////////////////////////////////////////////

$Cookie_read_test_error = 'Järjestelmä ei pystynyt lukemaan testi evästettä uutta käyttäjää luodessa.';
$Create_New_User = 'Luo uusi käyttäjä';
$User_ID_Required = 'Käyttäjätunnus vaadittu';
$User_ID_too_long = 'Käyttäjätunnus liian pitkä. Se voi olla korkeintaan 32 merkkiä.';
$Passwords_is_required = 'Salasana vaadittu';
$Passwords_do_not_match = 'Salasanat eivät täsmää';
$Password_too_long = 'Salasana liian pitkä. Se voi olla korkeintaan 32 merkkiä.';
$Email_address_is_required = 'Sähköposti vaadittu';
$Email_too_long = 'Sähköpostiosoite liian pitkä. Se voi olla korkeintaan 60 merkkiä.';
$Email_address_is_not_valid = 'Sähköpostiosoite ei ole oikeaa muotoa.';
$User_Info = 'Käyttäjätiedot';
$User_ID = 'Käyttäjätunnus';
$Select_user_ID = 'Valitse käyttäjätunnus.';
$Password_length = 'Salasanan täytyy olla vähintään 5 merkkiä pitkä.';
$Repeat_password = 'Toista salasana.';
$Email_Address_txt = 'Sähköposti';
$Enter_valid_email_address = 'Syötä toimiva sähköpostiosoiteesi.';
$Icon = 'Ikoni';
$Select_icon = 'Valitse käyttäjätunnustasi vastaava ikoni';
$Create = 'Luo';

// CreateUser.php

$Unable_to_register_txt = "Rekisteröinti epäonnistui $username : $User";
$Unable_to_register_3_attempts_txt = "Rekisteröinti epäonnistui $username Syy: 3 yritystä käyttäjällä: $User";
$Successfully_registered = 'Käyttäjätunnus $username luotu';
$Username_already_exists = 'Tunnus $username on jo olemassa. Valitse toinen';
$One_user_per_email = 'Vain yksi käyttäjä per sähköpostiosoite ($email) sallittu.';
$Username_required = 'Käyttäjätunnus pakollinen, valitse käyttäjätunnus';
$Password_required = 'Salasana pakollinen, valitse salasana';
$Email_Answer_txt = "Terve ".$username.",\n\nTervetuloa ".$PredictionLeagueTitle.". Kiitos liittymisestäsi.\n\nTarkista, että tietosi ovat oikein. Jos on jotain korjattavaa, niin kirjaudu sisään ja muokkaa profiiliasi.\n\nSalasana = ".$password."\nSähköposti = ".$email."\n\nOnnea veikkauksiin!\n ".$adminSignature;
$Email_Subject_txt = "Tervetuloa: $PredictionLeagueTitle";

 /////////////////////////////////////////////////////////
 // Change user variables
 /////////////////////////////////////////////////////////

$My_Profile_for = 'Profiilini';
$change_icon = 'vaihda';
$Join_Date = 'Kirjautumispäivä';
$Change_Profile = 'Vaihda profiilia';
$Change_Password = 'Vaihda salasana';
$Old_Password_txt = 'Vanha salasana';
$Password_Again = 'Uudestaan';
$Change_Password_button = 'Vaihda salasana';

// UserSelectIcon.php

$Click_on_the_Icon = 'Klikkaa ikonia, jota haluat käyttää';
$Profile_Icon_Selection = 'Ikonin valinta';
$Language = "Kieli";

 /////////////////////////////////////////////////////////
 // Help file variables
 /////////////////////////////////////////////////////////

$Prediction_League_Help_topic = 'Prediction League - käyttöohje';
$Creating_a_new_user = 'Uuden käyttäjän luominen';

$Creating_a_new_user_txt = '          Osallistuaksesi Prediction Liigaan luo käyttäjäprofiili.
Käyttäjäprofiili luodaan vasemalla puolella olevasta linkistä <b>Uusi käyttäjä</b>.
Varmistu, että internet-selaimessa on cookie-tuki päällä.
Jos cookie-tuki ei ole päällä, näet tämän sivun.          
 <br> <br>
Valitse käyttätunnus.
Käyttäjätunnus voi olla mikä tahansa, kunhan toisella käyttäjällä ei ole sama tunnus.
          <br> <br>
		  Kirjoita salasana (kahdesti). Salasanalla varmistetaan, ettei kuka tahansa voi muuttaa veikkauksia.
          <br> <br>
		  Valitse myös käytössä oleva sähköpostiosoite. Sähköpostiosoite on pakollinen osallistuaksesi Prediction Liigaan.
		  Sähköpostiosoitteella saat myös unohtuneen salasanan';

$Cookies_txt = 'Cookie-tuki';

$Cookies_txt_txt = '          Cookie-tuen <b>on oltava päällä</b>.
          <br><br>
		  Yleiset ongelmat, jos Cookie-tuki ei ole päällä:

          <ul>
          <li>Tämä sivu näkyy kirjautuessasi sisään.
          <li>Käyttäjätunnusta kysytään uudestaan suorittaessasi veikkausta.
          <li>Ikoni ei näy vasemmalla puolella.
		 
          <li> Luodessasi uutta käyttäjä näet tämän sivun. Laita cookie-tuki päälle.
          </ul>
		  Cookie-tuki asetetaan <b>Internet Explorer 6</b> päälle seuraavasti: Valikosta Tools->Internet Options, valitse Privacy-välilehti. Aseta Privacy Medium tai alemmalle tasolle. Paina OK. Joillankin käyttäjillä on ollut ongelmia Medium valinnalla. Laita Cookie-tuki vielä alemmalle.<br>
	<br>	  
	Cookie-tuki asetetaan <b>Netscape 6</b> päälle seuraavasti: Valikosta Edit->Preferences, valitse Privacy & tab ja valitse cookies. Valitse Cookies tälle sivustolle tai mahdollista kaikki cookiet. Paina OK.<br>';

$Scoring_txt = 'Pisteet';

$Scoring_txt_txt = '          Pisteet annetaan seuraavasti:
          <ul>
          <li><b>3</b> Pistettä täysin oikeasta tuloksesta (maalit mukaan lukien). Esimerkiksi veikkaat, että ottelu päättyy 3-1 ja peli päättyy 3-1. 
          <li><b>1</b> Piste oikeasta tuloksesta. Veikkaat, että ottelu päättyy 1-1 ja oikea tulos on 2-2.
          </ul>
          Maalierot
          <ul>
		  
          <li>Jokaisesta oikeasta maalimäärästä saat <b>+</b> maaleja. Esimerkkiksi veikkaat, että joukkue tekee 2 maalia ja joukkue tekee 2 maalia. Saat tällöin +2 maalia.
          <li>Jokaisesta väärästä maalimäärästä saat <b>-</b> maaleja. Esimerkkiksi veikkaat, että joukkue tekee 3 maalia ja joukkue tekee 2 maalia. Saat tällöin -1 maalia.
          </ul>';

// Version 1.0 Additions
 /////////////////////////////////////////////////////////
 // Messaging variables
 /////////////////////////////////////////////////////////
$Del = "Poista";
$Delete = "Poista";
$From = "From";
$To = "To";
$Date = "Päivä";
$Subject = "Aihe";
$My_Messages = "Viestini";
$My_Messages_For = "Viestini";
$My_Predictions_For = "Veikkaukseni";
$Send_Msg = "[msg] ";
$Create_Msg = "Lähetä viesti";
$Send_Msg_Button = "Lähetä viesti";
$Message_txt = "Viesti";
$RE = "Re:";
$REM = "Rem";  // For removing messages
$Remove_All_Deleted = "Poista kaikki hävitetyt viestit";
$View_All_Messages = "Katso kaikki viestit";
$Status_Col = "Status";

$Config_League = 'Konfiguroi Liiga';
$Predictions = 'Veikkaukset';
$Predict = 'Veikkaa';
$Next_Match = "Seuraava ottelu";
$Prediction_League_Standings = 'Prediction Liigan sarjataulukko';
$No_Matches_Scheduled = 'Ei otteluita';
$New_Message = "Uusi viesti";
$Subject_txt = "Aihe";
$User_Name = 'Käyttäjänimi';
  

// v1.03 addition
$Months = array('01'=>"tammi",
             '02'=>"helmi",
             '03'=>"maalis",
             '04'=>"huhti",
             '05'=>"touko",
             '06'=>"kesä",
             '07'=>"heinä",
             '08'=>"elo",
             '09'=>"syys",
             '10'=>"loka",
             '11'=>"marras",
             '12'=>"joulu");
$Set_Defaults = "Aseta perusasetukset";

// v1.06 additions
$FixturesResults = "Tilastot/Tulokset";
$Away = "Vieras"; // Away team title
$P = "Ott";  // Games played
$W = "V";  // Games won
$D = "T";  // Games drawn
$L = "H";  // Games lost
$F = "TM";  // Goals for
$A = "PM";  // Goals against
$GD = "GD";  // Goals difference
$POS = "Sij.";  // Position
$PTS = "Pis";  // Points
$Result = "Tulos";
$Prediction_Stats = "Veikkaustilastot";
$Download_Dbase = "Ota varmuuskopio tietokannasta";
$Lang_Report_Bug = "Ilmoita virheestä";

// v1.07 additions
$Lang_Enable_Auto_Predictions = "Automaattinen veikkaus";
$Lang_Enable = "Mahdollista";
$Lang_Enter_Outstanding_Results = "Lisää ottelun tulos";
$Lang_Enter_Multiple_Fixtures = "Lisää useampi ottelu";

// v1.08 additions
$Predictions_For = "Veikkaukset ottelulle";
$Game_Time = "Ottelun aika";

// v1.09 additions
$New_Message_Subject = "Sinulle on uusi viesti $PredictionLeagueTitle";
$New_Message_Body = "Sinulle on uusi viesti $PredictionLeagueTitle. Voit lukea viestin täältä http://$PLHome/";
$Games_Menu = "Valitse ottelu";
$Upload_Icon = "Lataa ikoni";
$Previous = "Edellinen";
$Next = "Seuraava";
$Upload_Instructions = "Pidä kuvan koko näytölle sopivana. Kuvasi koko on rajoitettu $MaxIconFileSize Kb";
?>
