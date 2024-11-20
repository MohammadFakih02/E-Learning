<?php

include ("../connection.php");

$userData = $jwtManager->checkToken();

if (!isset($userData['role']) || $userData['role'] !== 'student') {
    http_response_code(403);
    echo json_encode(["error" => "Access denied: Students only"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if(isset($data["assignment_id"], $data["content"], $data["private"])) {
    $assignment_id = $data["assignment_id"];
    $content = trim($data["content"]);
    $private = (int)$data["private"];

    if (empty($content)) {
        http_response_code(400);
        echo json_encode(["error" => "Comment content cannot be empty."]);
        exit;
    }

    if ($private !== 0 && $private !== 1) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid value for 'private'. Must be 0 or 1."]);
        exit;
    }

    $date = date('m/d/Y h:i:s a', time());
    $user_id = $userData["user_id"];
    
    $sql = $connection->prepare('INSERT INTO comments (student_id, assignment_id, content, date, private) VALUES (?, ?, ?, ?, ?)');
    $sql->bind_param("iissi", $user_id, $assignment_id, $content, $date, $private);

    if($sql->execute()) {
        echo json_encode(["message"=> "Created comment successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["error"=> "Unable to create comment, please try again later"]);
    }

} else {
    http_response_code(400);
    echo json_encode(["error"=> "Invalid input: assignment_id, content, and private are required"]);
}
