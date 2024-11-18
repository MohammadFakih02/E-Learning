<?php

include("connection.php");


$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['email']) && isset($data['password'])) {
    $email = $data['email'];
    $password = $data['password'];

    $sql = $connection->prepare('SELECT user_id,password,email,is_banned,role from users WHERE email = ?');
    $sql->bind_param('s', $email);
    $sql->execute();
    $result = $sql->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if ($user['is_banned'] == 1) {
            echo json_encode([
                "success" => false,
                "message" => "This account has been banned."
            ]);
            exit;
        }
        
        $payload = [
            "user_id" => $user["user_id"],
            "role" => $user["role"],    
        ];
        if (password_verify($password, $user['password'])) {
            $token = $jwtManager->createToken($payload);
            echo json_encode([
                "token" => $token,
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "Invalid Credentials."
            ]);
        }
    } else {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Invalid Credentials."
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid input'
    ]);
}
