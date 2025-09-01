<!-- Inheritance: Blog inherits from Content, gaining access to $conn and requiring implementation of abstract methods.

Encapsulation: $table is private; direct access from outside the class is restricted.

Abstraction in action: Abstract methods in Content are implemented here.

Reusability: The class can be instantiated anywhere to manage blogs without duplicating SQL logic. -->

<?php
require_once 'Content.php';

class Blog extends Content {
    private $table = "blogs";

    public function getAll($limit = 0, $offset = 0) {
        $sql = "SELECT b.*, u.u_name 
                FROM {$this->table} b 
                JOIN user u ON b.user_id = u.user_id 
                ORDER BY b.created_at DESC";

        if ($limit > 0) {
            $stmt = $this->conn->prepare($sql . " LIMIT ? OFFSET ?");
            $stmt->bind_param("ii", $limit, $offset);
        } else {
            $stmt = $this->conn->prepare($sql);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getByUser($userId) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE user_id=? ORDER BY created_at DESC");
        $stmt->bind_param("s", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function create($userId, $title, $intro, $body, $conclusion, $comments) {
        $blogId = uniqid("blog_", true);
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} 
            (blog_id, user_id, b_title, b_intro, b_body, b_con, b_comm, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssssss", $blogId, $userId, $title, $intro, $body, $conclusion, $comments);
        $stmt->execute();
        return $blogId;
    }
}
?>
