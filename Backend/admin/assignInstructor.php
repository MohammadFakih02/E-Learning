<?php

include ("../connection.php");

$userData = $jwtManager->checkToken();

if (!isset($userData['role']) || $userData['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["error" => "Access denied: Admins only"]);
    exit;
}

if (isset($_GET["course_ids"]) && isset($_GET['user_id'])) {
    $user_id = $_GET["user_id"];
    $course_ids = explode(",", $_GET["course_ids"]);  // Convert the comma-separated string into an array

    // Loop through the selected course IDs and assign/unassign them
    $response_messages = [];

    foreach ($course_ids as $course_id) {
        // Check if the instructor is already assigned to this course
        $checkSql = $connection->prepare("SELECT * FROM user_courses WHERE user_id = ? AND course_id = ?");
        $checkSql->bind_param("ii", $user_id, $course_id);
        $checkSql->execute();
        $result = $checkSql->get_result();

        if ($result->num_rows > 0) {
            // Instructor is already assigned to the course, unassign them
            $deleteSql = $connection->prepare("DELETE FROM user_courses WHERE user_id = ? AND course_id = ?");
            $deleteSql->bind_param("ii", $user_id, $course_id);
            if ($deleteSql->execute()) {
                $response_messages[] = "Unassigned from course ID $course_id successfully.";
            } else {
                $response_messages[] = "Failed to unassign from course ID $course_id.";
            }
        } else {
            // Assign the instructor to the course
            $assignSql = $connection->prepare("INSERT INTO user_courses (user_id, course_id) VALUES (?, ?)");
            $assignSql->bind_param("ii", $user_id, $course_id);
            if ($assignSql->execute()) {
                $response_messages[] = "Assigned to course ID $course_id successfully.";
            } else {
                $response_messages[] = "Failed to assign to course ID $course_id.";
            }
        }
    }

    echo json_encode(["message" => implode(" ", $response_messages)]);

} else {
    http_response_code(400);
    echo json_encode(["error" => "Input missing"]);
}
