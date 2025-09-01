<?php
// Start session and include DB
session_start();
include 'dbconnection.php';
include_once 'User.php';

// Handle form submit
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Wrap input in a User object (encapsulation)
    $user = new User(
        $_POST['username'] ?? '',
        $_POST['email'] ?? '',
        $_POST['contact'] ?? null
    );

    $u_name  = $conn->real_escape_string($user->getUsername());
    $email   = $conn->real_escape_string($user->getEmail());
    $contact = $conn->real_escape_string((string)$user->getContact());
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // You should validate email and other fields here

    $sql = "INSERT INTO user (u_name, password, email, contact) 
            VALUES ('$u_name', '$password', '$email', '$contact')";
    if ($conn->query($sql)) {
        $message = 'Registration successful. <a href="login.php">Login here</a>.';
    } else {
        $message = 'Could not register: ' . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="../assets/styles.css">
</head>
<body>
    <h1>User Registration</h1>
    <form method="POST" action="register_proj.php">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="text" name="contact" placeholder="Contact"><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Create Account</button>
    </form>
    <p><?php echo $message; ?></p>
</body>
</html>
