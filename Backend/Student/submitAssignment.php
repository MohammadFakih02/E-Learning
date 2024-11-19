<?php

include("connection.php"); 

$userData = $jwtManager->checkToken();
if (!isset($userData['role']) || $userData['role'] !== 'student') {
    http_response_code(403);
    echo json_encode(["error" => "Access denied: Students only"]);
    exit;
}

$fileName = null;
$filePath = null;

if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $fileTmpPath = $_FILES['file']['tmp_name'];
    $fileName = basename($_FILES['file']['name']);
    $filePath = $uploadDir . $fileName;

    if (!move_uploaded_file($fileTmpPath, $filePath)) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded file.']);
        exit;
    }
}

$content = $_POST['content'] ?? '';
$userId = $userData['user_id']; 

    $sql = $mysqli->prepare("
        INSERT INTO submissions (user_id, file_name, file_path, content, created_at)
        VALUES (?, ?, ?, ?, NOW())
    ");
    $sql->bind_param("isss", $userId, $fileName, $filePath, $content);

    if ($sql->execute()) {


    echo json_encode(['status' => 'success', 'message' => 'Submission saved successfully.']);

    $sql->close();
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'database error']);
}
$mysqli->close();
