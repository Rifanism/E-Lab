<?php require_once __DIR__ . '/../layout/header_admin.php'; ?>

<div class="topbar">
  <div class="topbar-title">Verifikasi Peminjaman</div>
  <div class="chip"><?= count($data) ?> pengajuan pending</div>
</div>

<div class="page-body">
  <?php if (empty($data)): ?>
    <div class="empty" style="margin-top:60px">
      <div class="empty-icon">✅</div>
      <h4>Semua sudah diverifikasi</h4>
      <p>Tidak ada pengajuan yang menunggu persetujuan</p>
    </div>
  <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID</th><th>Mahasiswa</th><th>Alat</th>
            <th>Jml</th><th>Tgl Pinjam</th><th>Tgl Kembali</th>
            <th>Keperluan</th><th>Diajukan</th><th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($data as $p): ?>
          <tr>
            <td class="td-code">#PJM-<?= str_pad($p['id'],4,'0',STR_PAD_LEFT) ?></td>
            <td>
              <div class="td-name"><?= htmlspecialchars($p['nama_user']) ?></div>
              <div class="td-sub"><?= htmlspecialchars($p['npm']) ?></div>
            </td>
            <td class="td-name"><?= htmlspecialchars($p['nama_alat']) ?></td>
            <td><?= $p['jumlah'] ?></td>
            <td><?= date('d M Y', strtotime($p['tgl_pinjam'])) ?></td>
            <td><?= date('d M Y', strtotime($p['tgl_kembali'])) ?></td>
            <td style="max-width:160px;font-size:12px;color:var(--tx2)">
              <?= htmlspecialchars(mb_strimwidth($p['keperluan'],0,60,'...')) ?>
            </td>
            <td style="font-size:11px;color:var(--tx3)"><?= date('d M, H:i', strtotime($p['created_at'])) ?></td>
            <td>
              <div style="display:flex;gap:6px">
                <form action="index.php?c=admin&a=verifikasi" method="POST">
                  <input type="hidden" name="id" value="<?= $p['id'] ?>">
                  <input type="hidden" name="action" value="setujui">
                  <button type="submit" class="btn btn-success btn-xs">✅ Setujui</button>
                </form>
                <form action="index.php?c=admin&a=verifikasi" method="POST" onsubmit="return confirm('Tolak peminjaman ini?')">
                  <input type="hidden" name="id" value="<?= $p['id'] ?>">
                  <input type="hidden" name="action" value="tolak">
                  <button type="submit" class="btn btn-danger btn-xs">❌ Tolak</button>
                </form>
                <button class="btn btn-ghost btn-xs"
                        onclick="openModal('modal-detail-<?= $p['id'] ?>')">Detail</button>
              </div>
            </td>
          </tr>

          <div id="modal-detail-<?= $p['id'] ?>" class="modal-bg">
            <div class="modal-box">
              <div class="modal-head">
                <div class="modal-title">Detail Pengajuan</div>
                <button class="modal-close" onclick="closeModal('modal-detail-<?= $p['id'] ?>')">✕</button>
              </div>
              <div class="detail-grid">
                <div class="detail-item"><label>Mahasiswa</label><span><?= htmlspecialchars($p['nama_user']) ?></span></div>
                <div class="detail-item"><label>NPM</label><span><?= htmlspecialchars($p['npm']) ?></span></div>
                <div class="detail-item"><label>Alat</label><span><?= htmlspecialchars($p['nama_alat']) ?></span></div>
                <div class="detail-item"><label>Jumlah</label><span><?= $p['jumlah'] ?> pcs</span></div>
                <div class="detail-item"><label>Tgl Pinjam</label><span><?= date('d M Y', strtotime($p['tgl_pinjam'])) ?></span></div>
                <div class="detail-item"><label>Tgl Kembali</label><span><?= date('d M Y', strtotime($p['tgl_kembali'])) ?></span></div>
              </div>
              <div class="divider"></div>
              <label class="form-label">Keperluan</label>
              <p style="font-size:13px;color:var(--tx2);line-height:1.6"><?= nl2br(htmlspecialchars($p['keperluan'])) ?></p>
              <div class="divider"></div>
              <div style="display:flex;gap:8px">
                <form action="index.php?c=admin&a=verifikasi" method="POST" style="flex:1">
                  <input type="hidden" name="id" value="<?= $p['id'] ?>">
                  <input type="hidden" name="action" value="setujui">
                  <button type="submit" class="btn btn-success" style="width:100%">✅ Setujui</button>
                </form>
                <form action="index.php?c=admin&a=verifikasi" method="POST" style="flex:1" onsubmit="return confirm('Tolak?')">
                  <input type="hidden" name="id" value="<?= $p['id'] ?>">
                  <input type="hidden" name="action" value="tolak">
                  <button type="submit" class="btn btn-danger" style="width:100%">❌ Tolak</button>
                </form>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>