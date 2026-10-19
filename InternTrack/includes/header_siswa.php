<?php
require_once __DIR__ . '/functions.php';
requireSiswa();
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Siswa' ?> - InternTrack</title>
    <link rel="icon" type="image/svg+xml" href="../assets/img/favicon.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="app-wrapper">
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo-wrap">
                <img src="../assets/img/logo.svg" alt="InternTrack" class="logo-img">
                <div>
                    <h2>InternTrack</h2>
                    <small>Portal Siswa</small>
                </div>
            </div>
        </div>
        <nav class="sidebar-menu">
            <div class="menu-label">Menu</div>
            <a href="dashboard.php" class="<?= $current_page=='dashboard.php'?'active':'' ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="profil.php" class="<?= $current_page=='profil.php'?'active':'' ?>"><i class="fas fa-user"></i> Profil Saya</a>
            <a href="jurnal.php" class="<?= $current_page=='jurnal.php'?'active':'' ?>"><i class="fas fa-book-open"></i> Jurnal Kegiatan</a>
            <a href="absensi.php" class="<?= $current_page=='absensi.php'?'active':'' ?>"><i class="fas fa-calendar-check"></i> Kehadiran</a>
            <a href="monitoring.php" class="<?= $current_page=='monitoring.php'?'active':'' ?>"><i class="fas fa-search"></i> Hasil Monitoring</a>
            <a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>
    <main class="main-content">
        <div class="topbar">
            <h1><?= $page_title ?? 'Dashboard' ?></h1>
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?= e($_SESSION['nama'] ?? 'Siswa') ?></span>
                <span class="badge">Siswa</span>
            </div>
        </div>
        <?php
        $flash = getFlash();
        if ($flash):
        ?>
            <div class="alert alert-<?= $flash['type'] ?>"><?= e($flash['message']) ?></div>
        <?php endif; ?>
