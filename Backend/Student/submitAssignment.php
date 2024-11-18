<?php

include("connection.php");

// Verify user role
$userData = $jwtManager->checkToken();
if (!isset($userData['role']) || $userData['role'] !== 'student') {
    http_response_code(403);
    echo json_encode(["error" => "Access denied: Students only"]);
    exit;
}

if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $fileTmpPath = $_FILES['file']['tmp_name'];
    $fileName = basename($_FILES['file']['name']);
    $destination = $uploadDir . $fileName;

    if (move_uploaded_file($fileTmpPath, $destination)) {
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
                ':file_path' => $destination,
                ':content' => $content,
            ]);

            echo json_encode(['status' => 'success', 'message' => 'File uploaded and data saved successfully.']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded file.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No file uploaded or there was an upload error.']);
}
