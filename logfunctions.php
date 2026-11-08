<?php
/*********************************************************
 * Author: John Astill
 * Date  : 15th June 2002
 * File  : logfunctions.php
 ********************************************************/

/*******************************************************
* Append the given message to the log file.
*******************************************************/
function LogMsg($msg) {
  global $logfile;
  // If the log filename is "", don't try to write to it.
  if ($logfile == "") {
    return;
  }

  // Write to the end of the file
  $file = fopen($logfile,'a+');
  if ($file == FALSE) {
    ErrorNotify("Unable to write to Log : $logfile . This could be due to the log file not having the correct permissions on the server.");
    return;
  }

  $date = date("M/d/Y H:i:s T");
  fwrite($file,"\n------------------------\n$date\n$msg");
  
  fclose($file);
}

?>
