<?php
require_once '../db/db.php';
require_once 'helpers.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Function to generate next ID for books
function generateBookId($conn) {
    $result = $conn->query("SELECT book_id FROM book ORDER BY created_at DESC LIMIT 1");
    if ($row = $result->fetch_assoc()) {
        $lastId = intval(substr($row['book_id'], 1));
        return 'B' . ($lastId + 1);
    } else {
        return 'B1';
    }
}

// Function to generate next ID for authors
function generateAuthorId($conn) {
    $result = $conn->query("SELECT auth_id FROM author ORDER BY auth_id DESC LIMIT 1");
    if ($row = $result->fetch_assoc()) {
        $lastId = intval(substr($row['auth_id'], 1));
        return 'A' . ($lastId + 1);
    } else {
        return 'A1';
    }
}

// Handle book submission
$success = false;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['title'])) {
    $book_id = generateBookId($conn);
    $user_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $pub_year = $_POST['pub_year'];
    $price = $_POST['price'];
    $abstract = $_POST['abstract'];
    $genre = $_POST['genre'];

    // Only take the first author
    $author_name = trim($_POST['authors'][0]);
    $auth_id = generateAuthorId($conn);

    // Insert author
    $stmt = $conn->prepare("INSERT INTO author (auth_id, auth_name) VALUES (?, ?)");
    $stmt->bind_param("ss", $auth_id, $author_name);
    $stmt->execute();

    // Insert book
    $stmt = $conn->prepare("INSERT INTO book (book_id, user_id, author_id, title, pub_year, price, abstract, genre, created_at) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssssisds", $book_id, $user_id, $auth_id, $title, $pub_year, $price, $abstract, $genre);
    
    if ($stmt->execute()) {
        // Handle cover image upload
        if (!empty($_FILES['book_cover']['name'])) {
            $targetDir = "../assets/bookimg/";
            $ext = strtolower(pathinfo($_FILES['book_cover']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif','webp'];
            if (in_array($ext, $allowed)) {
                move_uploaded_file($_FILES['book_cover']['tmp_name'], $targetDir . $book_id . ".png");
            }
        }
        $success = true;
    } else {
        echo "❌ DB Error: " . $stmt->error;
    }
}

// Fetch books for current user
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT b.*, a.auth_name FROM book b 
                        JOIN author a ON b.author_id = a.auth_id
                        WHERE b.user_id = ? ORDER BY b.created_at DESC");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$books = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Genre options
$genres = [
    "Fantasy","Science Fiction (Sci-Fi)","Mystery","Thriller","Horror","Romance",
    "Historical Fiction","Adventure","Dystopian / Post-Apocalyptic","Literary Fiction",
    "Young Adult (YA)","Children’s Literature","Graphic Novels / Comics","Magical Realism",
    "Crime / Detective","Biography / Autobiography","Memoir","Self-Help","True Crime","History",
    "Science & Nature","Travel","Philosophy","Religion & Spirituality","Business & Economics",
    "Psychology","Politics & Current Affairs","Essays","Cooking / Food","Health & Fitness"
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Book - Short Story</title>
  <link rel="stylesheet" href="../assets/style.css">
  <link rel="stylesheet" href="../assets/blog.css">
  <link href="https://fonts.googleapis.com/css2?family=Tangerine:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
  <script>
    <?php if ($success): ?>
      alert("✅ Book added successfully!");
    <?php endif; ?>

    function addAuthorField() {
        alert("⚠️ Only one author allowed per book in this version.");
    }
  </script>
</head>
<body>
<header class="login-header">
  <p class="site-title">Short Story</p>
  <nav class="back-nav">
    <ul>
        <li><a href="home.php">⬅ Back</a></li>
    </ul>
  </nav>
</header>

<!-- Book form -->
<section class="blog-section">
  <div class="blog-form">
    <h2>Add Book</h2>
    <form action="" method="POST" enctype="multipart/form-data">
      <label>Book Title</label>
      <input type="text" name="title" required>

      <label>Publication Year</label>
      <input type="number" name="pub_year" min="1900" max="<?= date('Y'); ?>" required>

      <label>Price</label>
      <input type="number" step="0.01" name="price" required>

      <label>Abstract</label>
      <textarea name="abstract" rows="4" required></textarea>

      <label>Genre</label>
      <select name="genre" required>
        <option value="">--Select Genre--</option>
        <?php foreach ($genres as $g): ?>
            <option value="<?= htmlspecialchars($g); ?>"><?= htmlspecialchars($g); ?></option>
        <?php endforeach; ?>
      </select>

      <label>Author</label>
      <div id="authors-container">
        <input type="text" name="authors[]" placeholder="Author Name" required>
      </div>

      <label>Upload Cover Image</label>
      <input type="file" name="book_cover" accept="image/*">

      <button type="submit" class="btn">Add Book</button>
    </form>
  </div>
</section>

<!-- Display books -->
<section class="blogs-display">
  <h2>My Books</h2>
  <?php if (!empty($books)): ?>
    <div class="blogs-grid">
    <?php foreach ($books as $book): ?>
      <div class="blog-card">
        <?php
            $imgPath = "../assets/bookimg/" . $book['book_id'] . ".png";
            if (!file_exists($imgPath)) {
                $imgPath = "https://via.placeholder.com/120x160.png?text=No+Cover";
            }
        ?>
        <img src="<?= htmlspecialchars($imgPath); ?>" alt="Book Cover">
        <h3><?= htmlspecialchars($book['title']); ?></h3>
        <p><strong>Genre:</strong> <?= htmlspecialchars($book['genre']); ?></p>
        <p><strong>Abstract:</strong> <?= nl2br(htmlspecialchars($book['abstract'])); ?></p>
        <p><strong>Author:</strong> <?= htmlspecialchars($book['auth_name']); ?></p>
      </div>
    <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p>No books added yet.</p>
  <?php endif; ?>
</section>

<footer>
  <p>&copy; 2025 Short Story</p>
  <p>All rights reserved.</p>
</footer>
</body>
</html>
