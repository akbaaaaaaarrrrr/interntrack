<?php
require_once __DIR__ . '/functions.php';
requireAdmin();
$current_page = basename($_SERVER['PHP_SELF']);

// Deteksi folder saat ini agar link sidebar selalu benar
// (admin/ vs laporan/)
$in_laporan = (strpos($_SERVER['PHP_SELF'], '/laporan/') !== false);
$adm = $in_laporan ? '../admin/' : '';
$lap = $in_laporan ? '' : '../laporan/';
$asset = '../assets/';
$auth = '../auth/';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Admin' ?> - InternTrack</title>
    <link rel="icon" type="image/svg+xml" href="<?= $asset ?>img/favicon.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= $asset ?>css/style.css">
</head>
<body>
<div class="app-wrapper">
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo-wrap">
                <img src="<?= $asset ?>img/logo.svg" alt="InternTrack" class="logo-img">
                <div>
                    <h2>InternTrack</h2>
                    <small>Admin / Pembimbing</small>
                </div>
            </div>
        </div>
        <nav class="sidebar-menu">
            <div class="menu-label">Menu Utama</div>
            <a href="<?= $adm ?>dashboard.php" class="<?= $current_page=='dashboard.php'?'active':'' ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            
            <div class="menu-label">Data Master</div>
            <a href="<?= $adm ?>siswa.php" class="<?= $current_page=='siswa.php'?'active':'' ?>"><i class="fas fa-user-graduate"></i> Data Siswa</a>
            <a href="<?= $adm ?>perusahaan.php" class="<?= $current_page=='perusahaan.php'?'active':'' ?>"><i class="fas fa-building"></i> Perusahaan</a>
            <a href="<?= $adm ?>pembimbing.php" class="<?= $current_page=='pembimbing.php'?'active':'' ?>"><i class="fas fa-chalkboard-teacher"></i> Pembimbing</a>
            <a href="<?= $adm ?>penempatan.php" class="<?= $current_page=='penempatan.php'?'active':'' ?>"><i class="fas fa-clipboard-list"></i> Penempatan PKL</a>
            
            <div class="menu-label">Monitoring</div>
            <a href="<?= $adm ?>jurnal.php" class="<?= $current_page=='jurnal.php'?'active':'' ?>"><i class="fas fa-book-open"></i> Jurnal Kegiatan</a>
            <a href="<?= $adm ?>absensi.php" class="<?= $current_page=='absensi.php'?'active':'' ?>"><i class="fas fa-calendar-check"></i> Kehadiran</a>
            <a href="<?= $adm ?>monitoring.php" class="<?= $current_page=='monitoring.php'?'active':'' ?>"><i class="fas fa-search"></i> Monitoring</a>
            
            <div class="menu-label">Laporan</div>
            <a href="<?= $lap ?>siswa.php" class="<?= ($in_laporan && $current_page=='siswa.php')?'active':'' ?>"><i class="fas fa-file-alt"></i> Laporan Siswa</a>
            <a href="<?= $lap ?>kehadiran.php" class="<?= $current_page=='kehadiran.php'?'active':'' ?>"><i class="fas fa-file-alt"></i> Laporan Kehadiran</a>
            <a href="<?= $lap ?>monitoring.php" class="<?= ($in_laporan && $current_page=='monitoring.php')?'active':'' ?>"><i class="fas fa-file-alt"></i> Laporan Monitoring</a>
            
            <div class="menu-label">Akun</div>
            <a href="<?= $auth ?>logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>
    <main class="main-content">
        <div class="topbar">
            <h1><?= $page_title ?? 'Dashboard' ?></h1>
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?= e($_SESSION['nama'] ?? 'Admin') ?></span>
                <span class="badge">Admin</span>
            </div>
        </div>
        <?php
        $flash = getFlash();
        if ($flash):
        ?>
            <div class="alert alert-<?= $flash['type'] ?>"><?= e($flash['message']) ?></div>
        <?php endif; ?>
