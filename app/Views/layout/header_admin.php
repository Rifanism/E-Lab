<?php
$activeMenu    = $activeMenu ?? 'dashboard';
$pendingPinjam = $pendingPinjam ?? 0;
$pendingExt    = $pendingExt ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>E-Lab Admin — <?= ucfirst($activeMenu) ?></title>
<link rel="stylesheet" href="assets/css/style.css">
<link rel="icon" href="assets/img/ICON.svg">
</head>
<body>
<div class="admin-layout">

<aside class="sidebar">
  <div class="sb-brand">
      <img src="assets/img/ICON.svg" alt="E-Lab Borrow Logo" class="sb-logo">
    <div>
      <div class="sb-app-name">E-Lab</div>
      <div class="sb-app-sub">Admin Panel</div>
    </div>
  </div>

  <nav class="sb-nav">
    <div class="sb-section">Menu</div>
    <a class="sb-item <?= $activeMenu === 'dashboard' ? 'active' : '' ?>" href="index.php?c=admin&a=dashboard">
      <span class="sb-icon">📊</span> Dashboard
    </a>
    <a class="sb-item <?= $activeMenu === 'alat' ? 'active' : '' ?>" href="index.php?c=admin&a=alat">
      <span class="sb-icon">🔧</span> Kelola Alat
    </a>

    <div class="sb-section">Transaksi</div>
    <a class="sb-item <?= $activeMenu === 'verifikasi' ? 'active' : '' ?>" href="index.php?c=admin&a=verifikasi">
      <span class="sb-icon">✅</span> Verifikasi
      <?php if ($pendingPinjam > 0): ?>
        <span class="sb-badge"><?= $pendingPinjam ?></span>
      <?php endif; ?>
    </a>
    <a class="sb-item <?= $activeMenu === 'perpanjangan' ? 'active' : '' ?>" href="index.php?c=admin&a=perpanjangan">
      <span class="sb-icon">🔄</span> Perpanjangan
      <?php if ($pendingExt > 0): ?>
        <span class="sb-badge"><?= $pendingExt ?></span>
      <?php endif; ?>
    </a>
    <a class="sb-item <?= $activeMenu === 'pengembalian' ? 'active' : '' ?>" href="index.php?c=admin&a=pengembalian">
      <span class="sb-icon">📥</span> Pengembalian
    </a>

    <div class="sb-section">Laporan</div>
    <a class="sb-item <?= $activeMenu === 'monitoring' ? 'active' : '' ?>" href="index.php?c=admin&a=monitoring">
      <span class="sb-icon">👁</span> Monitoring
    </a>
    <a class="sb-item <?= $activeMenu === 'rusak' ? 'active' : '' ?>" href="index.php?c=admin&a=rusak">
      <span class="sb-icon">⚠️</span> Barang Rusak
    </a>    
  </nav>

  <div class="sb-foot">
    <div class="user-chip">
      <div class="avatar av-red"><?= strtoupper(substr($_SESSION['nama'] ?? 'A', 0, 1)) ?></div>
      <div class="uc-info">
        <div class="uc-name"><?= htmlspecialchars($_SESSION['nama'] ?? 'Admin') ?></div>
        <div class="uc-role">Administrator</div>
      </div>
    </div>
    <a href="index.php?c=auth&a=logout" class="btn btn-ghost btn-sm" style="width:100%;justify-content:center">🚪 Logout</a>
  </div>
</aside>

<main class="admin-main">
