<?php

require_once __DIR__ . '/../env.php';

use App\Models\Book;

$page_title = 'All Books';

$search_term = trim($_GET['search'] ?? '');
$books_per_page = 10;
$current_page = (int)($_GET['page'] ?? 1);
if ($current_page < 1) {
    $current_page = 1;
}

$total_books = Book::countAll($search_term);
$total_pages = ceil($total_books / $books_per_page);
$offset = ($current_page - 1) * $books_per_page;

$books = Book::findAllPaginated($books_per_page, $offset, $search_term);

require_once __DIR__ . '/../templates/header.php';
?>

<div class="search-container">
    <h2>Find a Book</h2>
    <form action="all_books.php" method="GET">
        <input type="text" name="search" placeholder="Search by title or author..." value="<?php echo htmlspecialchars($search_term); ?>">
        <button type="submit" class="btn btn-primary">Search</button>
    </form>
</div>

<div class="book-grid">
    <?php if (!empty($books)): ?>
        <?php foreach ($books as $book): ?>
            <div class="book-card">
                <a href="book.php?id=<?php echo htmlspecialchars($book['book_id']); ?>">
                    <img src="<?php echo htmlspecialchars($book['cover_image']); ?>" alt="Cover for <?php echo htmlspecialchars($book['title']); ?>">
                    <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                    <p><?php echo htmlspecialchars($book['auth_name']); ?></p>
                </a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No books found matching your criteria.</p>
    <?php endif; ?>
</div>

<div class="pagination">
    <?php if ($total_pages > 1): ?>
        <?php if ($current_page > 1): ?>
            <a href="all_books.php?page=<?php echo $current_page - 1; ?>&search=<?php echo urlencode($search_term); ?>" class="btn">&laquo; Prev</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="all_books.php?page=<?php echo $i; ?>&search=<?php echo urlencode($search_term); ?>" class="<?php echo ($i == $current_page) ? 'btn btn-primary' : 'btn'; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>

        <?php if ($current_page < $total_pages): ?>
            <a href="all_books.php?page=<?php echo $current_page + 1; ?>&search=<?php echo urlencode($search_term); ?>" class="btn">Next &raquo;</a>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>