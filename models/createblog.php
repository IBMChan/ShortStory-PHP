<?php
require_once '../db/db.php';
require_once '../services/Blog.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$blogModel = new Blog($conn);
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['b_title'])) {
    $userId = $_SESSION['user_id'];
    $b_title = $_POST['b_title'];
    $b_intro = $_POST['b_intro'];
    $b_body  = $_POST['b_body'];
    $b_con   = $_POST['b_con'];
    $b_comm  = $_POST['b_comm'];

    $blogId = $blogModel->create($userId, $b_title, $b_intro, $b_body, $b_con, $b_comm);

    // Handle image upload
    if (!empty($_FILES['blog_image']['name'])) {
        $targetDir = "../assets/blogimg/";
        $ext = strtolower(pathinfo($_FILES['blog_image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];
        if (in_array($ext, $allowed)) {
            move_uploaded_file($_FILES['blog_image']['tmp_name'], $targetDir . $blogId . "." . $ext);
        }
    }
    $success = true;
}

// Fetch blogs for current user
$blogs = $blogModel->getByUser($_SESSION['user_id']);
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create Blog - Short Story</title>
  <link rel="stylesheet" href="../assets/style.css">
  <link rel="stylesheet" href="../assets/blog.css">
  <link href="https://fonts.googleapis.com/css2?family=Tangerine:wght@400;700&display=swap" rel="stylesheet">
  <script>
    <?php if ($success): ?>
      alert("✅ Blog published successfully!");
    <?php endif; ?>
  </script>
</head>
<body>
<header class="login-header">
  <p class="site-title">Short Story</p>
  <nav class="back-nav">
    <ul>
        <li><a href="home.php">⬅ Back</a></li>
    </ul>
  </nav>
</header>

<!-- Blog form -->
<section class="blog-section">
  <div class="blog-form">
    <h2>Create Blog</h2>
    <form action="" method="POST" enctype="multipart/form-data">
      <label>Blog Title</label>
      <input type="text" name="b_title" required>

      <label>Intro</label>
      <textarea name="b_intro" rows="3" required></textarea>

      <label>Body</label>
      <textarea name="b_body" rows="6" required></textarea>

      <label>Conclusion</label>
      <textarea name="b_con" rows="3" required></textarea>

      <label>Comments / Hashtags</label>
      <input type="text" name="b_comm">

      <label>Upload Image (optional)</label>
      <input type="file" name="blog_image" accept="image/*">

      <button type="submit" class="btn">Publish Blog</button>
    </form>
  </div>
</section>

<!-- Display blogs -->
<section class="blogs-display">
  <h2>Your Blogs</h2>
  <?php if (!empty($blogs)): ?>
    <div class="blogs-grid">
    <?php foreach ($blogs as $blog): ?>
      <div class="blog-card">
        <!-- Card Header -->
        <div class="blog-card-header">
          <?php
$baseName = $blog['blog_id'];
$imgFound = null;
$exts = ['png', 'jpg', 'jpeg', 'gif', 'webp'];
foreach ($exts as $ext) {
    $serverPath = __DIR__ . "/../assets/blogimg/$baseName.$ext"; // server path
    if (file_exists($serverPath)) {
        $imgFound = "../assets/blogimg/$baseName.$ext"; // browser path
        break;
    }
}
?>
<?php if ($imgFound): ?>
    <img src="<?= htmlspecialchars($imgFound); ?>" alt="Blog Image">
<?php else: ?>
    <img src="https://via.placeholder.com/120x80.png?text=No+Image" alt="No Image">
<?php endif; ?>



          <h3><?= htmlspecialchars($blog['b_title']); ?></h3>
        </div>

        <!-- Card Body -->
        <div class="blog-card-body">
          <p><strong>Intro:</strong> <?= nl2br(htmlspecialchars($blog['b_intro'])); ?></p>
          <p><strong>Body:</strong> <?= nl2br(htmlspecialchars($blog['b_body'])); ?></p>
          <p><strong>Conclusion:</strong> <?= nl2br(htmlspecialchars($blog['b_con'])); ?></p>
          <p><strong>Comments:</strong> <?= htmlspecialchars($blog['b_comm']); ?></p>
          <small>Published on: <?= htmlspecialchars($blog['created_at']); ?></small>
        </div>

        <!-- Card Footer -->
        <div class="blog-card-footer">
          <a href="editblog.php?id=<?= urlencode($blog['blog_id']); ?>" class="btn edit-btn">✏️ Edit</a>


          <form action="deleteblog.php" method="POST" style="display:inline;">
            <input type="hidden" name="id" value="<?= $blog['blog_id']; ?>">
            <button type="submit" class="btn delete-btn" onclick="return confirm('⚠️ Are you sure you want to delete this blog?');">🗑 Delete</button>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p>No blogs created yet.</p>
  <?php endif; ?>
</section>

<footer>
  <p>&copy; 2025 Short Story</p>
  <p>All rights reserved.</p>
</footer>
</body>
</html>
