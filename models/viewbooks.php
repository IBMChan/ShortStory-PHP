<?php
require_once '../db/db.php'; // database connection

// Pagination setup
$limit = 12;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Total books
$totalQuery = "SELECT COUNT(*) as total FROM book";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalBooks = $totalRow['total'];
$totalPages = ceil($totalBooks / $limit);

// Books for current page
$bookQuery = "
    SELECT b.*, a.auth_name AS author_name
    FROM book b
    LEFT JOIN author a ON b.author_id = a.auth_id
    ORDER BY b.created_at DESC
    LIMIT $limit OFFSET $offset
";
$bookResult = mysqli_query($conn, $bookQuery);
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Books - Short Story</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="../assets/views.css">
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

<main class="books-page">
    <h2 class="page-title">All Books</h2>
    <div class="books-grid">
        <?php while($book = mysqli_fetch_assoc($bookResult)) : ?>
            <?php
            $base = "../assets/bookimg/" . $book['book_id'];
            $candidates = [$base . ".png", $base . ".jpg", $base . ".jpeg", $base . ".gif", $base . ".webp"];
            $imgFound = null;
            foreach ($candidates as $path) {
                if (file_exists($path)) { $imgFound = $path; break; }
            }
            ?>
            <div class="book-card" onclick="openCardPopup('popup-<?= $book['book_id']; ?>')">
                <img src="<?= $imgFound ? htmlspecialchars($imgFound) : 'https://via.placeholder.com/150x200?text=No+Image'; ?>" alt="<?= htmlspecialchars($book['title']); ?>">
                <h3><?= htmlspecialchars($book['title']); ?></h3>
                <p>Author: <?= htmlspecialchars($book['author_name'] ?? 'Unknown'); ?></p>
                <p>Genre: <?= htmlspecialchars($book['genre']); ?></p>
            </div>

            <!-- Card-style Popup -->
            <div id="popup-<?= $book['book_id']; ?>" class="card-popup">
                <div class="card-content">
                    <span class="close" onclick="closeCardPopup('popup-<?= $book['book_id']; ?>')">&times;</span>
                    <img src="<?= $imgFound ? htmlspecialchars($imgFound) : 'https://via.placeholder.com/150x200?text=No+Image'; ?>" alt="<?= htmlspecialchars($book['title']); ?>">
                    <h3><?= htmlspecialchars($book['title']); ?></h3>
                    <p><strong>Author:</strong> <?= htmlspecialchars($book['author_name'] ?? 'Unknown'); ?></p>
                    <p><strong>Genre:</strong> <?= htmlspecialchars($book['genre']); ?></p>
                    <p><strong>Publication Year:</strong> <?= htmlspecialchars($book['pub_year']); ?></p>
                    <p><strong>Price:</strong> ₹<?= htmlspecialchars($book['price']); ?></p>
                    <p><strong>Abstract:</strong> <?= nl2br(htmlspecialchars($book['abstract'])); ?></p>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Pagination -->
    <div class="pagination">
        <?php for($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
</main>

<footer>
    <p>&copy; 2025 Short Story</p>
    <p>All rights reserved.</p>
</footer>

<script>
function openCardPopup(id) {
    document.getElementById(id).style.display = 'flex';
}
function closeCardPopup(id) {
    document.getElementById(id).style.display = 'none';
}
</script>
</body>
</html>
