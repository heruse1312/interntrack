<?php
require_once __DIR__ . '/../config/auth.php';
requireAdmin();
$page_title = 'Laporan Data Siswa PKL';
require_once __DIR__ . '/../admin/includes/header.php';
$conn = getConnection();
$data = $conn->query("SELECT s.*, pe.status status_pkl, p.nama_perusahaan, pb.nama pembimbing
    FROM siswa s LEFT JOIN penempatan pe ON s.id=pe.siswa_id
    LEFT JOIN perusahaan p ON pe.perusahaan_id=p.id LEFT JOIN pembimbing pb ON pe.pembimbing_id=pb.id
    ORDER BY s.nama");
?>
<div class="card-custom no-print mb-3">
    <div class="card-body d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Laporan Data Siswa PKL</h5>
        <button onclick="window.print()" class="btn btn-outline-primary"><i class="bi bi-printer"></i> Cetak</button>
    </div>
</div>
<div class="card-custom"><div class="card-body">
    <div class="text-center mb-4 d-none d-print-block"><h4>LAPORAN DATA SISWA PKL</h4><p>InternTrack - SMK Hidayah Semarang</p><p>Tanggal: <?= date('d/m/Y') ?></p></div>
    <table class="table table-bordered table-sm">
        <thead class="table-light"><tr><th>No</th><th>NIS</th><th>Nama</th><th>Kelas</th><th>Perusahaan</th><th>Pembimbing</th><th>Status PKL</th><th>% Hadir</th><th>Status Hadir</th></tr></thead>
        <tbody>
        <?php $no=1; while($r=$data->fetch_assoc()):
            $persen=getPersentaseKehadiran($r['id']); $st=hitungStatusKehadiran($persen);
        ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($r['nis']) ?></td>
                <td><?= htmlspecialchars($r['nama']) ?></td>
                <td><?= htmlspecialchars($r['kelas']) ?></td>
                <td><?= htmlspecialchars($r['nama_perusahaan'] ?? '-') ?></td>
                <td><?= htmlspecialchars($r['pembimbing'] ?? '-') ?></td>
                <td><?= $r['status_pkl'] ?? '-' ?></td>
                <td><?= $persen ?>%</td>
                <td><?= $st ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div></div>
<?php require_once __DIR__ . '/../admin/includes/footer.php'; ?>
