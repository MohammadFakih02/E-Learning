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

$metaData = json_decode($_POST['metaData'], true);
$content = $_POST['content'] ?? ''; 
$userId = $userData['user_id'];

try {
    $stmt = $pdo->prepare("
        INSERT INTO submissions (user_id, file_name, file_path, content, created_at)
        VALUES (:user_id, :file_name, :file_path, :content, NOW())
    ");
    $stmt->execute([
        ':user_id' => $userId,
        ':file_name' => $fileName,
        ':file_path' => $filePath,
        ':content' => $content,
    ]);

    echo json_encode(['status' => 'success', 'message' => 'Submission saved successfully.']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
