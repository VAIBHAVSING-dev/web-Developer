<?php 
include("include/connection.php");
$userid=isset($_POST['userid']) ? mysqli_real_escape_string($con, $_POST['userid']) : '';
$oldpassword=md5(isset($_POST['oldpassword']) ? $_POST['oldpassword'] : '');
$newpassword=isset($_POST['newpassword']) ? $_POST['newpassword'] : '';
$cpassword=isset($_POST['cpassword']) ? $_POST['cpassword'] : '';
$finalnewpassword=md5($newpassword);
if($newpassword == '' || $newpassword !== $cpassword){echo "2";}else{
$sql="select * from `tbl_admin` where `id`='".$userid."' and `password`='".$oldpassword."'";
	$result=mysqli_query($con,$sql);
	$num=$result ? mysqli_num_rows($result) : 0;
	if($num!='')
	{
$sql2= "UPDATE `tbl_admin` SET `password`= '".$finalnewpassword."' WHERE `id`='".$userid."'";
$query2=mysqli_query($con,$sql2);

	echo "1";
	
session_start();
session_unset();
session_destroy();
	}
	else
	{
echo "0";
	}
}
	?>
