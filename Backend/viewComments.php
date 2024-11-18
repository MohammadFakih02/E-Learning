<?php

include("connection.php");

$userData = $jwtManager->checkToken();
if (!isset($userData['role'])) {
    http_response_code(403);
    echo json_encode(["error" => "Access denied: not logged in"]);
    exit;
}

if ($userData["role"] == "student") {
    $private= 0;
}else{
    $private= 1;
}

$comments = [];

if(isset($_GET["assignment_id"])){
$assignment_id=$_GET['assignment_id'];
$sql = $connection->prepare("SELECT student_id,content,date from comments where private=$private and assignment_id=?");
$sql->bind_param("i",$assignment_id);
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
