<?php
include_once "connection.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // 1. Basic validations
    if (empty($email) || empty($newPassword) || empty($confirmPassword)) {
        die("All fields are required.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    if ($newPassword !== $confirmPassword) {
        die("Passwords do not match.");
    }

    // 2. Optional: Enforce password strength (you can improve this)
    if (strlen($newPassword) < 6) {
        die("Password must be at least 6 characters long.");
    }

    // 3. Hash and update password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE user SET password = ?, reset_code = NULL WHERE email = ?");
    $stmt->bind_param("ss", $hashedPassword, $email);

    if ($stmt->execute()) {
        echo "✅ Password successfully updated. <a href='login_form.php'>Login now</a>";
    } else {
        echo "❌ Error updating password. Please try again.";
    }

    $stmt->close();
    $conn->close();
}
?>
