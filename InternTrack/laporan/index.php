<?php
require_once __DIR__ . '/../config/auth.php';
requireAdmin();
$page_title = 'Laporan';
require_once __DIR__ . '/../admin/includes/header.php';
?>
<div class="row g-3">
    <div class="col-md-4">
        <div class="card-custom h-100"><div class="card-body text-center py-4">
            <i class="bi bi-people display-4 text-primary"></i>
            <h5 class="mt-3">Laporan Data Siswa PKL</h5>
            <p class="text-muted small">Daftar siswa beserta penempatan dan status kehadiran</p>
            <a href="<?= base_url('laporan/siswa.php') ?>" class="btn btn-primary">Lihat Laporan</a>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card-custom h-100"><div class="card-body text-center py-4">
            <i class="bi bi-calendar-check display-4 text-success"></i>
            <h5 class="mt-3">Laporan Kehadiran</h5>
            <p class="text-muted small">Rekapitulasi kehadiran dengan persentase</p>
            <a href="<?= base_url('laporan/kehadiran.php') ?>" class="btn btn-success">Lihat Laporan</a>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card-custom h-100"><div class="card-body text-center py-4">
            <i class="bi bi-clipboard-check display-4 text-warning"></i>
            <h5 class="mt-3">Laporan Monitoring</h5>
            <p class="text-muted small">Hasil monitoring pembimbing terhadap siswa</p>
            <a href="<?= base_url('laporan/monitoring.php') ?>" class="btn btn-warning">Lihat Laporan</a>
        </div></div>
    </div>
</div>
<?php require_once __DIR__ . '/../admin/includes/footer.php'; ?>
