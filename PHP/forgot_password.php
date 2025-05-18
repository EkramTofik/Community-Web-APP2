<?php
session_start();
include_once "connection.php";

// Load PHPMailer classes
require_once '../PHPMailer/PHPMailer.php';
require_once '../PHPMailer/SMTP.php';
require_once '../PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: forgot_password_form.php?msg=Invalid+email+format");
        exit();
    }

    // Check if email exists in database
    $stmt = $conn->prepare("SELECT id FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        $stmt->close();
        header("Location: forgot_password_form.php?msg=Email+not+found");
        exit();
    }
    $stmt->close();

    // Generate a 6-digit reset code
    $resetCode = rand(100000, 999999);

    // Save reset code to database
    $stmt = $conn->prepare("UPDATE user SET reset_code = ? WHERE email = ?");
    $stmt->bind_param("ss", $resetCode, $email);
    $stmt->execute();
    $stmt->close();

    // Send reset code via email
    $mail = new PHPMailer(true);

    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'ekramtofik44@gmail.com'; // Your Gmail
        $mail->Password = 'rmxovxclrokalgoi'; // App Password (from Google)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Use SSL
        $mail->Port = 465;

        // Email content
        $mail->setFrom('ekramtofik44@gmail.com', 'Community Web App');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Your Password Reset Code';
        $mail->Body = "Hello,<br><br>Your password reset code is: <strong>$resetCode</strong><br><br>If you didn't request this, please ignore this email.";

        $mail->send();
        header("Location: verify_code_form.php?email=" . urlencode($email) . "&msg=Check+your+email+for+the+reset+code");
        exit();
    } catch (Exception $e) {
        echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
