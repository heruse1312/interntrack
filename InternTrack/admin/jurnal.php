<?php
$page_title = 'Jurnal Kegiatan';
require_once __DIR__ . '/includes/header.php';
$conn = getConnection();

$search = sanitize($_GET['search'] ?? '');
$filter_siswa = (int)($_GET['siswa'] ?? 0);
$where = '1=1';
if ($search) $where .= " AND (j.kegiatan LIKE '%$search%' OR s.nama LIKE '%$search%')";
if ($filter_siswa) $where .= " AND j.siswa_id=$filter_siswa";

$data = $conn->query("SELECT j.*, s.nama siswa, s.nis FROM jurnal j JOIN siswa s ON j.siswa_id=s.id WHERE $where ORDER BY j.tanggal DESC LIMIT 100");
$siswa_list = $conn->query('SELECT id,nis,nama FROM siswa ORDER BY nama');
?>
<div class="card-custom mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4"><input type="text" name="search" class="form-control" value="<?= htmlspecialchars($search) ?>" placeholder="Cari kegiatan / nama..."></div>
            <div class="col-md-3">
                <select name="siswa" class="form-select">
                    <option value="">Semua Siswa</option>
                    <?php while($s=$siswa_list->fetch_assoc()): ?>
                        <option value="<?= $s['id'] ?>" <?= $filter_siswa==$s['id']?'selected':'' ?>><?= $s['nis'] ?> - <?= htmlspecialchars($s['nama']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Cari</button>
                <a href="<?= base_url('admin/jurnal.php') ?>" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>
<div class="card-custom"><div class="card-body p-0"><div class="table-responsive">
    <table class="table table-custom table-hover mb-0">
        <thead><tr><th>No</th><th>Tanggal</th><th>Siswa</th><th>Kegiatan</th><th>Hasil</th><th>Kendala</th></tr></thead>
        <tbody>
        <?php $no=1; while($r=$data->fetch_assoc()): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= date('d/m/Y', strtotime($r['tanggal'])) ?></td>
                <td><?= htmlspecialchars($r['siswa']) ?><br><small class="text-muted"><?= $r['nis'] ?></small></td>
                <td><?= htmlspecialchars($r['kegiatan']) ?></td>
                <td><?= htmlspecialchars($r['hasil'] ?? '-') ?></td>
                <td><?= htmlspecialchars($r['kendala'] ?? '-') ?></td>
            </tr>
        <?php endwhile; ?>
        <?php if($no===1): ?><tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data jurnal</td></tr><?php endif; ?>
        </tbody>
    </table>
</div></div></div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
