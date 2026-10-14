<?php
// Endpoint pencatat klik tombol WhatsApp (leads).
// Dipanggil via navigator.sendBeacon / fetch dari frontend.
require_once __DIR__ . '/config/database.php';

header('Content-Type: application/json');

$sumber = trim($_GET['sumber'] ?? $_POST['sumber'] ?? 'umum');
$ref_id = isset($_GET['ref_id']) ? (int)$_GET['ref_id'] : (int)($_POST['ref_id'] ?? 0);
$sumber = substr(preg_replace('/[^a-z0-9_\-]/i', '', $sumber), 0, 50);
if ($sumber === '') $sumber = 'umum';

$db = getDB();
if ($db) {
    try {
        // Self-heal: tabel mungkin belum ada di install lama.
        $db->exec("CREATE TABLE IF NOT EXISTS `wa_clicks` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `sumber` VARCHAR(50) NOT NULL DEFAULT 'umum',
            `ref_id` INT NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX (`sumber`),
            INDEX (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $stmt = $db->prepare("INSERT INTO wa_clicks (sumber, ref_id) VALUES (?, ?)");
        $stmt->execute([$sumber, $ref_id > 0 ? $ref_id : null]);
        echo json_encode(['success' => true]);
        exit;
    } catch (Exception $e) {
        echo json_encode(['success' => false]);
        exit;
    }
}
echo json_encode(['success' => false]);
