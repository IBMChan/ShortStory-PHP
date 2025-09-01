<?php
require_once 'Content.php';

class Blog extends Content {
    private $table = "blogs";

    // Fetch all blogs with optional limit and offset
    public function getAll($limit = 0, $offset = 0) {
        $sql = "SELECT b.*, u.u_name 
                FROM {$this->table} b 
                JOIN user u ON b.user_id = u.user_id 
                ORDER BY b.created_at DESC";

        // MySQLi cannot bind LIMIT/OFFSET as parameters, so cast them and append
        if ($limit > 0) {
            $sql .= " LIMIT " . intval($limit) . " OFFSET " . intval($offset);
        }

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->conn->error);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Fetch blogs for a specific user
    public function getByUser($userId) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE user_id=? ORDER BY created_at DESC");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->conn->error);
        }

        // Change "s" to "i" if user_id is integer
        $stmt->bind_param("s", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Create a new blog
    public function create($userId, $title, $intro, $body, $conclusion, $comments) {
        $blogId = uniqid("blog_", true);
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} 
            (blog_id, user_id, b_title, b_intro, b_body, b_con, b_comm, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param("sssssss", $blogId, $userId, $title, $intro, $body, $conclusion, $comments);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        return $blogId;
    }
}
?>
