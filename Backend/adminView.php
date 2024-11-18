<?php

include("connection.php");

$userData= $jwtManager->checkToken();


if (!isset($userData['role']) || $userData['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["error" => "Access denied: Admins only"]);
    exit;
}

$sql = $connection->prepare("Select user_id,username,role,is_banned from users where role = 'student'");
if($sql->execute()){
    $result = $sql->get_result();
    if ($result->num_rows > 0) {
        $students = [];

        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
    }
}else{
    http_response_code(500);
    echo ("Error with database");
}
$sql = $connection->prepare("Select user_id,username,role,is_banned from users where role = 'instructor'");
if($sql->execute()){
    $result = $sql->get_result();
    if ($result->num_rows > 0) {
        $instructors = [];

        while ($row = $result->fetch_assoc()) {
            $instructors[] = $row;
        }
    }
}else{
    http_response_code(500);
    echo ("Error with database");
}
$sql = $connection->prepare("Select user_id,username,role,is_banned from users where role = 'admin'");
if($sql->execute()){
    $result = $sql->get_result();
    if ($result->num_rows > 0) {
        $admins = [];

        while ($row = $result->fetch_assoc()) {
            $admins[] = $row;
        }
    }
}else{
    http_response_code(500);
    echo ("Error with database");
}
$sql = $connection->prepare("Select * from courses");
if($sql->execute()){
    $result = $sql->get_result();
    if ($result->num_rows > 0) {
        $admins = [];

        while ($row = $result->fetch_assoc()) {
            $admins[] = $row;
        }
    }
}else{
    http_response_code(500);
    echo ("Error with database");
}

$result['student']=$students;
$result['instructors']=$instructors;
$result['admins']=$admins;
$result['courses']=$courses;

echo json_encode(["success" => true, "data" => $result]);


