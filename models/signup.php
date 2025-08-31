<?php
require_once '../db/db.php';
require_once 'helpers.php'; // ID generator
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } elseif (!preg_match("/^\d{10}$/", $phone)) {
        $error = "Phone number must be exactly 10 digits.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check username
        $stmt_check = $conn->prepare("SELECT user_id FROM user WHERE u_name = ?");
        $stmt_check->bind_param("s", $username);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $error = "Username already exists!";
        } else {
            // Generate user_id (U1, U2…)
            $user_id = generateId($conn, "user", "U", "user_id");

            $stmt = $conn->prepare("INSERT INTO user (user_id, u_name, password, email, contact, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("sssss", $user_id, $username, $hashed_password, $email, $phone);

            if ($stmt->execute()) {
                echo "<script>alert('Successfully registered!'); window.location.href='login.php';</script>";
                exit();
            } else {
                $error = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
        $stmt_check->close();
        $conn->close();
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
