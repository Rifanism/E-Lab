<?php require_once __DIR__ . '/../layout/header_user.php'; ?>

<div class="user-hero" style="padding:20px 28px">
  <div class="hero-greeting" style="font-size:18px">Perpanjangan Peminjaman</div>
  <div class="hero-sub">Ajukan perpanjangan maks. 1× per transaksi</div>
</div>

<div class="user-body">
  <div class="alert alert-warn">
    ⚠️ Perpanjangan hanya bisa dilakukan <strong>1 kali</strong> per peminjaman dan maksimal <strong>7 hari</strong> tambahan dari tanggal kembali semula.
  </div>

  <?php if ($error): ?>
    <div class="alert alert-danger">❌ <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <?php if (!empty($bisa)): ?>
  <div class="card" style="margin-bottom:26px">
    <div class="card-header"><div class="card-title">🔄 Form Perpanjangan</div></div>
    <div class="card-body">
      <form action="index.php?c=user&a=perpanjangan" method="POST">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Pilih Peminjaman <span style="color:var(--danger)">*</span></label>
            <select class="form-control" name="peminjaman_id" id="pinjam-sel" onchange="updateMax(this)" required>
              <option value="">— Pilih —</option>
              <?php foreach ($bisa as $b): ?>
                <option value="<?= $b['id'] ?>"
                  data-tgl="<?= $b['tgl_kembali'] ?>"
                  <?= ($prePinjam && $prePinjam['id'] == $b['id']) ? 'selected' : '' ?>>
                  #PJM-<?= str_pad($b['id'],4,'0',STR_PAD_LEFT) ?> — <?= htmlspecialchars($b['nama_alat']) ?> (kembali: <?= date('d M', strtotime($b['tgl_kembali'])) ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Tanggal Kembali Baru <span style="color:var(--danger)">*</span></label>
            <input class="form-control" type="date" name="tgl_kembali_baru" id="tgl-baru" required>
            <div class="form-hint" id="tgl-hint">Pilih peminjaman dulu</div>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Alasan Perpanjangan <span style="color:var(--danger)">*</span></label>
          <textarea class="form-control" name="alasan" rows="2"
                    placeholder="Jelaskan alasan perpanjangan..." required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">🔄 Kirim Pengajuan</button>
      </form>
    </div>
  </div>
  <?php else: ?>
    <div class="alert alert-info">ℹ️ Tidak ada peminjaman yang bisa diperpanjang saat ini.</div>
  <?php endif; ?>

  <?php if (!empty($riwayat)): ?>
  <div class="sec-head"><div class="sec-title">Riwayat Perpanjangan</div></div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr><th>ID</th><th>Alat</th><th>Tgl Lama</th><th>Tgl Baru</th><th>Alasan</th><th>Status</th></tr>
      </thead>
      <tbody>
        <?php foreach ($riwayat as $r):
          $sm = ['pending'=>'s-pending','disetujui'=>'s-approved','ditolak'=>'s-rejected'];
          $sl = ['pending'=>'Menunggu','disetujui'=>'Disetujui','ditolak'=>'Ditolak'];
        ?>
        <tr>
          <td class="td-code">#EXT-<?= str_pad($r['id'],3,'0',STR_PAD_LEFT) ?></td>
          <td class="td-name"><?= htmlspecialchars($r['nama_alat']) ?></td>
          <td><?= date('d M Y', strtotime($r['tgl_lama'])) ?></td>
          <td style="color:var(--accent);font-weight:600"><?= date('d M Y', strtotime($r['tgl_kembali_baru'])) ?></td>
          <td style="font-size:12px;color:var(--tx2);max-width:140px"><?= htmlspecialchars($r['alasan']) ?></td>
          <td><span class="status <?= $sm[$r['status']] ?? '' ?>"><?= $sl[$r['status']] ?? $r['status'] ?></span></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<script>
function updateMax(sel) {
  const opt  = sel.options[sel.selectedIndex];
  const tgl  = opt.dataset.tgl;
  const inp  = document.getElementById('tgl-baru');
  const hint = document.getElementById('tgl-hint');
  if (!tgl) return;
  const min = new Date(tgl); min.setDate(min.getDate()+1);
  const max = new Date(tgl); max.setDate(max.getDate()+7);
  inp.min = min.toISOString().split('T')[0];
  inp.max = max.toISOString().split('T')[0];
  inp.value = max.toISOString().split('T')[0];
  hint.textContent = `Maks: ${max.toLocaleDateString('id-ID',{day:'numeric',month:'short',year:'numeric'})}`;
}
document.addEventListener('DOMContentLoaded', () => {
  const sel = document.getElementById('pinjam-sel');
  if (sel && sel.value) updateMax(sel);
});
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
