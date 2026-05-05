<?php 
ob_start();
session_start();
include("include/connection.php");

$mobile=isset($_POST['mobile']) ? mysqli_real_escape_string($con, $_POST['mobile']) : '';
$email=isset($_POST['email']) ? mysqli_real_escape_string($con, $_POST['email']) : '';
$password=isset($_POST['password']) ? $_POST['password'] : '';
$rcode=isset($_POST['rcode']) ? mysqli_real_escape_string($con, $_POST['rcode']) : '';
$acceptTC=isset($_POST['remember']) ? mysqli_real_escape_string($con, $_POST['remember']) : '';
$action=isset($_POST['action']) ? $_POST['action'] : '';
$otpmobile=isset($_SESSION["signup_mobilematched"]) ? $_SESSION["signup_mobilematched"] : '';

if($action=="register")
{
  if($mobile == '' || $password == '' || $otpmobile !== $mobile){echo"4";}else{
	$chkuser=mysqli_query($con,"select * from `tbl_user` where `mobile`='".$mobile."'");
	$userRow=$chkuser ? mysqli_num_rows($chkuser) : 0;
if($userRow==0){
$chkrcode=mysqli_query($con,"select * from `tbl_user` where `owncode`='".$rcode."'");
	$codeRow=$chkrcode ? mysqli_num_rows($chkrcode) : 0;
	if($codeRow!=''){	
$sql= mysqli_query($con,"INSERT INTO `tbl_user` (`mobile`, `email`, `password`,`code`,`owncode`,`privacy`,`status`) VALUES ('".$mobile."','".$email."','".md5($password)."','".$rcode."','','".$acceptTC."','1')");
$userid=mysqli_insert_id($con);
$refcode=$userid.refcode();
$sql= mysqli_query($con,"UPDATE `tbl_user` SET `owncode` = '$refcode' WHERE `id`= '".$userid."'");
$sql2= mysqli_query($con,"INSERT INTO `tbl_wallet`(`userid`,`amount`,`envelopestatus`) VALUES ('".$userid."','20','0')");
$sql3= mysqli_query($con,"INSERT INTO `tbl_bonus`(`userid`,`amount`,`level1`,`level2`) VALUES ('".$userid."','0','0','0')");

	if($sql){
      unset($_SESSION["signup_mobilematched"]);
echo"1";}else{ echo"0";}
	}else{echo"3";}
	}else{ echo"2";}
}
}
?>
