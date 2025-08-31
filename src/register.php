<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

require_once __DIR__ . '/../includes/db.php';

$errors = [];
$username = '';
$email = '';
$contact = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $email = trim($_POST['email']);
    $contact = trim($_POST['contact']);

    if (empty($username) || empty($password) || empty($email)) {
        $errors[] = 'Username, password, and email are required.';
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
        $sql_check = "SELECT user_id FROM user WHERE u_name = ? OR email = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param('ss', $username, $email);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            $errors[] = 'Username or email already exists.';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $sql_insert = "INSERT INTO user (u_name, password, email, contact) VALUES (?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param('ssss', $username, $hashed_password, $email, $contact);

            if ($stmt_insert->execute()) {
                header('Location: login.php?status=registered');
                exit();
            } else {
                $errors[] = 'Could not register account. Please try again.';
            }
            $stmt_insert->close();
        }
        $stmt_check->close();
    }
}

$page_title = 'Register';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="form-container">
    <form action="register.php" method="POST" class="auth-form">
        <h2>Create an Account</h2>

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

<?php
require_once __DIR__ . '/../includes/footer.php';
?>