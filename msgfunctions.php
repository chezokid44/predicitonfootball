<?php 
/*********************************************************
 * Author: John Astill (c) 2003
 * Date  : 30 January 2003
 * File  : msgfunctions.php
 ********************************************************/

/******************************************************
 * Determine if there are any new messages for the user.
 ******************************************************/
  function NewMail($uid) {
    global $dbaseMsgData, $leagueID;
    $link = OpenConnection();

    $query = "select sender,msgtime,subject from $dbaseMsgData where receiver=? and status=? and lid=?";
    $result = DBExecute($query, array((int)$uid, "new", (int)$leagueID));
    if ($result === FALSE) {
      die("Unable to read message status: ".mysqli_error($link));
    }
    if (mysql_num_rows($result) > 0) {
      return "<img border=\"0\" src=\"img/emailiconnew18x12.gif\" width=\"18\" height=\"12\"";
    }
    CloseConnection($link);
  }

/******************************************************
 * Show all messages
 ******************************************************/
  function ShowAllMessages() {
    global $dbaseMsgData, $dbaseUserData, $SID, $REM,$Remove_All_Deleted, $Status_Col,$From,$To,$Date,$Subject, $leagueID;
    $link = OpenConnection();

    $query = "select $dbaseUserData.username, sender, receiver, msgid, subject, msgtime, body, status, receiverdata.username as recvuser from $dbaseMsgData inner join $dbaseUserData on $dbaseUserData.userid=$dbaseMsgData.sender and $dbaseUserData.lid=$dbaseMsgData.lid inner join $dbaseUserData as receiverdata on receiverdata.userid=$dbaseMsgData.receiver and $dbaseUserData.lid=receiverdata.lid where $dbaseUserData.lid='$leagueID'";
    
    $result = mysql_query($query) 
      or die("Unable to access database: $query<br>\n".mysql_error());
    $count = mysql_num_rows($result);

    echo "<form method=\"POST\" action=\"removealldeletedmsgs.php\">";
    echo "<table class=\"MSGTB\">";
    echo "<tr>";
    echo "<td class=\"TBLHEAD\" width=\"80\">";
    echo "<font class=\"TBLHEAD\">";
    echo "$From\n";
    echo "</font>";
    echo "<td class=\"TBLHEAD\" width=\"80\">";
    echo "<font class=\"TBLHEAD\">";
    echo "$To\n";
    echo "</font>";
    echo "</td>";
    echo "<td class=\"TBLHEAD\" width=\"80\">";
    echo "<font class=\"TBLHEAD\">";
    echo "$Date\n";
    echo "</font>";
    echo "</td>";
    echo "<td class=\"TBLHEAD\">";
    echo "<font class=\"TBLHEAD\">";
    echo "$Subject\n";
    echo "</font>";
    echo "</td>";
    echo "<td class=\"TBLHEAD\">";
    echo "<font class=\"TBLHEAD\">";
    echo "$Status_Col\n";
    echo "</font>";
    echo "</td>";
    echo "</tr>";
    while ($line = mysql_fetch_array($result)) {
      $sender = $line["username"];
      $senderid = $line["sender"];
      $receiverid = $line["receiver"];
      $recvuser = $line["recvuser"];
      $msgid = $line["msgid"];
      $subject = $line["subject"];
      $msgdate = GetDateFromTimestamp($line["msgtime"]);
      $body = $line["body"];
      $status = $line["status"];

      echo "<tr>";
      echo "<td class=\"TBLROW\">";
      echo "<font class=\"TBLROW\">";
      echo "<a href=\"showmsg.php?sid=$SID&msgid=$msgid&fs=X\">$sender</a>\n";
      echo "</font>";
      echo "</td>";
      echo "<td class=\"TBLROW\">";
      echo "<font class=\"TBLROW\">";
      echo "$recvuser\n";
      echo "</font>";
      echo "</td>";
      echo "<td class=\"TBLROW\">";
      echo "<font class=\"TBLROW\">";
      echo "$msgdate\n";
      echo "</font>";
      echo "</td>";
      echo "<td class=\"TBLROW\">";
      echo "<font class=\"TBLROW\">";
      echo "$subject\n";
      echo "</font>";
      echo "</td>";
      echo "<td class=\"TBLROW\">";
      echo "<font class=\"TBLROW\">";
      echo $status;
      echo "</font>";
      echo "</td>";
      echo "</tr>";

    }
    if ($count > 0) {
      echo "<tr>";
      echo "<td class=\"TBLROW\" align=\"CENTER\" colspan=\"5\">";
      echo "<font class=\"TBLROW\">";
      echo "<input type=\"submit\" value=\"$Remove_All_Deleted\" name=\"DELETE\">";
      echo "</font>";
      echo "</td>";
      echo "</tr>";
    }

    echo "</table>";
    echo "</form>";

    CloseConnection($link);
  }

/******************************************************
 * Show the messages for the given user
 ******************************************************/
  function ShowUserMessages($uid) {
    global $dbaseMsgData, $dbaseUserData,$SID,$From,$Date,$Subject,$Del,$Delete, $leagueID;
    $link = OpenConnection();

    $query = "select * from $dbaseMsgData inner join $dbaseUserData on $dbaseUserData.userid=$dbaseMsgData.sender and $dbaseUserData.lid=$dbaseMsgData.lid where receiver=? and status<>? and $dbaseUserData.lid=?";
    
    $result = DBExecute($query, array((int)$uid, "deleted", (int)$leagueID));
    if ($result === FALSE) {
      die("Unable to read messages: ".mysqli_error($link));
    }
    $count = mysql_num_rows($result);

    echo "<form method=\"POST\" action=\"deletemsgs.php\">";
    echo "<table class=\"MSGTB\">";
    echo "<tr>";
    echo "<td class=\"TBLHEAD\" width=\"80\">";
    echo "<font class=\"TBLHEAD\">";
    echo "$From\n";
    echo "</font>";
    echo "</td>";
    echo "<td class=\"TBLHEAD\" width=\"80\">";
    echo "<font class=\"TBLHEAD\">";
    echo "$Date\n";
    echo "</font>";
    echo "</td>";
    echo "<td class=\"TBLHEAD\">";
    echo "<font class=\"TBLHEAD\">";
    echo "$Subject\n";
    echo "</font>";
    echo "</td>";
    echo "<td class=\"TBLHEAD\">";
    echo "<font class=\"TBLHEAD\">";
    echo "$Del\n";
    echo "</font>";
    echo "</td>";
    echo "</tr>";
    while ($line = mysql_fetch_array($result)) {
      $sender = $line["username"];
      $senderid = $line["sender"];
      $msgid = $line["msgid"];
      $subject = $line["subject"];
      $msgdate = GetDateFromTimestamp($line["msgtime"]);
      $body = $line["body"];
      $status = $line["status"];

      echo "<tr>";
      echo "<td class=\"TBLROW\">";
      echo "<font class=\"TBLROW\">";
      echo "<a href=\"showmsg.php?sid=$SID&msgid=$msgid&fs=\">$sender</a>\n";
      echo "</font>";
      echo "</td>";
      echo "<td class=\"TBLROW\">";
      echo "<font class=\"TBLROW\">";
      echo "$msgdate\n";
      echo "</font>";
      echo "</td>";
      echo "<td class=\"TBLROW\">";
      echo "<font class=\"TBLROW\">";
      echo "$subject\n";
      echo "</font>";
      echo "</td>";
      echo "<td class=\"TBLROW\">";
      echo "<font class=\"TBLROW\">";
      echo "<input type=\"checkbox\" name=\"$msgid\" value=\"$msgid\">\n";
      echo "</font>";
      echo "</td>";
      echo "</tr>";

    }
    if ($count > 0) {
      echo "<tr>";
      echo "<td class=\"TBLROW\" align=\"CENTER\" colspan=\"4\">";
      echo "<font class=\"TBLROW\">";
      echo "<input type=\"submit\" value=\"$Delete\" name=\"DELETE\">";
      echo "</font>";
      echo "</td>";
      echo "</tr>";
    }

    echo "</table>";
    echo "</form>";

    CloseConnection($link);
  }

/******************************************************
 * Show the given message
 ******************************************************/
  function ShowMessage($msgid, $fs) {
    global $dbaseMsgData, $dbaseUserData, $SID, $RE,$From,$Date,$Message_txt, $leagueID;
    $link = OpenConnection();

    $query = "select * from $dbaseMsgData inner join $dbaseUserData on $dbaseUserData.userid=$dbaseMsgData.sender where msgid=? and $dbaseUserData.lid=$dbaseMsgData.lid and not status=? and $dbaseUserData.lid=?";
    $result = DBExecute($query, array((int)$msgid, "deleted", (int)$leagueID));
    if ($result === FALSE) {
      die("Unable to read message: ".mysqli_error($link));
    }

    if ($fs == "") {
      $query = "update $dbaseMsgData set status=? where msgid=? and lid=?";
      $result2 = DBExecute($query, array("read", (int)$msgid, (int)$leagueID));
      if ($result2 === FALSE) {
        die("Unable to update message status: ".mysqli_error($link));
      }
    }

    echo "<table class=\"MSGTB\">";
    echo "<tr>";
    echo "<td class=\"TBLHEAD\" colspan=\"2\" align=\"CENTER\">";
    echo "<font class=\"TBLHEAD\">";
    echo "Message $msgid\n";
    echo "</font>";
    echo "</td>";
    echo "</tr>";

    $line = mysql_fetch_array($result);
    $sender = $line["username"];
    $senderid = $line["sender"];
    $msgid = $line["msgid"];
    $subject = $line["subject"];
    $msgdate = GetDateFromTimestamp($line["msgtime"]);
    $body = $line["body"];
    $status = $line["status"];

    echo "<tr>\n";
    echo "<td class=\"TBLHEAD\" width=\"80\">";
    echo "<font class=\"TBLHEAD\">";
    echo "$From\n";
    echo "</font>";
    echo "</td>";
    echo "<td class=\"TBLROW\">";
    echo "<font class=\"TBLROW\">";
    echo "$sender <a href=\"createmsg.php?sid=$SID&userid=$senderid&subj=$RE:$subject\">[reply]</a>\n";
    echo "</font>";
    echo "</td>";
    echo "</tr>\n";
    echo "<tr>\n";
    echo "<td class=\"TBLHEAD\" width=\"80\">";
    echo "<font class=\"TBLHEAD\">";
    echo "$Date\n";
    echo "</font>";
    echo "</td>";
    echo "<td class=\"TBLROW\">";
    echo "<font class=\"TBLROW\">";
    echo "$msgdate\n";
    echo "</font>";
    echo "</td>";
    echo "</tr>\n";
    echo "<tr>\n";
    echo "<td class=\"TBLHEAD\">";
    echo "<font class=\"TBLHEAD\">";
    echo "$Message_txt\n";
    echo "</font>";
    echo "</td>";
    echo "<td class=\"TBLROW\">";
    echo "<font class=\"TBLROW\">";
    echo "$body\n";
    echo "</font>";
    echo "</td>";
    echo "</tr>";

    echo "</table>";

    CloseConnection($link);
  }
  
?>
