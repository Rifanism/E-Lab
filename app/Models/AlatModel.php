<?php

require_once __DIR__ . '/../../config/database.php';

class AlatModel {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function getAllAlat(): array {
        $res = $this->db->query("SELECT * FROM alat ORDER BY nama ASC");
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getAlatById(int $id): ?array {
        $res = $this->db->query("SELECT * FROM alat WHERE id=$id LIMIT 1");
        return ($res && $res->num_rows) ? $res->fetch_assoc() : null;
    }

    public function tambahAlat(array $d): bool {
        $nama = $this->db->real_escape_string($d['nama']);
        $kode = $this->db->real_escape_string($d['kode']);
        $kat  = $this->db->real_escape_string($d['kategori']);
        $stok = (int)$d['stok_total'];
        $desk = $this->db->real_escape_string($d['deskripsi'] ?? '');

        return $this->db->query("INSERT INTO alat (nama,kode,kategori,stok_total,stok_tersedia,deskripsi)
                                 VALUES ('$nama','$kode','$kat',$stok,$stok,'$desk')");
    }

    public function editAlat(int $id, array $d): bool {
        $nama = $this->db->real_escape_string($d['nama']);
        $kode = $this->db->real_escape_string($d['kode']);
        $kat  = $this->db->real_escape_string($d['kategori']);
        $stok = (int)$d['stok_total'];
        $desk = $this->db->real_escape_string($d['deskripsi'] ?? '');

        return $this->db->query("UPDATE alat SET nama='$nama',kode='$kode',kategori='$kat',
                                 stok_total=$stok,deskripsi='$desk' WHERE id=$id");
    }

    public function hapusAlat(int $id): bool {
        return $this->db->query("DELETE FROM alat WHERE id=$id");
    }

    public function kurangiStok(int $alatId, int $jml): bool {
        return $this->db->query("UPDATE alat SET stok_tersedia = stok_tersedia - $jml
                                 WHERE id=$alatId AND stok_tersedia >= $jml");
    }

    public function tambahStok(int $alatId, int $jml): bool {
        return $this->db->query("UPDATE alat SET stok_tersedia = stok_tersedia + $jml
                                 WHERE id=$alatId");
    }

    public function statusStok(array $alat): string {
        $pct = $alat['stok_total'] > 0
             ? ($alat['stok_tersedia'] / $alat['stok_total']) * 100
             : 0;
             
        if ($alat['stok_tersedia'] == 0) return 'empty';
        if ($pct <= 40)                  return 'limited';
        return 'available';
    }

    public function getKatalogPreview(int $limit = 3): array {
        $res = $this->db->query("SELECT * FROM alat ORDER BY stok_tersedia DESC LIMIT $limit");
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getAlatFiltered(string $keyword, string $kategori): array {
        $where = "WHERE 1=1";
        
        if ($keyword !== '') {
            $k = $this->db->real_escape_string($keyword);
            $where .= " AND (nama LIKE '%$k%' OR kode LIKE '%$k%')";
        }
        if ($kategori !== '') {
            $kat = $this->db->real_escape_string($kategori);
            $where .= " AND kategori='$kat'";
        }
        
        $res = $this->db->query("SELECT * FROM alat $where ORDER BY nama ASC");
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getKategoriList(): array {
        $res = $this->db->query("SELECT DISTINCT kategori FROM alat ORDER BY kategori");
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getAlatRusak(): array {
        $res = $this->db->query("SELECT * FROM alat WHERE stok_rusak > 0 ORDER BY nama ASC");
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function perbaikiAlat(int $id, int $jumlah): bool {
        return $this->db->query("UPDATE alat SET stok_rusak = stok_rusak - $jumlah, stok_tersedia = stok_tersedia + $jumlah WHERE id = $id AND stok_rusak >= $jumlah");
    }

    public function buangAlat(int $id, int $jumlah): bool {
        return $this->db->query("UPDATE alat SET stok_rusak = stok_rusak - $jumlah, stok_total = stok_total - $jumlah WHERE id = $id AND stok_rusak >= $jumlah");
    }
}