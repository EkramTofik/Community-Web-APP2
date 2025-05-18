<?php
session_start();
include_once "connection.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize user input
    $email = htmlspecialchars(trim($_POST['email']));
    $enteredCode = htmlspecialchars(trim($_POST['reset_code']));

    // Query to fetch the stored reset code for the given email
    $stmt = $conn->prepare("SELECT reset_code FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($storedCode);
    $stmt->fetch();
    $stmt->close();

    // Compare entered code with the stored reset code
    if ($storedCode && $enteredCode === $storedCode) {
        // Clear the reset code after successful verification
        $stmt = $conn->prepare("UPDATE user SET reset_code = NULL WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->close();

        // Redirect to password reset page
        header("Location: recreate_password_form.php?email=" . urlencode($email));
        exit();
    } else {
        // If reset code is incorrect, show error message
        header("Location: verify_code_form.php?email=" . urlencode($email) . "&msg=Invalid+reset+code");
        exit();
    }
}
?>
