<?php

include("../connection.php");

$userData = $jwtManager->checkToken();

if (!isset($userData['role']) || $userData['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["error" => "Access denied: Admins only"]);
    exit;
}


$data = json_decode(file_get_contents("php://input"), true);

if(isset($data["course_name"])){
    $course_name = $data["course_name"];
    $sql= $connection->prepare("Delete from courses where course_id=?");
    $sql->bind_param("s", $course_id);
    if ($sql->execute()) {
        echo json_encode(["message"=> "deleted course"]);
    } else {
        http_response_code(500);
        echo json_encode(["error"=> "Unable to create course"]);
    }
}else{
    http_response_code(400);
    echo json_encode(["error"=> "invalid input"]);
}