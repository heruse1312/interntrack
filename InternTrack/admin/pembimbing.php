<?php
$page_title = 'Data Pembimbing';
require_once __DIR__ . '/includes/header.php';
$conn = getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'tambah' || $action === 'edit') {
        $nama = sanitize($_POST['nama'] ?? '');
        $nip = sanitize($_POST['nip'] ?? '');
        $no_hp = sanitize($_POST['no_hp'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $id = (int)($_POST['id'] ?? 0);
        if ($nama === '') {
            flash('error', 'Nama wajib diisi.');
        } else {
            if ($action === 'tambah') {
                $st = $conn->prepare('INSERT INTO pembimbing (nama,nip,no_hp,email) VALUES (?,?,?,?)');
                $st->bind_param('ssss', $nama, $nip, $no_hp, $email);
                $st->execute() ? flash('success','Pembimbing ditambahkan.') : flash('error','Gagal.');
            } else {
                $st = $conn->prepare('UPDATE pembimbing SET nama=?,nip=?,no_hp=?,email=? WHERE id=?');
                $st->bind_param('ssssi', $nama, $nip, $no_hp, $email, $id);
                $st->execute() ? flash('success','Data diupdate.') : flash('error','Gagal.');
            }
            $st->close();
        }
        redirect('admin/pembimbing.php');
    }
    if ($action === 'hapus') {
        $conn->query('DELETE FROM pembimbing WHERE id='.(int)$_POST['id']);
        flash('success', 'Data dihapus.');
        redirect('admin/pembimbing.php');
    }
}
$data = $conn->query('SELECT * FROM pembimbing ORDER BY nama');
?>
<div class="card-custom mb-3"><div class="card-body text-end">
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambah"><i class="bi bi-plus-lg"></i> Tambah Pembimbing</button>
</div></div>
<div class="card-custom"><div class="card-body p-0">
    <table class="table table-custom table-hover mb-0">
        <thead><tr><th>No</th><th>Nama</th><th>NIP</th><th>No. HP</th><th>Email</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php $no=1; while($r=$data->fetch_assoc()): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($r['nama']) ?></td>
                <td><?= htmlspecialchars($r['nip'] ?? '-') ?></td>
                <td><?= htmlspecialchars($r['no_hp'] ?? '-') ?></td>
                <td><?= htmlspecialchars($r['email'] ?? '-') ?></td>
                <td>
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#e<?= $r['id'] ?>"><i class="bi bi-pencil"></i></button>
                    <form method="POST" class="d-inline" onsubmit="return confirm('Yakin?')"><input type="hidden" name="action" value="hapus"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button></form>
                </td>
            </tr>
            <div class="modal fade" id="e<?= $r['id'] ?>" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
                <form method="POST">
                    <div class="modal-header"><h5 class="modal-title">Edit</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit"><input type="hidden" name="id" value="<?= $r['id'] ?>">
                        <div class="mb-3"><label class="form-label">Nama *</label><input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($r['nama']) ?>" required></div>
                        <div class="mb-3"><label class="form-label">NIP</label><input type="text" name="nip" class="form-control" value="<?= htmlspecialchars($r['nip'] ?? '') ?>"></div>
                        <div class="mb-3"><label class="form-label">No. HP</label><input type="text" name="no_hp" class="form-control" value="<?= htmlspecialchars($r['no_hp'] ?? '') ?>"></div>
                        <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($r['email'] ?? '') ?>"></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
                </form>
            </div></div></div>
        <?php endwhile; ?>
        </tbody>
    </table>
</div></div>
<div class="modal fade" id="modalTambah" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <form method="POST">
        <div class="modal-header"><h5 class="modal-title">Tambah Pembimbing</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <input type="hidden" name="action" value="tambah">
            <div class="mb-3"><label class="form-label">Nama *</label><input type="text" name="nama" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">NIP</label><input type="text" name="nip" class="form-control"></div>
            <div class="mb-3"><label class="form-label">No. HP</label><input type="text" name="no_hp" class="form-control"></div>
            <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control"></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
    </form>
</div></div></div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
