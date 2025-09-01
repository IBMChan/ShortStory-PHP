<?php

require_once __DIR__ . '/../env.php';

use App\Models\User;

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$errors = [];
$username = '';
$email = '';
$contact = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $contact = trim($_POST['contact']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if (empty($username) || empty($email) || empty($password)) {
        $errors[] = 'Username, email, and password are required.';
    }
    if ($password !== $password_confirm) {
        $errors[] = 'Passwords do not match.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if (empty($errors)) {
        if (User::findByUsername($username)) {
            $errors[] = 'Username is already taken.';
        } else {
            $contact_value = !empty($contact) ? $contact : null;
            $success = User::create($username, $email, $password, $contact_value);

            if ($success) {
                header('Location: login.php?registered=success');
                exit();
            } else {
                $errors[] = 'Could not register user. Please try again.';
            }
        }
    }
}

$page_title = 'Register';
require_once __DIR__ . '/../templates/header.php';
?>

<div class="form-container">
    <form action="register.php" method="POST" class="auth-form">
        <h2>Create a New Account</h2>

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
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>
         <div class="form-group">
            <label for="contact">Contact (Optional)</label>
            <input type="text" id="contact" name="contact" value="<?php echo htmlspecialchars($contact); ?>">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="password_confirm">Confirm Password</label>
            <input type="password" id="password_confirm" name="password_confirm" required>
        </div>
        <button type="submit" class="btn btn-primary btn-full">Register</button>
        <p class="form-switch">
            Already have an account? <a href="login.php">Login here</a>
        </p>
    </form>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>