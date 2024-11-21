<?php

include("../connection.php");

$userData = $jwtManager->checkToken();
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($userData['role']) || $userData['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["error" => "Access denied: Admins only"]);
    exit;
}

if (isset($data["username"], $data["password"], $data["email"], $data['course_ids']) && is_array($data['course_ids'])) {
    $username = $data['username'];
    $password = $data['password'];
    $email = $data['email'];
    $course_ids = $data['course_ids'];
    
    check_password($password);

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $connection->begin_transaction();

    try {
        $sql = $connection->prepare('INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, "instructor")');
        $sql->bind_param("sss", $username, $email, $hashedPassword);
        if (!$sql->execute()) {
            throw new Exception("Failed to insert user");
        }

        $user_id = $connection->insert_id;

        foreach ($course_ids as $course_id) {
            $sql = $connection->prepare('INSERT INTO user_courses (user_id, course_id) VALUES (?, ?)');
            $sql->bind_param("ii", $user_id, $course_id);
            if (!$sql->execute()) {
                throw new Exception("Failed to assign instructor to course with ID $course_id");
            }
        }

        $connection->commit();

        echo json_encode(["message" => "Added new instructor and assigned courses", "data" => ["user_id" => $user_id]]);


    } catch (Exception $e) {
        $connection->rollback();
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }

} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input or missing course_ids']);
}

function check_password($password)
{
    if (strlen($password) < 12) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Password must be longer than 12 characters'
        ]);
        exit;
    } else if (!preg_match('/[A-Z]/', $password)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'password must contain at least one upercase letter'
        ]);
        exit;
    } else if (!preg_match('/[a-z]/', $password)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'password must contain at least one lowercase letter'
        ]);
        exit;
    } else if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'password must contain at least one special character'
        ]);
        exit;
    }
}