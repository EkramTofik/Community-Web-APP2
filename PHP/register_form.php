<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>AASTU Community | Register</title>
    <link rel="stylesheet" href="../../COMMUNITY WEB APP/css/registration.css" />
  </head>
  <body>
    <div class="register-card">
      <h2>AASTU Community Registration</h2>
      <form action="register.php" method="POST" enctype="multipart/form-data">

        <input class="register-input" type="text" id="username" name="username" required placeholder="Enter your username" />

        <input class="register-input" type="email" id="email" name="email" required placeholder="Enter your email" />

        <input class="register-input" type="password" id="password" name="password" required placeholder="Create a password" />

        <input class="register-input" type="password" id="confirm_password" name="confirm_password" required placeholder="Repeat your password" />

       

        <input class="register-input" type="text" id="department" name="department" placeholder="e.g. Software Engineering" />
        <select class="register-input" name="role" id="role" required>
          <option value="">-- Select Role --</option>
          <option value="student">Student</option>
          <option value="faculty">Faculty</option>
          <option value="staff">Staff</option>
        </select>

        <button class="register-button" type="submit" name="signUp">Sign Up</button>

        <p class="login-text">Already have an account? <a href="login_form.php">Sign In</a></p>
      </form>
    </div>
  </body>
</html>
