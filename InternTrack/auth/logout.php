<?php
require_once __DIR__ . '/../config/auth.php';
logoutUser();
redirect('auth/login.php');
?>
