<?php require_once __DIR__ . '/../layout/header_admin.php'; ?>

<div class="topbar">
  <div class="topbar-title">Catat Pengembalian</div>
</div>

<div class="page-body">
  <div class="alert alert-info">
    ℹ️ Catat kondisi alat saat dikembalikan. Stok akan <strong>otomatis bertambah</strong> kecuali kondisi <strong>Hilang</strong>.
  </div>

  <?php if (empty($data)): ?>
    <div class="empty" style="margin-top:60px">
      <div class="empty-icon">📥</div>
      <h4>Tidak ada peminjaman aktif</h4>
    </div>
  <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID</th><th>Mahasiswa</th><th>Alat</th>
            <th>Jml</th><th>Tgl Wajib Kembali</th><th>Kondisi</th><th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($data as $p):
            $late = strtotime($p['tgl_kembali']) < time(); 
          ?>
          <tr>
            <td class="td-code">#PJM-<?= str_pad($p['id'],4,'0',STR_PAD_LEFT) ?></td>
            <td>
              <div class="td-name"><?= htmlspecialchars($p['nama_user']) ?></div>
              <div class="td-sub"><?= htmlspecialchars($p['npm']) ?></div>
            </td>
            <td class="td-name"><?= htmlspecialchars($p['nama_alat']) ?></td>
            <td><strong style="font-size:16px;color:var(--accent)"><?= $p['jumlah'] ?></strong></td>
            
            <td colspan="2">
              <form action="index.php?c=admin&a=pengembalian" method="POST" id="form-<?= $p['id'] ?>" style="display:flex;gap:10px;align-items:center">
                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                
                <div style="display:flex;flex-direction:column;gap:4px">
                  <label style="font-size:11px;color:var(--tx2)">✅ Baik</label>
                  <input class="form-control" type="number" name="jml_baik" value="<?= $p['jumlah'] ?>" min="0" max="<?= $p['jumlah'] ?>" style="width:60px;padding:4px">
                </div>
                
                <div style="display:flex;flex-direction:column;gap:4px">
                  <label style="font-size:11px;color:var(--warn)">🔧 Rusak</label>
                  <input class="form-control" type="number" name="jml_rusak" value="0" min="0" max="<?= $p['jumlah'] ?>" style="width:60px;padding:4px">
                </div>
                
                <div style="display:flex;flex-direction:column;gap:4px">
                  <label style="font-size:11px;color:var(--danger)">❌ Hilang</label>
                  <input class="form-control" type="number" name="jml_hilang" value="0" min="0" max="<?= $p['jumlah'] ?>" style="width:60px;padding:4px">
                </div>
                
                <button type="button" class="btn btn-success btn-sm" style="margin-top:16px" onclick="validateReturn(<?= $p['id'] ?>, <?= $p['jumlah'] ?>)">
                    📥 Konfirmasi
                </button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<script>
function validateReturn(id, totalPinjam) {
    const form = document.getElementById('form-' + id);
    const baik = parseInt(form.jml_baik.value) || 0;
    const rusak = parseInt(form.jml_rusak.value) || 0;
    const hilang = parseInt(form.jml_hilang.value) || 0;
    
    if ((baik + rusak + hilang) !== totalPinjam) {
        alert(`Total pengembalian (${baik + rusak + hilang}) tidak sesuai dengan jumlah dipinjam (${totalPinjam})!`);
        return;
    }
    
    if(confirm(`Konfirmasi pengembalian:\n- Baik: ${baik}\n- Rusak: ${rusak}\n- Hilang: ${hilang}`)) {
        form.submit();
    }
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>