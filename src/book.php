<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$book_id = (int)$_GET['id'];

$sql_book = "
    SELECT b.*, a.auth_name, g.genre_name
    FROM book b
    JOIN author a ON b.author_id = a.auth_id
    LEFT JOIN genre g ON b.genre_id = g.genre_id
    WHERE b.book_id = ?
";
$stmt_book = $conn->prepare($sql_book);
$stmt_book->bind_param('i', $book_id);
$stmt_book->execute();
$result_book = $stmt_book->get_result();
$book = $result_book->fetch_assoc();

if (!$book) {
    header('Location: index.php');
    exit();
}

$sql_reviews = "
    SELECT r.*, u.u_name
    FROM review r
    JOIN user u ON r.user_id = u.user_id
    WHERE r.book_id = ?
    ORDER BY r.r_date DESC
";
$stmt_reviews = $conn->prepare($sql_reviews);
$stmt_reviews->bind_param('i', $book_id);
$stmt_reviews->execute();
$result_reviews = $stmt_reviews->get_result();
$reviews = $result_reviews->fetch_all(MYSQLI_ASSOC);

$page_title = htmlspecialchars($book['title']);
require_once __DIR__ . '/../includes/header.php';
?>

<div class="book-detail-container">
    <div class="book-header">
        <div class="book-cover">
            <img src="../assets/images/<?php echo htmlspecialchars($book['book_id']); ?>/<?php echo htmlspecialchars($book['cover_image']); ?>" alt="Cover for <?php echo htmlspecialchars($book['title']); ?>">
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

<?php
require_once __DIR__ . '/../includes/footer.php';
?>