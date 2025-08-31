<?php
require_once '../db/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $blog_id = $_POST['id']; // keep as string

    // Verify blog belongs to user
    $stmt = $conn->prepare("SELECT blog_id FROM blogs WHERE blog_id = ? AND user_id = ?");
    $stmt->bind_param("ss", $blog_id, $_SESSION['user_id']); // string, string
    $stmt->execute();
    $result = $stmt->get_result();
    $blog = $result->fetch_assoc();

    if ($blog) {
        // Delete DB entry
        $stmt = $conn->prepare("DELETE FROM blogs WHERE blog_id = ? AND user_id = ?");
        $stmt->bind_param("ss", $blog_id, $_SESSION['user_id']);
        $stmt->execute();

        // Delete image if exists
        $base = "../assets/image/" . $blog_id;
        $candidates = [$base . ".png", $base . ".jpg", $base . ".jpeg", $base . ".gif", $base . ".webp"];
        foreach ($candidates as $path) {
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }
}

header("Location: createblog.php");
exit();
?>