<?php
session_start();
include_once __DIR__ . "/connection.php";

$error = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT Id, UserName, Email, Password, Role FROM user WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['Password'])) {
                // Store session data
                $_SESSION['id'] = $user['Id'];
                $_SESSION['name'] = $user['UserName'];
                $_SESSION['email'] = $user['Email'];
                $_SESSION['role'] = $user['Role'];

                // Redirect based on role
                switch (strtolower($user['Role'])) {
                    case 'admin':
                        header("Location: ../Admin/dashboard.php");
                        break;
                    case 'faculty':
                        header("Location: ../Faculty/dashboard.php");
                        break;
                    case 'student':
                        header("Location: ../Student/dashboard.php");
                        break;
                    case 'staff':
                        header("Location: ../Staff/dashboard.php");
                        break;
                    default:
                        $error = "Unknown user role.";
                        session_destroy();
                        header("Location: login_form.php?msg=" . urlencode($error));
                        exit();
                }
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "No account found with that email.";
        }

        $stmt->close();
    } else {
        $error = "Please enter both email and password.";
    }

    // Redirect back to login form with error
    header("Location: login_form.php?msg=" . urlencode($error));
    exit();
}
?>
