<?php
/*********************************************************
 * Author: John Astill (c)
 * Date  : 20th March 2003
 * File  : getservertime.php
 *********************************************************/
$date = date("h:i:s A") ." [". (date("Z")/(60*60))."]";
$offset = (date("Z")/(60*60));
?>
<script type="text/javascript">
  var fixt = "<?php echo $date ?>";
  var offset = "<?php echo $offset ?>";
  window.parent.setServerDate(fixt,offset);
</script>
