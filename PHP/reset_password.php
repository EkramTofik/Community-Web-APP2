<?php
session_start();
include_once "connection.php";

// Get email from GET request
$email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
$newPassword = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';
$confirmPassword = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';

// Check if the passwords match
if ($newPassword === $confirmPassword) {
    // Hash the new password for security
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update the password in the database
    $stmt = $conn->prepare("UPDATE user SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $hashedPassword, $email);
    if ($stmt->execute()) {
        // Password updated successfully, redirect to login page
        header("Location: login_form.php?msg=Password+updated+successfully");
        exit();
    } else {
        // Error updating password
        header("Location: reset_password_form.php?msg=Error+updating+password");
        exit();
    }
} else {
    // Passwords do not match
    header("Location: reset_password_form.php?msg=Passwords+do+not+match");
    exit();
}
?>
