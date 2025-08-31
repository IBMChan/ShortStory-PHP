<?php
require_once '../db/db.php';
require_once 'helpers.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle blog submission
$success = false;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['b_title'])) {
    $b_title = $_POST['b_title'];
    $b_intro = $_POST['b_intro'];
    $b_body  = $_POST['b_body'];
    $b_con   = $_POST['b_con'];
    $b_comm  = $_POST['b_comm'];

    $user_id = $_SESSION['user_id'];  // varchar(50)
    $blog_id = uniqid("blog_", true); // generate unique blog_id

    $stmt = $conn->prepare("INSERT INTO blogs (blog_id, user_id, b_title, b_intro, b_body, b_con, b_comm, created_at) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssssss", $blog_id, $user_id, $b_title, $b_intro, $b_body, $b_con, $b_comm);

    if ($stmt->execute()) {
        // Handle image upload
        if (!empty($_FILES['blog_image']['name'])) {
            $targetDir = "../assets/blogimg/";
            $ext = strtolower(pathinfo($_FILES['blog_image']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif','webp'];
            if (in_array($ext, $allowed)) {
                $newName = $blog_id . "." . $ext;
                move_uploaded_file($_FILES['blog_image']['tmp_name'], $targetDir . $newName);
            }
        }
        $success = true;
    } else {
        echo "❌ DB Error: " . $stmt->error;
    }
}

// Fetch blogs for current user
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM blogs WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$blogs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
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
    <div class="blogs-grid"> <!-- Add this wrapper -->
    <?php foreach ($blogs as $blog): ?>
      <div class="blog-card">
        <!-- Card Header -->
        <div class="blog-card-header">
          <?php
            $base = "../assets/image/" . $blog['blog_id'];
            $candidates = [$base . ".png", $base . ".jpg", $base . ".jpeg", $base . ".gif", $base . ".webp"];
            $imgFound = null;
            foreach ($candidates as $path) {
                if (file_exists($path)) { $imgFound = $path; break; }
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
    </div> <!-- End of blogs-grid -->
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
