<?php
include_once 'auth.php';
session_unset();
session_destroy();
header('Location: index_proj.php');
exit();
?>
