<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/../includes/db.php';

$user_id = (int)$_SESSION['user_id'];
$errors = [];
$success_message = '';

if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $u_name = trim($_POST['u_name']);
        $email = trim($_POST['email']);
        $contact = trim($_POST['contact']);

        if (empty($u_name)) { $errors[] = 'Username is required.'; }
        if (empty($email)) { $errors[] = 'Email is required.'; }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = 'Invalid email format.'; }

        $stmt_check = $conn->prepare("SELECT user_id FROM user WHERE (u_name = ? OR email = ?) AND user_id != ?");
        $stmt_check->bind_param('ssi', $u_name, $email, $user_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        if ($result_check->num_rows > 0) {
            $errors[] = 'Username or email already taken.';
        }
        $stmt_check->close();

        if (empty($errors)) {
            $stmt_update = $conn->prepare("UPDATE user SET u_name = ?, email = ?, contact = ? WHERE user_id = ?");
            $stmt_update->bind_param('sssi', $u_name, $email, $contact, $user_id);
            if ($stmt_update->execute()) {
                $_SESSION['success_message'] = 'Profile updated successfully!';
                header('Location: profile.php');
                exit();
            } else {
                $errors[] = 'Failed to update profile. Please try again.';
            }
            $stmt_update->close();
        }
    } elseif (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $errors[] = 'All password fields are required.';
        } elseif ($new_password !== $confirm_password) {
            $errors[] = 'New passwords do not match.';
        } elseif (strlen($new_password) < 8) {
            $errors[] = 'New password must be at least 8 characters long.';
        } else {
            $stmt_pass = $conn->prepare("SELECT password FROM user WHERE user_id = ?");
            $stmt_pass->bind_param('i', $user_id);
            $stmt_pass->execute();
            $result_pass = $stmt_pass->get_result();
            $user_data = $result_pass->fetch_assoc();
            $stmt_pass->close();

            if ($user_data && password_verify($current_password, $user_data['password'])) {
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt_update_pass = $conn->prepare("UPDATE user SET password = ? WHERE user_id = ?");
                $stmt_update_pass->bind_param('si', $new_password_hash, $user_id);
                if ($stmt_update_pass->execute()) {
                    $_SESSION['success_message'] = 'Password changed successfully!';
                    header('Location: profile.php');
                    exit();
                } else {
                    $errors[] = 'Failed to update password. Please try again.';
                }
                $stmt_update_pass->close();
            } else {
                $errors[] = 'Incorrect current password.';
            }
        }
    }
}

$stmt_user = $conn->prepare("SELECT u_name, email, contact, created_at FROM user WHERE user_id = ?");
$stmt_user->bind_param('i', $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();

if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit();
}

$stmt_reviews = $conn->prepare("
    SELECT r.r_title, r.review, r.rating, r.r_date, b.book_id, b.title AS book_title
    FROM review r
    JOIN book b ON r.book_id = b.book_id
    WHERE r.user_id = ?
    ORDER BY r.r_date DESC
");
$stmt_reviews->bind_param('i', $user_id);
$stmt_reviews->execute();
$result_reviews = $stmt_reviews->get_result();
$reviews = $result_reviews->fetch_all(MYSQLI_ASSOC);

$page_title = 'My Profile';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="profile-container">
    <div class="profile-header">
        <h1><?php echo htmlspecialchars($user['u_name']); ?>'s Profile</h1>
        <p>Member since <?php echo date('F Y', strtotime($user['created_at'])); ?></p>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="error-message">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($success_message): ?>
        <div class="success-message">
            <p><?php echo $success_message; ?></p>
        </div>
    <?php endif; ?>

    <div class="profile-grid">
        <div class="profile-left-column">
            <div class="profile-details-card">
                <h2>My Details</h2>
                <form action="profile.php" method="POST" class="auth-form">
                    <div class="form-group">
                        <label for="u_name">Username</label>
                        <input type="text" id="u_name" name="u_name" value="<?php echo htmlspecialchars($user['u_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="contact">Contact (Optional)</label>
                        <input type="text" id="contact" name="contact" value="<?php echo htmlspecialchars($user['contact'] ?? ''); ?>">
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-primary">Update Details</button>
                </form>
            </div>
            
            <div class="profile-password-card">
                <h2>Change Password</h2>
                <form action="profile.php" method="POST" class="auth-form">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                </form>
            </div>
        </div>

        <div class="profile-reviews-card">
            <h2>My Reviews (<?php echo count($reviews); ?>)</h2>
            <?php if (!empty($reviews)): ?>
                <div class="profile-reviews-list">
                    <?php foreach ($reviews as $review): ?>
                        <div class="review-card-condensed">
                            <div class="review-info">
                                <strong><?php echo htmlspecialchars($review['r_title']); ?></strong>
                                <span class="review-book-title">for <?php echo htmlspecialchars($review['book_title']); ?></span>
                            </div>
                            <a href="book.php?id=<?php echo $review['book_id']; ?>" class="btn">View</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>You haven't written any reviews yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>