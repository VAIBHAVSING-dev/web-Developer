<?php
include("include/connection.php");
$userid=isset($_POST['userid']) ? trim($_POST['userid']) : '';
if($userid == '' || !ctype_digit($userid)){
 echo number_format(0, 2);
 exit;
}
echo number_format((float)wallet($con,'amount',$userid), 2); 
 ?>
