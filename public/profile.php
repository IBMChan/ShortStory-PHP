<?php

require_once __DIR__ . '/../env.php';

use App\Models\User;
use App\Models\Review;

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

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

        if (empty($u_name)) $errors[] = 'Username is required.';
        if (empty($email)) $errors[] = 'Email is required.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format.';
        
        if (User::isUsernameOrEmailTaken($u_name, $email, $user_id)) {
            $errors[] = 'Username or email is already in use by another account.';
        }

        if (empty($errors)) {
            $contact_value = !empty($contact) ? $contact : null;
            if (User::updateDetails($user_id, $u_name, $email, $contact_value)) {
                $_SESSION['success_message'] = 'Profile details updated successfully!';
                header('Location: profile.php');
                exit();
            } else {
                $errors[] = 'Could not update profile. Please try again.';
            }
        }
    }

    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $errors[] = 'All password fields are required.';
        }
        if ($new_password !== $confirm_password) {
            $errors[] = 'New passwords do not match.';
        }
        if (strlen($new_password) < 8) {
            $errors[] = 'New password must be at least 8 characters long.';
        }

        if (empty($errors)) {
            $user_for_pass_check = User::findByUsername($_SESSION['u_name']);
            if ($user_for_pass_check && password_verify($current_password, $user_for_pass_check['password'])) {
                if (User::updatePassword($user_id, $new_password)) {
                    $_SESSION['success_message'] = 'Password changed successfully!';
                    header('Location: profile.php');
                    exit();
                } else {
                    $errors[] = 'Could not change password. Please try again.';
                }
            } else {
                $errors[] = 'Incorrect current password.';
            }
        }
    }
}

$user = User::findById($user_id);
$reviews = Review::findByUserId($user_id);

$page_title = 'My Profile';
require_once __DIR__ . '/../templates/header.php';
?>

<div class="profile-header">
    <h1>Welcome, <?php echo htmlspecialchars($user['u_name']); ?>!</h1>
    <p>Manage your account details and view your review history.</p>
</div>

<?php if ($success_message): ?>
    <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <div class="error-message">
        <?php foreach ($errors as $error): ?>
            <p><?php echo htmlspecialchars($error); ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="profile-grid">
    <div class="profile-left-column">
        <div class="profile-details-card">
            <h2>Your Details</h2>
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
                <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
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

<?php require_once __DIR__ . '/../templates/footer.php'; ?>