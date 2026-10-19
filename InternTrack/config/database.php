<?php
/**
 * Konfigurasi Database & Helper InternTrack
 */

// ============ DATABASE CONFIG ============
// Sesuaikan dengan setting MySQL/XAMPP/Laragon Anda
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');          // kosongkan jika XAMPP default
define('DB_NAME', 'interntrack');

// ============ BASE URL (otomatis) ============
// Deteksi path aplikasi secara otomatis
$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
// Naik ke root aplikasi (keluar dari subfolder admin/siswa/auth/laporan)
while (basename($scriptName) === 'admin' || basename($scriptName) === 'siswa' || 
       basename($scriptName) === 'auth' || basename($scriptName) === 'laporan' ||
       basename($scriptName) === 'includes') {
    $scriptName = dirname($scriptName);
}
if ($scriptName === '/' || $scriptName === '\\' || $scriptName === '.') {
    $scriptName = '';
}
define('BASE_URL', rtrim($scriptName, '/'));

function base_url($path = '') {
    $path = ltrim($path, '/');
    return BASE_URL . ($path ? '/' . $path : '');
}

function getConnection() {
    static $conn = null;
    if ($conn === null) {
        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if ($conn->connect_error) {
                die('<div style="font-family:sans-serif;max-width:600px;margin:50px auto;padding:20px;border:1px solid #f5c6cb;background:#f8d7da;border-radius:8px;">
                    <h3>Koneksi Database Gagal</h3>
                    <p>' . htmlspecialchars($conn->connect_error) . '</p>
                    <p><strong>Solusi:</strong></p>
                    <ol>
                        <li>Pastikan MySQL/MariaDB sudah running</li>
                        <li>Import file <code>database/interntrack.sql</code> ke phpMyAdmin</li>
                        <li>Edit <code>config/database.php</code> (DB_USER, DB_PASS) sesuai setting Anda</li>
                    </ol>
                    </div>');
            }
            $conn->set_charset('utf8mb4');
        } catch (Exception $e) {
            die('Database error: ' . $e->getMessage());
        }
    }
    return $conn;
}

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim((string)$data)), ENT_QUOTES, 'UTF-8');
}

function redirect($path) {
    // Jika path sudah absolute (mulai http), pakai langsung
    if (strpos($path, 'http') === 0) {
        header('Location: ' . $path);
    } else {
        header('Location: ' . base_url($path));
    }
    exit;
}

function flash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function showFlash() {
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        $cls = $f['type'] === 'success' ? 'alert-success' : 'alert-danger';
        echo '<div class="alert ' . $cls . ' alert-dismissible fade show" role="alert">'
            . htmlspecialchars($f['message'])
            . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        unset($_SESSION['flash']);
    }
}

function hitungStatusKehadiran($persen) {
    if ($persen >= 90) return 'Sangat Baik';
    if ($persen >= 75) return 'Baik';
    if ($persen >= 60) return 'Perlu Perhatian';
    return 'Perlu Pembinaan';
}

function getPersentaseKehadiran($siswa_id) {
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT COUNT(*) as total, SUM(CASE WHEN status='Hadir' THEN 1 ELSE 0 END) as hadir FROM absensi WHERE siswa_id=?");
    $stmt->bind_param('i', $siswa_id);
    $stmt->execute();
    $r = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$r || $r['total'] == 0) return 0;
    return round(($r['hadir'] / $r['total']) * 100, 1);
}
?>
