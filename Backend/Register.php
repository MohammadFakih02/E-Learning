<?php

include("connection.php");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data["username"]) && $data["password"] && $data["email"]) {
    $username = $data["username"];
    $email = $data["email"];
    $password = $data["password"];

    $checkEmailSql = $connection->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $checkEmailSql->bind_param("s", $email);
    $checkEmailSql->execute();
    
    $checkEmailSql->store_result();
    $checkEmailSql->bind_result($emailCount);
    $checkEmailSql->fetch();

    if ($emailCount > 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Email is already taken'
        ]);
        $checkEmailSql->close(); 
    } else {
        check_password($password);
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = $connection->prepare("INSERT INTO users (username,email,password,role,is_banned) VALUES (?, ?, ?, 'student', 0)");
        $sql->bind_param("sss", $username, $email, $hashedPassword);

        if ($sql->execute()) {
            $response = [
                'success' => true,
                'message' => 'User created successfully'
            ];
            echo json_encode($response);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to create user'
            ]);
        }
        $sql->close(); 
    }
} else {
    http_response_code(400);
    echo "Invalid input";
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
