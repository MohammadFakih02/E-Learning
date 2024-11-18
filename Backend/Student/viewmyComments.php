<?php

include("connection.php");

$userData = $jwtManager->checkToken();
if (!isset($userData['role']) || $userData['role'] !== 'student') {
    http_response_code(403);
    echo json_encode(["error" => "Access denied: Admins only"]);
    exit;
}

$comments = [];

if(isset($_GET["assignment_id"])){
$assignment_id=$_GET['assignment_id'];
$sql = $connection->prepare("SELECT content,date from comments where assignment_id=? and student_id=?");
$sql->bind_param("ii",$assignment_id, $userData["user_id"]);
if($sql->execute()){
    $result = $sql->get_result();
    while ($row = $result->fetch_assoc()) {
        $comments[] = $row;
    }
    echo json_encode(["success" => true, "data" => $response]);
}else{
    http_response_code(500);
    echo json_encode(["error"=>"unable to access database"]);
}
}else{
    http_response_code(400);
    echo json_encode(["error"=> "invalid input"]);
}
