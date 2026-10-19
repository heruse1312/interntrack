<?php
require_once __DIR__ . '/../../config/auth.php';
requireAdmin();
$user = currentUser();
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Admin' ?> - InternTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body>
<div class="d-flex">
    <nav class="sidebar">
        <div class="sidebar-brand">
            <h4><i class="bi bi-briefcase-fill"></i> INTERNTRACK</h4>
            <small>Admin / Pembimbing</small>
        </div>
        <div class="sidebar-nav">
            <a href="<?= base_url('admin/dashboard.php') ?>" class="<?= $current_page=='dashboard.php'?'active':'' ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="<?= base_url('admin/siswa.php') ?>" class="<?= $current_page=='siswa.php'?'active':'' ?>">
                <i class="bi bi-people"></i> Data Siswa
            </a>
            <a href="<?= base_url('admin/perusahaan.php') ?>" class="<?= $current_page=='perusahaan.php'?'active':'' ?>">
                <i class="bi bi-building"></i> Perusahaan
            </a>
            <a href="<?= base_url('admin/pembimbing.php') ?>" class="<?= $current_page=='pembimbing.php'?'active':'' ?>">
                <i class="bi bi-person-badge"></i> Pembimbing
            </a>
            <a href="<?= base_url('admin/penempatan.php') ?>" class="<?= $current_page=='penempatan.php'?'active':'' ?>">
                <i class="bi bi-geo-alt"></i> Penempatan PKL
            </a>
            <a href="<?= base_url('admin/jurnal.php') ?>" class="<?= $current_page=='jurnal.php'?'active':'' ?>">
                <i class="bi bi-journal-text"></i> Jurnal Kegiatan
            </a>
            <a href="<?= base_url('admin/absensi.php') ?>" class="<?= $current_page=='absensi.php'?'active':'' ?>">
                <i class="bi bi-calendar-check"></i> Absensi
            </a>
            <a href="<?= base_url('admin/monitoring.php') ?>" class="<?= $current_page=='monitoring.php'?'active':'' ?>">
                <i class="bi bi-clipboard-check"></i> Monitoring
            </a>
            <a href="<?= base_url('laporan/index.php') ?>" class="<?= strpos($_SERVER['PHP_SELF'],'laporan')!==false?'active':'' ?>">
                <i class="bi bi-file-earmark-bar-graph"></i> Laporan
            </a>
            <hr style="border-color:rgba(255,255,255,0.15);margin:1rem 1.5rem;">
            <a href="<?= base_url('auth/logout.php') ?>">
                <i class="bi bi-box-arrow-left"></i> Logout
            </a>
        </div>
    </nav>
    <div class="main-content flex-grow-1">
        <div class="topbar">
            <h2><?= $page_title ?? 'Dashboard' ?></h2>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted small"><i class="bi bi-person-circle"></i> <?= htmlspecialchars($user['nama']) ?></span>
            </div>
        </div>
        <?php showFlash(); ?>
