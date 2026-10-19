<?php
$page_title = 'Jurnal Kegiatan';
require_once __DIR__ . '/includes/header.php';
$conn = getConnection();
$siswa_id = (int)$user['related_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = $_POST['tanggal'] ?? '';
    $kegiatan = sanitize($_POST['kegiatan'] ?? '');
    $hasil = sanitize($_POST['hasil'] ?? '');
    $kendala = sanitize($_POST['kendala'] ?? '');
    if ($tanggal === '' || $kegiatan === '') {
        flash('error', 'Tanggal dan Kegiatan wajib diisi.');
    } else {
        $st = $conn->prepare('INSERT INTO jurnal (siswa_id,tanggal,kegiatan,hasil,kendala) VALUES (?,?,?,?,?)');
        $st->bind_param('issss', $siswa_id, $tanggal, $kegiatan, $hasil, $kendala);
        $st->execute() ? flash('success','Jurnal berhasil disimpan.') : flash('error','Gagal menyimpan.');
        $st->close();
    }
    redirect('siswa/jurnal.php');
}
$data = $conn->query("SELECT * FROM jurnal WHERE siswa_id=$siswa_id ORDER BY tanggal DESC");
?>
<div class="card-custom mb-3">
    <div class="card-header">Input Jurnal Baru</div>
    <div class="card-body">
        <form method="POST">
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label">Tanggal *</label><input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
                <div class="col-md-9"><label class="form-label">Kegiatan *</label><input type="text" name="kegiatan" class="form-control" placeholder="Contoh: Membuat desain database" required></div>
                <div class="col-md-6"><label class="form-label">Hasil</label><textarea name="hasil" class="form-control" rows="2" placeholder="Contoh: ERD selesai dibuat"></textarea></div>
                <div class="col-md-6"><label class="form-label">Kendala</label><textarea name="kendala" class="form-control" rows="2" placeholder="Contoh: Kesulitan menentukan relasi tabel"></textarea></div>
                <div class="col-12"><button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Jurnal</button></div>
            </div>
        </form>
    </div>
</div>
<div class="card-custom">
    <div class="card-header">Riwayat Jurnal</div>
    <div class="card-body p-0">
        <table class="table table-custom table-hover mb-0">
            <thead><tr><th>No</th><th>Tanggal</th><th>Kegiatan</th><th>Hasil</th><th>Kendala</th></tr></thead>
            <tbody>
            <?php $no=1; while($r=$data->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= date('d/m/Y', strtotime($r['tanggal'])) ?></td>
                    <td><?= htmlspecialchars($r['kegiatan']) ?></td>
                    <td><?= htmlspecialchars($r['hasil'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($r['kendala'] ?? '-') ?></td>
                </tr>
            <?php endwhile; ?>
            <?php if($no===1): ?><tr><td colspan="5" class="text-center text-muted py-4">Belum ada jurnal</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
