<?php
// Helper Functions InternTrack

session_start();

// Cek apakah user sudah login
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Cek role admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Cek role siswa
function isSiswa() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'siswa';
}

// Redirect jika belum login
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: ../auth/login.php");
        exit;
    }
}

// Redirect jika bukan admin
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header("Location: ../siswa/dashboard.php");
        exit;
    }
}

// Redirect jika bukan siswa
function requireSiswa() {
    requireLogin();
    if (!isSiswa()) {
        header("Location: ../admin/dashboard.php");
        exit;
    }
}

// Sanitize input
function clean($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));
}

// Hitung persentase kehadiran
function hitungPersentaseKehadiran($id_siswa) {
    global $conn;
    $query = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'Hadir' THEN 1 ELSE 0 END) as hadir
              FROM absensi WHERE id_siswa = $id_siswa";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    
    if ($row['total'] == 0) return 0;
    return round(($row['hadir'] / $row['total']) * 100, 2);
}

// Tentukan status PKL berdasarkan kehadiran
function statusKehadiran($persentase) {
    if ($persentase >= 90) return ['label' => 'Sangat Baik', 'class' => 'success'];
    if ($persentase >= 75) return ['label' => 'Baik', 'class' => 'primary'];
    if ($persentase >= 60) return ['label' => 'Perlu Perhatian', 'class' => 'warning'];
    return ['label' => 'Perlu Pembinaan', 'class' => 'danger'];
}

// Format tanggal Indonesia
function tglIndo($tanggal) {
    if (!$tanggal) return '-';
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $tgl = date('j', strtotime($tanggal));
    $bln = $bulan[(int)date('n', strtotime($tanggal))];
    $thn = date('Y', strtotime($tanggal));
    return "$tgl $bln $thn";
}

// Alert helper
function setAlert($type, $message) {
    $_SESSION['alert'] = ['type' => $type, 'message' => $message];
}

function showAlert() {
    if (isset($_SESSION['alert'])) {
        $type = $_SESSION['alert']['type'];
        $msg  = $_SESSION['alert']['message'];
        echo "<div class='alert alert-{$type} alert-dismissible fade show' role='alert'>
                {$msg}
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
              </div>";
        unset($_SESSION['alert']);
    }
}
?>
