<?php

include("../connection.php");

$userData = $jwtManager->checkToken();

// Only admins can delete courses
if (!isset($userData['role']) || $userData['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["error" => "Access denied: Admins only"]);
    exit;
}

// Read the input data
$data = json_decode(file_get_contents("php://input"), true);

// Ensure the course_id is provided
if (isset($data["course_id"])) {
    $course_id = intval($data["course_id"]);  // Ensure it's an integer

    // Prepare and execute the SQL query to delete the course
    $sql = $connection->prepare("DELETE FROM courses WHERE course_id = ?");
    $sql->bind_param("i", $course_id);

    if ($sql->execute()) {
        if ($sql->affected_rows > 0) {
            echo json_encode(["message" => "Course deleted successfully"]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No course found with the provided ID"]);
        }
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Unable to delete course"]);
    }

    $sql->close();
} else {
    http_response_code(400);
    echo json_encode(["error" => "Invalid input: course_id is required"]);
}

$connection->close();
