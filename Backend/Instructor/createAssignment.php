<?php

include ("../connection.php");

$userData = $jwtManager->checkToken();

if (!isset($userData['role']) || $userData['role'] !== 'instructor') {
    http_response_code(403);
    echo json_encode(["error" => "Access denied: Admins only"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data["course_id"])&& isset($data["content"])&& isset($data["title"])&&isset($userData["user_id"])&& isset($data["deadline"])){
    $date = date('m/d/Y h:i:s a', time());
    $course_id=$data["course_id"];
    $content = $data["content"];
    $title  = $data["title"];
    $instructor_id = $userData["user_id"];
    $deadline = $data["deadline"];
    
    $sql =$connection->prepare("INSERT INTO assignments (course_id,instructor_id,content,title,created_at,deadline) values (?,?,?,?,?,?");
    $sql->bind_param("i,i,s,s,s,s",$course_id,$instructor_id,$content,$title,$date);
    if( $sql->execute()){
        echo json_encode(["message"=> "added new assignment"]);
    }else{
        http_response_code(500);
        echo json_encode(["error"=> "unable to access database"]);
    }
}{
    http_response_code(400);
    echo json_encode(["error"=> "invalid input"]);
}
