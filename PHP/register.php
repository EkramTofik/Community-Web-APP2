<?php
session_start();
include_once "connection.php"; // ✔️ Same folder, simple include

// Sanitize input function
function filter($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signUp'])) {
    // Collect and sanitize input
    $username = filter($_POST['username']);
    $email = filter($_POST['email']);
    $password = filter($_POST['password']);
    $confirmPassword = filter($_POST['confirm_password']);
    $role = filter($_POST['role']);
    $department = filter($_POST['department']);

    // Basic Validation
    if (empty($username) || empty($email) || empty($password) || empty($confirmPassword) || empty($role)) {
        header("Location: register_form.php?msg=All+required+fields+must+be+filled+out.");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: register_form.php?msg=Invalid+email+format.");
        exit();
    }

    if ($password !== $confirmPassword) {
        header("Location: register_form.php?msg=Passwords+do+not+match.");
        exit();
    }

    // Check if email already exists
    $check = $conn->prepare("SELECT id FROM user WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        header("Location: register_form.php?msg=An+account+with+this+email+already+exists.");
        $check->close();
        exit();
    }
    $check->close();

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO user (username, email, password, role, department) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $username, $email, $hashedPassword, $role, $department);

    if ($stmt->execute()) {
        // Set session variables (optional)
        $_SESSION['email'] = $email;
        $_SESSION['role'] = $role;

        // Redirect all users to the login page after registration
        header("Location: login_form.php?msg=Registration+successful,+please+log+in.");
        exit();
    } else {
        header("Location: register_form.php?msg=Error+during+registration.+Please+try+again.");
    }

    $stmt->close();
    $conn->close();
}
