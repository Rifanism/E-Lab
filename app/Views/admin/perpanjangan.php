<?php require_once __DIR__ . '/../layout/header_admin.php'; ?>

<div class="topbar">
  <div class="topbar-title">Review Perpanjangan</div>
  <div class="chip"><?= count($data) ?> pengajuan</div>
</div>

<div class="page-body">
  <?php if (empty($data)): ?>
    <div class="empty" style="margin-top:60px">
      <div class="empty-icon">🔄</div>
      <h4>Tidak ada pengajuan perpanjangan</h4>
    </div>
  <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID</th><th>Mahasiswa</th><th>Alat</th>
            <th>Tgl Kembali Lama</th><th>Tgl Kembali Baru</th>
            <th>Alasan</th><th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($data as $r): ?>
          <tr>
            <td class="td-code">#EXT-<?= str_pad($r['id'],3,'0',STR_PAD_LEFT) ?></td>
            <td>
              <div class="td-name"><?= htmlspecialchars($r['nama_user']) ?></div>
              <div class="td-sub"><?= htmlspecialchars($r['npm']) ?></div>
            </td>
            <td class="td-name"><?= htmlspecialchars($r['nama_alat']) ?></td>
            <td style="text-decoration:line-through;color:var(--tx3)"><?= date('d M Y', strtotime($r['tgl_lama'])) ?></td>
            <td style="color:var(--accent);font-weight:700"><?= date('d M Y', strtotime($r['tgl_kembali_baru'])) ?></td>
            <td style="max-width:160px;font-size:12px;color:var(--tx2)"><?= htmlspecialchars($r['alasan']) ?></td>
            <td>
              <div style="display:flex;gap:6px">
                <form action="index.php?c=admin&a=perpanjangan" method="POST">
                  <input type="hidden" name="id" value="<?= $r['id'] ?>">
                  <input type="hidden" name="action" value="setujui">
                  <button type="submit" class="btn btn-success btn-xs">✅ Setujui</button>
                </form>
                <form action="index.php?c=admin&a=perpanjangan" method="POST" onsubmit="return confirm('Tolak perpanjangan?')">
                  <input type="hidden" name="id" value="<?= $r['id'] ?>">
                  <input type="hidden" name="action" value="tolak">
                  <button type="submit" class="btn btn-danger btn-xs">❌ Tolak</button>
                </form>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>