<?php

require_once __DIR__ . '/../env.php';

use App\Models\Book;
use App\Models\Review;

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$book_id = (int)$_GET['id'];

$book = Book::findById($book_id);
if (!$book) {
    header('Location: index.php');
    exit();
}

$reviews = Review::findByBookId($book_id);

$page_title = htmlspecialchars($book['title']);
require_once __DIR__ . '/../templates/header.php';
?>

<div class="book-detail-container">
    <div class="book-header">
        <div class="book-cover">
            <img src="<?php echo htmlspecialchars($book['cover_image']); ?>" alt="Cover for <?php echo htmlspecialchars($book['title']); ?>">
        </div>
        <div class="book-info">
            <h1><?php echo htmlspecialchars($book['title']); ?></h1>
            <h2>by <?php echo htmlspecialchars($book['auth_name']); ?></h2>
            <p class="meta"><strong>Genre:</strong> <?php echo htmlspecialchars($book['genre_name'] ?? 'N/A'); ?></p>
            <p class="meta"><strong>Published:</strong> <?php echo date('Y', strtotime($book['pub_year'])); ?></p>
            <p class="price">₹<?php echo htmlspecialchars(number_format($book['price'], 2)); ?></p>
            <div class="book-abstract">
                <h3>Abstract</h3>
                <p><?php echo nl2br(htmlspecialchars($book['abstract'])); ?></p>
            </div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="add_review.php?book_id=<?php echo $book['book_id']; ?>" class="btn btn-primary">Write a Review</a>
            <?php else: ?>
                <p><a href="login.php">Login</a> to write a review.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="reviews-section">
        <h2>Reviews</h2>
        <?php if (!empty($reviews)): ?>
            <?php foreach ($reviews as $review): ?>
                <div class="review-card">
                    <div class="review-header">
                        <h3><?php echo htmlspecialchars($review['r_title']); ?></h3>
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $review['user_id']): ?>
                            <a href="delete_review.php?id=<?php echo $review['rev_id']; ?>" 
                               class="btn btn-delete" 
                               onclick="return confirm('Are you sure you want to delete this review?');">Delete</a>
                        <?php endif; ?>
                    </div>
                    <div class="review-meta">
                        <span>by <strong><?php echo htmlspecialchars($review['u_name']); ?></strong> on <?php echo date('M j, Y', strtotime($review['r_date'])); ?></span>
                        <span class="rating">★ <?php echo htmlspecialchars($review['rating']); ?>/5</span>
                    </div>
                    <p><?php echo nl2br(htmlspecialchars($review['review'])); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No reviews yet. Be the first to write one!</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>