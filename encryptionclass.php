<?php
/*********************************************************
 * Author: John Astill (c)
 * Date  : 20th March 2003
 * File  : encryptionclass.php
 *********************************************************/

class Encryption {

  var $pwd = "";
  
  var $key = "D";

  function Encryption($val) {
    $this->pwd = $val;
  }
  
  function Decrypt() {
    return $this->change(urldecode($this->pwd));
  }

  function Encrypt() {
    global $PasswordEncryption;
    if ($PasswordEncryption == "TRUE") {
      return md5($this->pwd);
    } else {
      return urlencode($this->change($this->pwd));
    }
  }

  function change($val) {
    $newval = $val;
    
    return $newval;
  }
}


?>
