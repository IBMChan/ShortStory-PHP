<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$review_id = (int)$_GET['id'];
$current_user_id = (int)$_SESSION['user_id'];

$conn->begin_transaction();

try {
    $stmt = $conn->prepare("SELECT user_id, book_id FROM review WHERE rev_id = ?");
    $stmt->bind_param('i', $review_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $review = $result->fetch_assoc();

    if ($review) {
        $book_id = $review['book_id'];

        if ($review['user_id'] === $current_user_id) {
            $delete_stmt = $conn->prepare("DELETE FROM review WHERE rev_id = ?");
            $delete_stmt->bind_param('i', $review_id);
            $delete_stmt->execute();
            $delete_stmt->close();
        }
        
        $conn->commit();
        header('Location: book.php?id=' . $book_id);
        exit();
    } else {
        $conn->rollback();
        header('Location: index.php');
        exit();
    }

    $stmt->close();

} catch (Exception $e) {
    $conn->rollback();
    header('Location: index.php');
    exit();
}
