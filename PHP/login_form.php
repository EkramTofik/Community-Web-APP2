<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <link rel="stylesheet" href="../Community Web App/css/login.css" />
  </head>
  <body>
    <form action="login.php" method="POST">
      <div class="login login-card">
        <h1>Login</h1>
        <input type="text" class="login login-input" placeholder="Username" name="username" required />
        <input type="email" placeholder="Email" name="email" required />
        <input type="password" class="login login-input" placeholder="Password" name="password" required />
        <button type="submit" class="login login-button" name="signIn">SIGN IN</button>
        <button class="signup signup-text"><a href="registration_form.php">Sign Up</a></button>
      </div>
    </form>
  </body>
</html>
