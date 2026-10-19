<?php
require_once __DIR__ . '/../config/auth.php';
requireAdmin();
$page_title = 'Laporan Monitoring';
require_once __DIR__ . '/../admin/includes/header.php';
$conn = getConnection();
$data = $conn->query("SELECT m.*, s.nis, s.nama siswa, pb.nama pembimbing FROM monitoring m JOIN siswa s ON m.siswa_id=s.id JOIN pembimbing pb ON m.pembimbing_id=pb.id ORDER BY m.tanggal_monitoring DESC");
?>
<div class="card-custom no-print mb-3">
    <div class="card-body d-flex justify-content-between">
        <h5 class="mb-0">Laporan Monitoring PKL</h5>
        <button onclick="window.print()" class="btn btn-outline-primary"><i class="bi bi-printer"></i> Cetak</button>
    </div>
</div>
<div class="card-custom"><div class="card-body">
    <div class="text-center mb-4 d-none d-print-block"><h4>LAPORAN MONITORING SISWA PKL</h4><p>InternTrack - SMK Hidayah Semarang | <?= date('d/m/Y') ?></p></div>
    <table class="table table-bordered table-sm">
        <thead class="table-light"><tr><th>No</th><th>Tanggal</th><th>NIS</th><th>Nama Siswa</th><th>Pembimbing</th><th>Nilai</th><th>Catatan</th><th>Rekomendasi</th></tr></thead>
        <tbody>
        <?php $no=1; while($r=$data->fetch_assoc()): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= date('d/m/Y', strtotime($r['tanggal_monitoring'])) ?></td>
                <td><?= $r['nis'] ?></td>
                <td><?= htmlspecialchars($r['siswa']) ?></td>
                <td><?= htmlspecialchars($r['pembimbing']) ?></td>
                <td><strong><?= $r['nilai'] ?></strong></td>
                <td><?= htmlspecialchars($r['catatan']) ?></td>
                <td><?= htmlspecialchars($r['rekomendasi'] ?? '-') ?></td>
            </tr>
        <?php endwhile; ?>
        <?php if($no===1): ?><tr><td colspan="8" class="text-center">Belum ada data</td></tr><?php endif; ?>
        </tbody>
    </table>
</div></div>
<?php require_once __DIR__ . '/../admin/includes/footer.php'; ?>
