<?php

include ("../connection.php");

$userData = $jwtManager->checkToken();

if (!isset($userData['role']) || $userData['role'] !== 'student') {
    http_response_code(403);
    echo json_encode(["error" => "Access denied: you have to be logged in"]);
    exit;
}

if(isset($_GET["course_id"])){
    $user_id = $userData["user_id"];
    $course_id=$_GET["course_id"];
    $sql = $connection->prepare("INSERT INTO user_courses (user_id,course_id) values (?,?)");
    $sql->bind_param("ii", $user_id, $course_id);
    if($sql->execute()){
        echo json_encode(["data"=> "enrolled into course"]);
    }else{
        http_response_code(500);
        echo json_encode(["error"=> "Could not enroll"]);
    }
}else{
    http_response_code(400);
    echo json_encode(["error"=> "Input missing"]);
}