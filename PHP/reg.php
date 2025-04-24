<?php
session_start();
include 'connection.php'; // ensure $conn is valid

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
        die("All required fields must be filled out.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    if ($password !== $confirmPassword) {
        die("Passwords do not match.");
    }

    // Check if email already exists
    $check = $conn->prepare("SELECT id FROM user WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "An account with this email already exists.";
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
        // Set session and redirect by role
        $_SESSION['email'] = $email;
        $_SESSION['role'] = $role;

        // Redirect based on user role
        switch ($role) {
            case 'admin':
                header("Location: admin_dashboard.php");
                break;
            case 'faculty':
                header("Location: faculty_home.php");
                break;
            case 'student':
            default:
                header("Location: student_home.php");
                break;
        }
        exit();
    } else {
        echo "Error during registration. Please try again.";
    }

    $stmt->close();
    $conn->close();
}
?>
