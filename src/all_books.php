<?php
require_once __DIR__ . '/../includes/db.php';

$page_title = 'All Books';

$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
$books_per_page = 10;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) {
    $current_page = 1;
}

$offset = ($current_page - 1) * $books_per_page;

$sql_count = "
    SELECT COUNT(b.book_id) as total
    FROM book b
    JOIN author a ON b.author_id = a.auth_id
";
$sql = "
    SELECT b.book_id, b.title, b.cover_image, a.auth_name
    FROM book b
    JOIN author a ON b.author_id = a.auth_id
";

$params = [];
$types = '';

if (!empty($search_term)) {
    $search_like = '%' . $search_term . '%';
    $sql_where = " WHERE b.title LIKE ? OR a.auth_name LIKE ?";
    $sql_count .= $sql_where;
    $sql .= $sql_where;
    $params[] = $search_like;
    $params[] = $search_like;
    $types = 'ss';
}

$stmt_count = $conn->prepare($sql_count);
if (!empty($search_term)) {
    $stmt_count->bind_param($types, ...$params);
}
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$total_books = $result_count->fetch_assoc()['total'];
$total_pages = ceil($total_books / $books_per_page);

if ($current_page > $total_pages && $total_pages > 0) {
    $current_page = $total_pages;
    $offset = ($current_page - 1) * $books_per_page;
}

$sql .= " ORDER BY b.title ASC LIMIT ? OFFSET ?";
$params[] = $books_per_page;
$params[] = $offset;
$types .= 'ii';

$stmt = $conn->prepare($sql);
if (!empty($search_term)) {
    $stmt->bind_param($types, ...$params);
} else {
    $stmt->bind_param('ii', $books_per_page, $offset);
}
$stmt->execute();
$result_books = $stmt->get_result();
$books = $result_books->fetch_all(MYSQLI_ASSOC);

require_once __DIR__ . '/../includes/header.php';
?>

<div class="search-container">
    <h2>Browse All Books</h2>
    <form action="all_books.php" method="GET" class="search-form">
        <input type="text" name="search" placeholder="Search by title or author..." value="<?php echo htmlspecialchars($search_term); ?>">
        <button type="submit" class="btn">Search</button>
    </form>
</div>

<div class="book-grid">
    <?php if (!empty($books)): ?>
        <?php foreach ($books as $book): ?>
            <div class="book-card">
                 <a href="book.php?id=<?php echo htmlspecialchars($book['book_id']); ?>">
                    <img src="../assets/images/<?php echo htmlspecialchars($book['book_id']); ?>/<?php echo htmlspecialchars($book['cover_image']); ?>" alt="Cover for <?php echo htmlspecialchars($book['title']); ?>">
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

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
