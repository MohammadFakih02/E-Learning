<?php

include("../connection.php");

$userData = $jwtManager->checkToken();

if (!isset($userData['role']) || $userData['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["error" => "Access denied: Admins only"]);
    exit;
}

$users = [];
$courses = [];

$sql = "
    SELECT 'student' AS category, user_id, username, role, is_banned FROM users WHERE role = 'student'
    UNION ALL
    SELECT 'instructor' AS category, user_id, username, role, is_banned FROM users WHERE role = 'instructor'
    UNION ALL
    SELECT 'admin' AS category, user_id, username, role, is_banned FROM users WHERE role = 'admin';
    
    SELECT * FROM courses;
";

if ($connection->multi_query($sql)) {
    do {
        if ($result = $connection->store_result()) {
            while ($row = $result->fetch_assoc()) {
                if (isset($row['category'])) {
                    $users[$row['category']][] = $row;
                } else {
                    $courses[] = $row;
                }
            }
            $result->free();
        }
    } while ($connection->next_result());
} else {
    http_response_code(500);
    echo json_encode(["error" => "Database error while fetching data"]);
    exit;
}

$response = [
    "students" => $users['student'] ?? [],
    "instructors" => $users['instructor'] ?? [],
    "admins" => $users['admin'] ?? [],
    "courses" => $courses
];

header('Content-Type: application/json');
echo json_encode(["success" => true, "data" => $response]);
