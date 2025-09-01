<!-- Interface Implementation: Auth implements IAuth, which enforces login, signup, and logout methods.

Encapsulation: Database connection is private; authentication logic is contained within the class.

Separation of Concerns: All auth-related functionality is in one class. -->
<?php
// services/auth.php
require_once '../db/db.php';
require_once 'helpers.php'; // ID generator
require_once 'interfaces/IAuth.php';

class Auth implements IAuth {
    private $conn; // DB connection

    // Constructor
    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    // Login method
    public function login($username, $password) {
        $sql = "SELECT user_id, u_name, password FROM user WHERE u_name=? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                session_start();
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['username'] = $row['u_name'];
                return ["status" => true, "message" => "Login successful!"];
            } else {
                return ["status" => false, "message" => "Invalid password."];
            }
        } else {
            return ["status" => false, "message" => "User not found."];
        }
    }

    // Signup method
    public function signup($username, $password, $email, $phone, $confirmPassword) {
        if ($password !== $confirmPassword) {
            return ["status" => false, "message" => "Passwords do not match!"];
        } elseif (!preg_match("/^\d{10}$/", $phone)) {
            return ["status" => false, "message" => "Phone number must be exactly 10 digits."];
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Check if username exists
        $stmt_check = $this->conn->prepare("SELECT user_id FROM user WHERE u_name=?");
        $stmt_check->bind_param("s", $username);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            return ["status" => false, "message" => "Username already exists!"];
        }

        // Generate user_id
        $user_id = generateId($this->conn, "user", "U", "user_id");

        // Insert user
        $stmt = $this->conn->prepare("INSERT INTO user (user_id, u_name, password, email, contact, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssss", $user_id, $username, $hashedPassword, $email, $phone);

        if ($stmt->execute()) {
            return ["status" => true, "message" => "Successfully registered!"];
        } else {
            return ["status" => false, "message" => "Error: " . $stmt->error];
        }
    }

    // Logout method
    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        return ["status" => true, "message" => "Logged out successfully."];
    }
}
?>