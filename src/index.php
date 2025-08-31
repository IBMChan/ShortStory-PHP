<?php

$page_title = 'Home';

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

$latest_books = [];
$top_rated_books = [];

$sql_latest = "
    SELECT b.book_id, b.title, b.cover_image, a.auth_name
    FROM book b
    JOIN author a ON b.author_id = a.auth_id
    ORDER BY b.pub_year DESC
    LIMIT 5
";
$result_latest = $conn->query($sql_latest);

if ($result_latest) {
    $latest_books = $result_latest->fetch_all(MYSQLI_ASSOC);
} else {
    echo '<p class="error-message">Error fetching latest books: ' . $conn->error . '</p>';
}

$sql_top_rated = "
    SELECT b.book_id, b.title, b.cover_image, a.auth_name, AVG(r.rating) as avg_rating
    FROM book b
    JOIN author a ON b.author_id = a.auth_id
    JOIN review r ON b.book_id = r.book_id
    GROUP BY b.book_id
    ORDER BY avg_rating DESC
    LIMIT 5
";
$result_top_rated = $conn->query($sql_top_rated);

if ($result_top_rated) {
    $top_rated_books = $result_top_rated->fetch_all(MYSQLI_ASSOC);
} else {
    echo '<p class="error-message">Error fetching top rated books: ' . $conn->error . '</p>';
}

?>

<section class="hero">
    <h1>Discover Your Next Favorite Book</h1>
    <p>Explore reviews, share your thoughts, and join a community of readers.</p>
</section>

<section class="book-list">
    <h2>Top Rated Books</h2>
    <div class="book-grid">
        <?php if (!empty($top_rated_books)): ?>
            <?php foreach ($top_rated_books as $book): ?>
                <div class="book-card">
                    <a href="book.php?id=<?php echo htmlspecialchars($book['book_id']); ?>">
                        <img src="../assets/images/<?php echo htmlspecialchars($book['book_id']); ?>/<?php echo htmlspecialchars($book['cover_image']); ?>" alt="Cover for <?php echo htmlspecialchars($book['title']); ?>">
                        <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                        <p><?php echo htmlspecialchars($book['auth_name']); ?></p>
                        <div class="rating">
                            ★ <?php echo round($book['avg_rating'], 1); ?>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No top-rated books to display yet.</p>
        <?php endif; ?>
    </div>
</section>

<section class="book-list">
    <h2>Latest Additions</h2>
    <div class="book-grid">
        <?php if (!empty($latest_books)): ?>
            <?php foreach ($latest_books as $book): ?>
                <div class="book-card">
                     <a href="book.php?id=<?php echo htmlspecialchars($book['book_id']); ?>">
                        <img src="../assets/images/<?php echo htmlspecialchars($book['book_id']); ?>/<?php echo htmlspecialchars($book['cover_image']); ?>" alt="Cover for <?php echo htmlspecialchars($book['title']); ?>">
                        <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                        <p><?php echo htmlspecialchars($book['auth_name']); ?></p>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No new books to display yet.</p>
        <?php endif; ?>
    </div>
</section>


<?php
require_once __DIR__ . '/../includes/footer.php';
?>
