<?php
session_start();
include 'db.php';
include 'helper.php';

$ipAddress = $_SERVER['REMOTE_ADDR'];
// For test in local host = $ipAddress = '192.168.3.3';
if (isBlocked($ipAddress)) {
    die('ایپی شما جز ایپی های مسدود شده است لطفا با پشتیبانی در ارتباط باشید.');
}

?>