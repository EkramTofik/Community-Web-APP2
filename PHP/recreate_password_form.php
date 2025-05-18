<?php
$email = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Reset Password</title>
  <link rel="stylesheet" href="../../COMMUNITY WEB APP/css/recreate_password.css" />
</head>
<body>
  <form action="reset_password.php" method="POST">
    <div class="login-card">
      <h1>Reset Password</h1>
      <input type="hidden" name="email" value="<?php echo $email; ?>" />
      <input type="password" name="new_password" class="login-input" placeholder="New Password" required />
      <input type="password" name="confirm_password" class="login-input" placeholder="Confirm Password" required />
      <button type="submit" class="login-button">Update Password</button>
    </div>
  </form>
</body>
</html>
