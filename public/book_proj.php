<?php
include 'dbconnection.php';
include 'header.php';

$book_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch book details
$sql = "SELECT b.*, a.auth_name,
        (SELECT AVG(rating) FROM review r WHERE r.book_id=b.book_id) as avg_rating
        FROM book b
        JOIN author a ON b.author_id = a.auth_id
        WHERE b.book_id = $book_id
        LIMIT 1";

$result = $conn->query($sql);
$book = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($book['title']); ?></title>
 <link rel="stylesheet" href="../assets/styles.css">
</head>
<body>
    <?php if ($book): ?>
        <div class="book-detail">
            <img src="../assets/images/<?php echo htmlspecialchars($book['image_path'] ?: 'default.png'); ?>" 
     alt="<?php echo htmlspecialchars($book['title']); ?>" height="150">

            <h1><?php echo htmlspecialchars($book['title']); ?></h1>
            <p>Author: <?php echo htmlspecialchars($book['auth_name']); ?></p>
            <p>Genre: <?php echo htmlspecialchars($book['genre']); ?></p>
            <p>Published: <?php echo htmlspecialchars($book['pub_year']); ?></p>
            <p>Price: ₹<?php echo htmlspecialchars($book['price']); ?></p>
            <p>Average Rating: 
                <span class="stars">
                    <?php
                    $avg = round($book['avg_rating']);
                    for ($i = 1; $i <= 5; $i++) echo $i <= $avg ? "★" : "☆";
                    ?>
                </span>
            </p>
            <h2>Abstract:</h2>
            <p><?php echo htmlspecialchars($book['abstract']); ?></p>
        </div>

        <!-- Reviews -->
        <h2>Reviews</h2>
        <?php
        $sql_rev = "SELECT r.*, u.u_name FROM review r JOIN user u ON r.user_id = u.user_id WHERE r.book_id = $book_id ORDER BY r_date DESC";
        $res_rev = $conn->query($sql_rev);
        if ($res_rev->num_rows > 0):
            while ($rev = $res_rev->fetch_assoc()): ?>
                <div class="review">
                    <strong><?php echo htmlspecialchars($rev['u_name']); ?></strong>:
                    <span class="stars">
                        <?php for ($i = 1; $i <= 5; $i++) echo $i <= $rev['rating'] ? "★" : "☆"; ?>
                    </span>
                    <p><strong><?php echo htmlspecialchars($rev['r_title']); ?></strong></p>
                    <p><?php echo htmlspecialchars($rev['review']); ?></p>
                    <small><?php echo htmlspecialchars($rev['r_date']); ?></small>
                </div>
            <?php endwhile;
        else:
            echo '<p>No reviews yet.</p>';
        endif;
        ?>

        <!-- Add review only if logged in -->
        <?php if (is_logged_in()): ?>
            <h2>Add Your Review</h2>
            <form method="POST" action="add_review.php">
                <input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
                <label for="r_title">Review Title:</label><br>
                <input type="text" id="r_title" name="r_title" required><br><br>

                <label for="review">Your Review:</label><br>
                <textarea id="review" name="review" rows="5" cols="50" required></textarea><br><br>

                <label for="rating">Rating:</label><br>
                <select name="rating" id="rating" required>
                    <option value="">Select rating</option>
                    <?php for ($i=1; $i<=5; $i++) echo "<option value='$i'>$i</option>"; ?>
                </select><br><br>

                <button type="submit">Submit Review</button>
            </form>
        <?php else: ?>
            <p><a href="login.php">Login</a> or <a href="register_proj.php">Register</a> to leave a review and rating.</p>
        <?php endif; ?>

    <?php else: ?>
        <p>Book not found!</p>
    <?php endif; ?>
</body>
</html>
