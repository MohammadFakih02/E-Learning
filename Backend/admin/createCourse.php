<?php

include("../connection.php");

if (!isset($userData['role']) || $userData['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["error" => "Access denied: Admins only"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if(isset($data["course_id"])&&isset($data["course_name"])){
    $course_id=$data["course_id"];
    $course_name = $data["course_name"];
    $sql= $connection->prepare("INSERT INTO courses (course_id,course_name) values (?,?)");
    $sql->bind_param("is", $course_id,$course_name);
    if ($sql->execute()) {
    } else {
        http_response_code(500);
        echo json_encode(["error"=> "Unable to create course"]);
    }
}else{
    http_response_code(400);
    echo json_encode(["error"=> "invalid input"]);
}