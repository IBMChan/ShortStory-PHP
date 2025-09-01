<?php
require_once '../db/db.php';
require_once '../services/Blog.php';

$blogModel = new Blog($conn);

// Pagination setup
$limit = 6;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Total number of blogs
$totalBlogs = count($blogModel->getAll()); // can optimize later with COUNT query
$totalPages = ceil($totalBlogs / $limit);

// Fetch blogs with limit and offset using Blog class
$blogs = $blogModel->getAll($limit, $offset);
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Blogs - Short Story</title>
    <link rel="stylesheet" href="../assets/style.css"> <!-- global -->
    <link rel="stylesheet" href="../assets/views.css"> <!-- page-specific -->
    <link href="https://fonts.googleapis.com/css2?family=Tangerine:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
<header>
    <p class="site-title">Short Story</p>
    <div class="search-box">
        <input type="text" placeholder="Search...">
        <button type="submit">&#128269;</button>
    </div>
    <nav>
        <ul>
            <li><a href="home.php">Home</a></li>
            <li><a href="createblog.php">Create Blog</a></li>
            <li><a href="addbook.php">Add Book</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
</header>

<main class="blogs-display">
    <h2>All Blogs</h2>
    <div class="blogs-grid">
        <?php foreach($blogs as $blog): ?>
            <div class="blog-card">
                <?php
                $base = "../assets/blogimg/" . $blog['blog_id'];
                $candidates = [$base . ".png", $base . ".jpg", $base . ".jpeg", $base . ".gif", $base . ".webp"];
                $imgFound = null;
                foreach ($candidates as $path) {
                    if (file_exists($path)) { $imgFound = $path; break; }
                }
                ?>
                <img src="<?= $imgFound ? htmlspecialchars($imgFound) : 'https://via.placeholder.com/300x200?text=No+Image'; ?>" alt="Blog Image">
                <div class="blog-details">
                    <h3><?= htmlspecialchars($blog['b_title']); ?></h3>
                    <p><strong>Intro:</strong> <?= htmlspecialchars($blog['b_intro']); ?></p>
                    <p><strong>Body:</strong> <?= htmlspecialchars($blog['b_body']); ?></p>
                    <p><strong>Conclusion:</strong> <?= htmlspecialchars($blog['b_con']); ?></p>
                    <p class="b-comm"><strong>Comments:</strong> <?= htmlspecialchars($blog['b_comm']); ?></p>
                    <p class="b-author"><em>By: <?= htmlspecialchars($blog['u_name']); ?></em></p>
                    <p class="b-date"><em>Posted on: <?= date("F j, Y, g:i a", strtotime($blog['created_at'])); ?></em></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <div class="pagination">
        <?php if($page > 1): ?>
            <a href="?page=<?= $page-1 ?>">&laquo; Prev</a>
        <?php endif; ?>

        <?php for($i=1; $i<=$totalPages; $i++): ?>
            <a href="?page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>

        <?php if($page < $totalPages): ?>
            <a href="?page=<?= $page+1 ?>">Next &raquo;</a>
        <?php endif; ?>
    </div>
</main>

<footer>
    <p>&copy; 2025 Short Story</p>
    <p>All rights reserved.</p>
</footer>
</body>
</html>
