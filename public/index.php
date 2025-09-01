<?php

require_once __DIR__ . '/../env.php';

use App\Models\Book;

$page_title = 'Welcome to ShortStory';

try {
    $top_rated_books = Book::getTopRated(5);
    $latest_books = Book::getLatest(5);
} catch (Exception $e) {
    error_log($e->getMessage());
    $top_rated_books = [];
    $latest_books = [];
    $error_message = "Could not fetch books at this time. Please try again later.";
}

require_once __DIR__ . '/../templates/header.php';
?>

<section class="hero">
    <h1>Discover Your Next Favorite Book</h1>
    <p>Explore reviews, share your thoughts, and join a community of readers.</p>
</section>

<?php if (isset($error_message)): ?>
    <p class="error-message"><?php echo $error_message; ?></p>
<?php endif; ?>

<section class="book-list">
    <h2>Top Rated Books</h2>
    <div class="book-grid">
        <?php if (!empty($top_rated_books)): ?>
            <?php foreach ($top_rated_books as $book): ?>
                <div class="book-card">
                    <a href="book.php?id=<?php echo htmlspecialchars($book['book_id']); ?>">
                        <img src="<?php echo htmlspecialchars($book['cover_image']); ?>" alt="Cover for <?php echo htmlspecialchars($book['title']); ?>">
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
                        <img src="<?php echo htmlspecialchars($book['cover_image']); ?>" alt="Cover for <?php echo htmlspecialchars($book['title']); ?>">
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

<?php require_once __DIR__ . '/../templates/footer.php'; ?>