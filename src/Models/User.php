<?php

namespace App\Models;

use App\Core\Database;
use mysqli;

class User
{
    public static function findByUsername(string $username): ?array
    {
        $db = Database::getInstance();
        $sql = "SELECT user_id, u_name, password FROM user WHERE u_name = ?";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        return $user ?: null;
    }

    public static function create(string $username, string $email, string $password, ?string $contact): bool
    {
        $db = Database::getInstance();
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO user (u_name, email, password, contact) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('ssss', $username, $email, $hashedPassword, $contact);

        return $stmt->execute();
    }

    public static function findById(int $userId): ?array
    {
        $db = Database::getInstance();
        $sql = "SELECT user_id, u_name, email, contact FROM user WHERE user_id = ?";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        return $user ?: null;
    }

    public static function updateDetails(int $userId, string $username, string $email, ?string $contact): bool
    {
        $db = Database::getInstance();
        $sql = "UPDATE user SET u_name = ?, email = ?, contact = ? WHERE user_id = ?";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param('sssi', $username, $email, $contact, $userId);

        return $stmt->execute();
    }

    public static function updatePassword(int $userId, string $newPassword): bool
    {
        $db = Database::getInstance();
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE user SET password = ? WHERE user_id = ?";

        $stmt = $db->prepare($sql);
        $stmt->bind_param('si', $hashedPassword, $userId);

        return $stmt->execute();
    }
    
    public static function isUsernameOrEmailTaken(string $username, string $email, int $excludeUserId): bool
    {
        $db = Database::getInstance();
        $sql = "SELECT user_id FROM user WHERE (u_name = ? OR email = ?) AND user_id != ?";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param('ssi', $username, $email, $excludeUserId);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows > 0;
    }
}