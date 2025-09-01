<?php

require_once __DIR__ . '/../env.php';

use App\Models\Author;
use App\Models\Genre;
use App\Models\Book;

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$authors = Author::getAll();
$genres = Genre::getAll();

$errors = [];
$form_data = [
    'title' => '',
    'pub_year' => '',
    'price' => '',
    'abstract' => '',
    'author_id' => '',
    'new_author' => '',
    'genre_id' => '',
    'new_genre' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_data = array_map('trim', $_POST);
    
    if (empty($form_data['title'])) $errors[] = 'Title is required.';
    if (empty($form_data['pub_year'])) $errors[] = 'Publication year is required.';
    if (!is_numeric($form_data['pub_year']) || strlen($form_data['pub_year']) !== 4) $errors[] = 'Invalid year format.';
    if (empty($form_data['price'])) $errors[] = 'Price is required.';
    if (!is_numeric($form_data['price'])) $errors[] = 'Price must be a number.';
    if (empty($form_data['abstract'])) $errors[] = 'Abstract is required.';
    if (empty($form_data['author_id']) && empty($form_data['new_author'])) $errors[] = 'Please select or add an author.';
    if (empty($form_data['genre_id']) && empty($form_data['new_genre'])) $errors[] = 'Please select or add a genre.';
    
    if (!isset($_FILES['cover_image']) || $_FILES['cover_image']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Cover image is required and must be uploaded successfully.';
    } else {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['cover_image']['type'], $allowed_types)) {
            $errors[] = 'Invalid file type. Please upload a JPG, PNG, or GIF.';
        }
    }

    if (empty($errors)) {
        $data_to_save = $form_data;
        $data_to_save['user_id'] = $_SESSION['user_id'];
        
        $new_book_id = Book::create($data_to_save, $_FILES['cover_image']);

        if ($new_book_id) {
            header("Location: book.php?id=" . $new_book_id);
            exit();
        } else {
            $errors[] = "An error occurred while adding the book. Please try again.";
        }
    }
}

$page_title = 'Add a New Book';
require_once __DIR__ . '/../templates/header.php';
?>

<div class="form-container">
    <form action="add_book.php" method="POST" enctype="multipart/form-data" class="auth-form">
        <h2>Add a New Book</h2>
        
        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($form_data['title']); ?>" required>
        </div>

        <div class="form-group">
            <label for="author_id">Select Author</label>
            <select id="author_id" name="author_id">
                <option value="">-- Select an Author --</option>
                <?php foreach ($authors as $author): ?>
                    <option value="<?php echo $author['auth_id']; ?>" <?php echo ($form_data['author_id'] == $author['auth_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($author['auth_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label for="new_author">Or Add New Author</label>
            <input type="text" id="new_author" name="new_author" value="<?php echo htmlspecialchars($form_data['new_author']); ?>" placeholder="e.g., George Orwell">
        </div>

        <div class="form-group">
            <label for="genre_id">Select Genre</label>
            <select id="genre_id" name="genre_id">
                <option value="">-- Select a Genre --</option>
                <?php foreach ($genres as $genre): ?>
                    <option value="<?php echo $genre['genre_id']; ?>" <?php echo ($form_data['genre_id'] == $genre['genre_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($genre['genre_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label for="new_genre">Or Add New Genre</label>
            <input type="text" id="new_genre" name="new_genre" value="<?php echo htmlspecialchars($form_data['new_genre']); ?>" placeholder="e.g., Dystopian">
        </div>

        <div class="form-group">
            <label for="pub_year">Publication Year</label>
            <input type="number" id="pub_year" name="pub_year" min="1000" max="<?php echo date('Y'); ?>" value="<?php echo htmlspecialchars($form_data['pub_year']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="price">Price (INR)</label>
            <input type="text" id="price" name="price" value="<?php echo htmlspecialchars($form_data['price']); ?>" placeholder="e.g., 1299.00" required>
        </div>

        <div class="form-group">
            <label for="abstract">Abstract</label>
            <textarea id="abstract" name="abstract" rows="6" required><?php echo htmlspecialchars($form_data['abstract']); ?></textarea>
        </div>

        <div class="form-group">
            <label for="cover_image">Cover Image</label>
            <input type="file" id="cover_image" name="cover_image" required>
        </div>

        <button type="submit" class="btn btn-primary btn-full">Add Book</button>
    </form>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>