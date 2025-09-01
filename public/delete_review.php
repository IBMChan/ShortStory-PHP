<?php

require_once __DIR__ . '/../env.php';

use App\Models\Review;

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

$review = Review::findById($review_id);

if ($review) {
    $book_id = $review['book_id'];

    if ($review['user_id'] === $current_user_id) {
        Review::delete($review_id);
    }

    header('Location: book.php?id=' . $book_id);
    exit();
} else {
    header('Location: index.php');
    exit();
}