<?php

$host = "localhost";
$dbuser ="root";
$pass = "";
$dbname = "mydb";
include ("Jwt.php");
$secretKey = 'HammoudHabibiHammoud';
$jwtManager = new JwtManager($secretKey);

$connection = mysqli_connect($host, $dbuser, $pass, $dbname);

if ($connection->connect_error) {
    die("Error connecting to database");
}

header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");