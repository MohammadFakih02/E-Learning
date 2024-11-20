<?php

include("connection.php");

$userData = $jwtManager->checkToken();
if (!isset($userData['role'])) {
    http_response_code(403);
    echo json_encode(["error" => "Access denied: not logged in"]);
    exit;
}
$user_id=$userData["user_id"];
$courses = [];

$sql = $connection->prepare("SELECT 
    courses.course_id, 
    courses.course_name
FROM 
    courses 
LEFT JOIN 
    user_courses 
ON 
    courses.course_id = user_courses.course_id 
    AND user_courses.user_id = ?
WHERE 
    user_courses.course_id IS NULL;");
$sql->bind_param("i", $user_id);
if($sql->execute()){
    $result = $sql->get_result();
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
    echo json_encode(["success" => true, "data" => $courses]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Unable to access database"]);
}

