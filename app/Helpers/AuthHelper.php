<?php

class AuthHelper {
    
    private static function initSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function requireLogin() {
        self::initSession();
        if (empty($_SESSION['user_id'])) {
            header("Location: index.php?c=auth&a=login");
            exit;
        }
    }

    public static function requireAdmin() {
        self::requireLogin();

        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            session_destroy();
            header("Location: index.php?c=auth&a=login");
            exit;
        }
    }

    public static function requireUser() {
        self::requireLogin();

        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'mahasiswa') {
            session_destroy();
            header("Location: index.php?c=auth&a=login");
            exit;
        }
    }

    public static function isLoggedIn() {
        self::initSession();
        return !empty($_SESSION['user_id']);
    }

    public static function isAdmin() {
        self::initSession();
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    public static function currentUser() {
        self::initSession();
        return $_SESSION ?? [];
    }
}