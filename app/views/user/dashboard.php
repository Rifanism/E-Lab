<?php require_once __DIR__ . '/../layout/header_user.php'; ?>

<!-- HERO -->
<div class="user-hero">
  <div class="hero-greeting">Halo, <?= htmlspecialchars(explode(' ', $_SESSION['nama'])[0]) ?>! 👋</div>
  <div class="hero-sub">Senin, <?= date('d F Y') ?> · Selamat datang di E-Lab</div>
</div>

<div class="user-body">
  <div class="stats-row" style="grid-template-columns:repeat(3,1fr);margin-bottom:26px">
    <div class="stat-card">
      <div class="stat-icon icon-blue">📋</div>
      <div class="stat-val"><?= $stats['aktif'] ?></div>
      <div class="stat-lbl">Peminjaman Aktif</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon icon-amber">⏳</div>
      <div class="stat-val"><?= $stats['pending'] ?></div>
      <div class="stat-lbl">Menunggu Persetujuan</div>
      <?php if ($stats['pending'] > 0): ?><div class="stat-badge badge-warn">pending</div><?php endif; ?>
    </div>
    <div class="stat-card">
      <div class="stat-icon icon-teal">✅</div>
      <div class="stat-val"><?= $stats['selesai'] ?></div>
      <div class="stat-lbl">Total Selesai</div>
    </div>
  </div>

  <!-- Peminjaman Aktif -->
  <div class="sec-head">
    <div class="sec-title">Peminjaman Saya</div>
    <a href="index.php?c=user&a=riwayat" class="btn btn-ghost btn-sm">Lihat Semua →</a>
  </div>

  <?php if (empty($pinjamAktif)): ?>
    <div class="card" style="margin-bottom:26px">
      <div class="empty">
        <div class="empty-icon">📭</div>
        <h4>Belum ada peminjaman</h4>
        <p>Kunjungi katalog untuk meminjam alat</p>
      </div>
    </div>
  <?php else: ?>
    <div class="table-wrap" style="margin-bottom:26px">
      <table>
        <thead>
          <tr>
            <th>Alat</th>
            <th>Tgl Pinjam</th>
            <th>Tgl Kembali</th>
            <th>Jml</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($pinjamAktif as $p):
            $late = ($p['status']==='disetujui' && strtotime($p['tgl_kembali']) < time());
          ?>
          <tr>
            <td>
              <div class="td-name"><?= htmlspecialchars($p['nama_alat']) ?></div>
              <div class="td-sub"><?= htmlspecialchars($p['kode_alat']) ?></div>
            </td>
            <td><?= date('d M Y', strtotime($p['tgl_pinjam'])) ?></td>
            <td <?= $late ? 'style="color:var(--danger)"' : '' ?>>
              <?= date('d M Y', strtotime($p['tgl_kembali'])) ?>
              <?= $late ? ' ⚠️' : '' ?>
            </td>
            <td><?= $p['jumlah'] ?> pcs</td>
            <td>
              <?php
                $map = ['pending'=>'s-pending','disetujui'=>'s-approved','perpanjangan'=>'s-extended','selesai'=>'s-returned','ditolak'=>'s-rejected'];
                $lbl = ['pending'=>'Menunggu','disetujui'=>'Disetujui','perpanjangan'=>'Diperpanjang','selesai'=>'Selesai','ditolak'=>'Ditolak'];
              ?>
              <span class="status <?= $map[$p['status']] ?? '' ?>"><?= $lbl[$p['status']] ?? $p['status'] ?></span>
            </td>
            <td>
              <?php if ($p['status'] === 'disetujui'): ?>
                <a href="index.php?c=user&a=perpanjangan&pid=<?= $p['id'] ?>" class="btn btn-ghost btn-xs">Perpanjang</a>
              <?php else: ?>—
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

  <!-- Preview Katalog -->
  <div class="sec-head">
    <div class="sec-title">Alat Tersedia</div>
    <a href="index.php?c=user&a=katalog" class="btn btn-ghost btn-sm">Lihat Semua →</a>
  </div>
  <div class="katalog-grid">
    <?php foreach ($katalog as $alat):
      $st = $alatModel->statusStok($alat);
      $stBadge = ['available'=>'s-good','limited'=>'s-limited','empty'=>'s-empty'];
      $stLabel = ['available'=>'Tersedia','limited'=>'Terbatas','empty'=>'Habis'];
      $pct = $alat['stok_total'] > 0 ? ($alat['stok_tersedia']/$alat['stok_total'])*100 : 0;
      $fillClass = $pct > 50 ? 'fill-high' : ($pct > 20 ? 'fill-mid' : 'fill-low');
    ?>
      <div class="alat-card">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:12px">
          <div class="alat-icon">🔌</div>
          <span class="status <?= $stBadge[$st] ?>"><?= $stLabel[$st] ?></span>
        </div>
        <div class="alat-name"><?= htmlspecialchars($alat['nama']) ?></div>
        <div class="alat-code"><?= htmlspecialchars($alat['kode']) ?></div>
        <div class="alat-stok">Stok: <strong><?= $alat['stok_tersedia'] ?></strong>/<?= $alat['stok_total'] ?> pcs</div>
        <div class="stok-bar"><div class="stok-fill <?= $fillClass ?>" style="width:<?= $pct ?>%"></div></div>
        <div style="margin-top:14px">
          <?php if ($st !== 'empty'): ?>
            <a href="index.php?c=user&a=pinjam&alat_id=<?= $alat['id'] ?>" class="btn btn-primary btn-sm" style="width:100%;justify-content:center">+ Pinjam</a>
          <?php else: ?>
            <button class="btn btn-ghost btn-sm" style="width:100%" disabled>Tidak Tersedia</button>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
