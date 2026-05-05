<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$session_role = $_SESSION['role_id'] ?? '';

$url = basename($_SERVER['PHP_SELF']);

$chkurl = mysqli_query($con,"SELECT `id`,`p_id` FROM `child_menu` WHERE `url`='$url' AND `status`='1'");
$chkurl_result = mysqli_fetch_assoc($chkurl);

$chk = '';
$chkid = '';

if($chkurl_result){
    $chk = $chkurl_result['p_id'];
    $chkid = $chkurl_result['id'];
}

$valurl = mysqli_query($con,"SELECT * FROM `task` WHERE `role_id`='$session_role' AND task LIKE '%$chk%' AND `status`='1'");
$val_row = mysqli_num_rows($valurl);

if($val_row == 0){
    session_unset();
    session_destroy();
    header("location:index.php?msg1=notauthorized");
    exit();
}

$task = mysqli_query($con,"SELECT * FROM `task` WHERE `role_id`='$session_role' AND `status`='1'");
$task_result = mysqli_fetch_assoc($task);
$taskid = $task_result['task'] ?? '';

$parent = mysqli_query($con,"SELECT * FROM `services` WHERE id IN($taskid) AND `status`='1'");
?>

<aside class="main-sidebar">
<section class="sidebar">
<ul class="sidebar-menu">
<li class="header">MAIN NAVIGATION</li>

<?php
while($parent_array = mysqli_fetch_assoc($parent)){

    if($parent_array['url'] == 'desktop.php'){
?>
<li class="active treeview">
<a href="<?php echo $parent_array['url']; ?>">
<i class="fa fa-dashboard"></i>
<span><?php echo $parent_array['services']; ?></span>
</a>
</li>

<?php } else { ?>

<li class="treeview">
<a href="<?php echo $parent_array['url']; ?>">
<i class="fa fa-list-alt"></i>
<span><?php echo $parent_array['services']; ?></span>
<i class="fa fa-angle-left pull-right"></i>
</a>

<ul class="treeview-menu">

<?php
$child = mysqli_query($con,"SELECT * FROM `child_menu` WHERE `p_id`='".$parent_array['id']."' AND `status`='1'");

while($child_array = mysqli_fetch_assoc($child)){
?>

<li>
<a href="<?php echo $child_array['url']; ?>">
<i class="fa fa-circle-o"></i>
<?php echo $child_array['child']; ?>
</a>
</li>

<?php } ?>

</ul>
</li>

<?php } } ?>

<li>&nbsp;</li>
<li>&nbsp;</li>

</ul>
</section>
</aside>