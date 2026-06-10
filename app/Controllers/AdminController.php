<?php

require_once __DIR__ . '/../Helpers/AuthHelper.php';
require_once __DIR__ . '/../Models/PeminjamanModel.php';
require_once __DIR__ . '/../Models/AlatModel.php';
require_once __DIR__ . '/../Models/PerpanjanganModel.php';

class AdminController {
    private $peminjamanModel;
    private $alatModel;
    private $perpanjanganModel;

    public function __construct() {
        AuthHelper::requireAdmin();
        $this->peminjamanModel   = new PeminjamanModel();
        $this->alatModel         = new AlatModel();
        $this->perpanjanganModel = new PerpanjanganModel();
    }

    public function dashboard() {
        $stats            = $this->peminjamanModel->getStatistikDashboard();
        $pengajuanTerbaru = $this->peminjamanModel->getPengajuanTerbaru();
        $stokKritis       = $this->peminjamanModel->getStokKritis();

        $activeMenu    = 'dashboard';
        $pendingPinjam = $stats['pending'];
        $pendingExt    = $stats['extPending'];

        require_once __DIR__ . '/../Views/admin/dashboard.php';
    }

    public function alat() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'tambah') {
                if ($this->alatModel->tambahAlat($_POST)) {
                    $_SESSION['flash'] = ['msg' => 'Alat berhasil ditambahkan.', 'type' => 'success'];
                } else {
                    $_SESSION['flash'] = ['msg' => 'Gagal menambahkan alat.', 'type' => 'error'];
                }
            } elseif ($action === 'edit') {
                $id = (int)$_POST['id'];
                if ($this->alatModel->editAlat($id, $_POST)) {
                    $_SESSION['flash'] = ['msg' => 'Alat berhasil diperbarui.', 'type' => 'success'];
                } else {
                    $_SESSION['flash'] = ['msg' => 'Gagal memperbarui alat.', 'type' => 'error'];
                }
            } elseif ($action === 'hapus') {
                $id = (int)$_POST['id'];
                if ($this->alatModel->hapusAlat($id)) {
                    $_SESSION['flash'] = ['msg' => 'Alat berhasil dihapus.', 'type' => 'success'];
                } else {
                    $_SESSION['flash'] = ['msg' => 'Alat tidak dapat dihapus (masih ada transaksi aktif).', 'type' => 'error'];
                }
            }
            header('Location: index.php?c=admin&a=alat');
            exit;
        }

        $alats         = $this->alatModel->getAllAlat();
        $stats         = $this->peminjamanModel->getStatistikDashboard();
        $activeMenu    = 'alat';
        $pendingPinjam = $stats['pending'];
        $pendingExt    = $stats['extPending'];
        $alatModel     = $this->alatModel;

        require_once __DIR__ . '/../Views/admin/alat.php';
    }

    public function monitoring() {
        $stats         = $this->peminjamanModel->getStatistikMonitoring();
        $semua         = $this->peminjamanModel->getAllPeminjaman();
        
        $headerStats   = $this->peminjamanModel->getStatistikDashboard();
        $pendingPinjam = $headerStats['pending'];
        $pendingExt    = $headerStats['extPending'];

        $statusMap = [
            'pending'      => ['label' => 'Menunggu',     'class' => 's-pending'],
            'disetujui'    => ['label' => 'Aktif',        'class' => 's-approved'],
            'perpanjangan' => ['label' => 'Diperpanjang', 'class' => 's-extended'],
            'selesai'      => ['label' => 'Selesai',      'class' => 's-returned'],
            'ditolak'      => ['label' => 'Ditolak',      'class' => 's-rejected'],
        ];

        $activeMenu = 'monitoring';
        require_once __DIR__ . '/../Views/admin/monitoring.php';
    }

    public function pengembalian() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id     = (int)$_POST['id'];
            $baik   = (int)$_POST['jml_baik'];
            $rusak  = (int)$_POST['jml_rusak'];
            $hilang = (int)$_POST['jml_hilang'];
            
            if ($this->peminjamanModel->konfirmasiKembali($id, $baik, $rusak, $hilang)) {
                $_SESSION['flash'] = ['msg' => "Pengembalian ID #$id dikonfirmasi. Baik: $baik, Rusak: $rusak, Hilang: $hilang.", 'type' => 'success'];
            } else {
                $_SESSION['flash'] = ['msg' => 'Gagal mengkonfirmasi. Pastikan jumlah total sesuai dengan yang dipinjam.', 'type' => 'error'];
            }
            header('Location: index.php?c=admin&a=pengembalian');
            exit;
        }

        $data = $this->peminjamanModel->getPeminjamanAktif();
        $headerStats   = $this->peminjamanModel->getStatistikDashboard();
        $pendingPinjam = $headerStats['pending'];
        $pendingExt    = $headerStats['extPending'];
        $activeMenu    = 'pengembalian';

        require_once __DIR__ . '/../Views/admin/pengembalian.php';
    }

    public function perpanjangan() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id     = (int)$_POST['id'];
            $action = $_POST['action'] ?? '';
            
            if ($action === 'setujui') {
                if ($this->perpanjanganModel->setujuiPerpanjangan($id)) {
                    $_SESSION['flash'] = ['msg' => 'Perpanjangan disetujui.', 'type' => 'success'];
                } else {
                    $_SESSION['flash'] = ['msg' => 'Gagal menyetujui.', 'type' => 'error'];
                }
            } elseif ($action === 'tolak') {
                if ($this->perpanjanganModel->tolakPerpanjangan($id)) {
                    $_SESSION['flash'] = ['msg' => 'Perpanjangan ditolak.', 'type' => 'success'];
                } else {
                    $_SESSION['flash'] = ['msg' => 'Gagal menolak.', 'type' => 'error'];
                }
            }
            
            header('Location: index.php?c=admin&a=perpanjangan');
            exit;
        }

        $data = $this->perpanjanganModel->getPerpanjanganPending();
        $headerStats   = $this->peminjamanModel->getStatistikDashboard();
        $pendingPinjam = $headerStats['pending'];
        $pendingExt    = $headerStats['extPending'];
        $activeMenu    = 'perpanjangan';

        require_once __DIR__ . '/../Views/admin/perpanjangan.php';
    }

    public function verifikasi() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id     = (int)$_POST['id'];
            $action = $_POST['action'] ?? '';
            
            if ($action === 'setujui') {
                if ($this->peminjamanModel->setujuiPeminjaman($id)) {
                    $_SESSION['flash'] = ['msg' => 'Peminjaman disetujui.', 'type' => 'success'];
                } else {
                    $_SESSION['flash'] = ['msg' => 'Gagal menyetujui.', 'type' => 'error'];
                }
            } elseif ($action === 'tolak') {
                if ($this->peminjamanModel->tolakPeminjaman($id)) {
                    $_SESSION['flash'] = ['msg' => 'Peminjaman ditolak & stok dikembalikan.', 'type' => 'success'];
                } else {
                    $_SESSION['flash'] = ['msg' => 'Gagal menolak.', 'type' => 'error'];
                }
            }
            
            header('Location: index.php?c=admin&a=verifikasi');
            exit;
        }

        $data = $this->peminjamanModel->getPeminjamanPending();
        $headerStats   = $this->peminjamanModel->getStatistikDashboard();
        $pendingPinjam = $headerStats['pending'];
        $pendingExt    = $headerStats['extPending'];
        $activeMenu    = 'verifikasi';

        require_once __DIR__ . '/../Views/admin/verifikasi.php';
    }

    public function rusak() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id     = (int)$_POST['id'];
            $jumlah = (int)$_POST['jumlah'];
            $action = $_POST['action'];

            if ($action === 'perbaiki') {
                if ($this->alatModel->perbaikiAlat($id, $jumlah)) {
                    $_SESSION['flash'] = ['msg' => "$jumlah alat berhasil diperbaiki dan kembali ke stok tersedia.", 'type' => 'success'];
                }
            } elseif ($action === 'buang') {
                if ($this->alatModel->buangAlat($id, $jumlah)) {
                    $_SESSION['flash'] = ['msg' => "$jumlah alat rusak telah dibuang (stok total berkurang).", 'type' => 'success'];
                }
            }
            header('Location: index.php?c=admin&a=rusak');
            exit;
        }

        $data = $this->alatModel->getAlatRusak();

        $headerStats   = $this->peminjamanModel->getStatistikDashboard();
        $pendingPinjam = $headerStats['pending'];
        $pendingExt    = $headerStats['extPending'];
        $activeMenu    = 'rusak';

        require_once __DIR__ . '/../Views/admin/rusak.php';
    }
}