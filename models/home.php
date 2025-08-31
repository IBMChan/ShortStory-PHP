<?php
require_once '../db/db.php'; // database connection

// Fetch latest 5 blogs
$blogQuery = "SELECT * FROM blogs ORDER BY created_at DESC LIMIT 5";
$blogResult = mysqli_query($conn, $blogQuery);

// Fetch latest 5 books
$bookQuery = "
    SELECT b.*, a.auth_name AS author_name
    FROM book b
    LEFT JOIN author a ON b.author_id = a.auth_id
    ORDER BY b.created_at DESC
    LIMIT 5
";
$bookResult = mysqli_query($conn, $bookQuery);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Short Story</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="../assets/home.css">
    <link href="https://fonts.googleapis.com/css2?family=Tangerine:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <p class="site-title">Short Story</p>
        <div class="search-boxx">
            <input type="text" placeholder="Search...">
            <button type="submit">&#128269;</button>
        </div>
        <nav>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="createblog.php">Create Blog</a></li>
                <li><a href="addbook.php">Add Book</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="content-wrapper">
        <main>
            <!-- Recent Blogs -->
            <div class="hero-section">
                <div class="section-header">
                    <h2>Recent Blogs</h2>
                    <a href="viewblogs.php" class="view-all-btn">View All</a>
                </div>
                <div class="blogs-grid">
                    <?php while($blog = mysqli_fetch_assoc($blogResult)) : ?>
                        <div class="blog-card">
                            <?php
                            $base = "../assets/blogimg/" . $blog['blog_id'];
                            $candidates = [$base . ".png", $base . ".jpg", $base . ".jpeg", $base . ".gif", $base . ".webp"];
                            $imgFound = null;
                            foreach ($candidates as $path) {
                                if (file_exists($path)) { $imgFound = $path; break; }
                            }
                            ?>
                            <img src="<?= $imgFound ? htmlspecialchars($imgFound) : 'https://via.placeholder.com/150x100?text=No+Image'; ?>" alt="Blog Image">
                            <h3><?= htmlspecialchars($blog['b_title']); ?></h3>
                            <p><?= substr(htmlspecialchars($blog['b_intro']), 0, 80) . '...'; ?></p>
                            <p class="b-comm">Comments: <?= htmlspecialchars($blog['b_comm']); ?></p>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Recent Books -->
            <div class="hero-section">
                <div class="section-header">
                    <h2>Recent Books</h2>
                    <a href="viewbooks.php" class="view-all-btn">View All</a>
                </div>
                <div class="blogs-grid">
                    <?php while($book = mysqli_fetch_assoc($bookResult)) : ?>
                        <div class="blog-card">
                            <?php
                            $base = "../assets/bookimg/" . $book['book_id'];
                            $candidates = [$base . ".png", $base . ".jpg", $base . ".jpeg", $base . ".gif", $base . ".webp"];
                            $imgFound = null;
                            foreach ($candidates as $path) {
                                if (file_exists($path)) { $imgFound = $path; break; }
                            }
                            ?>
                            <img src="<?= $imgFound ? htmlspecialchars($imgFound) : 'https://via.placeholder.com/150x100?text=No+Image'; ?>" alt="<?= htmlspecialchars($book['title']); ?>">
                            <h3><?= htmlspecialchars($book['title']); ?></h3>
                            <p>Author: <?= htmlspecialchars($book['author_name'] ?? 'Unknown'); ?></p>
                            <p>Price: ₹<?= htmlspecialchars($book['price']); ?></p>
                            <p>Year: <?= htmlspecialchars($book['pub_year']); ?></p>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Conveyor Belt -->
<div class="book-slider">
    <div class="book-track">
        <img src="https://covers.openlibrary.org/b/id/8231856-L.jpg" alt="Fiction Book">
        <img src="https://covers.openlibrary.org/b/id/10909258-L.jpg" alt="Romance Book">
        <img src="https://covers.openlibrary.org/b/id/240726-L.jpg" alt="Classic Novel">
        <img src="https://covers.openlibrary.org/b/id/8235112-L.jpg" alt="Fantasy Book">
        <img src="https://covers.openlibrary.org/b/id/9251995-L.jpg" alt="Adventure Book">
        <img src="https://covers.openlibrary.org/b/id/8231991-L.jpg" alt="Mystery Book">
        <img src="https://covers.openlibrary.org/b/id/8231872-L.jpg" alt="Thriller Book">
        <img src="https://covers.openlibrary.org/b/id/8235120-L.jpg" alt="Sci-Fi Book">
        <img src="https://covers.openlibrary.org/b/id/8235133-L.jpg" alt="Historical Fiction Book">
        <img src="https://covers.openlibrary.org/b/id/8235145-L.jpg" alt="Biography Book">
        <img src="https://covers.openlibrary.org/b/id/8235157-L.jpg" alt="Children's Book">
        <img src="https://covers.openlibrary.org/b/id/8235168-L.jpg" alt="Young Adult Book">
        <img src="https://covers.openlibrary.org/b/id/8235179-L.jpg" alt="Horror Book">
        <img src="https://covers.openlibrary.org/b/id/8235188-L.jpg" alt="Poetry Book">
        <img src="https://covers.openlibrary.org/b/id/8235197-L.jpg" alt="Drama Book">
        <img src="https://covers.openlibrary.org/b/id/8235206-L.jpg" alt="Non-Fiction Book">
        <img src="https://covers.openlibrary.org/b/id/8235215-L.jpg" alt="Philosophy Book">
        <img src="https://covers.openlibrary.org/b/id/8235224-L.jpg" alt="Self-Help Book">
        <img src="https://covers.openlibrary.org/b/id/8235233-L.jpg" alt="Science Book">
        <img src="https://covers.openlibrary.org/b/id/8235242-L.jpg" alt="Math Book">
        <img src="https://covers.openlibrary.org/b/id/8235251-L.jpg" alt="Cookbook">
        <img src="https://covers.openlibrary.org/b/id/8235260-L.jpg" alt="Travel Book">
        <img src="https://covers.openlibrary.org/b/id/8235269-L.jpg" alt="Art Book">
        <img src="https://covers.openlibrary.org/b/id/8235278-L.jpg" alt="Photography Book">
        <img src="https://covers.openlibrary.org/b/id/8235287-L.jpg" alt="Music Book">
        <img src="https://covers.openlibrary.org/b/id/8235296-L.jpg" alt="Graphic Novel">
        <img src="https://covers.openlibrary.org/b/id/8235305-L.jpg" alt="Comic Book">
        <img src="https://covers.openlibrary.org/b/id/8235314-L.jpg" alt="Mystery Thriller">
        <img src="https://covers.openlibrary.org/b/id/8235323-L.jpg" alt="Classic Literature">
        <img src="https://covers.openlibrary.org/b/id/8235332-L.jpg" alt="Adventure Novel">
        <img src="https://covers.openlibrary.org/b/id/8235341-L.jpg" alt="Fantasy Adventure">
        <img src="https://covers.openlibrary.org/b/id/8235350-L.jpg" alt="Romantic Fiction">
        <img src="https://covers.openlibrary.org/b/id/8235359-L.jpg" alt="Science Fiction Novel">
        <img src="https://covers.openlibrary.org/b/id/8235368-L.jpg" alt="Historical Novel">
        <img src="https://covers.openlibrary.org/b/id/8235377-L.jpg" alt="Classic Drama">
        <img src="https://covers.openlibrary.org/b/id/8235386-L.jpg" alt="Biography Memoir">
        <img src="https://covers.openlibrary.org/b/id/8235395-L.jpg" alt="Horror Thriller">
        <img src="https://covers.openlibrary.org/b/id/8235404-L.jpg" alt="Children's Adventure">
    </div>
</div>

            <!-- Contact Section -->
            <section id="contact">
                <h2>Contact Us</h2>
                <p>We’d love to hear from you! Whether you have questions, suggestions, or just want to talk about books, here’s how you can reach us:</p>
                <div class="contact-container">
                    <div class="contact-card">
                        <h3>Email</h3>
                        <p><a href="mailto:hello@booksphere.com">hello@shortstory.com</a></p>
                    </div>
                    <div class="contact-card">
                        <h3>Phone</h3>
                        <p>📞 +91 98765 43210</p>
                    </div>
                    <div class="contact-card">
                        <h3>Social Media</h3>
                        <p>
                            📘 <a href="#">Facebook</a><br>
                            🐦 <a href="#">Twitter</a><br>
                            📸 <a href="#">Instagram</a>
                        </p>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <footer>
        <p>&copy; 2025 My PHP Project</p>
        <p>All rights reserved.</p>
    </footer>
</body>
</html>
