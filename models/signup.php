
<?php
require_once '../services/auth.php';
$auth = new Auth($conn);

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $result = $auth->signup(
        $_POST['username'],
        $_POST['password'],
        $_POST['email'],
        $_POST['phone'],
        $_POST['confirm_password']
    );
    if ($result['status']) {
        echo "<script>alert('".$result['message']."'); window.location.href='login.php';</script>";
        exit();
    } else {
        $error = $result['message'];
    }
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
