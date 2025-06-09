

<?php
session_start();
// session_start();

// // Check if user is logged in
// function is_logged_in() {
//     return isset($_SESSION['user_id']);
// }

// // Secure file upload
// function upload_file($file, $upload_dir = 'uploads/') {
//     if (!is_dir($upload_dir)) {
//         mkdir($upload_dir, 0777, true);
//     }
    
//     $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
//     $max_size = 5 * 1024 * 1024; // 5MB
    
//     if ($file['error'] !== UPLOAD_ERR_OK) {
//         return ['error' => 'Upload error'];
//     }
    
//     if (!in_array($file['type'], $allowed_types)) {
//         return ['error' => 'Invalid file type'];
//     }
    
//     if ($file['size'] > $max_size) {
//         return ['error' => 'File too large'];
//     }
    
//     $filename = uniqid() . '-' . basename($file['name']);
//     $destination = $upload_dir . $filename;
    
//     if (move_uploaded_file($file['tmp_name'], $destination)) {
//         return ['path' => $destination];
//     }
//     return ['error' => 'Failed to move file'];
// }

// // Sanitize input
// //function sanitize($input) {
// //    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
// //}
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function sanitize($data) {
    return htmlspecialchars(trim($data));
}

function upload_file($file, $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf']) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'File upload error: ' . $file['error']];
    }
    
    if (!in_array($file['type'], $allowed_types)) {
        return ['error' => 'Invalid file type. Allowed: ' . implode(', ', array_map('basename', $allowed_types))];
    }
    
    $max_size = 5 * 1024 * 1024; // 5MB
    if ($file['size'] > $max_size) {
        return ['error' => 'File size exceeds 5MB limit.'];
    }
    
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $filename = uniqid() . '-' . basename($file['name']);
    $destination = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return ['path' => $destination];
    }
    
    return ['error' => 'Failed to move uploaded file.'];
}

?>