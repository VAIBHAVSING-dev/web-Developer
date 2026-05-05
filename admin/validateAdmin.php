<?php 
ob_start();
session_start();
include("include/connection.php");
if(isset($_POST['login']) && $_POST['login']=="login")
{
	$admin_id=isset($_POST['admin_id']) ? mysqli_real_escape_string($con, $_POST['admin_id']) : '';
	$password_admin=isset($_POST['password_admin']) ? $_POST['password_admin'] : '';
	$sql="select * from `tbl_admin` where `admin_name`='".$admin_id."' and `password`='".md5($password_admin)."' and `status`='1'";
	$result=mysqli_query($con,$sql);
	$num=$result ? mysqli_num_rows($result) : 0;
	if($num>=1)
	{
		$line=mysqli_fetch_assoc($result);
		
		$userid=$line['id'] ;
		$_SESSION['userid']=$userid;
		$_SESSION['admin_name']=$line['admin_name'];
		$_SESSION['role_id']=$line['role'];
		
		
		header("location:desktop.php");
		exit;
	}
	else
	{
		header("location:index.php?err=ture");
		exit;
	}
}
else
{
	header("location:index.php?err=ture");
	exit;
}
?>
