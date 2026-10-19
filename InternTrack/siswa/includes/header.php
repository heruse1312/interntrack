<?php
require_once __DIR__ . '/../../config/auth.php';
requireSiswa();
$user = currentUser();
$current_page = basename($_SERVER['PHP_SELF']);
$siswa_id = $user['related_id'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Siswa' ?> - InternTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body>
<div class="d-flex">
    <nav class="sidebar">
        <div class="sidebar-brand">
            <h4><i class="bi bi-briefcase-fill"></i> INTERNTRACK</h4>
            <small>Portal Siswa</small>
        </div>
        <div class="sidebar-nav">
            <a href="<?= base_url('siswa/dashboard.php') ?>" class="<?= $current_page=='dashboard.php'?'active':'' ?>"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="<?= base_url('siswa/profil.php') ?>" class="<?= $current_page=='profil.php'?'active':'' ?>"><i class="bi bi-person"></i> Profil</a>
            <a href="<?= base_url('siswa/jurnal.php') ?>" class="<?= $current_page=='jurnal.php'?'active':'' ?>"><i class="bi bi-journal-text"></i> Jurnal Kegiatan</a>
            <a href="<?= base_url('siswa/absensi.php') ?>" class="<?= $current_page=='absensi.php'?'active':'' ?>"><i class="bi bi-calendar-check"></i> Absensi</a>
            <a href="<?= base_url('siswa/monitoring.php') ?>" class="<?= $current_page=='monitoring.php'?'active':'' ?>"><i class="bi bi-clipboard-check"></i> Hasil Monitoring</a>
            <hr style="border-color:rgba(255,255,255,0.15);margin:1rem 1.5rem;">
            <a href="<?= base_url('auth/logout.php') ?>"><i class="bi bi-box-arrow-left"></i> Logout</a>
        </div>
    </nav>
    <div class="main-content flex-grow-1">
        <div class="topbar">
            <h2><?= $page_title ?? 'Dashboard' ?></h2>
            <div><span class="text-muted small"><i class="bi bi-person-circle"></i> <?= htmlspecialchars($user['nama']) ?></span></div>
        </div>
        <?php showFlash(); ?>
