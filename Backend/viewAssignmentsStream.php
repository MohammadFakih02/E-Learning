<?php

include("connection.php");

$userData = $jwtManager->checkToken();
if (!isset($userData['role'])) {
    http_response_code(403);
    echo json_encode(["error" => "Access denied: not logged in"]);
    exit;
}

if (!isset($_GET['course_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid course ID']);
    exit;  // Add exit to stop further execution
}

$course_id = $_GET['course_id'];  // Fixing this to correctly access the 'course_id' parameter
$assignments = [];

$sql = $connection->prepare("SELECT assignments.assignment_id, users.username, assignments.title,
                                    assignments.created_at, assignments.deadline
                                    FROM users
                                    JOIN assignments ON users.user_id = assignments.instructor_id
                                    WHERE assignments.course_id = ?
                                    ORDER BY assignments.created_at DESC;");

$sql->bind_param("i", $course_id);

if ($sql->execute()) {
    $result = $sql->get_result();

    while ($row = $result->fetch_assoc()) {
        $assignments[] = $row;
    }

    echo json_encode(["success" => true, "data" => $assignments]);  // Use $assignments here
} else {
    http_response_code(500);
    echo json_encode(["error" => "Unable to access database"]);
}

