<?php
require_once __DIR__ . '/../config/auth.php';
requireAdmin();
$page_title = 'Laporan Kehadiran';
require_once __DIR__ . '/../admin/includes/header.php';
$conn = getConnection();
$data = $conn->query("SELECT s.id, s.nis, s.nama, s.kelas,
    COUNT(a.id) total, SUM(CASE WHEN a.status='Hadir' THEN 1 ELSE 0 END) hadir,
    SUM(CASE WHEN a.status='Izin' THEN 1 ELSE 0 END) izin,
    SUM(CASE WHEN a.status='Sakit' THEN 1 ELSE 0 END) sakit,
    SUM(CASE WHEN a.status='Alpa' THEN 1 ELSE 0 END) alpa
    FROM siswa s LEFT JOIN absensi a ON s.id=a.siswa_id GROUP BY s.id ORDER BY s.nama");
?>
<div class="card-custom no-print mb-3">
    <div class="card-body d-flex justify-content-between">
        <h5 class="mb-0">Laporan Kehadiran Siswa PKL</h5>
        <button onclick="window.print()" class="btn btn-outline-primary"><i class="bi bi-printer"></i> Cetak</button>
    </div>
</div>
<div class="card-custom"><div class="card-body">
    <div class="text-center mb-4 d-none d-print-block"><h4>LAPORAN KEHADIRAN SISWA PKL</h4><p>InternTrack - SMK Hidayah Semarang | <?= date('d/m/Y') ?></p></div>
    <table class="table table-bordered table-sm">
        <thead class="table-light"><tr><th>No</th><th>NIS</th><th>Nama</th><th>Kelas</th><th>Total</th><th>Hadir</th><th>Izin</th><th>Sakit</th><th>Alpa</th><th>%</th><th>Status</th></tr></thead>
        <tbody>
        <?php $no=1; while($r=$data->fetch_assoc()):
            $persen=$r['total']>0?round(($r['hadir']/$r['total'])*100,1):0; $st=hitungStatusKehadiran($persen);
        ?>
            <tr>
                <td><?= $no++ ?></td><td><?= $r['nis'] ?></td><td><?= htmlspecialchars($r['nama']) ?></td><td><?= $r['kelas'] ?></td>
                <td><?= $r['total'] ?></td><td><?= $r['hadir'] ?></td><td><?= $r['izin'] ?></td><td><?= $r['sakit'] ?></td><td><?= $r['alpa'] ?></td>
                <td><strong><?= $persen ?>%</strong></td><td><?= $st ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <div class="mt-3 small text-muted"><strong>Keterangan:</strong> ≥90% Sangat Baik | 75-89% Baik | 60-74% Perlu Perhatian | &lt;60% Perlu Pembinaan</div>
</div></div>
<?php require_once __DIR__ . '/../admin/includes/footer.php'; ?>
