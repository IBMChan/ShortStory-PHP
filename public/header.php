<?php
include 'auth.php';
?>

<header>
    <nav>
        <a href="index_proj.php">Home</a> |
        <?php if (is_logged_in()) : ?>
            <a href="add_review.php">Add Review</a> |
            <a href="logout.php">Logout (<?php echo htmlspecialchars(get_logged_user()); ?>)</a>
        <?php else : ?>
            <a href="login.php">Login</a> |
            <a href="register_proj.php">Register</a>
        <?php endif; ?>
    </nav>
</header>
