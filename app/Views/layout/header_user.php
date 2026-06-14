<?php
$activeMenu = $activeMenu ?? 'dashboard';
$badgeCount = $badgeCount ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>E-Lab — <?= ucfirst($activeMenu) ?></title>
<link rel="stylesheet" href="assets/css/style.css">
<link rel="icon" href="assets/img/ICON.svg">
</head>
<body>
<div class="user-layout">

<nav class="topnav">
  <a class="topnav-brand" href="index.php?c=user&a=dashboard">
      <img src="assets/img/ICON.svg" alt="E-Lab Borrow Logo" class="nav-logo">
    <span class="nav-brand-text">E-Lab</span>
  </a>

  <div class="topnav-links">
    <a class="nav-link <?= $activeMenu === 'dashboard' ? 'active' : '' ?>" href="index.php?c=user&a=dashboard">
      <span class="nav-link-ico">🏠</span>
      <span class="nav-link-text">Dashboard</span>
    </a>
    <a class="nav-link <?= $activeMenu === 'katalog' ? 'active' : '' ?>" href="index.php?c=user&a=katalog">
      <span class="nav-link-ico">📦</span>
      <span class="nav-link-text">Katalog</span>
    </a>
    <a class="nav-link <?= $activeMenu === 'pinjam' ? 'active' : '' ?>" href="index.php?c=user&a=pinjam">
      <span class="nav-link-ico">📝</span>
      <span class="nav-link-text">Ajukan Pinjam</span>
    </a>
    <a class="nav-link <?= $activeMenu === 'riwayat' ? 'active' : '' ?>" href="index.php?c=user&a=riwayat">
      <span class="nav-link-ico">📋</span>
      <span class="nav-link-text">Riwayat</span>
      <?php if ($badgeCount > 0): ?>
        <span class="nav-link-badge"><?= $badgeCount ?></span>
      <?php endif; ?>
    </a>
    <a class="nav-link <?= $activeMenu === 'perpanjangan' ? 'active' : '' ?>" href="index.php?c=user&a=perpanjangan">
      <span class="nav-link-ico">🔄</span>
      <span class="nav-link-text">Perpanjangan</span>
    </a>
  </div>

  <div class="topnav-right">
    <div class="nav-user">
      <div class="avatar av-blue"><?= strtoupper(substr($_SESSION['nama'] ?? 'U', 0, 1)) ?></div>
      <div class="nav-user-info">
        <div class="nav-user-name"><?= htmlspecialchars($_SESSION['nama'] ?? 'Mahasiswa') ?></div>
        <div class="nav-user-npm"><?= htmlspecialchars($_SESSION['npm'] ?? '') ?></div>
      </div>
    </div>
    <a href="index.php?c=auth&a=logout" class="btn btn-ghost btn-sm">🚪 Keluar</a>
  </div>
</nav>
