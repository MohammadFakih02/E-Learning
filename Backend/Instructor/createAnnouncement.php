<?php

include("../connection.php");

$userData = $jwtManager->checkToken();

if (!isset($userData['role']) || $userData['role'] !== 'instructor') {
    http_response_code(403);
    echo json_encode(["error" => "Access denied: Instructors only"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data["course_id"]) && isset($data["content"]) && isset($data["title"]) && isset($userData["user_id"])) {
    $date = date('m/d/Y h:i:s a', time());
    $course_id = $data["course_id"];
    $content = $data["content"];
    $title  = $data["title"];
    $instructor_id = $userData["user_id"];

    $sql = $connection->prepare("INSERT INTO announcements (course_id, instructor_id, content, title, date) VALUES (?, ?, ?, ?, ?)");
    $sql->bind_param("iisss", $course_id, $instructor_id, $content, $title, $date);

    if ($sql->execute()) {
        echo json_encode(["message" => "New announcement added"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Unable to access the database"]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "Invalid input"]);
}

