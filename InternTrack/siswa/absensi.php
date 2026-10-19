<?php
$page_title = 'Absensi';
require_once __DIR__ . '/includes/header.php';
$conn = getConnection();
$siswa_id = (int)$user['related_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = $_POST['tanggal'] ?? '';
    $status = $_POST['status'] ?? '';
    $keterangan = sanitize($_POST['keterangan'] ?? '');
    if ($tanggal === '' || $status === '') {
        flash('error', 'Tanggal dan Status wajib diisi.');
    } else {
        $cek = $conn->prepare('SELECT id FROM absensi WHERE siswa_id=? AND tanggal=?');
        $cek->bind_param('is', $siswa_id, $tanggal);
        $cek->execute();
        if ($cek->get_result()->num_rows > 0) {
            flash('error', 'Anda sudah mengisi absensi untuk tanggal tersebut.');
        } else {
            $st = $conn->prepare('INSERT INTO absensi (siswa_id,tanggal,status,keterangan) VALUES (?,?,?,?)');
            $st->bind_param('isss', $siswa_id, $tanggal, $status, $keterangan);
            $st->execute() ? flash('success','Absensi berhasil disimpan.') : flash('error','Gagal.');
            $st->close();
        }
        $cek->close();
    }
    redirect('siswa/absensi.php');
}
$data = $conn->query("SELECT * FROM absensi WHERE siswa_id=$siswa_id ORDER BY tanggal DESC");
$persen = getPersentaseKehadiran($siswa_id);
$status_kehadiran = hitungStatusKehadiran($persen);
$badge = $persen>=90?'badge-sangatbaik':($persen>=75?'badge-baik':($persen>=60?'badge-perhatian':'badge-pembinaan'));
?>
<div class="row g-3 mb-4">
    <div class="col-md-6"><div class="stat-card success"><div class="stat-value"><?= $persen ?>%</div><div class="stat-label">Persentase Kehadiran</div></div></div>
    <div class="col-md-6"><div class="stat-card"><div class="stat-value"><span class="badge-status <?= $badge ?>" style="font-size:1rem;"><?= $status_kehadiran ?></span></div><div class="stat-label">Status</div></div></div>
</div>
<div class="card-custom mb-3">
    <div class="card-header">Isi Absensi</div>
    <div class="card-body">
        <form method="POST" class="row g-3">
            <div class="col-md-3"><label class="form-label">Tanggal *</label><input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
            <div class="col-md-3"><label class="form-label">Status *</label>
                <select name="status" class="form-select" required>
                    <option value="">-- Pilih --</option>
                    <option value="Hadir">Hadir</option>
                    <option value="Izin">Izin</option>
                    <option value="Sakit">Sakit</option>
                    <option value="Alpa">Alpa</option>
                </select>
            </div>
            <div class="col-md-4"><label class="form-label">Keterangan</label><input type="text" name="keterangan" class="form-control" placeholder="Opsional"></div>
            <div class="col-md-2 d-flex align-items-end"><button type="submit" class="btn btn-primary w-100"><i class="bi bi-check-lg"></i> Simpan</button></div>
        </form>
    </div>
</div>
<div class="card-custom">
    <div class="card-header">Riwayat Absensi</div>
    <div class="card-body p-0">
        <table class="table table-custom table-hover mb-0">
            <thead><tr><th>No</th><th>Tanggal</th><th>Status</th><th>Keterangan</th></tr></thead>
            <tbody>
            <?php $no=1; while($r=$data->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= date('d/m/Y', strtotime($r['tanggal'])) ?></td>
                    <td><span class="badge bg-<?= $r['status']=='Hadir'?'success':($r['status']=='Alpa'?'danger':'warning') ?>"><?= $r['status'] ?></span></td>
                    <td><?= htmlspecialchars($r['keterangan'] ?? '-') ?></td>
                </tr>
            <?php endwhile; ?>
            <?php if($no===1): ?><tr><td colspan="4" class="text-center text-muted py-4">Belum ada absensi</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
