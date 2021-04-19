<?php 
error_reporting(E_ALL); 
ini_set("display_errors", 1); 
$host_db= 'localhost';
$user_db = 'u0605_dev';
$password_db = '1234567890Qwe';
$db = 'u0605727_mt_qc';
$mysqli_req = new mysqli($host_db, $user_db, $password_db, $db);
$mysqli_req->set_charset("utf8mb4");
if($mysqli_req -> connect_error) die("error");
session_start();
if(@$_SESSION['auth'] !== 1 && basename($_SERVER['SCRIPT_FILENAME']) !== 'signin.php' && basename($_SERVER['SCRIPT_FILENAME']) !== 'qualityControl.php') {header('Location: signin.php');}
if(@isset($_POST['exit'])){session_destroy(); header('Location: signin.php');}
date_default_timezone_set("Asia/Krasnoyarsk");
?>