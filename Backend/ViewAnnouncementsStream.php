<?php

include("connection.php");

$userData = $jwtManager->checkToken();
if (!isset($userData['role'])) {
    http_response_code(403);
    echo json_encode(["error" => "Access denied: not logged in"]);
    exit;
}
if(!isset($_GET['course_id'])){
    http_response_code(400);
    echo json_encode(['error'=> 'invalid course']);
}

$course_id= $_GET["course_id"];
$announcements = [];


$sql = $connection->prepare("SELECT announcements.announcement_id, users.username, announcements.content,
                                    announcements.title, announcements.date
                                    FROM users
                                    JOIN announcements ON users.user_id = announcements.instructor_id
                                    ORDER BY announcements.date DESC;");

if ($sql->execute()) {
    $result = $sql->get_result();

    while ($row = $result->fetch_assoc()) {
        $announcements[] = $row;
    }

    echo json_encode(["success" => true, "data" => $announcements]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Unable to access database"]);
}