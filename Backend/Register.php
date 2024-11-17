<?php

include("connection.php");

$data = json_decode(file_get_contents("php://input"), true);

if(isset($data["username"]) && $data["passowrd"] && $data["email"]) {
    $username = $data["username"];
    $email = $data["email"];
    $password = $data["password"];
    check_password($password);
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $query = $connection->prepare("INSERT INTO users (name, password, budget) VALUES (?, ?, ?)");
    $query->bind_param("sss", $username, $hashedPassword, $budget);

}else{
    http_response_code(400);
    echo "invalid input";
}

function check_password( $password ){
    if(strlen($password)<12){
        echo 'password must be longer than 12 characters';
    }else if(!preg_match('/[A-Z]/', $password)){
        echo 'password must contain at least one upper case letter';
    }else if(!preg_match('/[a-z]/', $password)) {
        echo 'password must contain at least one lower case letter';
    }else if(!preg_match('/[^a-zA-Z0-9]/', $password)){
        echo 'password must contain at least one special character';
    }
}