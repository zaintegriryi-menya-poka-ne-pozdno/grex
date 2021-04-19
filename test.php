<?php
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');

header('Content-Security-Policy: upgrade-insecure-requests'); 
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 86400'); 
header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');


file_put_contents("test.txt", print_r($_POST, 1));

exit(json_encode((object)array("privet"=>"ebana")));
?>