<?php
include_once 'dbconnection.php';
include_once 'auth.php';
include_once 'User.php';

$message = '';

// Absolute redirect path (unchanged)
$redirect_index = '/ride_app/public/index_proj.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input    = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Choose strategy based on input (polymorphism)
    $strategy = (strpos($input, '@') !== false) ? new EmailLogin() : new UsernameLogin();

    // Abstraction: use Auth implementation
    $auth = new UserAuth($conn);
    $row  = $auth->findUser($strategy, $input);

    if ($row) {
        // Accept hashed or plain (kept from your original logic)
        if ($password === $row['password'] || password_verify($password, $row['password'])) {
            $auth->setSessionAndLogin($row);
            header("Location: $redirect_index");
            exit();
        } else {
            $message = "Incorrect password.";
        }
    } else {
        $message = "Username not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../assets/styles.css">
</head>
<body>
    <h1>User Login</h1>
    <form method="POST" action="login.php">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Login</button>
    </form>
    <p><?php echo $message; ?></p>
</body>
</html>
