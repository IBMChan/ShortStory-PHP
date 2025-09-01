<?php
require_once '../services/auth.php';
require_once '../services/send_email.php'; // include the SMTP email function
$auth = new Auth($conn);

if ($result['status']) {
    // Prepare email content
    $to = $_POST['email'];
    $subject = "Welcome to Short Story!";
    $body = "
        <html>
        <head><title>Welcome to Short Story</title></head>
        <body>
            <h2>Hello ".$_POST['username'].",</h2>
            <p>Thank you for signing up at Short Story!</p>
            <p>We're excited to have you on board.</p>
            <p>Best Regards,<br>Short Story Team</p>
        </body>
        </html>
    ";

    // Send email via SMTP
    send_email_smtp($to, $subject, $body);

    echo "<script>alert('".$result['message']."'); window.location.href='login.php';</script>";
    exit();
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sign Up - Short Story</title>
  <link rel="stylesheet" href="../assets/style.css"> 
  <link href="https://fonts.googleapis.com/css2?family=Tangerine:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
<header class="login-header">
  <p class="site-title">Short Story</p>
  <nav class="back-nav">
    <ul>
        <li><a href="../index.php">⬅ Back</a></li>
    </ul>
  </nav>
</header>

<section class="login">
  <div class="form-container">
    <h2>Sign Up</h2>
    <?php if($error): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
      <input type="text" name="username" placeholder="Enter Username" required>
      <input type="password" name="password" placeholder="Enter Password" required>
      <input type="password" name="confirm_password" placeholder="Re-Enter Password" required>
      <input type="email" name="email" placeholder="Enter Email" required>
      <input type="tel" name="phone" placeholder="Valid phone Number" pattern="\d{10}" required>
      <button type="submit" class="btn">Sign Up</button>
    </form>

    <p>Already have an account? <a href="login.php">Login here</a></p>
  </div>
</section>

<footer>
  <p>&copy; 2025 My PHP Project</p>
  <p>All rights reserved.</p>
</footer>
</body>
</html>
