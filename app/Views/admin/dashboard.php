<?php require_once __DIR__ . '/../layout/header_admin.php'; ?>

<div class="topbar">
  <div class="topbar-title">Dashboard</div>
  <div class="chip">📅 <?= date('l, d F Y') ?></div>
</div>

<div class="page-body">
  <!-- Stats -->
  <div class="stats-row">
    <div class="stat-card">
      <div class="stat-icon icon-blue">📋</div>
      <div class="stat-val"><?= $stats['aktif'] ?></div>
      <div class="stat-lbl">Peminjaman Aktif</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon icon-amber">⏳</div>
      <div class="stat-val"><?= $stats['pending'] ?></div>
      <div class="stat-lbl">Menunggu Verifikasi</div>
      <?php if ($stats['pending']): ?><div class="stat-badge badge-warn">perlu aksi</div><?php endif; ?>
    </div>
    <div class="stat-card">
      <div class="stat-icon icon-indigo">🔄</div>
      <div class="stat-val"><?= $stats['extPending'] ?></div>
      <div class="stat-lbl">Pengajuan Perpanjangan</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon icon-red">⚠️</div>
      <div class="stat-val"><?= $stats['late'] ?></div>
      <div class="stat-lbl">Terlambat Kembali</div>
      <?php if ($stats['late']): ?><div class="stat-badge badge-red">overdue</div><?php endif; ?>
    </div>
  </div>

  <div style="display:grid;grid-template-columns:1.4fr 1fr;gap:20px">
    <!-- Pengajuan Terbaru -->
    <div>
      <div class="sec-head">
        <div class="sec-title">Pengajuan Terbaru</div>
        <a href="index.php?c=admin&a=verifikasi" class="btn btn-ghost btn-sm">Lihat Semua →</a>
      </div>
      <?php if (empty($pengajuanTerbaru)): ?>
        <div class="card"><div class="card-body"><div class="empty" style="padding:58px">
          <div class="empty-icon" style="font-size:32px">✅</div>
          <h4>Tidak ada pengajuan</h4>
        </div></div></div>
      <?php else: ?>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Mahasiswa</th><th>Alat</th><th>Tgl Ajukan</th><th>Aksi</th></tr></thead>
            <tbody>
              <?php foreach ($pengajuanTerbaru as $p): ?>
              <tr>
                <td>
                  <div class="td-name"><?= htmlspecialchars($p['nama_user']) ?></div>
                  <div class="td-sub"><?= htmlspecialchars($p['npm']) ?></div>
                </td>
                <td><?= htmlspecialchars($p['nama_alat']) ?></td>
                <td style="font-size:12px;color:var(--tx2)"><?= date('d M, H:i', strtotime($p['created_at'])) ?></td>
                <td>
                  <a href="index.php?c=admin&a=verifikasi" class="btn btn-ghost btn-xs">Review</a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

    <!-- Stok Kritis -->
    <div>
      <div class="sec-head">
        <div class="sec-title">Stok Kritis</div>
        <a href="index.php?c=admin&a=alat" class="btn btn-ghost btn-sm">Kelola →</a>
      </div>
      <?php if (empty($stokKritis)): ?>
        <div class="card"><div class="card-body">
          <p style="font-size:13px;color:var(--tx2);text-align:center;padding:20px">Semua stok aman ✅</p>
        </div></div>
      <?php else: ?>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Alat</th><th>Sisa</th></tr></thead>
            <tbody>
              <?php foreach ($stokKritis as $a):
                $pct = $a['stok_total'] > 0 ? ($a['stok_tersedia']/$a['stok_total'])*100 : 0;
              ?>
              <tr>
                <td>
                  <div class="td-name"><?= htmlspecialchars($a['nama']) ?></div>
                  <div class="stok-bar" style="margin-top:5px;width:80px">
                    <div class="stok-fill <?= $pct>40?'fill-mid':'fill-low' ?>" style="width:<?= $pct ?>%"></div>
                  </div>
                </td>
                <td>
                  <span style="font-size:16px;font-weight:800;color:<?= $a['stok_tersedia']==0?'var(--danger)':'var(--warn)' ?>"><?= $a['stok_tersedia'] ?></span>
                  <span style="font-size:11px;color:var(--tx3)">/<?= $a['stok_total'] ?></span>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>

      <div style="margin-top:16px">
        <div class="sec-head" style="margin-bottom:10px"><div class="sec-title" style="font-size:13px">Statistik Bulan Ini</div></div>
        <div class="card">
          <div class="card-body" style="display:flex;justify-content:space-around;padding:18px">
            <div style="text-align:center">
              <div style="font-size:26px;font-weight:800;color:var(--accent)"><?= $stats['bulanIni'] ?></div>
              <div style="font-size:11px;color:var(--tx2)">Total Transaksi</div>
            </div>
            <div style="text-align:center">
              <div style="font-size:26px;font-weight:800;color:var(--success)"><?= $stats['totalAlat'] ?></div>
              <div style="font-size:11px;color:var(--tx2)">Jenis Alat</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>