<?php require_once __DIR__ . '/../layout/header_admin.php'; ?>

<div class="topbar">
  <div class="topbar-title">Kelola Barang Rusak</div>
</div>

<div class="page-body">
  <div class="alert alert-warn">
    ⚠️ Barang yang ada di sini sedang dalam status <strong>Rusak</strong>. Anda bisa memindahkannya kembali ke stok tersedia jika sudah <strong>Diperbaiki</strong>, atau <strong>Dibuang</strong> jika sudah tidak bisa digunakan (akan mengurangi stok total).
  </div>

  <?php if (empty($data)): ?>
    <div class="empty" style="margin-top:60px">
      <div class="empty-icon">✨</div>
      <h4>Tidak ada barang rusak</h4>
      <p>Semua inventaris laboratorium dalam kondisi baik.</p>
    </div>
  <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Kode</th>
            <th>Nama Alat</th>
            <th>Stok Rusak Saat Ini</th>
            <th>Aksi Penanganan</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($data as $a): ?>
          <tr>
            <td class="td-code"><?= htmlspecialchars($a['kode']) ?></td>
            <td class="td-name"><?= htmlspecialchars($a['nama']) ?></td>
            <td>
              <span style="font-size:18px;font-weight:700;color:var(--danger)"><?= $a['stok_rusak'] ?></span> pcs
            </td>
            <td>
              <!-- Form Perbaiki -->
              <form action="index.php?c=admin&a=rusak" method="POST" style="display:inline-block; margin-right: 10px;">
                <input type="hidden" name="id" value="<?= $a['id'] ?>">
                <input type="hidden" name="action" value="perbaiki">
                <div style="display:flex;gap:6px">
                  <input type="number" name="jumlah" class="form-control" style="width:60px;padding:4px" min="1" max="<?= $a['stok_rusak'] ?>" value="<?= $a['stok_rusak'] ?>" required>
                  <button type="submit" class="btn btn-success btn-xs" onclick="return confirm('Tandai barang sudah diperbaiki? Stok tersedia akan bertambah.')">🔧 Diperbaiki</button>
                </div>
              </form>

              <!-- Form Buang -->
              <form action="index.php?c=admin&a=rusak" method="POST" style="display:inline-block;">
                <input type="hidden" name="id" value="<?= $a['id'] ?>">
                <input type="hidden" name="action" value="buang">
                <div style="display:flex;gap:6px">
                  <input type="number" name="jumlah" class="form-control" style="width:60px;padding:4px" min="1" max="<?= $a['stok_rusak'] ?>" value="<?= $a['stok_rusak'] ?>" required>
                  <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Buang barang ini? Stok TOTAL alat ini akan berkurang permanen!')">🗑️ Dibuang</button>
                </div>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>