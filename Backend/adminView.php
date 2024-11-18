<?php

include("connection.php");

$userData= $jwtManager->checkToken();


if (!isset($userData['role']) || $userData['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["error" => "Access denied: Admins only"]);
    exit;
}