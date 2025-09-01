<?php
include 'dbconnection.php';
include_once 'auth.php';
// Redirect if not logged in
if (!is_logged_in()) {
    header('Location: login.php');
    exit();
}

$message = '';

// Fetch books for dropdown
$books_result = $conn->query("SELECT book_id, title FROM book ORDER BY title ASC");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $book_id = intval($_POST['book_id']);
    $r_title = $conn->real_escape_string($_POST['r_title']);
    $review = $conn->real_escape_string($_POST['review']);
    $rating = intval($_POST['rating']); // Get the rating from the form
    $r_date = date('Y-m-d');

    if ($book_id > 0 && !empty($r_title) && !empty($review) && $rating >= 1 && $rating <= 5) {
        $sql = "INSERT INTO review (user_id, book_id, r_title, review, r_date, rating) 
                VALUES ($user_id, $book_id, '$r_title', '$review', '$r_date', $rating)";
        if ($conn->query($sql)) {
            $message = "Review submitted successfully!";
        } else {
            $message = "Error submitting review: " . $conn->error;
        }
    } else {
        $message = "Please fill all fields correctly and select a rating.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Review</title>
    <<link rel="stylesheet" href="../assets/styles.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <h1>Add a Book Review</h1>
    <?php if ($message) echo "<p style='color:green;'>$message</p>"; ?>

    <form method="POST" action="add_review.php">
        <label for="book_id">Select Book:</label><br>
        <select name="book_id" id="book_id" required>
            <option value="">-- Choose a book --</option>
            <?php
            while ($book = $books_result->fetch_assoc()) {
                echo '<option value="' . $book['book_id'] . '">' . htmlspecialchars($book['title']) . '</option>';
            }
            ?>
        </select><br><br>

        <label for="r_title">Review Title:</label><br>
        <input type="text" id="r_title" name="r_title" required><br><br>

        <label for="review">Your Review:</label><br>
        <textarea id="review" name="review" rows="5" cols="50" required></textarea><br><br>

        <label for="rating">Rating:</label><br>
        <select name="rating" id="rating" required>
            <option value="">Select rating</option>
            <?php for ($i = 1; $i <= 5; $i++) echo "<option value='$i'>$i</option>"; ?>
        </select><br><br>

        <button type="submit">Submit Review</button>
    </form>

    <footer>
        &copy; <?php echo date('Y'); ?> Book Review Site. All rights reserved. Contact Us: +91-98765-43210 |
        <a href="mailto: shortstories@gmail.com">
    </footer>
</body>
</html>
