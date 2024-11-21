<?php

include("../connection.php");

$userData= $jwtManager->checkToken();


if (!isset($userData['role']) || $userData['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["error" => "Access denied: Admins only"]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);
if (!isset($input["user_id"]) || empty($input["user_id"])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "User ID is required."]);
    exit;
}
$user_id = $input["user_id"];


$sql = $connection->prepare("UPDATE users SET is_banned=1 where user_id=?");
$sql->bind_param("i", $user_id);

if ($sql->execute()) {
    if ($sql->affected_rows > 0) {
        echo json_encode(["success" => true, "message" => "User has been banned."]);
    } else {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "No user found with the given user_id."]);
    }
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Query execution failed"]);
}

$sql->close();
$connection->close();