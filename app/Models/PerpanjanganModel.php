<?php

require_once __DIR__ . '/../../config/database.php';

class PerpanjanganModel {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function ajukanPerpanjangan(int $peminjamanId, int $userId, string $tglBaru, string $alasan): array {
        $cek = $this->db->query("SELECT id FROM perpanjangan WHERE peminjaman_id=$peminjamanId LIMIT 1");
        if ($cek && $cek->num_rows > 0) {
            return ['ok' => false, 'msg' => 'Peminjaman ini sudah pernah diperpanjang.'];
        }

        $p = $this->db->query("SELECT * FROM peminjaman WHERE id=$peminjamanId AND user_id=$userId AND status='disetujui' LIMIT 1");
        if (!$p || !$p->num_rows) {
            return ['ok' => false, 'msg' => 'Peminjaman tidak ditemukan atau tidak bisa diperpanjang.'];
        }

        $row = $p->fetch_assoc();
        $diffMax = (strtotime($row['tgl_kembali']) + 7 * 86400);
        if (strtotime($tglBaru) > $diffMax) {
            return ['ok' => false, 'msg' => 'Maksimal perpanjangan 7 hari dari tanggal kembali.'];
        }

        $al = $this->db->real_escape_string($alasan);
        $ok = $this->db->query("INSERT INTO perpanjangan (peminjaman_id,tgl_kembali_baru,alasan,status)
                                VALUES ($peminjamanId,'$tglBaru','$al','pending')");
        if ($ok) {
            $this->db->query("UPDATE peminjaman SET status='perpanjangan' WHERE id=$peminjamanId");
            return ['ok' => true, 'msg' => 'Pengajuan perpanjangan berhasil dikirim.'];
        }
        return ['ok' => false, 'msg' => 'Gagal menyimpan perpanjangan.'];
    }

    public function getPerpanjanganPending(): array {
        $res = $this->db->query("SELECT ex.*,p.tgl_kembali AS tgl_lama,
                                        a.nama AS nama_alat,u.nama AS nama_user,u.npm
                                 FROM perpanjangan ex
                                 JOIN peminjaman p ON p.id=ex.peminjaman_id
                                 JOIN alat a ON a.id=p.alat_id
                                 JOIN users u ON u.id=p.user_id
                                 WHERE ex.status='pending'
                                 ORDER BY ex.created_at ASC");
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getPerpanjanganUser(int $userId): array {
        $res = $this->db->query("SELECT ex.*,p.tgl_kembali AS tgl_lama,a.nama AS nama_alat
                                 FROM perpanjangan ex
                                 JOIN peminjaman p ON p.id=ex.peminjaman_id
                                 JOIN alat a ON a.id=p.alat_id
                                 WHERE p.user_id=$userId
                                 ORDER BY ex.created_at DESC");
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function setujuiPerpanjangan(int $id): bool {
        $res = $this->db->query("SELECT * FROM perpanjangan WHERE id=$id LIMIT 1");
        if (!$res || !$res->num_rows) return false;
        
        $ex = $res->fetch_assoc();
        if ($this->db->query("UPDATE perpanjangan SET status='disetujui' WHERE id=$id")) {
            $this->db->query("UPDATE peminjaman SET tgl_kembali='{$ex['tgl_kembali_baru']}',status='disetujui'
                              WHERE id={$ex['peminjaman_id']}");
            return true;
        }
        return false;
    }

    public function tolakPerpanjangan(int $id): bool {
        $res = $this->db->query("SELECT peminjaman_id FROM perpanjangan WHERE id=$id LIMIT 1");
        if (!$res || !$res->num_rows) return false;
        
        $pid = $res->fetch_assoc()['peminjaman_id'];
        if ($this->db->query("UPDATE perpanjangan SET status='ditolak' WHERE id=$id")) {
            $this->db->query("UPDATE peminjaman SET status='disetujui' WHERE id=$pid");
            return true;
        }
        return false;
    }
}