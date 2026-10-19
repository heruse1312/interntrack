<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/database.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect('auth/login.php');
    }
}

function requireAdmin() {
    requireLogin();
    if (($_SESSION['role'] ?? '') !== 'admin') {
        redirect('siswa/dashboard.php');
    }
}

function requireSiswa() {
    requireLogin();
    if (($_SESSION['role'] ?? '') !== 'siswa') {
        redirect('admin/dashboard.php');
    }
}

function currentUser() {
    return [
        'id'         => $_SESSION['user_id'] ?? null,
        'username'   => $_SESSION['username'] ?? null,
        'role'       => $_SESSION['role'] ?? null,
        'related_id' => $_SESSION['related_id'] ?? null,
        'nama'       => $_SESSION['nama'] ?? null,
    ];
}

function loginUser($username, $password) {
    $conn = getConnection();
    $stmt = $conn->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']     = $user['id'];
        $_SESSION['username']    = $user['username'];
        $_SESSION['role']        = $user['role'];
        $_SESSION['related_id']  = $user['related_id'];

        if ($user['role'] === 'siswa' && $user['related_id']) {
            $s = $conn->query('SELECT nama FROM siswa WHERE id = ' . (int)$user['related_id'])->fetch_assoc();
            $_SESSION['nama'] = $s['nama'] ?? $user['username'];
        } else {
            $_SESSION['nama'] = 'Administrator';
        }
        return true;
    }
    return false;
}

function logoutUser() {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}
?>
