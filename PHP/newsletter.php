<?php
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    
    $stmt = $pdo->prepare('SELECT * FROM newsletter WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $message = 'Email already subscribed';
    } else {
        $stmt = $pdo->prepare('INSERT INTO newsletter (email) VALUES (?)');
        $stmt->execute([$email]);
        $message = 'Subscribed successfully';
    }
    
    header("Location: homepage.php?message=" . urlencode($message));
    exit;
}
?>