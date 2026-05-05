<?php
include("include/connection.php");

if(!isset($_POST['type']))
{
echo "0";
exit;
}

if($_POST['type']=='mobile'){

$mobile = isset($_POST['mobile']) ? mysqli_real_escape_string($con, $_POST['mobile']) : '';
$otp = "1234"; // OTP always 1234
if($mobile == ''){
echo "0";
exit;
}

$chkuser = mysqli_query($con,"SELECT * FROM `tbl_user` WHERE `mobile`='".$mobile."'");
$userRow = $chkuser ? mysqli_num_rows($chkuser) : 0;

if($userRow == 0){

session_start();

unset($_SESSION["signup_mobile"]);
unset($_SESSION["signup_otp"]);

$_SESSION["signup_mobile"] = $mobile;
$_SESSION["signup_otp"] = $otp;

echo "1"; // OTP sent (virtually)

}else{

echo "2"; // Mobile already exists

}

}else if($_POST['type']=='otpval'){

session_start();

$otp = isset($_POST['otp']) ? $_POST['otp'] : '';
if(empty($_SESSION["signup_mobile"]) || empty($_SESSION["signup_otp"]) || $otp == ''){
echo "0";
exit;
}
$sessionotp = $_SESSION["signup_otp"];

if($sessionotp != $otp){

echo "0"; // Wrong OTP

}else{

$_SESSION["signup_mobilematched"] = $_SESSION["signup_mobile"];

unset($_SESSION["signup_mobile"]);
unset($_SESSION["signup_otp"]);

echo "1"; // OTP correct

}

}else{
echo "0";
}
?>
