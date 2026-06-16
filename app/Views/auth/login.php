<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>E-Lab Borrow — Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" href="assets/img/ICON.svg">
</head>
<body>
    <div class="login-page">
        <div class="login-glow1"></div>
        <div class="login-glow2"></div>
        
        <div class="login-wrap">
            <div class="login-left">
                <div class="login-left-glow"></div>
                <div class="ll-brand">
                    <img src="assets/img/ICON.svg" alt="E-Lab Borrow Logo" class="ll-logo">
                    <div>
                        <div class="ll-name">E-Lab</div>
                        <div class="ll-tagline">Sistem Peminjaman Alat Lab</div>
                    </div>
                </div>
                <h2 class="ll-headline">Portal Terpadu<br>Peminjaman Alat Laboratorium</h2>
                <p class="ll-desc">Gunakan kredensial akademik Anda untuk masuk ke sistem. Pastikan untuk selalu mengecek ketersediaan alat sebelum datang ke laboratorium.</p>
                <ul class="feature-list">
                    <li><span class="feat-dot"></span> Pengambilan &amp; pengembalian hanya pada jam operasional &nbsp; (08.00 - 17.30 WIB)</li>
                    <li><span class="feat-dot"></span> Wajib membawa KTM fisik saat validasi di lab</li>
                    <li><span class="feat-dot"></span> Keterlambatan pengembalian akan dikenakan sanksi sesuai ketentuan lab</li>
                    <li><span class="feat-dot"></span> Perpanjangan durasi pinjam hanya dapat dilakukan 1x sebelum tenggat waktu berakhir</li>
                </ul>
            </div>
        
            <div class="login-right">
                <h2 class="lr-title">Selamat Datang</h2>
                <p class="lr-sub">Masuk ke akun Anda untuk melanjutkan</p>
        
                <!--<div class="role-tabs" id="role-tabs">
                    <button class="role-tab active" onclick="setRole('mahasiswa',this)">👨‍🎓 Mahasiswa</button>
                    <button class="role-tab" onclick="setRole('admin',this)">🔧 Admin Lab</button>
                </div>-->
        
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">❌ <?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <?php
                if (!empty($_SESSION['flash'])) {
                    $flash = $_SESSION['flash'];
                    unset($_SESSION['flash']);
                    $ftype = $flash['type'] === 'success' ? 'alert-success' : 'alert-info';
                    echo '<div class="alert ' . $ftype . '">✅ ' . htmlspecialchars($flash['msg']) . '</div>';
                }
                ?>
        
                <form method="POST" action="index.php?c=auth&a=prosesLogin">
                    <div class="form-group">
                        <label class="form-label" id="id-label">NPM</label>
                        <input class="form-control" type="text" name="identifier" id="identifier"
                               placeholder="Masukkan NPM Anda" required autofocus>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <div class="input-pw-wrap">
                            <input class="form-control" type="password" name="password" id="pw" placeholder="Masukkan password" required>
                            <button type="button" class="pw-toggle2" onclick="togglePw('pw',this)">
                                <img src="https://cdn-icons-png.flaticon.com/128/8847/8847483.png" alt="icon" style="max-width: 20px; height: auto; padding-top: 5px;">
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-full">Login</button>
                </form>

                <div class="auth-divider">
                    <span>Belum punya akun?</span>
                </div>
                <a href="index.php?c=auth&a=register" class="btn btn-secondary btn-full">
                    Daftar Sekarang
                </a>
            </div>
        </div>
    </div>
    
    <script>
    function togglePw(id, btn) {
        const inp = document.getElementById(id);
        if (inp.type === 'password') {
            inp.type = 'text';
            btn.innerHTML = '<img src="https://cdn-icons-png.flaticon.com/128/9796/9796669.png" alt="icon" style="max-width: 20px; height: auto; padding-top: 5px;">';
        } else {
            inp.type = 'password';
            btn.innerHTML = '<img src="https://cdn-icons-png.flaticon.com/128/8847/8847483.png" alt="icon" style="max-width: 20px; height: auto; padding-top: 5px;">';
        }
    }
    function setRole(role, btn) {
        document.querySelectorAll('.role-tab').forEach(t => t.classList.remove('active'));
        btn.classList.add('active');
    }
    </script>
</body>
</html>
