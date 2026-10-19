<?php
$page_title = 'Dashboard Siswa';
require_once __DIR__ . '/includes/header.php';
$conn = getConnection();
$siswa_id = (int)$user['related_id'];

$siswa = $conn->query("SELECT * FROM siswa WHERE id=$siswa_id")->fetch_assoc();
$penempatan = $conn->query("SELECT pe.*, p.nama_perusahaan, p.alamat, p.bidang, pb.nama pembimbing
    FROM penempatan pe JOIN perusahaan p ON pe.perusahaan_id=p.id JOIN pembimbing pb ON pe.pembimbing_id=pb.id
    WHERE pe.siswa_id=$siswa_id ORDER BY pe.id DESC LIMIT 1")->fetch_assoc();

$jurnal_count = (int)$conn->query("SELECT COUNT(*) c FROM jurnal WHERE siswa_id=$siswa_id")->fetch_assoc()['c'];
$persen = getPersentaseKehadiran($siswa_id);
$status_kehadiran = hitungStatusKehadiran($persen);
$badge = $persen>=90?'badge-sangatbaik':($persen>=75?'badge-baik':($persen>=60?'badge-perhatian':'badge-pembinaan'));
?>
<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="stat-card"><div class="stat-value"><?= $jurnal_count ?></div><div class="stat-label">Total Jurnal</div></div></div>
    <div class="col-md-4"><div class="stat-card success"><div class="stat-value"><?= $persen ?>%</div><div class="stat-label">Persentase Kehadiran</div></div></div>
    <div class="col-md-4"><div class="stat-card"><div class="stat-value"><span class="badge-status <?= $badge ?>" style="font-size:1rem;"><?= $status_kehadiran ?></span></div><div class="stat-label">Status PKL</div></div></div>
</div>
<div class="row g-3">
    <div class="col-lg-6">
        <div class="card-custom">
            <div class="card-header">Profil Saya</div>
            <div class="card-body">
                <?php if ($siswa): ?>
                <table class="table table-borderless mb-0">
                    <tr><th width="120">NIS</th><td><?= htmlspecialchars($siswa['nis']) ?></td></tr>
                    <tr><th>Nama</th><td><?= htmlspecialchars($siswa['nama']) ?></td></tr>
                    <tr><th>Kelas</th><td><?= htmlspecialchars($siswa['kelas']) ?></td></tr>
                    <tr><th>Jurusan</th><td><?= htmlspecialchars($siswa['jurusan']) ?></td></tr>
                    <tr><th>No. HP</th><td><?= htmlspecialchars($siswa['no_hp'] ?? '-') ?></td></tr>
                </table>
                <?php else: ?><p class="text-muted">Data siswa tidak ditemukan.</p><?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card-custom">
            <div class="card-header">Tempat PKL</div>
            <div class="card-body">
                <?php if ($penempatan): ?>
                <table class="table table-borderless mb-0">
                    <tr><th width="130">Perusahaan</th><td><?= htmlspecialchars($penempatan['nama_perusahaan']) ?></td></tr>
                    <tr><th>Alamat</th><td><?= htmlspecialchars($penempatan['alamat']) ?></td></tr>
                    <tr><th>Bidang</th><td><?= htmlspecialchars($penempatan['bidang']) ?></td></tr>
                    <tr><th>Pembimbing</th><td><?= htmlspecialchars($penempatan['pembimbing']) ?></td></tr>
                    <tr><th>Periode</th><td><?= date('d/m/Y',strtotime($penempatan['tanggal_mulai'])) ?> - <?= date('d/m/Y',strtotime($penempatan['tanggal_selesai'])) ?></td></tr>
                    <tr><th>Status</th><td><?php $b=$penempatan['status']=='Aktif'?'badge-aktif':($penempatan['status']=='Selesai'?'badge-selesai':'badge-belum'); ?><span class="badge-status <?= $b ?>"><?= $penempatan['status'] ?></span></td></tr>
                </table>
                <?php else: ?><p class="text-muted mb-0">Belum ada penempatan PKL.</p><?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
