<?php

include ("../connection.php");

$userData = $jwtManager->checkToken();

if (!isset($userData['role']) || $userData['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["error" => "Access denied: Admins only"]);
    exit;
}

if(isset($_GET["course_id"])&& isset($_GET['user_id'])){
    $user_id = $_GET["user_id"];
    $course_id=$_GET["course_id"];
    $sql = $connection->prepare("INSERT INTO user_courses (user_id,course_id) values (?,?)");
    $sql->bind_param("ii", $user_id, $course_id);
    if($sql->execute()){
        echo("Instructor assigned to course");
    }else{
        http_response_code(500);
        echo json_encode(["error"=> "Could not assign instructor"]);
    }
}else{
    http_response_code(400);
    echo json_encode(["error"=> "Input missing"]);
}