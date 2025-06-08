<?php
header('Content-Type: application/json');

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration
$upload_dir = 'images/hero/';
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
$max_size = 5 * 1024 * 1024; // 5MB

// Create upload directory if it doesn't exist
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

try {
    // Check if file was uploaded
    if (!isset($_FILES['image'])) {
        throw new Exception('No file was uploaded');
    }

    $file = $_FILES['image'];
    $filename = $_POST['filename'] ?? $file['name'];

    // Validate file
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Upload failed with error code: ' . $file['error']);
    }

    if (!in_array($file['type'], $allowed_types)) {
        throw new Exception('Invalid file type. Only JPG, PNG and GIF are allowed');
    }

    if ($file['size'] > $max_size) {
        throw new Exception('File is too large. Maximum size is 5MB');
    }

    // Generate the target path
    $target_path = $upload_dir . $filename;

    // Backup existing file if it exists
    if (file_exists($target_path)) {
        $backup_path = $target_path . '.backup.' . date('Y-m-d-H-i-s');
        rename($target_path, $backup_path);
    }

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        echo json_encode([
            'success' => true,
            'message' => 'File uploaded successfully',
            'path' => $target_path
        ]);
    } else {
        throw new Exception('Failed to move uploaded file');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 