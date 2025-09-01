<?php

require_once __DIR__ . '/../env.php';

use App\Models\Book;
use App\Models\Review;

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['book_id']) || !is_numeric($_GET['book_id'])) {
    header('Location: index.php');
    exit();
}

$book_id = (int)$_GET['book_id'];
$book = Book::findById($book_id);

if (!$book) {
    header('Location: index.php');
    exit();
}

$errors = [];
$review_title = '';
$review_text = '';
$rating = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $review_title = trim($_POST['review_title']);
    $review_text = trim($_POST['review_text']);
    $rating = (int)($_POST['rating'] ?? 0);
    $user_id = $_SESSION['user_id'];

    if (empty($review_title)) $errors[] = 'Review title is required.';
    if (empty($review_text)) $errors[] = 'Review text is required.';
    if ($rating < 1 || $rating > 5) $errors[] = 'Please select a valid rating.';

    if (empty($errors)) {
        $success = Review::create($user_id, $book_id, $review_title, $review_text, $rating);
        if ($success) {
            header('Location: book.php?id=' . $book_id);
            exit();
        } else {
            $errors[] = 'Could not submit review. Please try again.';
        }
    }
}

$page_title = 'Write a Review for ' . htmlspecialchars($book['title']);
require_once __DIR__ . '/../templates/header.php';
?>

<div class="form-container">
    <form action="add_review.php?book_id=<?php echo $book_id; ?>" method="POST" class="auth-form">
        <h2>Write a Review for <em><?php echo htmlspecialchars($book['title']); ?></em></h2>

        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <label for="review_title">Review Title</label>
            <input type="text" id="review_title" name="review_title" value="<?php echo htmlspecialchars($review_title); ?>" required>
        </div>

        <div class="form-group">
            <label for="review_text">Your Review</label>
            <textarea id="review_text" name="review_text" rows="8" required><?php echo htmlspecialchars($review_text); ?></textarea>
        </div>

        <div class="form-group">
            <label for="rating">Rating</label>
            <select id="rating" name="rating" required>
                <option value="">Select a rating...</option>
                <option value="5" <?php echo ($rating == 5) ? 'selected' : ''; ?>>5 - Excellent</option>
                <option value="4" <?php echo ($rating == 4) ? 'selected' : ''; ?>>4 - Very Good</option>
                <option value="3" <?php echo ($rating == 3) ? 'selected' : ''; ?>>3 - Good</option>
                <option value="2" <?php echo ($rating == 2) ? 'selected' : ''; ?>>2 - Fair</option>
                <option value="1" <?php echo ($rating == 1) ? 'selected' : ''; ?>>1 - Poor</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary btn-full">Submit Review</button>
    </form>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>