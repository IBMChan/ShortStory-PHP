<?php

require_once __DIR__ . '/../env.php';

use App\Models\User;

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$errors = [];
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $errors[] = 'Both username and password are required.';
    } else {
        $user = User::findByUsername($username);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['u_name'] = $user['u_name'];
            header('Location: index.php');
            exit();
        } else {
            $errors[] = 'Invalid username or password.';
        }
    }
}

$page_title = 'Login';
require_once __DIR__ . '/../templates/header.php';
?>

<div class="form-container">
    <form action="login.php" method="POST" class="auth-form">
        <h2>Login to Your Account</h2>

        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary btn-full">Login</button>
        <p class="form-switch">
            Don't have an account? <a href="register.php">Register here</a>
        </p>
    </form>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>