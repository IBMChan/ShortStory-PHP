<?php

namespace App\Models;

use App\Core\Database;
use App\Core\S3Uploader;
use mysqli;
use Exception;

class Book
{
    public static function getTopRated(int $limit): array
    {
        $db = Database::getInstance();
        $sql = "
            SELECT b.book_id, b.title, b.cover_image, a.auth_name, AVG(r.rating) as avg_rating
            FROM book b
            JOIN author a ON b.author_id = a.auth_id
            JOIN review r ON b.book_id = r.book_id
            GROUP BY b.book_id, b.title, b.cover_image, a.auth_name
            ORDER BY avg_rating DESC
            LIMIT ?
        ";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public static function getLatest(int $limit): array
    {
        $db = Database::getInstance();
        $sql = "
            SELECT b.book_id, b.title, b.cover_image, a.auth_name
            FROM book b
            JOIN author a ON b.author_id = a.auth_id
            ORDER BY b.pub_year DESC
            LIMIT ?
        ";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public static function findById(int $bookId): ?array
    {
        $db = Database::getInstance();
        $sql = "
            SELECT b.*, a.auth_name, g.genre_name
            FROM book b
            JOIN author a ON b.author_id = a.auth_id
            LEFT JOIN genre g ON b.genre_id = g.genre_id
            WHERE b.book_id = ?
        ";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $bookId);
        $stmt->execute();
        $result = $stmt->get_result();
        $book = $result->fetch_assoc();

        return $book ?: null;
    }

    public static function create(array $data, array $file): ?int
    {
        $db = Database::getInstance();
        $db->begin_transaction();

        try {
            $author_id = (int)$data['author_id'];
            if (!empty($data['new_author'])) {
                $author_id = Author::create($data['new_author']);
            }

            $genre_id = (int)$data['genre_id'];
            if (!empty($data['new_genre'])) {
                $genre_id = Genre::create($data['new_genre']);
            }

            $formatted_pub_year = $data['pub_year'] . '-01-01';
            
            $sql_insert = "INSERT INTO book (user_id, title, author_id, genre_id, pub_year, price, abstract, cover_image) VALUES (?, ?, ?, ?, ?, ?, ?, 'temp')";
            $stmt_insert = $db->prepare($sql_insert);
            $stmt_insert->bind_param(
                'isiisds',
                $data['user_id'],
                $data['title'],
                $author_id,
                $genre_id,
                $formatted_pub_year,
                $data['price'],
                $data['abstract']
            );
            $stmt_insert->execute();
            $new_book_id = $db->insert_id;

            if ($new_book_id === 0) {
                throw new Exception("Failed to create book record.");
            }

            $s3_key = 'assets/images/' . $new_book_id . '/cover.jpg';
            $s3_url = S3Uploader::upload($file['tmp_name'], $s3_key);

            if ($s3_url === null) {
                throw new Exception("Failed to upload cover image to S3.");
            }

            $sql_update = "UPDATE book SET cover_image = ? WHERE book_id = ?";
            $stmt_update = $db->prepare($sql_update);
            $stmt_update->bind_param('si', $s3_url, $new_book_id);
            $stmt_update->execute();

            $db->commit();
            return $new_book_id;

        } catch (Exception $e) {
            $db->rollback();
            error_log($e->getMessage());
            return null;
        }
    }

    public static function countAll(string $searchTerm = ''): int
    {
        $db = Database::getInstance();
        $sql = "SELECT COUNT(b.book_id) as total FROM book b JOIN author a ON b.author_id = a.auth_id";
        
        if (!empty($searchTerm)) {
            $sql .= " WHERE b.title LIKE ? OR a.auth_name LIKE ?";
            $stmt = $db->prepare($sql);
            $search_like = '%' . $searchTerm . '%';
            $stmt->bind_param('ss', $search_like, $search_like);
        } else {
            $stmt = $db->prepare($sql);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        return (int)$result->fetch_assoc()['total'];
    }

    public static function findAllPaginated(int $limit, int $offset, string $searchTerm = ''): array
    {
        $db = Database::getInstance();
        $sql = "
            SELECT b.book_id, b.title, b.cover_image, a.auth_name
            FROM book b
            JOIN author a ON b.author_id = a.auth_id
        ";

        if (!empty($searchTerm)) {
            $sql .= " WHERE b.title LIKE ? OR a.auth_name LIKE ?";
        }
        
        $sql .= " ORDER BY b.title ASC LIMIT ? OFFSET ?";
        $stmt = $db->prepare($sql);

        if (!empty($searchTerm)) {
            $search_like = '%' . $searchTerm . '%';
            $stmt->bind_param('ssii', $search_like, $search_like, $limit, $offset);
        } else {
            $stmt->bind_param('ii', $limit, $offset);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}