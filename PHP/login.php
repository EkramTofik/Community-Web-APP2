<?php
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

if (is_logged_in()) {
    header('Location: homepage.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: homepage.php');
        exit;
    } else {
        $error = 'Invalid email or password';
    }
}
?>

<?php include 'includes/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
    <head>
    <link rel="stylesheet" href="css/login.css">
    </head>
<body>
<div class="container">
    <h2>Login</h2>
    <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST">
        <label>Email: <input type="email" name="email" required></label><br>
        <label>Password: <input type="password" name="password" required></label><br>
        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register</a></p>
</div>
</body>
