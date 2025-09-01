<?php

namespace App\Models;

use App\Core\Database;
use mysqli;

class Review
{
    public static function findByBookId(int $bookId): array
    {
        $db = Database::getInstance();
        $sql = "
            SELECT r.*, u.u_name
            FROM review r
            JOIN user u ON r.user_id = u.user_id
            WHERE r.book_id = ?
            ORDER BY r.r_date DESC
        ";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $bookId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public static function create(int $userId, int $bookId, string $title, string $reviewText, int $rating): bool
    {
        $db = Database::getInstance();
        $sql = "INSERT INTO review (user_id, book_id, r_title, review, rating) VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param('iissi', $userId, $bookId, $title, $reviewText, $rating);
        
        return $stmt->execute();
    }

    public static function findById(int $reviewId): ?array
    {
        $db = Database::getInstance();
        $sql = "SELECT user_id, book_id FROM review WHERE rev_id = ?";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $reviewId);
        $stmt->execute();
        $result = $stmt->get_result();
        $review = $result->fetch_assoc();

        return $review ?: null;
    }

    public static function delete(int $reviewId): bool
    {
        $db = Database::getInstance();
        $sql = "DELETE FROM review WHERE rev_id = ?";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $reviewId);

        return $stmt->execute();
    }

    public static function findByUserId(int $userId): array
    {
        $db = Database::getInstance();
        $sql = "
            SELECT r.r_title, r.book_id, b.title as book_title
            FROM review r
            JOIN book b ON r.book_id = b.book_id
            WHERE r.user_id = ?
            ORDER BY r.r_date DESC
        ";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }
}