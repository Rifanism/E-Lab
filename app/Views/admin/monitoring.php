<?php require_once __DIR__ . '/../layout/header_admin.php'; ?>

<div class="topbar">
  <div class="topbar-title">Monitoring Alat</div>
  <div class="search-wrap">
    <span class="search-ico">🔍</span>
    <input class="topbar-search" id="search-input" placeholder="Cari mahasiswa/alat..." oninput="filterTable()">
  </div>
</div>

<div class="page-body">
  <div class="stats-row">
    <div class="stat-card">
      <div class="stat-icon icon-blue">📋</div>
      <div class="stat-val"><?= $stats['aktif'] ?></div>
      <div class="stat-lbl">Sedang Dipinjam</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon icon-red">⚠️</div>
      <div class="stat-val"><?= $stats['late'] ?></div>
      <div class="stat-lbl">Terlambat Kembali</div>
      <?php if ($stats['late']): ?><div class="stat-badge badge-red">overdue</div><?php endif; ?>
    </div>
    <div class="stat-card">
      <div class="stat-icon icon-amber">🔧</div>
      <div class="stat-val"><?= $stats['rusak'] ?></div>
      <div class="stat-lbl">Alat Kondisi Rusak</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon icon-teal">📈</div>
      <div class="stat-val"><?= $stats['bulan'] ?></div>
      <div class="stat-lbl">Transaksi Bulan Ini</div>
    </div>
  </div>

  <div class="sec-head">
    <div class="sec-title">Semua Transaksi</div>
  </div>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>ID</th><th>Mahasiswa</th><th>Alat</th>
          <th>Jml</th><th>Tgl Pinjam</th><th>Tgl Kembali</th>
          <th>Status</th><th>Kondisi</th>
        </tr>
      </thead>
      <tbody id="monitor-table">
        <?php foreach ($semua as $p):
          $sm   = $statusMap[$p['status']] ?? ['label'=>$p['status'],'class'=>''];
          $late = in_array($p['status'],['disetujui','perpanjangan']) && strtotime($p['tgl_kembali']) < time();
        ?>
        <tr>
          <td class="td-code">#PJM-<?= str_pad($p['id'],4,'0',STR_PAD_LEFT) ?></td>
          <td>
            <div class="td-name"><?= htmlspecialchars($p['nama_user']) ?></div>
            <div class="td-sub"><?= htmlspecialchars($p['npm']) ?></div>
          </td>
          <td class="td-name"><?= htmlspecialchars($p['nama_alat']) ?></td>
          <td><?= $p['jumlah'] ?></td>
          <td><?= date('d M Y', strtotime($p['tgl_pinjam'])) ?></td>
          <td <?= $late ? 'style="color:var(--danger)"' : '' ?>>
            <?= date('d M Y', strtotime($p['tgl_kembali'])) ?>
            <?= $late ? ' ⚠️' : '' ?>
          </td>
          <td><span class="status <?= $sm['class'] ?>"><?= $sm['label'] ?></span></td>
          <td>
            <?php if ($p['status'] === 'selesai'): ?>
              <div style="font-size:11px;line-height:1.4">
                <span style="color:var(--success)">✅ <?= $p['jml_baik'] ?> Baik</span>
                <?php if($p['jml_rusak'] > 0): ?><br><span style="color:var(--warn)">🔧 <?= $p['jml_rusak'] ?> Rusak</span><?php endif; ?>
                <?php if($p['jml_hilang'] > 0): ?><br><span style="color:var(--danger)">❌ <?= $p['jml_hilang'] ?> Hilang</span><?php endif; ?>
              </div>
            <?php else: ?>
              <span style="color:var(--tx3)">—</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
function filterTable() {
  const q = document.getElementById('search-input').value.toLowerCase();
  document.querySelectorAll('#monitor-table tr').forEach(tr => {
    tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
  });
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>