<?php

require_once __DIR__ . '/../Helpers/AuthHelper.php';
require_once __DIR__ . '/../Models/AlatModel.php';
require_once __DIR__ . '/../Models/PeminjamanModel.php';
require_once __DIR__ . '/../Models/PerpanjanganModel.php';

class UserController {
    private $alatModel;
    private $peminjamanModel;
    private $perpanjanganModel;

    public function __construct() {
        AuthHelper::requireUser();
        $this->alatModel         = new AlatModel();
        $this->peminjamanModel   = new PeminjamanModel();
        $this->perpanjanganModel = new PerpanjanganModel();
    }

    public function dashboard() {
        $userId = (int)$_SESSION['user_id'];
        $stats       = $this->peminjamanModel->getStatistikUserDashboard($userId);
        $pinjamAktif = $this->peminjamanModel->getPeminjamanAktifUser($userId);
        $katalog     = $this->alatModel->getKatalogPreview(3);
        $badgeCount  = $this->peminjamanModel->getBadgeCountUser($userId);
        $activeMenu  = 'dashboard';
        $alatModel   = $this->alatModel;

        require_once __DIR__ . '/../Views/user/dashboard.php';
    }

    public function katalog() {
        $cari = trim($_GET['q'] ?? '');
        $kat  = trim($_GET['kategori'] ?? '');
        $alats        = $this->alatModel->getAlatFiltered($cari, $kat);
        $kategoriList = $this->alatModel->getKategoriList();
        $userId       = (int)$_SESSION['user_id'];
        $badgeCount   = $this->peminjamanModel->getBadgeCountUser($userId);
        $activeMenu   = 'katalog';
        $alatModel    = $this->alatModel;

        require_once __DIR__ . '/../Views/user/katalog.php';
    }

    public function pinjam() {
        $uid     = (int)$_SESSION['user_id'];
        $preAlat = (int)($_GET['alat_id'] ?? 0);
        $error   = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $alatId     = (int)$_POST['alat_id'];
            $jumlah     = (int)$_POST['jumlah'];
            $tglPinjam  = $_POST['tgl_pinjam'] ?? '';
            $tglKembali = $_POST['tgl_kembali'] ?? '';
            $keperluan  = trim($_POST['keperluan'] ?? '');

            if (!$alatId || !$jumlah || !$tglPinjam || !$tglKembali || !$keperluan) {
                $error = 'Semua field wajib diisi.';
            } else {
                $result = $this->peminjamanModel->ajukanPeminjaman($uid, $alatId, $jumlah, $tglPinjam, $tglKembali, $keperluan);
                if ($result['ok']) {
                    $_SESSION['flash'] = ['msg' => $result['msg'], 'type' => 'success'];
                    header('Location: index.php?c=user&a=riwayat');
                    exit;
                } else {
                    $error = $result['msg'];
                }
            }
        }

        $alats        = $this->alatModel->getAllAlat();
        $selectedAlat = $preAlat ? $this->alatModel->getAlatById($preAlat) : null;
        $badgeCount   = $this->peminjamanModel->getBadgeCountUser($uid);
        $activeMenu   = 'pinjam';

        require_once __DIR__ . '/../Views/user/pinjam.php';
    }

    public function perpanjangan() {
        $uid   = (int)$_SESSION['user_id'];
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pid     = (int)$_POST['peminjaman_id'];
            $tglBaru = $_POST['tgl_kembali_baru'] ?? '';
            $alasan  = trim($_POST['alasan'] ?? '');
            
            if (!$pid || !$tglBaru || !$alasan) {
                $error = 'Semua field wajib diisi.';
            } else {
                $res = $this->perpanjanganModel->ajukanPerpanjangan($pid, $uid, $tglBaru, $alasan);
                if ($res['ok']) {
                    $_SESSION['flash'] = ['msg' => $res['msg'], 'type' => 'success'];
                    header('Location: index.php?c=user&a=perpanjangan');
                    exit;
                } else {
                    $error = $res['msg'];
                }
            }
        }

        $bisa       = $this->peminjamanModel->getBisaDiperpanjang($uid);
        $riwayat    = $this->perpanjanganModel->getPerpanjanganUser($uid);
        $badgeCount = $this->peminjamanModel->getBadgeCountUser($uid);
        $preId      = (int)($_GET['pid'] ?? 0);
        $prePinjam  = null;

        if ($preId) {
            foreach ($bisa as $b) { 
                if ($b['id'] == $preId) { 
                    $prePinjam = $b; 
                    break; 
                } 
            }
        }

        $activeMenu = 'perpanjangan';
        require_once __DIR__ . '/../Views/user/perpanjangan.php';
    }

    public function riwayat() {
        $uid        = (int)$_SESSION['user_id'];
        $data       = $this->peminjamanModel->getPeminjamanUser($uid);
        $badgeCount = $this->peminjamanModel->getBadgeCountUser($uid);
        $activeMenu = 'riwayat';

        $statusMap = [
            'pending'      => ['label' => 'Menunggu',     'class' => 's-pending'],
            'disetujui'    => ['label' => 'Disetujui',    'class' => 's-approved'],
            'perpanjangan' => ['label' => 'Diperpanjang', 'class' => 's-extended'],
            'selesai'      => ['label' => 'Selesai',      'class' => 's-returned'],
            'ditolak'      => ['label' => 'Ditolak',      'class' => 's-rejected'],
        ];

        require_once __DIR__ . '/../Views/user/riwayat.php';
    }
}