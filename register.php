<?php
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

if (is_logged_in()) {
    header('Location: homepage.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $full_name = sanitize($_POST['full_name']);
    
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? OR username = ?');
    $stmt->execute([$email, $username]);
    if ($stmt->fetch()) {
        $error = 'Email or username already exists';
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (username, email, password, full_name) VALUES (?, ?, ?, ?)');
        if ($stmt->execute([$username, $email, $hashed_password, $full_name])) {
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['username'] = $username;
            header('Location: homepage.php');
            exit;
        } else {
            $error = 'Registration failed';
        }
    }
}
?>

<?php include 'includes/header.php'; ?>
<div class="container">
    <h2>Register</h2>
    <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST">
        <label>Username: <input type="text" name="username" required></label><br>
        <label>Email: <input type="email" name="email" required></label><br>
        <label>Full Name: <input type="text" name="full_name" required></label><br>
        <label>Password: <input type="password" name="password" required></label><br>
        <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login</a></p>
</div>
