<?php

include ("../connection.php");

$userData = $jwtManager->checkToken();

if (!isset($userData['role']) || $userData['role'] !== 'instructor') {
    http_response_code(403);
    echo json_encode(["error" => "Access denied: Instructors only"]);
    exit;
}

if (!isset($_GET['assignment_id']) || empty($_GET['assignment_id'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing or invalid assignment ID"]);
    exit;
}

$assignmentId =$_GET['assignment_id'];
    $sql = $connection->prepare("
        SELECT 
            submissions.id AS submission_id,
            submissions.user_id,
            submissions.file_name,
            submissions.file_path,
            submissions.content,
            submissions.created_at,
            users.name AS student_name
        FROM 
            submissions
        INNER JOIN 
            users ON submissions.user_id = users.id
        WHERE 
            submissions.assignment_id = ?
        ORDER BY 
            submissions.created_at DESC
    ");

    $sql->bind_param("i", $assignmentId);
    if($sql->execute()){
    $result = $sql->get_result();

    $submissions = [];
    while ($row = $result->fetch_assoc()) {
        $submissions[] = $row;
    }

    echo json_encode([
        'status' => 'success',
        'submissions' => $submissions
    ]);
}else{
    http_response_code(500);
    echo json_encode(['error' => 'database error']);
}

$mysqli->close();
