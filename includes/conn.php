<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../include/connection.php';
$conn = $con;

if (empty($_SESSION['userid']) && !empty($_SESSION['frontuserid'])) {
    $_SESSION['userid'] = $_SESSION['frontuserid'];
}

if (empty($_SESSION['username']) && !empty($_SESSION['mobile'])) {
    $_SESSION['username'] = $_SESSION['mobile'];
}
?>
