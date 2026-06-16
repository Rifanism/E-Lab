<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>E-Lab Borrow — Registrasi</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" href="assets/img/ICON.svg">
</head>
<body>
    <div class="login-page">
        <div class="login-glow1"></div>
        <div class="login-glow2"></div>

        <div class="login-wrap register-wrap">
            <!-- Panel Kiri -->
            <div class="login-left">
                <div class="login-left-glow"></div>
                <div class="ll-brand">
                    <img src="assets/img/ICON.svg" alt="E-Lab Borrow Logo" class="ll-logo">
                    <div>
                        <div class="ll-name">E-Lab</div>
                        <div class="ll-tagline">Sistem Peminjaman Alat Lab</div>
                    </div>
                </div>
                <h2 class="ll-headline">Bergabung dengan<br>E-Lab</h2>
                <p class="ll-desc">Daftarkan diri Anda untuk mulai meminjam peralatan laboratorium secara online. Proses cepat, mudah, dan transparan.</p>
                <ul class="feature-list">
                    <li><span class="feat-dot"></span> Pengambilan &amp; pengembalian hanya pada jam operasional &nbsp; (08.00 - 17.30 WIB)</li>
                    <li><span class="feat-dot"></span> Wajib membawa KTM fisik saat validasi di lab</li>
                    <li><span class="feat-dot"></span> Keterlambatan pengembalian akan dikenakan sanksi sesuai ketentuan lab</li>
                    <li><span class="feat-dot"></span> Perpanjangan durasi pinjam hanya dapat dilakukan 1x sebelum tenggat waktu berakhir</li>
                </ul>
            </div>

            <!-- Panel Kanan — Form Registrasi -->
            <div class="login-right register-right">
                <h2 class="lr-title">Buat Akun Baru</h2>
                <p class="lr-sub">Isi data di bawah untuk mendaftar sebagai mahasiswa</p>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">❌ <?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST" action="index.php?c=auth&a=prosesRegister" novalidate>

                    <div class="form-group">
                        <label class="form-label">Nama Lengkap <span class="req">*</span></label>
                        <input class="form-control" type="text" name="nama"
                               value="<?= htmlspecialchars($old['nama'] ?? '') ?>"
                               placeholder="Masukkan nama lengkap Anda" required autofocus>
                    </div>

                    <div class="form-group">
                        <div class="form-group">
                            <label class="form-label">NPM <span class="req">*</span></label>
                            <input class="form-control" type="text" name="npm"
                                   value="<?= htmlspecialchars($old['npm'] ?? '') ?>"
                                   placeholder="Masukkan NPM Anda" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email <span class="req">*</span></label>
                        <input class="form-control" type="email" name="email"
                               value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                               placeholder="email@students.unila.ac.id" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Password <span class="req">*</span></label>
                            <div class="input-pw-wrap">
                                <input class="form-control" type="password" name="password"
                                       id="pw1" placeholder="Min. 6 karakter" required>
                                <button type="button" class="pw-toggle" onclick="togglePw('pw1',this)">
                                    <img src="https://cdn-icons-png.flaticon.com/128/8847/8847483.png" alt="icon" style="max-width: 20px; height: auto; padding-top: 5px;">
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Konfirmasi Password <span class="req">*</span></label>
                            <div class="input-pw-wrap">
                                <input class="form-control" type="password" name="konfirm"
                                       id="pw2" placeholder="Ulangi password" required>
                                <button type="button" class="pw-toggle" onclick="togglePw('pw2',this)">
                                    <img src="https://cdn-icons-png.flaticon.com/128/8847/8847483.png" alt="icon" style="max-width: 20px; height: auto; padding-top: 5px;">
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="pw-match-msg" style="margin:-10px 0 14px;font-size:12px;display:none;"></div>

                    <button type="submit" class="btn btn-primary btn-full" id="submit-btn">
                        Daftar Sekarang
                    </button>
                </form>

                <div class="auth-divider">
                    <span>Sudah punya akun?</span>
                </div>
                <a href="index.php?c=auth&a=login" class="btn btn-secondary btn-full">
                    Masuk ke Akun
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

    const pw1 = document.getElementById('pw1');
    const pw2 = document.getElementById('pw2');
    const msg = document.getElementById('pw-match-msg');
    const btn = document.getElementById('submit-btn');

    function checkMatch() {
        if (pw2.value === '') { msg.style.display = 'none'; return; }
        if (pw1.value === pw2.value) {
            msg.style.display = 'block';
            msg.style.color   = '#06d6a0';
            msg.textContent   = '✔ Password cocok';
        } else {
            msg.style.display = 'block';
            msg.style.color   = '#ef4444';
            msg.textContent   = '✖ Password tidak cocok';
        }
    }

    pw1.addEventListener('input', checkMatch);
    pw2.addEventListener('input', checkMatch);
    </script>
</body>
</html>
