<?php

include("../connection.php");

$userData = $jwtManager->checkToken();

if (!isset($userData['role']) || $userData['role'] !== 'instructor') {
    http_response_code(403);
    echo json_encode(["error" => "Access denied: Instructors only"]);
    exit;
}

$students = [];

$sql = $connection->prepare("Select * from users where role='student'");
if($sql->execute()){
    $result = $sql->get_result();

    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }

    echo json_encode(["success" => true, "data" => $students]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Unable to access database"]);
}