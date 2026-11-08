<?php
/*********************************************************
 * Author: John Astill (c)
 * Date  : 20th March 2003
 * File  : adminmenus.php
 *********************************************************/
?>
<!-- Menu -->
<table class="MENUTB">
<tr>
<td align="center" class="LOGINHD">
<font class="LOGINHD">
<?php echo $Admin_Menu." [$leagueID]\n";?>
</font>
</td>
</tr>
<tr>
<td class="TBLROW">
<a href="adminenterfixture.php?sid=<?php echo $SID?>"><?php echo $Enter_Fixture;?></a><br>
<a href="adminentermultfixtures.php?sid=<?php echo $SID?>"><?php echo $Lang_Enter_Multiple_Fixtures;?></a><br>
<a href="adminenterresult.php?sid=<?php echo $SID?>"><?php echo $Enter_Result;?></a><br>
<a href="adminenteroutstandingresults.php?sid=<?php echo $SID?>"><?php echo $Lang_Enter_Outstanding_Results;?></a><br>
<a href="showusers.php?sid=<?php echo $SID?>"><?php echo $Show_Users;?></a><br>
<?php if ($LockedGame == "TRUE") { ?>
<a href="createnewuser.php?sid=<?php echo $SID;?>"><?php echo $New_User; ?></a><br>
<?php } ?>
<a href="updatestandings.php?sid=<?php echo $SID?>"><?php echo $Recalculate_Standings;?></a><br> 
<a href="emailallusers.php?sid=<?php echo $SID?>"><?php echo $Email_All_Users;?></a><br>
<?php if ($useMessaging == "TRUE") { ?>
<a href="viewallmessages.php?sid=<?php echo $SID?>"><?php echo $View_All_Messages;?></a><br>
<?php
  }
  if ($logfile != "") {
?>
<a href="viewlog.php?sid=<?php echo $SID?>"><?php echo $View_Log;?></a><br>
<?php
  }
?>
<a href="adminconfigleague.php?sid=<?php echo $SID?>"><?php echo $Config_League;?></a><br>
<a href="mailto:bugs@predictionfootball.com?subject=Prediction Football <?php echo VERSION ?> Bug Report for <?php echo $_SERVER["HTTP_HOST"] ?>"><?php echo $Lang_Report_Bug;?></a>
<!--
<a href="admindownloaddbase.php"><?php echo $Download_Dbase;?></a>
-->
</td>
</tr>
</table>
<!-- End Menu -->
