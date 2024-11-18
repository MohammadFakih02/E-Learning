<?php

include("../connection.php");

$userData = $jwtManager->checkToken();
$data = json_decode(file_get_contents("php://input"), true);

// Check if the user is an admin
if (!isset($userData['role']) || $userData['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["error" => "Access denied: Admins only"]);
    exit;
}

if (isset($data["username"], $data["password"], $data["email"], $data['course_id'])) {
    $username = $data['username'];
    $password = $data['password'];
    $email = $data['email'];
    $course_id = $data['course_id'];
    check_password($password);

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $connection->begin_transaction();

        $sql = $connection->prepare('INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, "instructor")');
        $sql->bind_param("sss", $username, $email, $hashedPassword);
        if (!$sql->execute()) {
            throw new Exception("Failed to insert user");
        }

        $user_id = $connection->insert_id;

        $sql = $connection->prepare('INSERT INTO user_courses (user_id, course_id) VALUES (?, ?)');
        $sql->bind_param("ii", $user_id, $course_id);
        if (!$sql->execute()) {
            throw new Exception("Failed to assign user to course");
        }

        // Commit the transaction
        $connection->commit();

        echo json_encode(["message" => "Added new instructor"]);
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
}