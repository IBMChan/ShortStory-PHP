<?php
require_once '../services/auth.php';
$auth = new Auth($conn);

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $result = $auth->login($_POST['username'], $_POST['password']);
    if ($result['status']) {
        header("Location: home.php");
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
  <title>Login - Short Story</title>
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
    <h2>Login</h2>

    <?php if (!empty($error)): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
      <input type="text" name="username" placeholder="Enter Username" required>
      <input type="password" name="password" placeholder="Enter Password" required>
      <button type="submit" class="btn">Login</button>
    </form>

    <p>Don't have an account? <a href="signup.php">Register here</a></p>
  </div>
</section>

<footer>
  <p>&copy; 2025 My PHP Project</p>
  <p>All rights reserved.</p>
</footer>
</body>
</html>
