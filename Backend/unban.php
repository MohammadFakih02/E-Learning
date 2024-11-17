<?php
include("connection.php");

if (!isset($_POST["user_id"]) || empty($_POST["user_id"])) {
    echo json_encode(["success" => false, "message" => "User ID is required."]);
    exit();
}

$user_id = $_POST['user_id'];

$sql = $connection->prepare('
    UPDATE users
    SET is_Banned = 0
    WHERE user_id = ?
');

$sql->bind_param('i', $user_id);

if ($sql->execute()) {
    if ($sql->affected_rows > 0) {
        echo json_encode(["success" => true, "message" => "User has been unbanned."]);
    } else {
        echo json_encode(["success" => false, "message" => "No user found with the given user_id."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Query execution failed"]);
}

$sql->close();
$connection->close();