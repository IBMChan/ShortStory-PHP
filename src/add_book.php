<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/../includes/db.php';

$authors = [];
$genres = [];

$result_authors = $conn->query("SELECT auth_id, auth_name FROM author ORDER BY auth_name");
if ($result_authors) {
    $authors = $result_authors->fetch_all(MYSQLI_ASSOC);
}

$result_genres = $conn->query("SELECT genre_id, genre_name FROM genre ORDER BY genre_name");
if ($result_genres) {
    $genres = $result_genres->fetch_all(MYSQLI_ASSOC);
}

$errors = [];
$title = '';
$pub_year = '';
$price = '';
$abstract = '';
$author_id = '';
$genre_id = '';
$new_author = '';
$new_genre = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $pub_year = trim($_POST['pub_year']);
    $price = trim($_POST['price']);
    $abstract = trim($_POST['abstract']);
    $author_id = $_POST['author_id'];
    $new_author = trim($_POST['new_author']);
    $genre_id = $_POST['genre_id'];
    $new_genre = trim($_POST['new_genre']);
    $user_id = $_SESSION['user_id'];

    if (empty($title)) $errors[] = 'Title is required.';
    if (empty($pub_year)) $errors[] = 'Publication year is required.';
    if (empty($price) || !is_numeric($price)) $errors[] = 'A valid price is required.';
    if (empty($abstract)) $errors[] = 'Abstract is required.';
    if (empty($author_id) && empty($new_author)) $errors[] = 'Please select or add an author.';
    if (!empty($author_id) && !empty($new_author)) $errors[] = 'Please either select an existing author or add a new one, not both.';
    if (!empty($genre_id) && !empty($new_genre)) $errors[] = 'Please either select an existing genre or add a new one, not both.';

    if (!isset($_FILES['cover_image']) || $_FILES['cover_image']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'A cover image is required.';
    } else {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['cover_image']['type'], $allowed_types)) {
            $errors[] = 'Invalid file type. Please upload a JPG, PNG, or GIF.';
        }
    }

    if (empty($errors)) {
        $conn->begin_transaction();
        try {
            if (!empty($new_author)) {
                $stmt = $conn->prepare("INSERT INTO author (auth_name) VALUES (?)");
                $stmt->bind_param('s', $new_author);
                $stmt->execute();
                $author_id = $conn->insert_id;
            }

            if (!empty($new_genre)) {
                $stmt = $conn->prepare("INSERT INTO genre (genre_name) VALUES (?)");
                $stmt->bind_param('s', $new_genre);
                $stmt->execute();
                $genre_id = $conn->insert_id;
            }
            
            $formatted_pub_date = $pub_year . '-01-01';
            $cover_image_filename = 'cover.jpg';
            
            $stmt = $conn->prepare("INSERT INTO book (user_id, title, author_id, genre_id, pub_year, price, abstract, cover_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('isiisdss', $user_id, $title, $author_id, $genre_id, $formatted_pub_date, $price, $abstract, $cover_image_filename);
            $stmt->execute();
            $new_book_id = $conn->insert_id;

            $upload_dir = __DIR__ . '/../assets/images/' . $new_book_id . '/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $upload_path = $upload_dir . $cover_image_filename;
            if (!move_uploaded_file($_FILES['cover_image']['tmp_name'], $upload_path)) {
                throw new Exception("Failed to move uploaded file.");
            }

            $conn->commit();
            header('Location: book.php?id=' . $new_book_id);
            exit();

        } catch (Exception $e) {
            $conn->rollback();
            $errors[] = "An error occurred while adding the book. Please try again.";
        }
    }
}

$page_title = 'Add a New Book';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="form-container">
    <form class="auth-form" method="POST" enctype="multipart/form-data">
        <h2>Add a New Book</h2>

        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <label for="title">Book Title</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
        </div>

        <div class="form-group">
            <label for="author_id">Select Author</label>
            <select id="author_id" name="author_id">
                <option value="">-- Select an Author --</option>
                <?php foreach ($authors as $author): ?>
                    <option value="<?php echo $author['auth_id']; ?>" <?php echo ($author_id == $author['auth_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($author['auth_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label for="new_author">Or Add New Author</label>
            <input type="text" id="new_author" name="new_author" value="<?php echo htmlspecialchars($new_author); ?>" placeholder="e.g., George Orwell">
        </div>

        <div class="form-group">
            <label for="genre_id">Select Genre</label>
            <select id="genre_id" name="genre_id">
                <option value="">-- Select a Genre --</option>
                 <?php foreach ($genres as $genre): ?>
                    <option value="<?php echo $genre['genre_id']; ?>" <?php echo ($genre_id == $genre['genre_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($genre['genre_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label for="new_genre">Or Add New Genre</label>
            <input type="text" id="new_genre" name="new_genre" value="<?php echo htmlspecialchars($new_genre); ?>" placeholder="e.g., Dystopian">
        </div>

        <div class="form-group">
            <label for="pub_year">Publication Year</label>
            <input type="number" id="pub_year" name="pub_year" min="1000" max="<?php echo date('Y'); ?>" value="<?php echo htmlspecialchars($pub_year); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="price">Price (INR)</label>
            <input type="text" id="price" name="price" value="<?php echo htmlspecialchars($price); ?>" placeholder="e.g., 1299.00" required>
        </div>

        <div class="form-group">
            <label for="abstract">Abstract</label>
            <textarea id="abstract" name="abstract" rows="6" required><?php echo htmlspecialchars($abstract); ?></textarea>
        </div>

        <div class="form-group">
            <label for="cover_image">Cover Image</label>
            <input type="file" id="cover_image" name="cover_image" required>
        </div>

        <button type="submit" class="btn btn-primary btn-full">Add Book</button>
    </form>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
