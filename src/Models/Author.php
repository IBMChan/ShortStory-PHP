<?php

namespace App\Models;

use App\Core\Database;
use mysqli;

class Author
{
    public static function getAll(): array
    {
        $db = Database::getInstance();
        $sql = "SELECT auth_id, auth_name FROM author ORDER BY auth_name";
        $result = $db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public static function create(string $name): int
    {
        $db = Database::getInstance();
        $sql = "INSERT INTO author (auth_name) VALUES (?)";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('s', $name);
        $stmt->execute();
        return $db->insert_id;
    }
}