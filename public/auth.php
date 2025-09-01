<?php
// Start session only if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Declare functions only if they don't exist
if (!function_exists('is_logged_in')) {
    function is_logged_in() {
        return isset($_SESSION['user_id']);
    }
}

if (!function_exists('get_logged_user')) {
    function get_logged_user() {
        return $_SESSION['u_name'] ?? null;
    }
}

// Load the User class only once
include_once 'User.php';

/** Abstract class for authentication */
abstract class Auth {
    abstract public function findUser(LoginMethod $method, $input);

    public function setSessionAndLogin(array $user) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['u_name']  = $user['u_name'];
    }

    public function logout() {
        session_unset();
        session_destroy();
    }
}

/** Polymorphism via Strategy Interface */
interface LoginMethod {
    public function buildQuery($conn, $input);
}

class UsernameLogin implements LoginMethod {
    public function buildQuery($conn, $input) {
        $u = $conn->real_escape_string($input);
        return "SELECT * FROM user WHERE u_name='$u' LIMIT 1";
    }
}

class EmailLogin implements LoginMethod {
    public function buildQuery($conn, $input) {
        $e = $conn->real_escape_string($input);
        return "SELECT * FROM user WHERE email='$e' LIMIT 1";
    }
}

/** Concrete implementation of Auth */
class UserAuth extends Auth {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function findUser(LoginMethod $method, $input) {
        $sql = $method->buildQuery($this->conn, $input);
        $result = $this->conn->query($sql);
        if ($result && $result->num_rows === 1) {
            return $result->fetch_assoc();
        }
        return null;
    }
}
