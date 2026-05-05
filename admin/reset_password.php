<?php 
include("include/connection.php");
$userid=isset($_POST['puserid']) ? mysqli_real_escape_string($con, $_POST['puserid']) : '';
$newpassword=isset($_POST['nnewpassword']) ? $_POST['nnewpassword'] : '';
$cpassword=isset($_POST['ccpassword']) ? $_POST['ccpassword'] : '';
$finalnewpassword=md5($newpassword);
if($newpassword == '' || $newpassword !== $cpassword){echo "2";}else{
$sql2= "UPDATE `tbl_admin` SET `password`= '".$finalnewpassword."' WHERE `id`='".$userid."'";
$query2=mysqli_query($con,$sql2);

	echo "1";
}
	?>
