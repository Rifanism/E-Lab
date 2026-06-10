<?php

require_once __DIR__ . '/../Models/UserModel.php';

class AuthController {
    public function login() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!empty($_SESSION['user_id']) && !empty($_SESSION['role'])) {
            if ($_SESSION['role'] === 'admin') {
                header("Location: index.php?c=admin&a=dashboard");
            } else {
                header("Location: index.php?c=user&a=dashboard");
            }
            exit;
        }

        $error = null;
        require_once __DIR__ . '/../Views/auth/login.php';
    }

    public function prosesLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?c=auth&a=login");
            exit;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $identifier = $_POST['identifier'] ?? '';
        $password   = $_POST['password'] ?? '';

        $userModel = new UserModel();
        $result    = $userModel->authenticate($identifier, $password);

        if ($result['ok']) {
            $this->setSession($result['user']);

            if ($result['user']['role'] === 'admin') {
                header("Location: index.php?c=admin&a=dashboard");
            } else {
                header("Location: index.php?c=user&a=dashboard");
            }
            exit;
        } else {
            $error = $result['msg'];
            require_once __DIR__ . '/../Views/auth/login.php';
        }
    }

    public function register() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!empty($_SESSION['user_id'])) {
            header("Location: index.php?c=auth&a=login");
            exit;
        }

        $error  = null;
        $old    = [];
        require_once __DIR__ . '/../Views/auth/register.php';
    }

    public function prosesRegister() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?c=auth&a=register");
            exit;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $nama     = trim($_POST['nama']     ?? '');
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email']    ?? '');
        $npm      = trim($_POST['npm']      ?? '');
        $password = $_POST['password']      ?? '';
        $konfirm  = $_POST['konfirm']       ?? '';

        $old = compact('nama', 'username', 'email', 'npm');
        $error = null;

        if ($nama === '' || $username === '' || $email === '' || $password === '') {
            $error = 'Semua field wajib diisi.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Format email tidak valid.';
        } elseif (strlen($password) < 6) {
            $error = 'Password minimal 6 karakter.';
        } elseif ($password !== $konfirm) {
            $error = 'Password dan konfirmasi password tidak cocok.';
        }

        if ($error) {
            require_once __DIR__ . '/../Views/auth/register.php';
            return;
        }

        $userModel = new UserModel();
        $result = $userModel->register([
            'nama'     => $nama,
            'username' => $username,
            'email'    => $email,
            'npm'      => $npm,
            'password' => $password,
        ]);

        if ($result['ok']) {
            self::setFlash($result['msg'], 'success');
            header("Location: index.php?c=auth&a=login");
            exit;
        } else {
            $error = $result['msg'];
            require_once __DIR__ . '/../Views/auth/register.php';
        }
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();

        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Pragma: no-cache");
        header("Location: index.php?c=auth&a=login");
        exit;
    }

    private function setSession(array $user): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_regenerate_id(true);

        $_SESSION['user_id']  = $user['id'];
        $_SESSION['nama']     = $user['nama'];
        $_SESSION['npm']      = $user['npm']      ?? '';
        $_SESSION['role']     = $user['role']; 
    }

    public static function setFlash(string $msg, string $type = 'info'): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['flash'] = ['msg' => $msg, 'type' => $type];
    }
}
