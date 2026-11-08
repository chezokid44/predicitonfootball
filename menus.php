<?php 
/*********************************************************
 * Author: John Astill
 * Date  : 9th December
 * File  : menus.php
 ********************************************************/
  /*******************************************************
  * Check the user id and password from the cookie.
  *******************************************************/
  $isAdmin =  CheckAdmin($User->usertype);

require "msgfunctions.php";
require ("lang/".GetLangFile());

?>
<script>
<!--
function setGameDate(date, serveroffset){
  var old = document.getElementById("SERVERTIME");
  var bodyRef = document.getElementById("SERVER");
  var newInput = document.createElement("div");
  var bold = document.createElement("b");
  var newText = document.createTextNode(date);
  //var newText = document.createTextNode("Server Side Time "+date);
  newInput.setAttribute("id","SERVERTIME");
  bold.appendChild(newText);
  newInput.appendChild(bold);
  bodyRef.replaceChild(newInput,old);  
  
  // Wait one minute
  setTimeout("document.frames[0].location.reload()",60000);
  return false;
}
//-->
</script>
<!-- Menu -->
<table class="MENUTB">
<!-- Show the game time. -->
<tr>
<td align="center" class="LOGINHD">
<font class="LOGINHD">
<?php echo $Game_Time."\n";?>
</font>
</td>
</tr>
<tr>
<td align="center" class="TBLROW">
<font class="TBLROW">
<iframe id="SERVERFRAME" name="SERVERFRAME" style="width:0px; height:0px;border:0px" src="getgametime.php"></iframe>

<div id="SERVER" name="SERVER">
<div id="SERVERTIME" name="SERVERTIME">
XX:XX
</div>
</div>

</font>
</td>
</tr>

<?php
  if (($homePage != "" and $homePageTitle != "") or $chatRoomURL != "") { 
?>
<tr>
<td align="center" class="LOGINHD">
<font class="LOGINHD">
<?php echo $Home."\n";?>
</font>
</td>
</tr>
<?php
}
?>
<tr>
<td class="TBLROW">
<!-- Home Page -->
<?php 
  if ($homePage != "" and $homePageTitle != "") {
    echo "<a href=\"$homePage\">$homePageTitle</a><br>";
  }
?>
<?php 
  if ($chatRoomURL != "") { 
?>
<a href="<?php echo $chatRoomURL ?>" target="_NewChatEnglFC">Chat Room</a><br>
<?php 
  } 
?>
</td>
</tr>
</table>

<?php
  if ($User != 0 && $User->loggedIn == TRUE) {
?>
    <table class="MENUTB">
    <tr>
    <td align="center" class="LOGINHD">
    <font class="LOGINHD">
    [<?php echo " $User->username ]"; ?>
    </font>
    </td>
    </tr>
    <tr>
    <td align="center" valign="middle" class="TBLROW">
      <a href="userselecticon.php?sid=<?php echo $SID?>">
      <img border="0" alt="<?php echo $User->icon; ?>" src="<?php echo $User->icon?>">
      </a>
    </td>
    </tr>
    </table>
<?php
  }
?>

<table class="MENUTB">
<tr>
<td align="center" class="LOGINHD">
<font class="LOGINHD">
<?php echo $Menu."\n";?>
</font>
</td>
</tr>
<tr>
<td class="TBLROW">
<a href="index.php<?php echo "?sid=$SID"?>"><?php echo $Prediction_Table;?></a><br>
<a href="showmatchresults.php<?php echo "?sid=$SID"?>"><?php echo $Fixtures_Results;?></a><br>

<?php 
  // Only show these if the user is loged on
  if ($User->loggedIn == TRUE) {
?>
<a href="showmypredictions.php<?php echo "?sid=$SID"?>"><?php echo $My_Predictions;?></a><br>
<a href="showmyprofile.php<?php echo "?sid=$SID"?>"><?php echo $My_Profile;?></a><br>
<a href="logout.php<?php echo "?sid=$SID"?>"><?php echo $Logout;?></a>

<?php 
  // Only show this if messaging enabled
  if ($useMessaging == "TRUE") {
?>
<br><a href="showmymessages.php<?php echo "?sid=$SID"?>"><?php echo $My_Messages.NewMail($User->userid);?></a>

<?php
  }
  }
?>
</td>
</tr>
<tr>
<td class="TBLROW">
<a href="helpindex.php<?php echo "?sid=$SID"?>"><?php echo $Help;?></a><br>
<a href="mailto:<?php echo $adminEmailAddr?>?subject=PredictionLeague"><?php echo $Email_Us;?></a>

</td>
</tr>
</table>

<?php 
  // If the user is an administrator, show the admin index.
  if($isAdmin) {
    require "adminmenus.php";
  }
?>
<!-- The display of powered by prediction football must not be removed without explicit permission. -->
<table class="MENUTB">
<tr>
<td class="TBLROW">
<font class="VERSION">
Powered by</small><br><a class="VERSION" href="<?php echo $PLHome?>">
<small>Prediction Football </a><small><?php echo VERSION ?></small>
</font>
</td>
</tr>
</table>
<!-- End Menu -->
