<?php
require_once __DIR__ . '/config/auth.php';

if (isLoggedIn()) {
    if ($_SESSION['role'] === 'admin') {
        redirect('admin/dashboard.php');
    } else {
        redirect('siswa/dashboard.php');
    }
} else {
    redirect('auth/login.php');
}
?>
