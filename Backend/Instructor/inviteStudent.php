<?php

include ("../connection.php");

$userData = $jwtManager->checkToken();

if (!isset($userData['role']) || $userData['role'] !== 'instructor') {
    http_response_code(403);
    echo json_encode(["error" => "Access denied: Instructors only"]);
    exit;
}

if (isset($data["course_id"])&& isset($data["student_id"])){
    $user_id = $data["student_id"];
    $course_id = $data["course_id"];

    $sql = $connection->prepare("INSERT INTO user_courses (course_id,user_id) values(?,?)");
    $sql->bind_param("ii", $user_id, $course_id);
    if( $sql->execute() ){
        echo json_encode(["message"=> "added student to course"]);
    }else{
        http_response_code(500);
        echo json_encode(["error"=> "unable to add to database"]);
    }
}else{
    http_response_code(400);
    echo json_encode(["error"=> "invalid input"]);
}