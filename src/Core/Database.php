<?php

namespace App\Core;

use mysqli;

class Database
{
    private static ?mysqli $instance = null;
    private mysqli $connection;

    private function __construct()
    {
        $host = $_ENV['DB_HOST'];
        $username = $_ENV['DB_USERNAME'];
        $password = $_ENV['DB_PASSWORD'];
        $database = $_ENV['DB_DATABASE'];
        $port = $_ENV['DB_PORT'] ?? 3306;

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $this->connection = new mysqli($host, $username, $password, $database, $port);
        $this->connection->set_charset("utf8mb4");
    }

    public static function getInstance(): mysqli
    {
        if (self::$instance === null) {
            self::$instance = (new self())->connection;
        }
        return self::$instance;
    }

    private function __clone() {}
    public function __wakeup() {}
}