<?php
$page_title = 'Profil Saya';
require_once __DIR__ . '/includes/header.php';
$conn = getConnection();
$siswa_id = (int)$user['related_id'];
$siswa = $conn->query("SELECT * FROM siswa WHERE id=$siswa_id")->fetch_assoc();
$persen = getPersentaseKehadiran($siswa_id);
$status = hitungStatusKehadiran($persen);
?>
<div class="card-custom">
    <div class="card-header">Data Pribadi</div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <?php if ($siswa): ?>
                <table class="table table-borderless">
                    <tr><th width="140">NIS</th><td><?= htmlspecialchars($siswa['nis']) ?></td></tr>
                    <tr><th>Nama Lengkap</th><td><?= htmlspecialchars($siswa['nama']) ?></td></tr>
                    <tr><th>Kelas</th><td><?= htmlspecialchars($siswa['kelas']) ?></td></tr>
                    <tr><th>Jurusan</th><td><?= htmlspecialchars($siswa['jurusan']) ?></td></tr>
                    <tr><th>No. HP</th><td><?= htmlspecialchars($siswa['no_hp'] ?? '-') ?></td></tr>
                    <tr><th>Alamat</th><td><?= htmlspecialchars($siswa['alamat'] ?? '-') ?></td></tr>
                </table>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <div class="stat-card success mb-3"><div class="stat-value"><?= $persen ?>%</div><div class="stat-label">Persentase Kehadiran</div></div>
                <div class="stat-card"><div class="stat-value" style="font-size:1.2rem;"><?= $status ?></div><div class="stat-label">Status Kehadiran PKL</div></div>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
