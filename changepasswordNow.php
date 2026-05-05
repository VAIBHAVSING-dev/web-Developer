<?php 
include("include/connection.php");
if(isset($_POST['action']))
{

$userid=isset($_POST['userid']) ? mysqli_real_escape_string($con, $_POST['userid']) : '';
$opassword=isset($_POST['opassword']) ? $_POST['opassword'] : '';
$newpassword=isset($_POST['npassword']) ? $_POST['npassword'] : '';
$cpassword=isset($_POST['cpassword']) ? $_POST['cpassword'] : '';

if($newpassword == '' || $newpassword !== $cpassword){echo "2";}else{
$sql="select * from `tbl_user` where `id`='".$userid."' and `password`='".md5($opassword)."'";
	$result=mysqli_query($con,$sql);
	$num=$result ? mysqli_num_rows($result) : 0;
	if($num!='')
	{
$sql2= "UPDATE `tbl_user` SET `password`= '".md5($cpassword)."' WHERE `id`='".$userid."'";
$query2=mysqli_query($con,$sql2);

	echo "1";
	
	
	}
	else
	{
echo "0";
	}
}
}
	?>
