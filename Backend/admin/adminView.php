<?php

include("../connection.php");

$userData = $jwtManager->checkToken();

if (!isset($userData['role']) || $userData['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["error" => "Access denied: Admins only"]);
    exit;
}

$students = [];
$instructors = [];
$admins = [];
$courses = [];


$sql = $connection->prepare("SELECT user_id, username, role, is_banned FROM users WHERE role = 'student'");
if ($sql->execute()) {
    $result = $sql->get_result();
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
} else {
    http_response_code(500);
    echo json_encode(["error" => "Database error while fetching students"]);
    exit;
}


$sql = $connection->prepare("SELECT user_id, username, role, is_banned FROM users WHERE role = 'instructor'");
if ($sql->execute()) {
    $result = $sql->get_result();
    while ($row = $result->fetch_assoc()) {
        $instructors[] = $row;
    }
} else {
    http_response_code(500);
    echo json_encode(["error" => "Database error while fetching instructors"]);
    exit;
}


$sql = $connection->prepare("SELECT user_id, username, role, is_banned FROM users WHERE role = 'admin'");
if ($sql->execute()) {
    $result = $sql->get_result();
    while ($row = $result->fetch_assoc()) {
        $admins[] = $row;
    }
} else {
    http_response_code(500);
    echo json_encode(["error" => "Database error while fetching admins"]);
    exit;
}

$sql = $connection->prepare("SELECT * FROM courses");
if ($sql->execute()) {
    $result = $sql->get_result();
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
} else {
    http_response_code(500);
    echo json_encode(["error" => "Database error while fetching courses"]);
    exit;
}

$response = [
    "students" => $students,
    "instructors" => $instructors,
    "admins" => $admins,
    "courses" => $courses
];

header('Content-Type: application/json');
echo json_encode(["success" => true, "data" => $response]);

