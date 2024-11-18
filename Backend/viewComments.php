<?php

include("connection.php");

$userData = $jwtManager->checkToken();
if (!isset($userData['role'])) {
    http_response_code(403);
    echo json_encode(["error" => "Access denied: not logged in"]);
    exit;
}

if ($userData["role"] == "student") {
    $private= 0;
}else{  
    $private= 1;
}

$comments = [];

if (isset($_GET["assignment_id"])) {
    $assignment_id = $_GET['assignment_id'];
    $sql = $connection->prepare("
        SELECT 
        comments.student_id, users.username, comments.content, comments.date 
        FROM  comments JOIN users  ON 
        comments.student_id = users.user_id 
        WHERE comments.private = ? AND comments.assignment_id = ?");

    $sql->bind_param("ii", $private, $assignment_id);

    if ($sql->execute()) {
        $result = $sql->get_result();

        while ($row = $result->fetch_assoc()) {
            $comments[] = $row;
        }

        echo json_encode(["success" => true, "data" => $comments]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Unable to access database"]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "Invalid input"]);
}