<?php

include("connection.php");

$userData= $jwtManager->checkToken();
$data = json_decode(file_get_contents("php://input"), true);


if (!isset($userData['role']) || $userData['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["error" => "Access denied: Admins only"]);
    exit;
}

if (isset($data["username"]) && $data["password"] && $data["email"]&& isset($data['course_id'])){
    $username = $data['username'];
    $password = $data['password'];
    $email = $data['email'];
    $course_id = $data['course_id'];

    $sql = $connection->prepare('INSERT Into courses (user_id,course_id) values ($user_id,$course_id)');
    if( $sql->execute() ){
        echo ("Instructor created successfully");
    }else{
        http_response_code(500);
        echo json_encode(['error'=> 'Database error']);
        exit;
    }

    $sql = $connection->prepare('INSERT Into users (username,email,password) values (?,?,?)');
    $sql->bind_param("sss",$username,$email,$passowrd);


}else{
    http_response_code(400);
    echo json_encode(['error'=> 'Invalid input']);
}