<?php require_once __DIR__ . '/../layout/header_user.php'; ?>

<div class="user-hero" style="padding:20px 28px">
  <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
    <div>
      <div class="hero-greeting" style="font-size:18px">Katalog Alat Laboratorium</div>
      <div class="hero-sub"><?= count($alats) ?> alat ditemukan</div>
    </div>
    
    <form action="index.php" method="GET" style="display:flex;gap:8px;flex-wrap:wrap">
      <input type="hidden" name="c" value="user">
      <input type="hidden" name="a" value="katalog">

      <input class="form-control" style="width:200px;padding:8px 12px" type="text" name="q"
             placeholder="🔍 Cari alat..." value="<?= htmlspecialchars($cari) ?>">
      <select class="form-control" style="width:150px;padding:8px 12px" name="kategori">
        <option value="">Semua Kategori</option>
        <?php foreach ($kategoriList as $k): ?>
          <option value="<?= htmlspecialchars($k['kategori']) ?>" <?= $kat === $k['kategori'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($k['kategori']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <button class="btn btn-primary btn-sm" type="submit">Filter</button>
      
      <?php if ($cari || $kat): ?>
        <a href="index.php?c=user&a=katalog" class="btn btn-ghost btn-sm">Reset</a>
      <?php endif; ?>
    </form>
  </div>
</div>

<div class="user-body">
  <?php if (empty($alats)): ?>
    <div class="empty">
      <div class="empty-icon">🔍</div>
      <h4>Alat tidak ditemukan</h4>
      <p>Coba kata kunci yang berbeda</p>
    </div>
  <?php else: ?>
    <div class="katalog-grid">
      <?php foreach ($alats as $alat):
        $st = $alatModel->statusStok($alat);
        $stBadge = ['available'=>'s-good','limited'=>'s-limited','empty'=>'s-empty'];
        $stLabel = ['available'=>'Tersedia','limited'=>'Terbatas','empty'=>'Habis'];
        $pct = $alat['stok_total'] > 0 ? ($alat['stok_tersedia'] / $alat['stok_total']) * 100 : 0;
        $fillClass = $pct > 50 ? 'fill-high' : ($pct > 20 ? 'fill-mid' : 'fill-low');
        $emojiMap = ['Elektronika'=>'🔌','Kimia'=>'🧪','Biologi'=>'🔬','Fisika'=>'⚡','Umum'=>'🛠'];
        $emo = $emojiMap[$alat['kategori']] ?? '📦';
      ?>
        <div class="alat-card">
          <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:12px">
            <div class="alat-icon"><?= $emo ?></div>
            <span class="status <?= $stBadge[$st] ?>"><?= $stLabel[$st] ?></span>
          </div>
          <div class="alat-name"><?= htmlspecialchars($alat['nama']) ?></div>
          <div class="alat-code"><?= htmlspecialchars($alat['kode']) ?> · <?= htmlspecialchars($alat['kategori']) ?></div>
          
          <?php if ($alat['deskripsi']): ?>
            <p style="font-size:12px;color:var(--tx2);margin:8px 0;line-height:1.5"><?= htmlspecialchars($alat['deskripsi']) ?></p>
          <?php endif; ?>
          
          <div class="alat-stok" style="margin-top:8px">Stok: <strong><?= $alat['stok_tersedia'] ?></strong>/<?= $alat['stok_total'] ?> pcs</div>
          <div class="stok-bar"><div class="stok-fill <?= $fillClass ?>" style="width:<?= $pct ?>%"></div></div>
          
          <div style="margin-top:14px">
            <?php if ($st !== 'empty'): ?>
              <a href="index.php?c=user&a=pinjam&alat_id=<?= $alat['id'] ?>" class="btn btn-primary btn-sm" style="width:100%;justify-content:center">+ Ajukan Pinjam</a>
            <?php else: ?>
              <button class="btn btn-ghost btn-sm" style="width:100%;opacity:.5;cursor:not-allowed" disabled>Stok Habis</button>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
