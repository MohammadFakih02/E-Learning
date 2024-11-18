<?php

include("connection.php");

$userData = $jwtManager->checkToken();
if (!isset($userData['role'])) {
    http_response_code(403);
    echo json_encode(["error" => "Access denied: not logged in"]);
    exit;
}

if (isset($_GET["assignment_id"])) {
    $assignment_id = $_GET["assignment_id"];

    $sql = $connection->prepare("
        SELECT 
            assignments.assignment_id, 
            users.username AS instructor_name, 
            assignments.title, 
            assignments.created_at, 
            assignments.deadline, 
            assignments.description
        FROM 
            users 
        JOIN 
            assignments 
        ON 
            users.user_id = assignments.instructor_id
        WHERE 
            assignments.assignment_id = ?
    ");

    $sql->bind_param("i", $assignment_id);

    if ($sql->execute()) {
        $result = $sql->get_result();

        if ($result->num_rows > 0) {
            $assignment = $result->fetch_assoc();
            echo json_encode(["success" => true, "data" => $assignment]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Assignment not found"]);
        }
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Unable to execute query"]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "Assignment ID needed"]);
}
