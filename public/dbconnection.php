<!-- <?php
$servername = "localhost";
$username = "root";
$password = "Raksha@2003";
$dbname = "booksdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";

$sql = "CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255),
    published_year INT(6),
    price DECIMAL(6,2),
    ratings FLOAT(7)
    )";

    if ($conn->query($sql) === TRUE) {
          echo "query executed successfully";
    } else {
          echo "query not executed " . $conn->error;
    }
    $conn->close();
?>  -->



<?php
class Database {
    private $host = "localhost";
    private $user = "root";
    private $pass = "Raksha@2003";
    private $dbname = "booksdb";
    private $conn = null;

    public function connect() {
        if ($this->conn instanceof mysqli) {
            return $this->conn;
        }
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
        return $this->conn;
    }
}

/* Backward compatibility: expose $conn like before */
$db = new Database();
$conn = $db->connect();
