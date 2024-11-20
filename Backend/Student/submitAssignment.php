<?php

include("../connection.php");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

$userData = $jwtManager->checkToken();
if (!isset($userData['role']) || $userData['role'] !== 'student') {
    http_response_code(403);
    echo json_encode(["error" => "Access denied: Students only"]);
    exit;
}

$assignment_id = $_GET["ass"];
$fileName = null;
$filePath = null;
if (isset($_FILES['file'])) {
    var_dump($_FILES); // Check the file details received from the frontend
} else {
    echo json_encode(['status' => 'error', 'message' => 'No file uploaded']);
    exit;
}

if (isset($_FILES['file'])) {
    if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['status' => 'error', 'message' => 'File upload error: ' . $_FILES['file']['error']]);
        exit;
    }

    $uploadDir = '../uploads/';
    if (!is_dir($uploadDir)) {
        echo json_encode(['status' => 'error', 'message' => 'Upload directory does not exist.']);
        exit;
    }

    $fileTmpPath = $_FILES['file']['tmp_name'];
    $fileName = basename($_FILES['file']['name']);
    $filePath = $uploadDir . $fileName;

    if (!move_uploaded_file($fileTmpPath, $filePath)) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded file to: ' . $filePath]);
        exit;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No file uploaded.']);
    exit;
}

$content = $_POST['content'] ?? '';
$userId = $userData['user_id']; 

$sql = $connection->prepare("
    REPLACE INTO submissions (student_id, file_name, file_path, content, assignment_id)
    VALUES (?, ?, ?, ?, ?)
");
$sql->bind_param("isssi", $userId, $fileName, $filePath, $content, $assignment_id);

if ($sql->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Submission saved successfully.']);
    $sql->close();
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
}

$connection->close();
