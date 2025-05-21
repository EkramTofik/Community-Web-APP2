<?php
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

// Fetch ads
$stmt = $pdo->query('SELECT a.*, u.full_name FROM ads a JOIN users u ON a.created_by = u.id ORDER BY a.created_at DESC');
$ads = $stmt->fetchAll();

// Handle ad submission
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_ad'])) {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $category = sanitize($_POST['category']);
    $contact_info = sanitize($_POST['contact_info']);
    $created_by = $_SESSION['user_id'];

    // Validate inputs
    if (empty($title)) {
        $errors[] = 'Title is required.';
    }
    if (empty($description)) {
        $errors[] = 'Description is required.';
    }
    if (empty($category)) {
        $errors[] = 'Category is required.';
    }
    if (empty($contact_info)) {
        $errors[] = 'Contact information is required.';
    }

    // Handle image upload
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $upload = upload_file($_FILES['image'], ['image/jpeg', 'image/png', 'image/gif']);
        if (isset($upload['error'])) {
            $errors[] = $upload['error'];
        } else {
            $image_path = $upload['path'];
        }
    }

    // Insert ad if no errors
    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO ads (title, description, category, contact_info, image, created_by) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$title, $description, $category, $contact_info, $image_path, $created_by]);
        header('Location: ads.php');
        exit;
    }
}

// Handle ad deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_ad'])) {
    $ad_id = (int)$_POST['ad_id'];
    $user_id = $_SESSION['user_id'];

    // Verify ownership
    $stmt = $pdo->prepare('SELECT image, created_by FROM ads WHERE id = ?');
    $stmt->execute([$ad_id]);
    $ad = $stmt->fetch();

    if ($ad && $ad['created_by'] == $user_id) {
        // Delete image file if exists
        if ($ad['image'] && file_exists($ad['image'])) {
            unlink($ad['image']);
        }
        // Delete ad
        $stmt = $pdo->prepare('DELETE FROM ads WHERE id = ?');
        $stmt->execute([$ad_id]);
    }
    header('Location: ads.php');
    exit;
}
?>