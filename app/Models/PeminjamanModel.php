<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/AlatModel.php';

class PeminjamanModel {
    private $db;
    private $alatModel;

    public function __construct() {
        $this->db = getDB();
        $this->alatModel = new AlatModel();
    }

    public function ajukanPeminjaman(int $userId, int $alatId, int $jumlah, string $tglPinjam, string $tglKembali, string $keperluan): array {
        $alat = $this->alatModel->getAlatById($alatId);
        
        if (!$alat) return ['ok' => false, 'msg' => 'Alat tidak ditemukan.'];
        if ($alat['stok_tersedia'] < $jumlah) {
            return ['ok' => false, 'msg' => "Stok tidak cukup. Tersedia: {$alat['stok_tersedia']} pcs."];
        }

        $diff = (strtotime($tglKembali) - strtotime($tglPinjam)) / 86400;
        if ($diff < 1 || $diff > 7) {
            return ['ok' => false, 'msg' => 'Durasi peminjaman 1–7 hari.'];
        }

        $kep = $this->db->real_escape_string($keperluan);
        $ok  = $this->db->query("INSERT INTO peminjaman (user_id,alat_id,jumlah,tgl_pinjam,tgl_kembali,keperluan,status)
                                 VALUES ($userId,$alatId,$jumlah,'$tglPinjam','$tglKembali','$kep','pending')");
                                 
        if ($ok) {
            $this->alatModel->kurangiStok($alatId, $jumlah);
            return ['ok' => true, 'msg' => 'Pengajuan berhasil dikirim.'];
        }
        
        return ['ok' => false, 'msg' => 'Gagal menyimpan data.'];
    }

    public function getPeminjamanUser(int $userId): array {
        $res = $this->db->query("SELECT p.*,a.nama AS nama_alat, a.kode AS kode_alat
                                 FROM peminjaman p
                                 JOIN alat a ON a.id=p.alat_id
                                 WHERE p.user_id=$userId
                                 ORDER BY p.created_at DESC");
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getAllPeminjaman(): array {
        $res = $this->db->query("SELECT p.*,a.nama AS nama_alat,u.nama AS nama_user,u.npm
                                 FROM peminjaman p
                                 JOIN alat a ON a.id=p.alat_id
                                 JOIN users u ON u.id=p.user_id
                                 ORDER BY p.created_at DESC");
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getPeminjamanAktif(): array {
        $res = $this->db->query("SELECT p.*,a.nama AS nama_alat,a.id AS alat_id_ref,u.nama AS nama_user,u.npm
                                 FROM peminjaman p
                                 JOIN alat a ON a.id=p.alat_id
                                 JOIN users u ON u.id=p.user_id
                                 WHERE p.status IN ('disetujui','perpanjangan')
                                 ORDER BY p.tgl_kembali ASC");
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getPeminjamanById(int $id): ?array {
        $res = $this->db->query("SELECT p.*,a.nama AS nama_alat,a.kode AS kode_alat,
                                        u.nama AS nama_user,u.npm
                                 FROM peminjaman p
                                 JOIN alat a ON a.id=p.alat_id
                                 JOIN users u ON u.id=p.user_id
                                 WHERE p.id=$id LIMIT 1");
        return ($res && $res->num_rows) ? $res->fetch_assoc() : null;
    }

    public function setujuiPeminjaman(int $id): bool {
        return $this->db->query("UPDATE peminjaman SET status='disetujui' WHERE id=$id AND status='pending'");
    }

    public function tolakPeminjaman(int $id): bool {
        $p = $this->getPeminjamanById($id);
        if (!$p) return false;
        
        if ($this->db->query("UPDATE peminjaman SET status='ditolak' WHERE id=$id")) {
            $this->alatModel->tambahStok((int)$p['alat_id'], (int)$p['jumlah']);
            return true;
        }
        return false;
    }

    public function konfirmasiKembali(int $id, int $baik, int $rusak, int $hilang): bool {
        $this->db->begin_transaction();
        try {
            $pjm = $this->db->query("SELECT alat_id, jumlah FROM peminjaman WHERE id = $id")->fetch_assoc();
            
            if (!$pjm || ($baik + $rusak + $hilang) !== (int)$pjm['jumlah']) {
                throw new Exception("Jumlah pengembalian tidak valid.");
            }

            $alatId = (int)$pjm['alat_id'];
            
            $this->db->query("UPDATE alat SET 
                stok_tersedia = stok_tersedia + $baik,
                stok_rusak = stok_rusak + $rusak,
                stok_total = stok_total - $hilang
                WHERE id = $alatId");

            $this->db->query("UPDATE peminjaman SET 
                status = 'selesai',
                jml_baik = $baik,
                jml_rusak = $rusak,
                jml_hilang = $hilang,
                tgl_dikembalikan = NOW()
                WHERE id = $id");

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    public function getStatistikDashboard(): array {
        $totalAlat = $this->db->query("SELECT COUNT(*) AS c FROM alat")->fetch_assoc()['c'];
        $aktif = $this->db->query("SELECT COUNT(*) AS c FROM peminjaman WHERE status IN ('disetujui','perpanjangan')")->fetch_assoc()['c'];
        $pending = $this->db->query("SELECT COUNT(*) AS c FROM peminjaman WHERE status='pending'")->fetch_assoc()['c'];
        $extPending = $this->db->query("SELECT COUNT(*) AS c FROM perpanjangan WHERE status='pending'")->fetch_assoc()['c'];
        $late = $this->db->query("SELECT COUNT(*) AS c FROM peminjaman WHERE status IN ('disetujui','perpanjangan') AND tgl_kembali < CURDATE()")->fetch_assoc()['c'];
        $bulanIni = $this->db->query("SELECT COUNT(*) AS c FROM peminjaman WHERE MONTH(created_at)=MONTH(NOW())")->fetch_assoc()['c'];

        return [
            'totalAlat'  => $totalAlat,
            'aktif'      => $aktif,
            'pending'    => $pending,
            'extPending' => $extPending,
            'late'       => $late,
            'bulanIni'   => $bulanIni
        ];
    }

    public function getPengajuanTerbaru(int $limit = 5): array {
        $res = $this->db->query(
            "SELECT p.*,a.nama AS nama_alat,u.nama AS nama_user,u.npm
             FROM peminjaman p 
             JOIN alat a ON a.id=p.alat_id 
             JOIN users u ON u.id=p.user_id
             WHERE p.status='pending' 
             ORDER BY p.created_at DESC 
             LIMIT $limit"
        );
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getStokKritis(int $limit = 5): array {
        $res = $this->db->query(
            "SELECT * FROM alat 
             WHERE stok_tersedia <= FLOOR(stok_total*0.4) 
             ORDER BY stok_tersedia ASC 
             LIMIT $limit"
        );
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getStatistikMonitoring(): array {
        $aktif = $this->db->query("SELECT COUNT(*) AS c FROM peminjaman WHERE status IN ('disetujui','perpanjangan')")->fetch_assoc()['c'] ?? 0;
        $late  = $this->db->query("SELECT COUNT(*) AS c FROM peminjaman WHERE status IN ('disetujui','perpanjangan') AND tgl_kembali < CURDATE()")->fetch_assoc()['c'] ?? 0;
        
        // UPDATE QUERY INI: Gunakan SUM(jml_rusak)
        $rusak = $this->db->query("SELECT SUM(jml_rusak) AS c FROM peminjaman")->fetch_assoc()['c'] ?? 0;
        
        $bulan = $this->db->query("SELECT COUNT(*) AS c FROM peminjaman WHERE MONTH(created_at)=MONTH(NOW())")->fetch_assoc()['c'] ?? 0;

        return [
            'aktif' => $aktif,
            'late'  => $late,
            'rusak' => $rusak ?: 0,
            'bulan' => $bulan
        ];
    }

    public function getStatistikUserDashboard(int $userId): array {
        $aktif   = $this->db->query("SELECT COUNT(*) AS c FROM peminjaman WHERE user_id=$userId AND status IN ('disetujui','perpanjangan')")->fetch_assoc()['c'] ?? 0;
        $pending = $this->db->query("SELECT COUNT(*) AS c FROM peminjaman WHERE user_id=$userId AND status='pending'")->fetch_assoc()['c'] ?? 0;
        $selesai = $this->db->query("SELECT COUNT(*) AS c FROM peminjaman WHERE user_id=$userId AND status='selesai'")->fetch_assoc()['c'] ?? 0;
        
        return [
            'aktif'   => $aktif,
            'pending' => $pending,
            'selesai' => $selesai
        ];
    }
    
    public function getPeminjamanAktifUser(int $userId, int $limit = 5): array {
        $res = $this->db->query(
            "SELECT p.*, a.nama AS nama_alat, a.kode AS kode_alat
             FROM peminjaman p
             JOIN alat a ON a.id = p.alat_id
             WHERE p.user_id=$userId AND p.status IN ('disetujui','perpanjangan','pending')
             ORDER BY p.tgl_kembali ASC LIMIT $limit"
        );
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getBadgeCountUser(int $userId): int {
        $res = $this->db->query("SELECT COUNT(*) AS cnt FROM peminjaman WHERE user_id=$userId AND status IN ('pending','disetujui')");
        return $res->fetch_assoc()['cnt'] ?? 0;
    }

    public function getBisaDiperpanjang(int $userId): array {
        $res = $this->db->query(
            "SELECT p.*, a.nama AS nama_alat
             FROM peminjaman p 
             JOIN alat a ON a.id = p.alat_id
             WHERE p.user_id = $userId 
               AND p.status = 'disetujui'
               AND p.id NOT IN (SELECT peminjaman_id FROM perpanjangan)
             ORDER BY p.tgl_kembali ASC"
        );
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getPeminjamanPending(): array {
        $res = $this->db->query(
            "SELECT p.*, a.nama AS nama_alat, u.nama AS nama_user, u.npm
             FROM peminjaman p
             JOIN alat a ON a.id = p.alat_id
             JOIN users u ON u.id = p.user_id
             WHERE p.status = 'pending'
             ORDER BY p.created_at DESC"
        );
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }
}