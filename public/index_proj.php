<?php
include 'dbconnection.php';
include 'header.php'; // ensures login/register links appear

$search_query = '';
if (!empty($_GET['query'])) {
    $search_query = $conn->real_escape_string($_GET['query']);
}

// Fetch only 10 books
$sql = "SELECT b.*, a.auth_name,
        (SELECT AVG(rating) FROM review r WHERE r.book_id=b.book_id) as avg_rating
        FROM book b
        JOIN author a ON b.author_id = a.auth_id";

if ($search_query) {
    $sql .= " WHERE b.title LIKE '%$search_query%' 
              OR a.auth_name LIKE '%$search_query%' 
              OR b.genre LIKE '%$search_query%'";
}

$sql .= " LIMIT 10"; // show only 10 books

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Review Landing Page</title>
    <link rel="stylesheet" href="../assets/styles.css">
</head>
<body>
    <!-- Search form -->
    <header>
        <h1>Welcome to the Book Review Site</h1>
        <form method="GET" action="index_proj.php">
            <input type="text" name="query" placeholder="Search books, authors, genres..." value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit">Search</button>
        </form>
    </header>

    <!-- Book list -->
    <section>
        <div class="book-list">
            <?php
            if ($result && $result->num_rows > 0) {
                while ($book = $result->fetch_assoc()) { ?>
                    <div class="book-card"> 
                        <!-- Book cover image -->
                        <img src="../assets/images/<?php echo htmlspecialchars($book['image_path'] ?: 'default.png'); ?>" 
     alt="<?php echo htmlspecialchars($book['title']); ?>" height="150">


                        <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                        <p>Author: <?php echo htmlspecialchars($book['auth_name']); ?></p>
                        <p>Genre: <?php echo htmlspecialchars($book['genre']); ?></p>
                        <p>Year: <?php echo htmlspecialchars($book['pub_year']); ?></p>
                        <p>Price: ₹<?php echo htmlspecialchars($book['price']); ?></p>
                        <p>Rating: 
                            <span class="stars">
                                <?php
                                $avg = round($book['avg_rating']);
                                for ($i = 1; $i <= 5; $i++) echo $i <= $avg ? "★" : "☆";
                                ?>
                            </span>
                        </p>
                        <a href="book_proj.php?id=<?php echo $book['book_id']; ?>">View Details</a>
                    </div>
                <?php }
            } else {
                echo '<p>No books found!</p>';
            }
            ?>
        </div>
    </section>

    <footer>
        &copy; <?php echo date('Y'); ?> Book Review Site. All rights reserved. Contact Us: +91-98765-43210 |
        <a href="mailto:   rakshaswamy22@gmail.com">
    </footer>
</body>
</html>
