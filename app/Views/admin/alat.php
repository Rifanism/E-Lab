<?php require_once __DIR__ . '/../layout/header_admin.php'; ?>

<div class="topbar">
  <div class="topbar-title">Kelola Alat</div>
  <button class="btn btn-primary btn-sm" onclick="openModal('modal-tambah')">+ Tambah Alat</button>
</div>

<div class="page-body">
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Kode</th><th>Nama Alat</th><th>Kategori</th>
          <th>Stok Total</th><th>Tersedia</th><th>Status</th><th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($alats as $a):
          $st = $alatModel->statusStok($a);
          $stBadge = ['available'=>'s-good','limited'=>'s-limited','empty'=>'s-empty'];
          $stLabel = ['available'=>'Tersedia','limited'=>'Terbatas','empty'=>'Habis'];
          $pct = $a['stok_total']>0 ? ($a['stok_tersedia']/$a['stok_total'])*100 : 0;
        ?>
        <tr>
          <td class="td-code"><?= htmlspecialchars($a['kode']) ?></td>
          <td>
            <div class="td-name"><?= htmlspecialchars($a['nama']) ?></div>
            <?php if ($a['deskripsi']): ?>
              <div class="td-sub" style="max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= htmlspecialchars($a['deskripsi']) ?></div>
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($a['kategori']) ?></td>
          <td><?= $a['stok_total'] ?></td>
          <td>
            <span style="font-size:15px;font-weight:700;color:<?= $a['stok_tersedia']==0?'var(--danger)':($pct<=40?'var(--warn)':'var(--success)') ?>">
              <?= $a['stok_tersedia'] ?>
            </span>
            <div class="stok-bar" style="width:60px;margin-top:4px">
              <div class="stok-fill <?= $pct>50?'fill-high':($pct>20?'fill-mid':'fill-low') ?>" style="width:<?= $pct ?>%"></div>
            </div>
          </td>
          <td><span class="status <?= $stBadge[$st] ?>"><?= $stLabel[$st] ?></span></td>
          <td style="display:flex;gap:6px">
            <button class="btn btn-ghost btn-xs" onclick='openEdit(<?= json_encode($a) ?>)'>Edit</button>
            
            <form action="index.php?c=admin&a=alat" method="POST" style="display:inline" onsubmit="return confirm('Hapus alat ini?')">
              <input type="hidden" name="action" value="hapus">
              <input type="hidden" name="id" value="<?= $a['id'] ?>">
              <button type="submit" class="btn btn-danger btn-xs">Hapus</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<div id="modal-tambah" class="modal-bg">
  <div class="modal-box">
    <div class="modal-head">
      <div class="modal-title">Tambah Alat Baru</div>
      <button class="modal-close" onclick="closeModal('modal-tambah')">✕</button>
    </div>
    <form action="index.php?c=admin&a=alat" method="POST">
      <input type="hidden" name="action" value="tambah">
      <div class="form-group">
        <label class="form-label">Nama Alat *</label>
        <input class="form-control" type="text" name="nama" required placeholder="Nama alat laboratorium">
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Kode Alat *</label>
          <input class="form-control" type="text" name="kode" required placeholder="#ALT-XXX">
        </div>
        <div class="form-group">
          <label class="form-label">Kategori *</label>
          <select class="form-control" name="kategori" required>
            <option>Elektronika</option>
            <option>Kimia</option>
            <option>Biologi</option>
            <option>Fisika</option>
            <option>Umum</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Stok Total *</label>
        <input class="form-control" type="number" name="stok_total" min="1" required placeholder="0">
      </div>
      <div class="form-group">
        <label class="form-label">Deskripsi</label>
        <textarea class="form-control" name="deskripsi" rows="2" placeholder="Deskripsi singkat alat..."></textarea>
      </div>
      <div style="display:flex;gap:10px;margin-top:6px">
        <button type="submit" class="btn btn-primary">Simpan Alat</button>
        <button type="button" class="btn btn-ghost" onclick="closeModal('modal-tambah')">Batal</button>
      </div>
    </form>
  </div>
</div>

<div id="modal-edit" class="modal-bg">
  <div class="modal-box">
    <div class="modal-head">
      <div class="modal-title">Edit Alat</div>
      <button class="modal-close" onclick="closeModal('modal-edit')">✕</button>
    </div>
    <form action="index.php?c=admin&a=alat" method="POST">
      <input type="hidden" name="action" value="edit">
      <input type="hidden" name="id" id="edit-id">
      <div class="form-group">
        <label class="form-label">Nama Alat *</label>
        <input class="form-control" type="text" name="nama" id="edit-nama" required>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Kode Alat *</label>
          <input class="form-control" type="text" name="kode" id="edit-kode" required>
        </div>
        <div class="form-group">
          <label class="form-label">Kategori *</label>
          <select class="form-control" name="kategori" id="edit-kat">
            <option>Elektronika</option><option>Kimia</option>
            <option>Biologi</option><option>Fisika</option><option>Umum</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Stok Total *</label>
        <input class="form-control" type="number" name="stok_total" id="edit-stok" min="1" required>
      </div>
      <div class="form-group">
        <label class="form-label">Deskripsi</label>
        <textarea class="form-control" name="deskripsi" id="edit-desk" rows="2"></textarea>
      </div>
      <div style="display:flex;gap:10px;margin-top:6px">
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <button type="button" class="btn btn-ghost" onclick="closeModal('modal-edit')">Batal</button>
      </div>
    </form>
  </div>
</div>

<script>
function openEdit(a) {
  document.getElementById('edit-id').value   = a.id;
  document.getElementById('edit-nama').value = a.nama;
  document.getElementById('edit-kode').value = a.kode;
  document.getElementById('edit-stok').value = a.stok_total;
  document.getElementById('edit-desk').value = a.deskripsi || '';
  const sel = document.getElementById('edit-kat');
  for (let o of sel.options) o.selected = (o.value === a.kategori);
  openModal('modal-edit');
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>