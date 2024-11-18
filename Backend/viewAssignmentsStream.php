<?php

include("connection.php");

$userData = $jwtManager->checkToken();
if (!isset($userData['role'])) {
    http_response_code(403);
    echo json_encode(["error" => "Access denied: not logged in"]);
    exit;
}

$assignments = [];


$sql = $connection->prepare("SELECT assignments.assignment_id,users.username,assignments.title,
                                    assignments.created_at,assignments.deadline 
                                    FROM users JOIN assignmenets on users.user_id = assignments.instructor_id");

if ($sql->execute()) {
    $result = $sql->get_result();

    while ($row = $result->fetch_assoc()) {
        $assignments[] = $row;
    }

    echo json_encode(["success" => true, "data" => $announcements]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Unable to access database"]);
}
