<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>AASTU Community | Register</title>
    <link rel="stylesheet" href="../Community Web App/css/registration.css" />
  </head>
  <body>
    <div class="register-container">
      <h2>AASTU Community Registration</h2>
      <form action="reg.php" method="POST" enctype="multipart/form-data">
        
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required placeholder="Enter your username" /><br /><br />

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required placeholder="Enter your email" /><br /><br />

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required placeholder="Create a password" /><br /><br />

        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required placeholder="Repeat your password" /><br /><br />

        <label for="role">Role:</label>
        <select name="role" id="role" required>
          <option value="">-- Select Role --</option>
          <option value="student">Student</option>
          <option value="faculty">Faculty</option>
          <option value="staff">Staff</option>
        </select><br /><br />

        <label for="department">Department:</label>
        <input type="text" id="department" name="department" placeholder="e.g. Software Engineering" /><br /><br />

        <button type="submit" name="signUp">Sign Up</button><br /><br />

        <p>Already have an account?
          <a href="login_form.php">Sign In</a>
        </p>
      </form>
    </div>
  </body>
</html>
