<?php
require_once __DIR__ . '/auth.php';

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $db = getDB();
    if ($db) {
        // Ambil info gambar
        $stmt = $db->prepare("SELECT gambar FROM produk WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if ($row) {
            // Hapus file gambar jika ada
            if (!empty($row['gambar'])) {
                $file_path = __DIR__ . '/../assets/images/uploads/' . $row['gambar'];
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }

            // Hapus dari database
            $del_stmt = $db->prepare("DELETE FROM produk WHERE id = ?");
            $del_stmt->execute([$id]);
        }
    }
}

header('Location: produk.php?msg=deleted');
exit;
