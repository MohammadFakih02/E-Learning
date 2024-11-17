<?php

include("connection.php");

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['username']) && isset($data['password'])) {
    $id = $data['username'];
    $password = $data['password'];


}else{
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid input'
    ]);
}