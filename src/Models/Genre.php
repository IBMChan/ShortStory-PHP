<?php

namespace App\Models;

use App\Core\Database;
use mysqli;

class Genre
{
    public static function getAll(): array
    {
        $db = Database::getInstance();
        $sql = "SELECT genre_id, genre_name FROM genre ORDER BY genre_name";
        $result = $db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public static function create(string $name): int
    {
        $db = Database::getInstance();
        $sql = "INSERT INTO genre (genre_name) VALUES (?)";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('s', $name);
        $stmt->execute();
        return $db->insert_id;
    }
}