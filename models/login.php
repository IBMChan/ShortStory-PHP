<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - Short Story</title>
  <link rel="stylesheet" href="../assets/style.css"> 
  <link href="https://fonts.googleapis.com/css2?family=Tangerine:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
  <header>
    <nav>
      <h1 class="logo">Short Story</h1>
    </nav>
  </header>

  <section class="login">
    <div class="form-container">
      <h2>Login</h2>
      <form method="POST" action="login.php">
        <input type="text" name="username" placeholder="Enter Username" required>
        <input type="password" name="password" placeholder="Enter Password" required>
        <button type="submit" class="btn">Login</button>
      </form>
      <p>Don't have an account? <a href="signup.php">Register here</a></p>
    </div>
  </section>
</body>
</html>
