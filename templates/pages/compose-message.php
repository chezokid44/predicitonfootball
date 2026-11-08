<form method="POST" action="sendmsg.php">
  <table class="STANDTB">
    <tr><td class="TBLHEAD" colspan="2"><?php echo htmlspecialchars($New_Message, ENT_QUOTES, "UTF-8"); ?></td></tr>
    <tr>
      <td class="TBLHEAD"><?php echo htmlspecialchars($User_Name, ENT_QUOTES, "UTF-8"); ?></td>
      <td class="TBLROW">
        <?php echo htmlspecialchars($tgtusername, ENT_QUOTES, "UTF-8"); ?>
        <input type="hidden" name="TGTUSER" value="<?php echo (int)$tgtuserid; ?>">
      </td>
    </tr>
    <tr>
      <td class="TBLHEAD"><?php echo htmlspecialchars($Subject_txt, ENT_QUOTES, "UTF-8"); ?></td>
      <td class="TBLROW"><input type="text" name="SUBJECT" maxlength="255" value="<?php echo htmlspecialchars($subject, ENT_QUOTES, "UTF-8"); ?>"></td>
    </tr>
    <tr>
      <td class="TBLHEAD"><?php echo htmlspecialchars($Message_txt, ENT_QUOTES, "UTF-8"); ?></td>
      <td class="TBLROW"><textarea name="MESSAGE" rows="12" cols="40" required></textarea></td>
    </tr>
    <tr><td class="TBLROW" colspan="2"><button type="submit" name="CREATE" value="CREATE"><?php echo htmlspecialchars($Send_Msg_Button, ENT_QUOTES, "UTF-8"); ?></button></td></tr>
  </table>
</form>
