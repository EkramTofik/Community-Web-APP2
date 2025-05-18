<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Forgot Password</title>
  <link rel="stylesheet" href="../../COMMUNITY WEB APP/css/forgot_password.css" />
</head>
<body>
  <form action="forgot_password.php" method="POST">
    <div class="login-card">
      <h1>Forgot Password</h1>
      <p>Enter your email to get a reset code</p>
      <input type="email" name="email" class="login-input" placeholder="Your email" required />
      <button type="submit" class="login-button">Send Reset Code</button>

      <?php if (!empty($_GET['msg'])): ?>
        <p class="message"><?php echo htmlspecialchars($_GET['msg']); ?></p>
      <?php endif; ?>

      <div class="signup-text">
        <a href="login_form.php">Back to Login</a>
      </div>
    </div>
  </form>
</body>
</html>
