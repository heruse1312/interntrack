<?php
$page_title = 'Data Siswa';
require_once __DIR__ . '/includes/header.php';
$conn = getConnection();

// ===================== PROSES CRUD =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // --- TAMBAH / EDIT ---
    if ($action === 'tambah' || $action === 'edit') {
        $nis     = sanitize($_POST['nis'] ?? '');
        $nama    = sanitize($_POST['nama'] ?? '');
        $kelas   = sanitize($_POST['kelas'] ?? '');
        $jurusan = sanitize($_POST['jurusan'] ?? '');
        $no_hp   = sanitize($_POST['no_hp'] ?? '');
        $alamat  = sanitize($_POST['alamat'] ?? '');
        $id      = (int)($_POST['id'] ?? 0);

        if ($nis === '' || $nama === '') {
            flash('error', 'NIS dan Nama wajib diisi.');
        } else {
            // Cek duplikat NIS
            $chk = $conn->prepare('SELECT id FROM siswa WHERE nis = ? AND id != ?');
            $chk->bind_param('si', $nis, $id);
            $chk->execute();
            if ($chk->get_result()->num_rows > 0) {
                flash('error', 'NIS sudah terdaftar (duplikat).');
            } else {
                if ($action === 'tambah') {
                    $st = $conn->prepare('INSERT INTO siswa (nis, nama, kelas, jurusan, no_hp, alamat) VALUES (?,?,?,?,?,?)');
                    $st->bind_param('ssssss', $nis, $nama, $kelas, $jurusan, $no_hp, $alamat);
                    if ($st->execute()) {
                        $newId = $conn->insert_id;
                        // Buat akun login otomatis (username = NIS, password = password123)
                        $hash = password_hash('password123', PASSWORD_DEFAULT);
                        $role = 'siswa';
                        $u = $conn->prepare('INSERT INTO users (username, password, role, related_id) VALUES (?,?,?,?)');
                        $u->bind_param('sssi', $nis, $hash, $role, $newId);
                        $u->execute();
                        $u->close();
                        flash('success', 'Data siswa berhasil ditambahkan. Username login: ' . $nis . ' / password123');
                    } else {
                        flash('error', 'Gagal menambah data.');
                    }
                    $st->close();
                } else {
                    // Ambil NIS lama untuk update username
                    $old = $conn->query("SELECT nis FROM siswa WHERE id = $id")->fetch_assoc();
                    $oldNis = $old['nis'] ?? '';

                    $st = $conn->prepare('UPDATE siswa SET nis=?, nama=?, kelas=?, jurusan=?, no_hp=?, alamat=? WHERE id=?');
                    $st->bind_param('ssssssi', $nis, $nama, $kelas, $jurusan, $no_hp, $alamat, $id);
                    if ($st->execute()) {
                        // Update username di tabel users jika NIS berubah
                        if ($oldNis !== $nis) {
                            $uu = $conn->prepare("UPDATE users SET username = ? WHERE related_id = ? AND role = 'siswa'");
                            $uu->bind_param('si', $nis, $id);
                            $uu->execute();
                            $uu->close();
                        }
                        flash('success', 'Data siswa berhasil diupdate.');
                    } else {
                        flash('error', 'Gagal update data.');
                    }
                    $st->close();
                }
            }
            $chk->close();
        }
        redirect('admin/siswa.php');
    }

    // --- HAPUS ---
    if ($action === 'hapus') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $conn->query("DELETE FROM users WHERE related_id = $id AND role = 'siswa'");
            $conn->query("DELETE FROM siswa WHERE id = $id");
            flash('success', 'Data siswa berhasil dihapus.');
        }
        redirect('admin/siswa.php');
    }
}

// ===================== FILTER & SEARCH =====================
$search       = sanitize($_GET['search'] ?? '');
$filter_kelas = sanitize($_GET['kelas'] ?? '');
$where        = '1=1';
$params       = [];
$types        = '';

if ($search !== '') {
    $where   .= " AND (nama LIKE ? OR nis LIKE ?)";
    $like     = '%' . $search . '%';
    $params[] = $like;
    $params[] = $like;
    $types   .= 'ss';
}
if ($filter_kelas !== '') {
    $where   .= " AND kelas = ?";
    $params[] = $filter_kelas;
    $types   .= 's';
}

$sql = "SELECT * FROM siswa WHERE $where ORDER BY nama ASC";
if ($params) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $siswa = $stmt->get_result();
} else {
    $siswa = $conn->query($sql);
}

$kelas_list = $conn->query('SELECT DISTINCT kelas FROM siswa ORDER BY kelas');

// Simpan data ke array agar bisa dipakai 2x (tabel + modal)
$rows = [];
while ($r = $siswa->fetch_assoc()) {
    $rows[] = $r;
}
$total = count($rows);
?>

<!-- Filter & Tombol Tambah -->
<div class="card-custom mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Cari (Nama / NIS)</label>
                <input type="text" name="search" class="form-control" value="<?= htmlspecialchars($search) ?>" placeholder="Ketik nama atau NIS...">
            </div>
            <div class="col-md-3">
                <label class="form-label">Filter Kelas</label>
                <select name="kelas" class="form-select">
                    <option value="">Semua Kelas</option>
                    <?php while ($k = $kelas_list->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($k['kelas']) ?>" <?= $filter_kelas === $k['kelas'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($k['kelas']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Cari</button>
                <a href="<?= base_url('admin/siswa.php') ?>" class="btn btn-outline-secondary">Reset</a>
            </div>
            <div class="col-md-2 text-end">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="bi bi-plus-lg"></i> Tambah
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Tabel Data -->
<div class="card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>NIS</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Jurusan</th>
                        <th>No. HP</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($total === 0): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            Tidak ada data siswa
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($rows as $r): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><code><?= htmlspecialchars($r['nis']) ?></code></td>
                        <td class="fw-semibold"><?= htmlspecialchars($r['nama']) ?></td>
                        <td><?= htmlspecialchars($r['kelas']) ?></td>
                        <td><?= htmlspecialchars($r['jurusan']) ?></td>
                        <td><?= htmlspecialchars($r['no_hp'] ?: '-') ?></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-info text-white" 
                                    data-bs-toggle="modal" data-bs-target="#detail<?= $r['id'] ?>" title="Detail">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-warning" 
                                    data-bs-toggle="modal" data-bs-target="#edit<?= $r['id'] ?>" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                <input type="hidden" name="action" value="hapus">
                                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ===================== MODAL DETAIL (di luar tabel) ===================== -->
<?php foreach ($rows as $r): 
    $persen = getPersentaseKehadiran($r['id']);
    $status = hitungStatusKehadiran($persen);
?>
<div class="modal fade" id="detail<?= $r['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-badge me-2"></i>Detail Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th width="130">NIS</th>
                        <td><code><?= htmlspecialchars($r['nis']) ?></code></td>
                    </tr>
                    <tr>
                        <th>Nama</th>
                        <td><?= htmlspecialchars($r['nama']) ?></td>
                    </tr>
                    <tr>
                        <th>Kelas</th>
                        <td><?= htmlspecialchars($r['kelas']) ?></td>
                    </tr>
                    <tr>
                        <th>Jurusan</th>
                        <td><?= htmlspecialchars($r['jurusan']) ?></td>
                    </tr>
                    <tr>
                        <th>No. HP</th>
                        <td><?= htmlspecialchars($r['no_hp'] ?: '-') ?></td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td><?= htmlspecialchars($r['alamat'] ?: '-') ?></td>
                    </tr>
                    <tr>
                        <th>Kehadiran</th>
                        <td>
                            <strong><?= $persen ?>%</strong>
                            <span class="badge bg-<?= $persen >= 90 ? 'success' : ($persen >= 75 ? 'primary' : ($persen >= 60 ? 'warning text-dark' : 'danger')) ?> ms-1">
                                <?= $status ?>
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<!-- ===================== MODAL EDIT (di luar tabel) ===================== -->
<?php foreach ($rows as $r): ?>
<div class="modal fade" id="edit<?= $r['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">NIS <span class="text-danger">*</span></label>
                        <input type="text" name="nis" class="form-control" value="<?= htmlspecialchars($r['nis']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($r['nama']) ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kelas</label>
                            <input type="text" name="kelas" class="form-control" value="<?= htmlspecialchars($r['kelas']) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jurusan</label>
                            <input type="text" name="jurusan" class="form-control" value="<?= htmlspecialchars($r['jurusan']) ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. HP</label>
                        <input type="text" name="no_hp" class="form-control" value="<?= htmlspecialchars($r['no_hp'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2"><?= htmlspecialchars($r['alamat'] ?? '') ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<!-- ===================== MODAL TAMBAH ===================== -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="tambah">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Tambah Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">NIS <span class="text-danger">*</span></label>
                        <input type="text" name="nis" class="form-control" required placeholder="Contoh: 2024026">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kelas</label>
                            <input type="text" name="kelas" class="form-control" value="XII RPL 1" placeholder="XII RPL 1">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jurusan</label>
                            <input type="text" name="jurusan" class="form-control" value="Rekayasa Perangkat Lunak">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. HP</label>
                        <input type="text" name="no_hp" class="form-control" placeholder="08xxxxxxxxxx">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="alert alert-info small mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        Akun login otomatis dibuat:<br>
                        Username = <strong>NIS</strong> &nbsp;|&nbsp; Password = <strong>password123</strong>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
