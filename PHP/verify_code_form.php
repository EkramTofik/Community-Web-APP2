<?php
$email = $_GET['email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verify Reset Code</title>
  <link rel="stylesheet" href="../../COMMUNITY WEB APP/css/verify_code.css" />
</head>
<body>
  <div class="wrapper">
    <form action="verify_code.php" method="POST">
      <h2>Enter Reset Code</h2>
       <?php if (isset($_GET['msg'])): ?>
        <p class="error-message"><?php echo htmlspecialchars($_GET['msg']); ?></p>
      <?php endif; ?>
      <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
      <input type="text" name="reset_code" placeholder="Enter 6-digit code" required>
      
     
      
      <button type="submit">Verify</button>
    </form>
  </div>
</body>
</html>
