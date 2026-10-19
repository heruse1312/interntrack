<?php
$page_title = 'Monitoring PKL';
require_once __DIR__ . '/includes/header.php';
$conn = getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'tambah' || $action === 'edit') {
        $siswa_id = (int)$_POST['siswa_id'];
        $pembimbing_id = (int)$_POST['pembimbing_id'];
        $tanggal = $_POST['tanggal_monitoring'] ?? '';
        $catatan = sanitize($_POST['catatan'] ?? '');
        $nilai = (int)($_POST['nilai'] ?? 0);
        $rekomendasi = sanitize($_POST['rekomendasi'] ?? '');
        $id = (int)($_POST['id'] ?? 0);
        if (!$siswa_id || $tanggal === '' || $catatan === '') {
            flash('error', 'Siswa, Tanggal, dan Catatan wajib diisi.');
        } else {
            if ($action === 'tambah') {
                $st = $conn->prepare('INSERT INTO monitoring (siswa_id,pembimbing_id,tanggal_monitoring,catatan,nilai,rekomendasi) VALUES (?,?,?,?,?,?)');
                $st->bind_param('iissis', $siswa_id, $pembimbing_id, $tanggal, $catatan, $nilai, $rekomendasi);
                $st->execute() ? flash('success','Monitoring berhasil dicatat.') : flash('error','Gagal.');
            } else {
                $st = $conn->prepare('UPDATE monitoring SET siswa_id=?,pembimbing_id=?,tanggal_monitoring=?,catatan=?,nilai=?,rekomendasi=? WHERE id=?');
                $st->bind_param('iissisi', $siswa_id, $pembimbing_id, $tanggal, $catatan, $nilai, $rekomendasi, $id);
                $st->execute() ? flash('success','Data diupdate.') : flash('error','Gagal.');
            }
            $st->close();
        }
        redirect('admin/monitoring.php');
    }
    if ($action === 'hapus') {
        $conn->query('DELETE FROM monitoring WHERE id='.(int)$_POST['id']);
        flash('success', 'Data dihapus.');
        redirect('admin/monitoring.php');
    }
}

$data = $conn->query("SELECT m.*, s.nama siswa, s.nis, pb.nama pembimbing FROM monitoring m JOIN siswa s ON m.siswa_id=s.id JOIN pembimbing pb ON m.pembimbing_id=pb.id ORDER BY m.tanggal_monitoring DESC");
$siswa_list = $conn->query('SELECT id,nis,nama FROM siswa ORDER BY nama');
$pembimbing_list = $conn->query('SELECT id,nama FROM pembimbing ORDER BY nama');
?>
<div class="card-custom mb-3"><div class="card-body text-end">
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambah"><i class="bi bi-plus-lg"></i> Input Monitoring</button>
</div></div>
<div class="card-custom"><div class="card-body p-0"><div class="table-responsive">
    <table class="table table-custom table-hover mb-0">
        <thead><tr><th>No</th><th>Tanggal</th><th>Siswa</th><th>Pembimbing</th><th>Catatan</th><th>Nilai</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php $no=1; while($r=$data->fetch_assoc()): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= date('d/m/Y', strtotime($r['tanggal_monitoring'])) ?></td>
                <td><?= htmlspecialchars($r['siswa']) ?><br><small class="text-muted"><?= $r['nis'] ?></small></td>
                <td><?= htmlspecialchars($r['pembimbing']) ?></td>
                <td><?= htmlspecialchars(mb_substr($r['catatan'],0,50)) ?>...</td>
                <td><strong><?= $r['nilai'] ?></strong></td>
                <td>
                    <button class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#d<?= $r['id'] ?>"><i class="bi bi-eye"></i></button>
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#e<?= $r['id'] ?>"><i class="bi bi-pencil"></i></button>
                    <form method="POST" class="d-inline" onsubmit="return confirm('Yakin?')"><input type="hidden" name="action" value="hapus"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button></form>
                </td>
            </tr>
            <div class="modal fade" id="d<?= $r['id'] ?>" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Detail Monitoring</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <p><strong>Siswa:</strong> <?= htmlspecialchars($r['siswa']) ?></p>
                    <p><strong>Tanggal:</strong> <?= date('d/m/Y', strtotime($r['tanggal_monitoring'])) ?></p>
                    <p><strong>Pembimbing:</strong> <?= htmlspecialchars($r['pembimbing']) ?></p>
                    <p><strong>Catatan:</strong><br><?= nl2br(htmlspecialchars($r['catatan'])) ?></p>
                    <p><strong>Nilai:</strong> <?= $r['nilai'] ?></p>
                    <p><strong>Rekomendasi:</strong><br><?= nl2br(htmlspecialchars($r['rekomendasi'] ?? '-')) ?></p>
                </div>
            </div></div></div>
            <div class="modal fade" id="e<?= $r['id'] ?>" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
                <form method="POST">
                    <div class="modal-header"><h5 class="modal-title">Edit</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit"><input type="hidden" name="id" value="<?= $r['id'] ?>">
                        <div class="mb-3"><label class="form-label">Siswa</label><select name="siswa_id" class="form-select" required>
                            <?php $siswa_list->data_seek(0); while($s=$siswa_list->fetch_assoc()): ?><option value="<?= $s['id'] ?>" <?= $r['siswa_id']==$s['id']?'selected':'' ?>><?= $s['nis'] ?> - <?= htmlspecialchars($s['nama']) ?></option><?php endwhile; ?>
                        </select></div>
                        <div class="mb-3"><label class="form-label">Pembimbing</label><select name="pembimbing_id" class="form-select" required>
                            <?php $pembimbing_list->data_seek(0); while($pb=$pembimbing_list->fetch_assoc()): ?><option value="<?= $pb['id'] ?>" <?= $r['pembimbing_id']==$pb['id']?'selected':'' ?>><?= htmlspecialchars($pb['nama']) ?></option><?php endwhile; ?>
                        </select></div>
                        <div class="mb-3"><label class="form-label">Tanggal</label><input type="date" name="tanggal_monitoring" class="form-control" value="<?= $r['tanggal_monitoring'] ?>" required></div>
                        <div class="mb-3"><label class="form-label">Catatan</label><textarea name="catatan" class="form-control" rows="3" required><?= htmlspecialchars($r['catatan']) ?></textarea></div>
                        <div class="mb-3"><label class="form-label">Nilai</label><input type="number" name="nilai" class="form-control" min="0" max="100" value="<?= $r['nilai'] ?>"></div>
                        <div class="mb-3"><label class="form-label">Rekomendasi</label><textarea name="rekomendasi" class="form-control" rows="2"><?= htmlspecialchars($r['rekomendasi'] ?? '') ?></textarea></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
                </form>
            </div></div></div>
        <?php endwhile; ?>
        <?php if($no===1): ?><tr><td colspan="7" class="text-center text-muted py-4">Belum ada data monitoring</td></tr><?php endif; ?>
        </tbody>
    </table>
</div></div></div>
<div class="modal fade" id="modalTambah" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <form method="POST">
        <div class="modal-header"><h5 class="modal-title">Input Monitoring</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <input type="hidden" name="action" value="tambah">
            <div class="mb-3"><label class="form-label">Siswa *</label><select name="siswa_id" class="form-select" required><option value="">-- Pilih --</option>
                <?php $siswa_list->data_seek(0); while($s=$siswa_list->fetch_assoc()): ?><option value="<?= $s['id'] ?>"><?= $s['nis'] ?> - <?= htmlspecialchars($s['nama']) ?></option><?php endwhile; ?>
            </select></div>
            <div class="mb-3"><label class="form-label">Pembimbing *</label><select name="pembimbing_id" class="form-select" required><option value="">-- Pilih --</option>
                <?php $pembimbing_list->data_seek(0); while($pb=$pembimbing_list->fetch_assoc()): ?><option value="<?= $pb['id'] ?>"><?= htmlspecialchars($pb['nama']) ?></option><?php endwhile; ?>
            </select></div>
            <div class="mb-3"><label class="form-label">Tanggal *</label><input type="date" name="tanggal_monitoring" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
            <div class="mb-3"><label class="form-label">Catatan *</label><textarea name="catatan" class="form-control" rows="3" required></textarea></div>
            <div class="mb-3"><label class="form-label">Nilai (0-100)</label><input type="number" name="nilai" class="form-control" min="0" max="100" value="80"></div>
            <div class="mb-3"><label class="form-label">Rekomendasi</label><textarea name="rekomendasi" class="form-control" rows="2"></textarea></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
    </form>
</div></div></div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
