<?php

include("../connection.php");

$userData = $jwtManager->checkToken();

if (!isset($userData['role']) || $userData['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["error" => "Access denied: Admins only"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data["course_name"])) {
    $course_name = $data["course_name"];

    $checkSql = $connection->prepare("SELECT COUNT(*) FROM courses WHERE course_name = ?");
    $checkSql->bind_param("s", $course_name);
    $checkSql->execute();
    $checkSql->bind_result($courseExists);
    $checkSql->fetch();
    $checkSql->close();

    if ($courseExists > 0) {
        http_response_code(409);
        echo json_encode(["error" => "Course already exists"]);
        exit;
    }
    
    $sql = $connection->prepare("INSERT INTO courses (course_name) VALUES (?)");
    $sql->bind_param("s", $course_name);
    if ($sql->execute()) {
        $course_id = $sql->insert_id;

        echo json_encode(["message" => "Added new course", "data" => ["course_id" => $course_id]]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Unable to create course"]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "Invalid input"]);
}