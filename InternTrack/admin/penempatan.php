<?php
$page_title = 'Penempatan PKL';
require_once __DIR__ . '/includes/header.php';
$conn = getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'tambah' || $action === 'edit') {
        $siswa_id = (int)$_POST['siswa_id'];
        $perusahaan_id = (int)$_POST['perusahaan_id'];
        $pembimbing_id = (int)$_POST['pembimbing_id'];
        $tgl_mulai = $_POST['tanggal_mulai'] ?? '';
        $tgl_selesai = $_POST['tanggal_selesai'] ?? '';
        $status = $_POST['status'] ?? 'Belum Mulai';
        $id = (int)($_POST['id'] ?? 0);
        if (!$siswa_id || !$perusahaan_id || !$pembimbing_id || $tgl_mulai === '' || $tgl_selesai === '') {
            flash('error', 'Semua field wajib diisi.');
        } else {
            if ($action === 'tambah') {
                $st = $conn->prepare('INSERT INTO penempatan (siswa_id,perusahaan_id,pembimbing_id,tanggal_mulai,tanggal_selesai,status) VALUES (?,?,?,?,?,?)');
                $st->bind_param('iiisss', $siswa_id, $perusahaan_id, $pembimbing_id, $tgl_mulai, $tgl_selesai, $status);
                $st->execute() ? flash('success','Penempatan berhasil.') : flash('error','Gagal.');
            } else {
                $st = $conn->prepare('UPDATE penempatan SET siswa_id=?,perusahaan_id=?,pembimbing_id=?,tanggal_mulai=?,tanggal_selesai=?,status=? WHERE id=?');
                $st->bind_param('iiisssi', $siswa_id, $perusahaan_id, $pembimbing_id, $tgl_mulai, $tgl_selesai, $status, $id);
                $st->execute() ? flash('success','Data diupdate.') : flash('error','Gagal.');
            }
            $st->close();
        }
        redirect('admin/penempatan.php');
    }
    if ($action === 'hapus') {
        $conn->query('DELETE FROM penempatan WHERE id='.(int)$_POST['id']);
        flash('success', 'Penempatan dihapus.');
        redirect('admin/penempatan.php');
    }
    if ($action === 'ubah_status') {
        $id = (int)$_POST['id'];
        $status = sanitize($_POST['status']);
        $st = $conn->prepare('UPDATE penempatan SET status=? WHERE id=?');
        $st->bind_param('si', $status, $id);
        $st->execute();
        flash('success', 'Status diubah menjadi '.$status);
        redirect('admin/penempatan.php');
    }
}

$filter_status = sanitize($_GET['status'] ?? '');
$filter_perusahaan = (int)($_GET['perusahaan'] ?? 0);
$where = '1=1';
if ($filter_status) $where .= " AND pe.status='$filter_status'";
if ($filter_perusahaan) $where .= " AND pe.perusahaan_id=$filter_perusahaan";

$data = $conn->query("SELECT pe.*, s.nama siswa, s.nis, p.nama_perusahaan, pb.nama pembimbing
    FROM penempatan pe JOIN siswa s ON pe.siswa_id=s.id JOIN perusahaan p ON pe.perusahaan_id=p.id JOIN pembimbing pb ON pe.pembimbing_id=pb.id
    WHERE $where ORDER BY pe.id DESC");
$siswa_list = $conn->query('SELECT id,nis,nama FROM siswa ORDER BY nama');
$perusahaan_list = $conn->query('SELECT id,nama_perusahaan FROM perusahaan ORDER BY nama_perusahaan');
$pembimbing_list = $conn->query('SELECT id,nama FROM pembimbing ORDER BY nama');
?>
<div class="card-custom mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Filter Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua</option>
                    <?php foreach (['Belum Mulai','Aktif','Selesai'] as $st): ?>
                        <option value="<?= $st ?>" <?= $filter_status===$st?'selected':'' ?>><?= $st ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Filter Perusahaan</label>
                <select name="perusahaan" class="form-select">
                    <option value="">Semua</option>
                    <?php $perusahaan_list->data_seek(0); while($p=$perusahaan_list->fetch_assoc()): ?>
                        <option value="<?= $p['id'] ?>" <?= $filter_perusahaan==$p['id']?'selected':'' ?>><?= htmlspecialchars($p['nama_perusahaan']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Filter</button>
                <a href="<?= base_url('admin/penempatan.php') ?>" class="btn btn-outline-secondary">Reset</a>
            </div>
            <div class="col-md-3 text-end">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambah"><i class="bi bi-plus-lg"></i> Tambah</button>
            </div>
        </form>
    </div>
</div>
<div class="card-custom"><div class="card-body p-0"><div class="table-responsive">
    <table class="table table-custom table-hover mb-0">
        <thead><tr><th>No</th><th>Siswa</th><th>Perusahaan</th><th>Pembimbing</th><th>Periode</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php $no=1; while($r=$data->fetch_assoc()):
            $b = $r['status']=='Aktif'?'badge-aktif':($r['status']=='Selesai'?'badge-selesai':'badge-belum');
        ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($r['siswa']) ?><br><small class="text-muted"><?= $r['nis'] ?></small></td>
                <td><?= htmlspecialchars($r['nama_perusahaan']) ?></td>
                <td><?= htmlspecialchars($r['pembimbing']) ?></td>
                <td><?= date('d/m/Y',strtotime($r['tanggal_mulai'])) ?> - <?= date('d/m/Y',strtotime($r['tanggal_selesai'])) ?></td>
                <td><span class="badge-status <?= $b ?>"><?= $r['status'] ?></span></td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">Status</button>
                        <ul class="dropdown-menu">
                            <?php foreach(['Belum Mulai','Aktif','Selesai'] as $st): ?>
                            <li><form method="POST"><input type="hidden" name="action" value="ubah_status"><input type="hidden" name="id" value="<?= $r['id'] ?>"><input type="hidden" name="status" value="<?= $st ?>"><button type="submit" class="dropdown-item"><?= $st ?></button></form></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#e<?= $r['id'] ?>"><i class="bi bi-pencil"></i></button>
                    <form method="POST" class="d-inline" onsubmit="return confirm('Yakin?')"><input type="hidden" name="action" value="hapus"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button></form>
                </td>
            </tr>
            <div class="modal fade" id="e<?= $r['id'] ?>" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
                <form method="POST">
                    <div class="modal-header"><h5 class="modal-title">Edit Penempatan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit"><input type="hidden" name="id" value="<?= $r['id'] ?>">
                        <div class="mb-3"><label class="form-label">Siswa</label><select name="siswa_id" class="form-select" required>
                            <?php $siswa_list->data_seek(0); while($s=$siswa_list->fetch_assoc()): ?><option value="<?= $s['id'] ?>" <?= $r['siswa_id']==$s['id']?'selected':'' ?>><?= $s['nis'] ?> - <?= htmlspecialchars($s['nama']) ?></option><?php endwhile; ?>
                        </select></div>
                        <div class="mb-3"><label class="form-label">Perusahaan</label><select name="perusahaan_id" class="form-select" required>
                            <?php $perusahaan_list->data_seek(0); while($p=$perusahaan_list->fetch_assoc()): ?><option value="<?= $p['id'] ?>" <?= $r['perusahaan_id']==$p['id']?'selected':'' ?>><?= htmlspecialchars($p['nama_perusahaan']) ?></option><?php endwhile; ?>
                        </select></div>
                        <div class="mb-3"><label class="form-label">Pembimbing</label><select name="pembimbing_id" class="form-select" required>
                            <?php $pembimbing_list->data_seek(0); while($pb=$pembimbing_list->fetch_assoc()): ?><option value="<?= $pb['id'] ?>" <?= $r['pembimbing_id']==$pb['id']?'selected':'' ?>><?= htmlspecialchars($pb['nama']) ?></option><?php endwhile; ?>
                        </select></div>
                        <div class="row"><div class="col-6 mb-3"><label class="form-label">Tgl Mulai</label><input type="date" name="tanggal_mulai" class="form-control" value="<?= $r['tanggal_mulai'] ?>" required></div>
                        <div class="col-6 mb-3"><label class="form-label">Tgl Selesai</label><input type="date" name="tanggal_selesai" class="form-control" value="<?= $r['tanggal_selesai'] ?>" required></div></div>
                        <div class="mb-3"><label class="form-label">Status</label><select name="status" class="form-select">
                            <?php foreach(['Belum Mulai','Aktif','Selesai'] as $st): ?><option value="<?= $st ?>" <?= $r['status']==$st?'selected':'' ?>><?= $st ?></option><?php endforeach; ?>
                        </select></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
                </form>
            </div></div></div>
        <?php endwhile; ?>
        <?php if($no===1): ?><tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data</td></tr><?php endif; ?>
        </tbody>
    </table>
</div></div></div>

<div class="modal fade" id="modalTambah" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <form method="POST">
        <div class="modal-header"><h5 class="modal-title">Tambah Penempatan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <input type="hidden" name="action" value="tambah">
            <div class="mb-3"><label class="form-label">Siswa *</label><select name="siswa_id" class="form-select" required><option value="">-- Pilih --</option>
                <?php $siswa_list->data_seek(0); while($s=$siswa_list->fetch_assoc()): ?><option value="<?= $s['id'] ?>"><?= $s['nis'] ?> - <?= htmlspecialchars($s['nama']) ?></option><?php endwhile; ?>
            </select></div>
            <div class="mb-3"><label class="form-label">Perusahaan *</label><select name="perusahaan_id" class="form-select" required><option value="">-- Pilih --</option>
                <?php $perusahaan_list->data_seek(0); while($p=$perusahaan_list->fetch_assoc()): ?><option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nama_perusahaan']) ?></option><?php endwhile; ?>
            </select></div>
            <div class="mb-3"><label class="form-label">Pembimbing *</label><select name="pembimbing_id" class="form-select" required><option value="">-- Pilih --</option>
                <?php $pembimbing_list->data_seek(0); while($pb=$pembimbing_list->fetch_assoc()): ?><option value="<?= $pb['id'] ?>"><?= htmlspecialchars($pb['nama']) ?></option><?php endwhile; ?>
            </select></div>
            <div class="row"><div class="col-6 mb-3"><label class="form-label">Tgl Mulai *</label><input type="date" name="tanggal_mulai" class="form-control" required></div>
            <div class="col-6 mb-3"><label class="form-label">Tgl Selesai *</label><input type="date" name="tanggal_selesai" class="form-control" required></div></div>
            <div class="mb-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="Belum Mulai">Belum Mulai</option><option value="Aktif" selected>Aktif</option><option value="Selesai">Selesai</option></select></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
    </form>
</div></div></div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
