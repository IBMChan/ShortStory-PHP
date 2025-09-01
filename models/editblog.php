<?php
require_once '../db/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch blog to edit
if (!isset($_GET['id'])) {
    header("Location: createblog.php");
    exit();
}

$blog_id = $_GET['id']; // keep it as string
$stmt = $conn->prepare("SELECT * FROM blogs WHERE blog_id = ? AND user_id = ?");
$stmt->bind_param("si", $blog_id, $_SESSION['user_id']); // "s" for string, "i" for integer user_id
$stmt->execute();
$result = $stmt->get_result();
$blog = $result->fetch_assoc();



if (!$blog) {
    header("Location: createblog.php");
    exit();
}

// Update blog
$success = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['b_title'];
    $intro = $_POST['b_intro'];
    $body  = $_POST['b_body'];
    $con   = $_POST['b_con'];
    $comm  = $_POST['b_comm'];

    $stmt = $conn->prepare("UPDATE blogs SET b_title=?, b_intro=?, b_body=?, b_con=?, b_comm=? WHERE blog_id=? AND user_id=?");
    $stmt->bind_param("ssssssi", $title, $intro, $body, $con, $comm, $blog_id, $_SESSION['user_id']);
    if ($stmt->execute()) {
        // Handle image upload if new image provided
        if (!empty($_FILES['blog_image']['name'])) {
            $targetDir = "../assets/blogimg/";
            $ext = strtolower(pathinfo($_FILES['blog_image']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif','webp'];
            if (in_array($ext, $allowed)) {
                move_uploaded_file($_FILES['blog_image']['tmp_name'], $targetDir . $blog_id . "." . $ext);
            }
        }

        $success = true;
        header("Location: createblog.php");
        exit();
    }
}

// Check if an image exists for this blog
$imgFound = null;
$baseName = $blog['blog_id'];
$exts = ['png','jpg','jpeg','gif','webp'];
foreach ($exts as $ext) {
    if (file_exists("../assets/blogimg/$baseName.$ext")) {
        $imgFound = "../assets/blogimg/$baseName.$ext";
        break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Blog - Short Story</title>
  <link rel="stylesheet" href="../assets/style.css">
  <link rel="stylesheet" href="../assets/blog.css">
  <link href="https://fonts.googleapis.com/css2?family=Tangerine:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
<header class="login-header">
  <p class="site-title">Short Story</p>
  <nav class="back-nav">
    <ul>
        <li><a href="createblog.php">⬅ Back</a></li>
    </ul>
  </nav>
</header>

<section class="blog-section">
  <div class="blog-form">
    <h2>Edit Blog</h2>
    <form action="" method="POST" enctype="multipart/form-data">
      <label>Blog Title</label>
      <input type="text" name="b_title" value="<?= htmlspecialchars($blog['b_title']); ?>" required>

      <label>Intro</label>
      <textarea name="b_intro" rows="3" required><?= htmlspecialchars($blog['b_intro']); ?></textarea>

      <label>Body</label>
      <textarea name="b_body" rows="6" required><?= htmlspecialchars($blog['b_body']); ?></textarea>

      <label>Conclusion</label>
      <textarea name="b_con" rows="3" required><?= htmlspecialchars($blog['b_con']); ?></textarea>

      <label>Comments / Hashtags</label>
      <input type="text" name="b_comm" value="<?= htmlspecialchars($blog['b_comm']); ?>">

      <?php if ($imgFound): ?>
        <p>Current Image:</p>
        <img src="<?= $imgFound ?>" alt="Blog Image" style="width:150px; display:block; margin-bottom:10px;">
      <?php endif; ?>
      <label>Change / Upload Image (optional)</label>
      <input type="file" name="blog_image" accept="image/*">

      <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px;">
        <button type="submit" class="btn">💾 Save</button>
        <a href="createblog.php" class="btn" style="background:#b23b3b;">✖ Cancel</a>
      </div>
    </form>
  </div>
</section>

<footer>
  <p>&copy; 2025 Short Story</p>
  <p>All rights reserved.</p>
</footer>
</body>
</html>
