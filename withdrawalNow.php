<?php
ob_start();
session_start();
include("include/connection.php");

$userid=isset($_POST['userid']) ? mysqli_real_escape_string($con, $_POST['userid']) : '';
$userammount=isset($_POST['userammount']) ? (float)$_POST['userammount'] : 0;
$optionsname=isset($_POST['optionsname']) ? mysqli_real_escape_string($con, $_POST['optionsname']) : '';
$acid=isset($_POST['acid']) ? mysqli_real_escape_string($con, $_POST['acid']) : '';
$action=isset($_POST['action']) ? $_POST['action'] : '';
$today = date("Y-m-d H:i:s");

if($action=="withdrawal")
{
    if($userid=='' || $userammount<=0)
    {
        echo"3";
        exit;
    }

    $chkbankdetailQuery=mysqli_query($con,"select * from `tbl_bankdetail` where `userid`='".$userid."'");
    $bankdetailRows=$chkbankdetailQuery ? mysqli_num_rows($chkbankdetailQuery) : 0;
    if($bankdetailRows<=0)
    {
        echo"2";
        exit;
    }

    $withdrawamount=number_format($userammount, 2, '.', '');
    mysqli_begin_transaction($con);

    $sqlwallet= mysqli_query($con,"UPDATE `tbl_wallet` SET `amount` = `amount` - ".$withdrawamount." WHERE `userid`= '".$userid."' AND `amount` >= ".$withdrawamount);
    if(!$sqlwallet || mysqli_affected_rows($con)!=1)
    {
        mysqli_rollback($con);
        echo"3";
        exit;
    }

    $withdrawalsql= mysqli_query($con,"INSERT INTO `tbl_withdrawal`(`userid`,`amount`,`payout`,`payid`,`status`,`createdate`) VALUES ('".$userid."','".$withdrawamount."','".$optionsname."','".$acid."','1','".$today."')");
    $withdrawalid=mysqli_insert_id($con);
    if(!$withdrawalsql || !$withdrawalid)
    {
        mysqli_rollback($con);
        echo"2";
        exit;
    }

    $sql= mysqli_query($con,"INSERT INTO `tbl_order`(`userid`,`transactionid`,`amount`,`status`) VALUES ('".$userid."','withdraw','".$withdrawamount."','1')");
    $orderid=mysqli_insert_id($con);
    if(!$sql || !$orderid)
    {
        mysqli_rollback($con);
        echo"2";
        exit;
    }

    $actiontype="withdraw~".$withdrawalid;
    $sql3= mysqli_query($con,"INSERT INTO `tbl_walletsummery`(`userid`,`orderid`,`amount`,`type`,`actiontype`) VALUES ('".$userid."','".$orderid."','".$withdrawamount."','debit','$actiontype')");
    if(!$sql3)
    {
        mysqli_rollback($con);
        echo"2";
        exit;
    }

    mysqli_commit($con);
    echo"1";
}
?>
