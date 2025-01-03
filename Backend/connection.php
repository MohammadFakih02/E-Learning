<?php

$host = "localhost";
$dbuser = "root";
$pass = "";
$dbname = "mydb";
include ("Jwt.php");
$secretKey = 'HammoudHabibiHammoud';
$jwtManager = new JwtManager($secretKey);

$connection = mysqli_connect($host, $dbuser, $pass, $dbname);

if ($connection->connect_error) {
    die("Error connecting to database");
}

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

