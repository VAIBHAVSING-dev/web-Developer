<?php 
ob_start();
session_start();
include("include/connection.php");

$userid=isset($_POST['userid']) ? mysqli_real_escape_string($con, $_POST['userid']) : '';
$type=isset($_POST['type']) ? mysqli_real_escape_string($con, $_POST['type']) : '';
$value=isset($_POST['value']) ? mysqli_real_escape_string($con, $_POST['value']) : '';
$counter=isset($_POST['counter']) ? (int)$_POST['counter'] : 0;
$inputgameid=isset($_POST['inputgameid']) ? mysqli_real_escape_string($con, $_POST['inputgameid']) : '';
$finalamount=isset($_POST['finalamount']) ? (float)$_POST['finalamount'] : 0;
$tab=isset($_POST['tab']) ? mysqli_real_escape_string($con, $_POST['tab']) : '';
$presalerule=isset($_POST['presalerule']) ? mysqli_real_escape_string($con, $_POST['presalerule']) : '';


if($userid=="" || $type=="" || $inputgameid=="" || $finalamount=="")
{
echo"2";
//check empty
}else{
if($counter<30)
{ echo"3";
//check counter
}else if($finalamount<=0)
{
	echo"4";
//check if amount 0	
	}
else if($finalamount<10){
	echo"5";
	//check if amount below 10
	}else
{
$debitamount=number_format($finalamount, 2, '.', '');
mysqli_begin_transaction($con);
$sqlwallet= mysqli_query($con,"UPDATE `tbl_wallet` SET `amount` = `amount` - ".$debitamount." WHERE `userid`= '".$userid."' AND `amount` >= ".$debitamount);
if(!$sqlwallet || mysqli_affected_rows($con)!=1){
mysqli_rollback($con);
echo"6";
exit;
}
$sql= mysqli_query($con,"INSERT INTO `tbl_betting` (`userid`, `periodid`, `type`,`value`,`amount`,`tab`,`acceptrule`) VALUES ('".$userid."','".$inputgameid."','".$type."','".$value."','".$debitamount."','".$tab."','".$presalerule."')");
if(!$sql){
mysqli_rollback($con);
echo"2";
exit;
}

//=====================transaction==================================================
$sql= mysqli_query($con,"INSERT INTO `tbl_order`(`userid`,`transactionid`,`amount`,`status`) VALUES ('".$userid."','".$inputgameid."','".$debitamount."','1')");
@$orderid=mysqli_insert_id($con);
if(!$sql || !$orderid){
mysqli_rollback($con);
echo"2";
exit;
}

$sql3= mysqli_query($con,"INSERT INTO `tbl_walletsummery`(`userid`,`orderid`,`amount`,`type`,`actiontype`) VALUES ('".$userid."','".$orderid."','".$debitamount."','debit','join')");
if(!$sql3){
mysqli_rollback($con);
echo"2";
exit;
}

//=====================transaction end==============================================
  userpromocode($con,$userid,user($con,'code',$userid),$debitamount,$inputgameid);//===bonus calculation

mysqli_commit($con);

echo"1~".wallet($con,'amount',$userid);

	}
}
?>
