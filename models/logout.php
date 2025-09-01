<?php
require_once '../services/auth.php';
$auth = new Auth($conn);
$auth->logout();
header("Location: ../index.php");
exit();
