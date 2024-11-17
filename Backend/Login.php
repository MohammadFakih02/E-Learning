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
            exit();
        }
        $role = $user["role"];
        $payload = [
            "user_id" => $user["user_id"],
            "email" => $user["email"],
        ];
        if (password_verify($password, $user['password'])) {
            $token = $jwtManager->createToken($payload);
            echo json_encode([
                "token" => $token,
                "role" => $role,
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Invalid password."
            ]);
        }
    } else {
        echo json_encode([
            "success" => false,
            "message" => "User not found."
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid input'
    ]);
}
