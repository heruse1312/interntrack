<?php
$page_title = 'Data Absensi';
require_once __DIR__ . '/includes/header.php';
$conn = getConnection();

$filter_siswa = (int)($_GET['siswa'] ?? 0);
$where = $filter_siswa ? "WHERE a.siswa_id=$filter_siswa" : '';
$data = $conn->query("SELECT a.*, s.nama siswa, s.nis FROM absensi a JOIN siswa s ON a.siswa_id=s.id $where ORDER BY a.tanggal DESC LIMIT 100");

$rekap = $conn->query("SELECT s.id, s.nama, s.nis,
    COUNT(a.id) total,
    SUM(CASE WHEN a.status='Hadir' THEN 1 ELSE 0 END) hadir,
    SUM(CASE WHEN a.status='Izin' THEN 1 ELSE 0 END) izin,
    SUM(CASE WHEN a.status='Sakit' THEN 1 ELSE 0 END) sakit,
    SUM(CASE WHEN a.status='Alpa' THEN 1 ELSE 0 END) alpa
    FROM siswa s LEFT JOIN absensi a ON s.id=a.siswa_id GROUP BY s.id ORDER BY s.nama");
$siswa_list = $conn->query('SELECT id,nis,nama FROM siswa ORDER BY nama');
?>
<ul class="nav nav-tabs mb-3">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#rekap">Rekap Kehadiran</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#detail">Detail Absensi</a></li>
</ul>
<div class="tab-content">
    <div class="tab-pane fade show active" id="rekap">
        <div class="card-custom"><div class="card-body p-0">
            <table class="table table-custom table-hover mb-0">
                <thead><tr><th>No</th><th>Siswa</th><th>Total</th><th>Hadir</th><th>Izin</th><th>Sakit</th><th>Alpa</th><th>%</th><th>Status</th></tr></thead>
                <tbody>
                <?php $no=1; while($r=$rekap->fetch_assoc()):
                    $persen = $r['total']>0 ? round(($r['hadir']/$r['total'])*100,1) : 0;
                    $status = hitungStatusKehadiran($persen);
                    $badge = $persen>=90?'badge-sangatbaik':($persen>=75?'badge-baik':($persen>=60?'badge-perhatian':'badge-pembinaan'));
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($r['nama']) ?><br><small class="text-muted"><?= $r['nis'] ?></small></td>
                        <td><?= $r['total'] ?></td><td><?= $r['hadir'] ?></td><td><?= $r['izin'] ?></td><td><?= $r['sakit'] ?></td><td><?= $r['alpa'] ?></td>
                        <td><strong><?= $persen ?>%</strong></td>
                        <td><span class="badge-status <?= $badge ?>"><?= $status ?></span></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div></div>
    </div>
    <div class="tab-pane fade" id="detail">
        <div class="card-custom mb-3"><div class="card-body">
            <form method="GET" class="row g-2">
                <div class="col-md-4">
                    <select name="siswa" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Siswa</option>
                        <?php while($s=$siswa_list->fetch_assoc()): ?>
                            <option value="<?= $s['id'] ?>" <?= $filter_siswa==$s['id']?'selected':'' ?>><?= $s['nis'] ?> - <?= htmlspecialchars($s['nama']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </form>
        </div></div>
        <div class="card-custom"><div class="card-body p-0">
            <table class="table table-custom table-hover mb-0">
                <thead><tr><th>No</th><th>Tanggal</th><th>Siswa</th><th>Status</th><th>Keterangan</th></tr></thead>
                <tbody>
                <?php $no=1; while($r=$data->fetch_assoc()): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= date('d/m/Y', strtotime($r['tanggal'])) ?></td>
                        <td><?= htmlspecialchars($r['siswa']) ?></td>
                        <td><span class="badge bg-<?= $r['status']=='Hadir'?'success':($r['status']=='Alpa'?'danger':'warning') ?>"><?= $r['status'] ?></span></td>
                        <td><?= htmlspecialchars($r['keterangan'] ?? '-') ?></td>
                    </tr>
                <?php endwhile; ?>
                <?php if($no===1): ?><tr><td colspan="5" class="text-center text-muted py-4">Tidak ada data</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div></div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
