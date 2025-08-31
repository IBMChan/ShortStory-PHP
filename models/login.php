<?php
session_start();
include("../db/db.php");

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT user_id, u_name, password FROM user WHERE u_name=? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['user_id']; // ✅ keep for later use
            $_SESSION['username'] = $row['u_name'];
            header("Location: home.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found.";
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
