<?php
/*********************************************************
 * Author: John Astill (c)
 * Date  : 16th September 2003
 * File  : testforward.php
 * Desc  : File for testing that the server can support 
 *       : the notion of forwarding.
 ********************************************************/
require "systemvars.php";
require "configvalues.php";
require "sortfunctions.php";
require "security.php";

$cmd = $HTTP_GET_VARS["CMD"];
if ($cmd == "") {
  forward("testforward.php?CMD=FWD");
} else if ($cmd == "FWD") {
?>
<html>
<head>
<title>
Test forwrding
</title>
</head>
<body>
  The forward function has worked.
</body>
</html>
<?php
}

?>

