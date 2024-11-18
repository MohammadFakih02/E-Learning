<?php

include ("../connection.php");

$userData = $jwtManager->checkToken();

if (!isset($userData['role']) || $userData['role'] !== 'student') {
    http_response_code(403);
    echo json_encode(["error" => "Access denied: Admins only"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if(isset($data["assignment_id"]) && isset($data["content"])&& isset($data["private"])){
    $assignment_id = $data["assignment_id"];
    $content =$data["content"];
    $private = $data["private"];
    $date = date('m/d/Y h:i:s a', time());
    $user_id = $userData["user_id"];
    
    $sql = $connection->prepare('INSERT INTO comments (student_id,assignment_id,content,date,private)values (?,?,?,?,?)');
    $sql->bind_param("i,i,s,s,i", $user_id,$assignment_id, $content, $private, $date);
    if($sql->execute()){
        echo json_encode(["message"=> "created comment successfully"]);
    }else{
        http_response_code(500);
        echo json_encode(["error"=> "unable to create comment, try again later"]);
    }
}else{
    http_response_code(400);
    echo json_encode(["error"=> "invalid input"]);
}