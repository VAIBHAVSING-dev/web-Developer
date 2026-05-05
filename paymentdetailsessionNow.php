<?php
ob_start();
session_start();
include("include/connection.php");
if(isset($_POST['action']) && $_POST['action']=="paydetail")
{ 
$name=isset($_POST['name']) ? $_POST['name'] : '';
$mobile=isset($_POST['mobile']) ? $_POST['mobile'] : '';
$email=isset($_POST['email']) ? $_POST['email'] : '';
$finalamount=isset($_POST['finalamount']) ? $_POST['finalamount'] : '';


@$_SESSION['name']=$name;
@$_SESSION['mobile']=$mobile;
@$_SESSION['email']=$email;
@$_SESSION['finalamount']=$finalamount;
echo"1";
}else{echo"0";}
?>
