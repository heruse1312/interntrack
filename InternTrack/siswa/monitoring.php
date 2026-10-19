<?php
$page_title = 'Hasil Monitoring';
require_once __DIR__ . '/includes/header.php';
$conn = getConnection();
$siswa_id = (int)$user['related_id'];
$data = $conn->query("SELECT m.*, pb.nama pembimbing FROM monitoring m JOIN pembimbing pb ON m.pembimbing_id=pb.id WHERE m.siswa_id=$siswa_id ORDER BY m.tanggal_monitoring DESC");
?>
<div class="card-custom">
    <div class="card-header">Hasil Monitoring dari Pembimbing</div>
    <div class="card-body p-0">
        <?php if ($data->num_rows == 0): ?>
            <p class="text-center text-muted py-4 mb-0">Belum ada hasil monitoring.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-custom table-hover mb-0">
                    <thead><tr><th>No</th><th>Tanggal</th><th>Pembimbing</th><th>Catatan</th><th>Nilai</th><th>Rekomendasi</th></tr></thead>
                    <tbody>
                    <?php $no=1; while($r=$data->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= date('d/m/Y', strtotime($r['tanggal_monitoring'])) ?></td>
                            <td><?= htmlspecialchars($r['pembimbing']) ?></td>
                            <td><?= htmlspecialchars($r['catatan']) ?></td>
                            <td><strong class="text-primary"><?= $r['nilai'] ?></strong></td>
                            <td><?= htmlspecialchars($r['rekomendasi'] ?? '-') ?></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
