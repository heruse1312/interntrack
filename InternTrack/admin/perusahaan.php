<?php
$page_title = 'Data Perusahaan';
require_once __DIR__ . '/includes/header.php';
$conn = getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'tambah' || $action === 'edit') {
        $nama = sanitize($_POST['nama_perusahaan'] ?? '');
        $alamat = sanitize($_POST['alamat'] ?? '');
        $bidang = sanitize($_POST['bidang'] ?? '');
        $kontak = sanitize($_POST['kontak'] ?? '');
        $id = (int)($_POST['id'] ?? 0);
        if ($nama === '' || $alamat === '' || $bidang === '') {
            flash('error', 'Nama, Alamat, dan Bidang wajib diisi.');
        } else {
            if ($action === 'tambah') {
                $st = $conn->prepare('INSERT INTO perusahaan (nama_perusahaan,alamat,bidang,kontak) VALUES (?,?,?,?)');
                $st->bind_param('ssss', $nama, $alamat, $bidang, $kontak);
                $st->execute() ? flash('success','Perusahaan ditambahkan.') : flash('error','Gagal.');
            } else {
                $st = $conn->prepare('UPDATE perusahaan SET nama_perusahaan=?,alamat=?,bidang=?,kontak=? WHERE id=?');
                $st->bind_param('ssssi', $nama, $alamat, $bidang, $kontak, $id);
                $st->execute() ? flash('success','Data diupdate.') : flash('error','Gagal.');
            }
            $st->close();
        }
        redirect('admin/perusahaan.php');
    }
    if ($action === 'hapus') {
        $conn->query('DELETE FROM perusahaan WHERE id='.(int)$_POST['id']);
        flash('success', 'Perusahaan dihapus.');
        redirect('admin/perusahaan.php');
    }
}

$search = sanitize($_GET['search'] ?? '');
$where = $search ? "WHERE nama_perusahaan LIKE '%$search%' OR bidang LIKE '%$search%'" : '';
$data = $conn->query("SELECT * FROM perusahaan $where ORDER BY nama_perusahaan");
?>
<div class="card-custom mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-6"><input type="text" name="search" class="form-control" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama / bidang..."></div>
            <div class="col-md-3"><button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Cari</button> <a href="<?= base_url('admin/perusahaan.php') ?>" class="btn btn-outline-secondary">Reset</a></div>
            <div class="col-md-3 text-end"><button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambah"><i class="bi bi-plus-lg"></i> Tambah</button></div>
        </form>
    </div>
</div>
<div class="card-custom">
    <div class="card-body p-0">
        <table class="table table-custom table-hover mb-0">
            <thead><tr><th>No</th><th>Nama Perusahaan</th><th>Bidang</th><th>Kontak</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php $no=1; while($r=$data->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($r['nama_perusahaan']) ?></td>
                    <td><?= htmlspecialchars($r['bidang']) ?></td>
                    <td><?= htmlspecialchars($r['kontak'] ?? '-') ?></td>
                    <td>
                        <button class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#d<?= $r['id'] ?>"><i class="bi bi-eye"></i></button>
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#e<?= $r['id'] ?>"><i class="bi bi-pencil"></i></button>
                        <form method="POST" class="d-inline" onsubmit="return confirm('Yakin?')"><input type="hidden" name="action" value="hapus"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button></form>
                    </td>
                </tr>
                <div class="modal fade" id="d<?= $r['id'] ?>" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
                    <div class="modal-header"><h5 class="modal-title">Detail</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <p><strong>Nama:</strong> <?= htmlspecialchars($r['nama_perusahaan']) ?></p>
                        <p><strong>Alamat:</strong> <?= htmlspecialchars($r['alamat']) ?></p>
                        <p><strong>Bidang:</strong> <?= htmlspecialchars($r['bidang']) ?></p>
                        <p><strong>Kontak:</strong> <?= htmlspecialchars($r['kontak'] ?? '-') ?></p>
                    </div>
                </div></div></div>
                <div class="modal fade" id="e<?= $r['id'] ?>" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
                    <form method="POST">
                        <div class="modal-header"><h5 class="modal-title">Edit</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                        <div class="modal-body">
                            <input type="hidden" name="action" value="edit"><input type="hidden" name="id" value="<?= $r['id'] ?>">
                            <div class="mb-3"><label class="form-label">Nama *</label><input type="text" name="nama_perusahaan" class="form-control" value="<?= htmlspecialchars($r['nama_perusahaan']) ?>" required></div>
                            <div class="mb-3"><label class="form-label">Alamat *</label><textarea name="alamat" class="form-control" required><?= htmlspecialchars($r['alamat']) ?></textarea></div>
                            <div class="mb-3"><label class="form-label">Bidang *</label><input type="text" name="bidang" class="form-control" value="<?= htmlspecialchars($r['bidang']) ?>" required></div>
                            <div class="mb-3"><label class="form-label">Kontak</label><input type="text" name="kontak" class="form-control" value="<?= htmlspecialchars($r['kontak'] ?? '') ?>"></div>
                        </div>
                        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
                    </form>
                </div></div></div>
            <?php endwhile; ?>
            <?php if($no===1): ?><tr><td colspan="5" class="text-center text-muted py-4">Tidak ada data</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="modalTambah" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <form method="POST">
        <div class="modal-header"><h5 class="modal-title">Tambah Perusahaan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <input type="hidden" name="action" value="tambah">
            <div class="mb-3"><label class="form-label">Nama *</label><input type="text" name="nama_perusahaan" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Alamat *</label><textarea name="alamat" class="form-control" required></textarea></div>
            <div class="mb-3"><label class="form-label">Bidang *</label><input type="text" name="bidang" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Kontak</label><input type="text" name="kontak" class="form-control"></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
    </form>
</div></div></div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
