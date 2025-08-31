<?php
require_once '../db/db.php'; // database connection

// Fetch latest 5 blogs
$blogQuery = "SELECT * FROM blogs ORDER BY created_at DESC LIMIT 5";
$blogResult = mysqli_query($conn, $blogQuery);

// Fetch latest 5 books
$bookQuery = "SELECT * FROM book ORDER BY created_at DESC LIMIT 5";
$bookResult = mysqli_query($conn, $bookQuery);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Short story</title>
    <link rel="stylesheet" href="../assets/style.css"> <!-- global -->
    <link rel="stylesheet" href="../assets/home.css"> <!-- page-specific -->
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
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <!-- Recent Blogs -->
        <div class="hero-section">
            <div class="section-header">
                <h2>Recent Blogs</h2>
                <a href="viewblogs.php" class="view-all-btn">View All</a>
            </div>
            <div class="card-slider">
                <?php while($blog = mysqli_fetch_assoc($blogResult)) : ?>
                    <div class="card blog-card">
                        <img src="https://via.placeholder.com/150x100" alt="Blog Image">
                        <h3><?php echo htmlspecialchars($blog['b_title']); ?></h3>
                        <p><?php echo substr(htmlspecialchars($blog['b_intro']), 0, 80) . '...'; ?></p>
                        <p class="b-comm">Comments: <?php echo htmlspecialchars($blog['b_comm']); ?></p>
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
            <div class="card-slider">
                <?php while($book = mysqli_fetch_assoc($bookResult)) : ?>
                    <div class="card book-card">
                        <img src="https://covers.openlibrary.org/b/id/8231856-L.jpg" alt="<?php echo htmlspecialchars($book['title']); ?>">
                        <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                        <p>Author: <?php echo htmlspecialchars($book['author']); ?></p>
                        <p>Price: ₹<?php echo htmlspecialchars($book['price']); ?></p>
                        <p>Year: <?php echo htmlspecialchars($book['pub_year']); ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

        <section id="about" class="about">
        <h2>About Us</h2>
        <p>
            At <strong>Short Story</strong>, Welcome to Short Story, a community built for book lovers!
            Our platform is more than just a place to read about books—it’s a space where imagination, knowledge, and connection come together. 
            Here’s what you can do with us:
            <strong>Explore Books:</strong> Discover books from different genres, from timeless classics to modern bestsellers.
            <strong>Write Blogs:</strong> Share your thoughts, reviews, and unique perspectives on the books you love.
            <strong>Join Discussions:</strong> Comment, interact, and engage with fellow readers who are just as passionate about stories as you are.
            <p>✨ Let’s explore the world of books—together!  </p>
        </p>

        <!-- Conveyor Belt of Books -->
        <div class="book-slider">
            <div class="book-track">
            <img src="https://covers.openlibrary.org/b/id/8231856-L.jpg" alt="Fiction Book">
            <img src="https://covers.openlibrary.org/b/id/10909258-L.jpg" alt="Romance Book">
            <img src="https://covers.openlibrary.org/b/id/240726-L.jpg" alt="Classic Novel">
            <img src="https://covers.openlibrary.org/b/id/8235112-L.jpg" alt="Fantasy Book">
            <img src="https://covers.openlibrary.org/b/id/9251995-L.jpg" alt="Adventure Book">
            <img src="https://covers.openlibrary.org/b/id/11153272-L.jpg" alt="Thriller Book">
            <img src="https://covers.openlibrary.org/b/id/13518240-L.jpg" alt="Mystery Book">
            <img src="https://covers.openlibrary.org/b/id/9874010-L.jpg" alt="Self Help Book">
            <img src="https://covers.openlibrary.org/b/id/12662858-L.jpg" alt="Motivational Book">
            <img src="https://covers.openlibrary.org/b/id/10521241-L.jpg" alt="Sci-Fi Book">
            <img src="https://covers.openlibrary.org/b/id/8231856-L.jpg" alt="Fiction Book">
            <img src="https://covers.openlibrary.org/b/id/10909258-L.jpg" alt="Romance Book">
            <img src="https://covers.openlibrary.org/b/id/240726-L.jpg" alt="Classic Novel">
            <img src="https://covers.openlibrary.org/b/id/8235112-L.jpg" alt="Fantasy Book">
            <img src="https://covers.openlibrary.org/b/id/9251995-L.jpg" alt="Adventure Book">
            <img src="https://covers.openlibrary.org/b/id/11153272-L.jpg" alt="Thriller Book">
            <img src="https://covers.openlibrary.org/b/id/13518240-L.jpg" alt="Mystery Book">
            <img src="https://covers.openlibrary.org/b/id/9874010-L.jpg" alt="Self Help Book">
            <img src="https://covers.openlibrary.org/b/id/12662858-L.jpg" alt="Motivational Book">
            <img src="https://covers.openlibrary.org/b/id/10521241-L.jpg" alt="Sci-Fi Book">
            </div>
        </div>
        </section>
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

    <footer>
        <p>&copy; 2025 My PHP Project</p>
        <p>All rights reserved.</p>
    </footer>
</body>
</html>
