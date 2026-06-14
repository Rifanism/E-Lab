<?php require_once __DIR__ . '/../layout/header_user.php'; ?>

<div class="user-hero" style="padding:20px 28px">
  <div class="hero-greeting" style="font-size:18px">Ajukan Peminjaman Alat</div>
  <div class="hero-sub">Isi form di bawah untuk mengajukan peminjaman</div>
</div>

<div class="user-body">
  <div class="alert alert-info">
    ℹ️ Peminjaman berlaku setelah disetujui admin. Maks. durasi <strong>7 hari</strong>. Jumlah tidak boleh melebihi stok tersedia.
  </div>

  <?php if ($error): ?>
    <div class="alert alert-danger">❌ <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <div class="card">
    <div class="card-header"><div class="card-title">📝 Form Pengajuan</div></div>
    <div class="card-body">
      <form method="POST" action="index.php?c=user&a=pinjam">

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Pilih Alat <span style="color:var(--danger)">*</span></label>
            <select class="form-control" name="alat_id" id="alat-select" onchange="updateStok(this)" required>
              <option value="">— Pilih alat —</option>
              <?php foreach ($alats as $a): ?>
                <?php if ($a['stok_tersedia'] > 0): ?>
                  <option value="<?= $a['id'] ?>"
                    data-stok="<?= $a['stok_tersedia'] ?>"
                    <?= ($preAlat && $preAlat == $a['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($a['nama']) ?> (<?= htmlspecialchars($a['kode']) ?>) — Stok: <?= $a['stok_tersedia'] ?>
                  </option>
                <?php endif; ?>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Jumlah <span style="color:var(--danger)">*</span></label>
            <input class="form-control" type="number" name="jumlah" id="jumlah-input"
                   min="1" max="<?= $selectedAlat ? $selectedAlat['stok_tersedia'] : 99 ?>"
                   value="<?= (int)($_POST['jumlah'] ?? 1) ?>" required>
            <div class="form-hint" id="stok-hint">
              <?= $selectedAlat ? "Maks. {$selectedAlat['stok_tersedia']} pcs" : 'Pilih alat dulu' ?>
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Tanggal Pinjam <span style="color:var(--danger)">*</span></label>
            <input class="form-control" type="date" name="tgl_pinjam" id="tgl-pinjam"
                   min="<?= date('Y-m-d') ?>"
                   value="<?= htmlspecialchars($_POST['tgl_pinjam'] ?? date('Y-m-d')) ?>"
                   onchange="setMaxKembali()" required>
          </div>
          <div class="form-group">
            <label class="form-label">Tanggal Kembali <span style="color:var(--danger)">*</span></label>
            <input class="form-control" type="date" name="tgl_kembali" id="tgl-kembali"
                   value="<?= htmlspecialchars($_POST['tgl_kembali'] ?? '') ?>" required>
            <div class="form-hint">Maks. 7 hari dari tanggal pinjam</div>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Keperluan / Keterangan <span style="color:var(--danger)">*</span></label>
          <textarea class="form-control" name="keperluan" rows="3"
                    placeholder="Contoh: Praktikum Elektronika Dasar — pengukuran tegangan dan arus" required><?= htmlspecialchars($_POST['keperluan'] ?? '') ?></textarea>
        </div>

        <div style="display:flex;gap:10px;padding-top:6px">
          <button type="submit" class="btn btn-primary">📤 Kirim Pengajuan</button>
          <a href="index.php?c=user&a=katalog" class="btn btn-ghost">Batal</a>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function updateStok(sel) {
  const opt = sel.options[sel.selectedIndex];
  const stok = parseInt(opt.dataset.stok) || 99;
  const inp  = document.getElementById('jumlah-input');
  const hint = document.getElementById('stok-hint');
  inp.max = stok;
  hint.textContent = stok ? `Maks. ${stok} pcs tersedia` : 'Pilih alat dulu';
}

function setMaxKembali() {
  const tglP = document.getElementById('tgl-pinjam').value;
  if (!tglP) return;
  const max = new Date(tglP);
  max.setDate(max.getDate() + 7);
  const inp = document.getElementById('tgl-kembali');
  inp.min = new Date(tglP).toISOString().split('T')[0];
  inp.max = max.toISOString().split('T')[0];
  if (!inp.value) inp.value = max.toISOString().split('T')[0];
}
document.addEventListener('DOMContentLoaded', setMaxKembali);
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
