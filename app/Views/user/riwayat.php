<?php require_once __DIR__ . '/../layout/header_user.php'; ?>

<div class="user-hero" style="padding:20px 28px">
  <div class="hero-greeting" style="font-size:18px">Riwayat Peminjaman</div>
  <div class="hero-sub"><?= count($data) ?> total transaksi</div>
</div>

<div class="user-body">
  <?php if (empty($data)): ?>
    <div class="empty">
      <div class="empty-icon">📋</div>
      <h4>Belum ada riwayat</h4>
      <p><a href="index.php?c=user&a=katalog">Kunjungi katalog</a> untuk meminjam alat</p>
    </div>
  <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Alat</th>
            <th>Tgl Pinjam</th>
            <th>Tgl Kembali</th>
            <th>Jml</th>
            <th>Status</th>
            <th>Kondisi</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($data as $p):
            $sm = $statusMap[$p['status']] ?? ['label'=>$p['status'],'class'=>''];
            $late = in_array($p['status'],['disetujui','perpanjangan']) && strtotime($p['tgl_kembali']) < time();
          ?>
          <tr>
            <td class="td-code">#PJM-<?= str_pad($p['id'],4,'0',STR_PAD_LEFT) ?></td>
            <td>
              <div class="td-name"><?= htmlspecialchars($p['nama_alat']) ?></div>
              <div class="td-sub"><?= htmlspecialchars($p['kode_alat']) ?></div>
            </td>
            <td><?= date('d M Y', strtotime($p['tgl_pinjam'])) ?></td>
            <td <?= $late ? 'style="color:var(--danger)"' : '' ?>>
              <?= date('d M Y', strtotime($p['tgl_kembali'])) ?>
              <?= $late ? ' ⚠️' : '' ?>
            </td>
            <td><?= $p['jumlah'] ?></td>
            <td><span class="status <?= $sm['class'] ?>"><?= $sm['label'] ?></span></td>
            <td>
              <?php if ($p['status'] === 'selesai'): ?>
                <div style="font-size:11px;line-height:1.4">
                  <span style="color:var(--success)">✅ <?= $p['jml_baik'] ?> Baik</span>
                  <?php if($p['jml_rusak'] > 0): ?><br><span style="color:var(--warn)">🔧 <?= $p['jml_rusak'] ?> Rusak</span><?php endif; ?>
                  <?php if($p['jml_hilang'] > 0): ?><br><span style="color:var(--danger)">❌ <?= $p['jml_hilang'] ?> Hilang</span><?php endif; ?>
                </div>
              <?php else: ?>
                —
              <?php endif; ?>
            </td>
            <td style="display:flex;gap:6px;flex-wrap:wrap">
              <button class="btn btn-ghost btn-xs" onclick="openModal('modal-detail-<?= $p['id'] ?>')">Detail</button>
              <?php if ($p['status'] === 'disetujui'): ?>
                <a href="index.php?c=user&a=perpanjangan&pid=<?= $p['id'] ?>" class="btn btn-ghost btn-xs">Perpanjang</a>
              <?php endif; ?>
            </td>
          </tr>

          <div id="modal-detail-<?= $p['id'] ?>" class="modal-bg">
            <div class="modal-box">
              <div class="modal-head">
                <div class="modal-title">Detail #PJM-<?= str_pad($p['id'],4,'0',STR_PAD_LEFT) ?></div>
                <button class="modal-close" onclick="closeModal('modal-detail-<?= $p['id'] ?>')">✕</button>
              </div>
              <div class="detail-grid">
                <div class="detail-item"><label>Alat</label><span><?= htmlspecialchars($p['nama_alat']) ?></span></div>
                <div class="detail-item"><label>Status</label><span class="status <?= $sm['class'] ?>"><?= $sm['label'] ?></span></div>
                <div class="detail-item"><label>Jumlah</label><span><?= $p['jumlah'] ?> pcs</span></div>
                <div class="detail-item"><label>Tgl Pinjam</label><span><?= date('d M Y', strtotime($p['tgl_pinjam'])) ?></span></div>
                <div class="detail-item"><label>Tgl Kembali</label><span><?= date('d M Y', strtotime($p['tgl_kembali'])) ?></span></div>
                <?php if ($p['tgl_dikembalikan']): ?>
                  <div class="detail-item"><label>Dikembalikan</label><span><?= date('d M Y', strtotime($p['tgl_dikembalikan'])) ?></span></div>
                <?php endif; ?>
              </div>
              <div class="divider"></div>
              <div class="form-group">
                <label class="form-label">Keperluan</label>
                <p style="font-size:13px;color:var(--tx2);line-height:1.6"><?= nl2br(htmlspecialchars($p['keperluan'])) ?></p>
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
