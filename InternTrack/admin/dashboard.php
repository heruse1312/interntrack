<?php
$page_title = 'Dashboard';
require_once __DIR__ . '/includes/header.php';
$conn = getConnection();

$total_siswa      = (int)$conn->query("SELECT COUNT(*) c FROM siswa")->fetch_assoc()['c'];
$total_perusahaan = (int)$conn->query("SELECT COUNT(*) c FROM perusahaan")->fetch_assoc()['c'];
$total_pembimbing = (int)$conn->query("SELECT COUNT(*) c FROM pembimbing")->fetch_assoc()['c'];
$pkl_aktif        = (int)$conn->query("SELECT COUNT(*) c FROM penempatan WHERE status='Aktif'")->fetch_assoc()['c'];
$pkl_selesai      = (int)$conn->query("SELECT COUNT(*) c FROM penempatan WHERE status='Selesai'")->fetch_assoc()['c'];
$jurnal_hari_ini  = (int)$conn->query("SELECT COUNT(*) c FROM jurnal WHERE tanggal=CURDATE()")->fetch_assoc()['c'];

$siswa_perhatian = 0;
$res = $conn->query("SELECT id FROM siswa");
while ($s = $res->fetch_assoc()) {
    $p = getPersentaseKehadiran($s['id']);
    if ($p > 0 && $p < 75) $siswa_perhatian++;
}
?>
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6"><div class="stat-card"><div class="stat-value"><?= $total_siswa ?></div><div class="stat-label">Total Siswa PKL</div></div></div>
    <div class="col-md-3 col-6"><div class="stat-card"><div class="stat-value"><?= $total_perusahaan ?></div><div class="stat-label">Perusahaan</div></div></div>
    <div class="col-md-3 col-6"><div class="stat-card"><div class="stat-value"><?= $total_pembimbing ?></div><div class="stat-label">Pembimbing</div></div></div>
    <div class="col-md-3 col-6"><div class="stat-card success"><div class="stat-value"><?= $pkl_aktif ?></div><div class="stat-label">PKL Aktif</div></div></div>
    <div class="col-md-3 col-6"><div class="stat-card"><div class="stat-value"><?= $pkl_selesai ?></div><div class="stat-label">PKL Selesai</div></div></div>
    <div class="col-md-3 col-6"><div class="stat-card"><div class="stat-value"><?= $jurnal_hari_ini ?></div><div class="stat-label">Jurnal Hari Ini</div></div></div>
    <div class="col-md-3 col-6"><div class="stat-card warning"><div class="stat-value"><?= $siswa_perhatian ?></div><div class="stat-label">Siswa Perlu Perhatian</div></div></div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card-custom">
            <div class="card-header">Penempatan Terbaru</div>
            <div class="card-body p-0">
                <table class="table table-custom table-hover mb-0">
                    <thead><tr><th>Siswa</th><th>Perusahaan</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php
                    $q = $conn->query("SELECT s.nama siswa, p.nama_perusahaan, pe.status FROM penempatan pe JOIN siswa s ON pe.siswa_id=s.id JOIN perusahaan p ON pe.perusahaan_id=p.id ORDER BY pe.id DESC LIMIT 5");
                    while ($r = $q->fetch_assoc()):
                        $b = $r['status']=='Aktif'?'badge-aktif':($r['status']=='Selesai'?'badge-selesai':'badge-belum');
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($r['siswa']) ?></td>
                            <td><?= htmlspecialchars($r['nama_perusahaan']) ?></td>
                            <td><span class="badge-status <?= $b ?>"><?= $r['status'] ?></span></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card-custom">
            <div class="card-header">Monitoring Terbaru</div>
            <div class="card-body p-0">
                <table class="table table-custom table-hover mb-0">
                    <thead><tr><th>Siswa</th><th>Tanggal</th><th>Nilai</th></tr></thead>
                    <tbody>
                    <?php
                    $q = $conn->query("SELECT s.nama siswa, m.tanggal_monitoring, m.nilai FROM monitoring m JOIN siswa s ON m.siswa_id=s.id ORDER BY m.tanggal_monitoring DESC LIMIT 5");
                    while ($r = $q->fetch_assoc()):
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($r['siswa']) ?></td>
                            <td><?= date('d/m/Y', strtotime($r['tanggal_monitoring'])) ?></td>
                            <td><strong><?= $r['nilai'] ?></strong></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
