<?php

$env = __DIR__ . '/../.env';
$conf = parse_ini_file($env);

function getDB() {
    global $conf;
    static $conn = null;
    if ($conn === null) {
        $conn = new mysqli($conf['DB_HOST'], $conf['DB_USER'], $conf['DB_PASS'], $conf['DB_NAME']);
        if ($conn->connect_error) {
            die("Koneksi gagal: " . $conn->connect_error);
        }
        $conn->set_charset("utf8mb4");
    }
    return $conn;
}
