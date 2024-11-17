<?php

include("connection.php");

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data["username"]) && $data["passowrd"] && $data["email"]) {
    $username = $data["username"];
    $email = $data["email"];
    $password = $data["password"];
    check_password($password);
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $sql = $connection->prepare("INSERT INTO users (name,email,password,role,is_banned) values(?,?,?,student,0)");
    $sql->bind_param("sss", $username, $email, $hashedPassword);

    if ($sql->execute()) {
        $response = [
            'success' => true,
            'message' => 'User created successfully'
        ];
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to create user'
        ]);
    }
} else {
    http_response_code(400);
    echo "invalid input";
}


function check_password($password)
{
    if (strlen($password) < 12) {
        echo 'password must be longer than 12 characters';
    } else if (!preg_match('/[A-Z]/', $password)) {
        echo 'password must contain at least one upper case letter';
    } else if (!preg_match('/[a-z]/', $password)) {
        echo 'password must contain at least one lower case letter';
    } else if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
        echo 'password must contain at least one special character';
    }
}
