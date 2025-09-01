<?php
abstract class Content {
    protected $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    abstract public function getAll($limit = 0, $offset = 0);
    abstract public function getByUser($userId);
}
?>
<!-- // OOP Concepts Demonstrated:

// Abstraction: The class defines a contract without specifying implementation.

// Encapsulation: Database connection is protected, restricting direct external access. -->