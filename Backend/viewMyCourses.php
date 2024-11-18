<?php

include("connection.php");

$userData = $jwtManager->checkToken();
if (!isset($userData['role'])) {
    http_response_code(403);
    echo json_encode(["error" => "Access denied: not logged in"]);
    exit;
}
$user_id=$userData["user_id"];
$courses = [];

$sql = $connection->prepare("Select * from courses where user_id=?");
$sql->bind_param("i", $user_id);
if($sql->execute()){
    $result = $sql->get_result();
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
    echo json_encode(["success" => true, "data" => $courses]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Unable to access database"]);
}

