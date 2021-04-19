<?php
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');

header('Content-Security-Policy: upgrade-insecure-requests'); 
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 86400'); 
header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');


$host_db= 'localhost';
$user_db = 'u0605_dev';
$password_db = '1234567890Qwe';
$db = 'u0605727_mt_qc';
$mysqli_req = new mysqli($host_db, $user_db, $password_db, $db);
$mysqli_req->set_charset("utf8mb4");
if($mysqli_req->connect_error) die("error");
date_default_timezone_set("Asia/Krasnoyarsk");
$chunk = 25;
$offset = empty($_POST) ? 0 : isset($_POST['page']) ? ($_POST['page']-1) * 25 : 0;
$pages = $mysqli_req->query("SELECT * FROM leads");
$res = $mysqli_req->query("SELECT id_lead, expert_id, expert_name, mng_id, mng_name, notice, report, minus, plus, date, date_mng  FROM leads ORDER BY date DESC LIMIT $offset, $chunk");
$item['rows'] = $res->fetch_all(MYSQLI_ASSOC);
$item['data'] = (object)array("pages"=>ceil(mysqli_num_rows($pages)/$chunk));
echo json_encode($item, JSON_UNESCAPED_UNICODE);
$mysqli_req->close();
    
exit();
?>