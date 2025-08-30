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
      <h2>Sign Up </h2>
      <h2>To Short Story</h2>
      <form method="POST" action="login.php">
        <input type="text" name="username" placeholder="Enter Username" required>
        <input type="password" name="password" placeholder="Enter Password" required>
        <button type="submit" class="btn">Login</button>
      </form>
      <p>Already have an account?<a href="login.php">Login here</a></p>
    </div>
  </section>
  <footer>
        <p>&copy; 2025 My PHP Project</p>
        <p>All rights reserved.</p>
    </footer>
</body>
</html>
