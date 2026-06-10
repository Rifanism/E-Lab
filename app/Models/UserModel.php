<?php

require_once __DIR__ . '/../../config/database.php';

class UserModel {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function authenticate(string $identifier, string $password): array {
        $id = $this->db->real_escape_string(trim($identifier));

        $res = $this->db->query("SELECT * FROM users WHERE npm='$id' AND role='mahasiswa' LIMIT 1");
        if ($res && $res->num_rows === 1) {
            $user = $res->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                return ['ok' => true, 'user' => $user];
            }
        }

        $res = $this->db->query("SELECT * FROM users WHERE username='$id' AND role='admin' LIMIT 1");
        if ($res && $res->num_rows === 1) {
            $user = $res->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                return ['ok' => true, 'user' => $user];
            }
        }

        return ['ok' => false, 'msg' => 'NPM/username atau password salah.'];
    }

    public function register(array $data): array {
        $nama     = $this->db->real_escape_string(trim($data['nama']));
        $username = $this->db->real_escape_string(trim($data['username']));
        $email    = $this->db->real_escape_string(trim($data['email']));
        $npm      = $this->db->real_escape_string(trim($data['npm']));
        $password = password_hash($data['password'], PASSWORD_BCRYPT);

        $res = $this->db->query("SELECT id FROM users WHERE username='$username' LIMIT 1");
        if ($res && $res->num_rows > 0) {
            return ['ok' => false, 'msg' => 'Username sudah digunakan, pilih username lain.'];
        }

        $res = $this->db->query("SELECT id FROM users WHERE email='$email' LIMIT 1");
        if ($res && $res->num_rows > 0) {
            return ['ok' => false, 'msg' => 'Email sudah terdaftar, gunakan email lain.'];
        }

        if ($npm !== '') {
            $res = $this->db->query("SELECT id FROM users WHERE npm='$npm' LIMIT 1");
            if ($res && $res->num_rows > 0) {
                return ['ok' => false, 'msg' => 'NPM sudah terdaftar dalam sistem.'];
            }
        }

        $npmVal = $npm !== '' ? "'$npm'" : 'NULL';

        $sql = "INSERT INTO users (npm, username, nama, email, password, role)
                VALUES ($npmVal, '$username', '$nama', '$email', '$password', 'mahasiswa')";

        if ($this->db->query($sql)) {
            return ['ok' => true, 'msg' => 'Registrasi berhasil! Silakan login.'];
        }

        return ['ok' => false, 'msg' => 'Gagal menyimpan data. Coba lagi.'];
    }

    public function findById(int $id): ?array {
        $res = $this->db->query("SELECT * FROM users WHERE id=$id LIMIT 1");
        if ($res && $res->num_rows === 1) {
            return $res->fetch_assoc();
        }
        return null;
    }
}
