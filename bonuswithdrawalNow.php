<?php
ob_start();
session_start();
include("include/connection.php");

$userid=isset($_POST['userid']) ? mysqli_real_escape_string($con, $_POST['userid']) : '';
$bonusammount=isset($_POST['bonusammount']) ? (float)$_POST['bonusammount'] : 0;
$action=isset($_POST['action']) ? $_POST['action'] : '';
$today = date("Y-m-d H:i:s");

if($action=="bonus")
{
    if($userid=='' || $bonusammount<=0)
    {
        echo"2";
        exit;
    }

    $bonusamount=number_format($bonusammount, 2, '.', '');
    mysqli_begin_transaction($con);

    $sqlbonus= mysqli_query($con,"UPDATE `tbl_bonus` SET `amount` = `amount` - ".$bonusamount." WHERE `userid`= '".$userid."' AND `amount` >= ".$bonusamount);
    if(!$sqlbonus || mysqli_affected_rows($con)!=1)
    {
        mysqli_rollback($con);
        echo"2";
        exit;
    }

    $bonussql= mysqli_query($con,"INSERT INTO `tbl_bonuswithdrawal`(`userid`,`amount`,`createdate`) VALUES ('".$userid."','".$bonusamount."','".$today."')");
    if(!$bonussql)
    {
        mysqli_rollback($con);
        echo"2";
        exit;
    }

    $sql= mysqli_query($con,"INSERT INTO `tbl_order`(`userid`,`transactionid`,`amount`,`status`) VALUES ('".$userid."','bonus','".$bonusamount."','1')");
    $orderid=mysqli_insert_id($con);
    if(!$sql || !$orderid)
    {
        mysqli_rollback($con);
        echo"2";
        exit;
    }

    $sql3= mysqli_query($con,"INSERT INTO `tbl_walletsummery`(`userid`,`orderid`,`amount`,`type`,`actiontype`) VALUES ('".$userid."','".$orderid."','".$bonusamount."','credit','bonus')");
    $sqlwallet= mysqli_query($con,"UPDATE `tbl_wallet` SET `amount` = `amount` + ".$bonusamount." WHERE `userid`= '".$userid."'");
    if(!$sql3 || !$sqlwallet)
    {
        mysqli_rollback($con);
        echo"2";
        exit;
    }

    mysqli_commit($con);
    echo"1~".bonus($con,'amount',$userid);
}
?>
