<?php
$msg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  <link rel="stylesheet" href="../../Community-Web-APP2/css/loginphp.css" />
</head>
<body>
  <form action="login.php" method="POST">
    <div class="login login-card">
      <h1>Login</h1>

      <!-- ✅ Error Message Display -->
      <?php if ($msg): ?>
        <div class="success-message"  style="color: green;
  font-size: 16px;
  margin-bottom: 15px;">
          <?php echo $msg; ?>
        </div>
      <?php endif; ?>

      <!-- ✅ Input Fields -->
      <input
        type="email"
        placeholder="Email"
        name="email"
        class="login-input"
        required
      />
      <input
        type="password"
        placeholder="Password"
        name="password"
        class="login-input"
        required
      />

      <!-- ✅ Forgot Password Link -->
      <div class="forgot-password">
        <a href="forgot_password_form.php">Forgot Password?</a>
      </div>

      <!-- ✅ Buttons -->
      <button type="submit" class="login-button" name="signIn">SIGN IN</button>
      <button class="signup signup-text">
        <a href="register_form.php">Sign Up</a>
      </button>
    </div>
  </form>
</body>
</html>
