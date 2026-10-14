<?php
/**
 * Konfigurasi Database MySQL (PDO)
 * HERIEST CV. ADVERTISING
 */

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'heriest_db';

try {
    $pdo = new PDO("mysql:host={$db_host};dbname={$db_name};charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    // Jika koneksi gagal, catat error (tetap aman untuk frontend)
    $pdo = null;
    error_log("Koneksi Database Gagal: " . $e->getMessage());
}

/**
 * Helper: Ambil koneksi database
 */
function getDB() {
    global $pdo;
    return $pdo;
}

/**
 * Helper: Format angka ke Rupiah
 */
function formatRupiah($angka) {
    return 'Rp ' . number_format((float)$angka, 0, ',', '.');
}

/**
 * Helper: Ambil nilai pengaturan dari database
 */
function getSetting($key, $default = '') {
    $db = getDB();
    if (!$db) return $default;
    
    try {
        $stmt = $db->prepare("SELECT setting_value FROM pengaturan WHERE setting_key = ? LIMIT 1");
        $stmt->execute([$key]);
        $row = $stmt->fetch();
        return $row ? $row['setting_value'] : $default;
    } catch (Exception $e) {
        return $default;
    }
}
