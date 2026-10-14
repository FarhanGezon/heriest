<?php
$page_title = "Mitra & Klien";
require_once __DIR__ . '/auth.php';

$db = getDB();
$error = '';
$msg = $_GET['msg'] ?? '';

// Auto-migrasi tabel mitra
try {
    $db->exec("CREATE TABLE IF NOT EXISTS `mitra` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `nama_perusahaan` VARCHAR(150) NOT NULL,
        `logo` VARCHAR(255) NULL,
        `link` VARCHAR(255) NULL,
        `is_active` TINYINT(1) NOT NULL DEFAULT 1,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
} catch (Exception $e) {
    $error = 'Gagal menyiapkan tabel mitra: ' . $e->getMessage();
}

$upload_dir = __DIR__ . '/../assets/images/uploads/';
$allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];
$max_size = 5 * 1024 * 1024;

function uploadLogoMitra($file, $upload_dir, $allowed_exts, $max_size, &$error) {
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = 'Gagal mengunggah logo.';
        return false;
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_exts)) {
        $error = 'Format logo hanya boleh JPG, JPEG, PNG, atau WEBP.';
        return false;
    }
    if ($file['size'] > $max_size) {
        $error = 'Ukuran logo maksimal 5MB.';
        return false;
    }
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    $filename = 'mitra_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
        $error = 'Gagal menyimpan file logo.';
        return false;
    }
    return $filename;
}

function hapusFileLogo($upload_dir, $logo) {
    if (!empty($logo) && file_exists($upload_dir . $logo)) {
        unlink($upload_dir . $logo);
    }
}

// Handle Tambah
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'tambah') {
    $nama = trim($_POST['nama_perusahaan'] ?? '');
    $link = trim($_POST['link'] ?? '');
    if (empty($nama)) {
        $error = 'Nama perusahaan tidak boleh kosong.';
    } else {
        $logo = uploadLogoMitra($_FILES['logo'] ?? null, $upload_dir, $allowed_exts, $max_size, $error);
        if ($logo !== false && empty($error)) {
            try {
                $stmt = $db->prepare("INSERT INTO mitra (nama_perusahaan, logo, link) VALUES (?, ?, ?)");
                $stmt->execute([$nama, $logo, $link !== '' ? $link : null]);
                header('Location: mitra.php?msg=created');
                exit;
            } catch (Exception $e) {
                if (!empty($logo)) hapusFileLogo($upload_dir, $logo);
                $error = 'Gagal menambah mitra: ' . $e->getMessage();
            }
        }
    }
}

// Handle Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = (int)($_POST['id'] ?? 0);
    $nama = trim($_POST['nama_perusahaan'] ?? '');
    $link = trim($_POST['link'] ?? '');
    if ($id > 0 && !empty($nama)) {
        try {
            $stmt = $db->prepare("SELECT logo FROM mitra WHERE id = ?");
            $stmt->execute([$id]);
            $lama = $stmt->fetch();
            $logo_lama = $lama['logo'] ?? null;
            $logo_baru = $logo_lama;

            $upload = uploadLogoMitra($_FILES['logo'] ?? null, $upload_dir, $allowed_exts, $max_size, $error);
            if ($upload === false) {
                // error sudah diisi, jangan lanjut
            } else {
                if ($upload !== null) {
                    hapusFileLogo($upload_dir, $logo_lama);
                    $logo_baru = $upload;
                }
                $stmt = $db->prepare("UPDATE mitra SET nama_perusahaan = ?, logo = ?, link = ? WHERE id = ?");
                $stmt->execute([$nama, $logo_baru, $link !== '' ? $link : null, $id]);
                header('Location: mitra.php?msg=updated');
                exit;
            }
        } catch (Exception $e) {
            $error = 'Gagal memperbarui mitra: ' . $e->getMessage();
        }
    } else {
        $error = 'Data mitra tidak valid.';
    }
}

// Handle Toggle aktif/nonaktif
if (isset($_GET['action']) && $_GET['action'] === 'toggle') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0) {
        try {
            $stmt = $db->prepare("UPDATE mitra SET is_active = 1 - is_active WHERE id = ?");
            $stmt->execute([$id]);
            header('Location: mitra.php?msg=toggled');
            exit;
        } catch (Exception $e) {
            $error = 'Gagal mengubah status mitra.';
        }
    }
}

// Handle Hapus
if (isset($_GET['action']) && $_GET['action'] === 'hapus') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0) {
        try {
            $stmt = $db->prepare("SELECT logo FROM mitra WHERE id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch();
            $stmt = $db->prepare("DELETE FROM mitra WHERE id = ?");
            $stmt->execute([$id]);
            if ($row) hapusFileLogo($upload_dir, $row['logo']);
            header('Location: mitra.php?msg=deleted');
            exit;
        } catch (Exception $e) {
            $error = 'Gagal menghapus mitra.';
        }
    }
}

// Ambil list mitra
$mitra = [];
if ($db) {
    $mitra = $db->query("SELECT * FROM mitra ORDER BY id DESC")->fetchAll();
}
require_once __DIR__ . '/includes/header.php';
?>

<div class="space-y-6">

    <!-- Flash Messages -->
    <?php if ($msg === 'created'): ?>
        <div class="bg-emerald-950/80 border border-emerald-800 text-emerald-300 text-xs sm:text-sm rounded-2xl p-4 flex items-center gap-2.5">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-400"></i>
            <span>Mitra baru berhasil ditambahkan!</span>
        </div>
    <?php elseif ($msg === 'updated'): ?>
        <div class="bg-sky-950/80 border border-sky-800 text-sky-300 text-xs sm:text-sm rounded-2xl p-4 flex items-center gap-2.5">
            <i data-lucide="check-circle" class="w-5 h-5 text-sky-400"></i>
            <span>Mitra berhasil diperbarui!</span>
        </div>
    <?php elseif ($msg === 'deleted'): ?>
        <div class="bg-red-950/80 border border-red-800 text-red-300 text-xs sm:text-sm rounded-2xl p-4 flex items-center gap-2.5">
            <i data-lucide="trash-2" class="w-5 h-5 text-red-400"></i>
            <span>Mitra berhasil dihapus.</span>
        </div>
    <?php elseif ($msg === 'toggled'): ?>
        <div class="bg-sky-950/80 border border-sky-800 text-sky-300 text-xs sm:text-sm rounded-2xl p-4 flex items-center gap-2.5">
            <i data-lucide="power" class="w-5 h-5 text-sky-400"></i>
            <span>Status mitra berhasil diubah.</span>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="bg-red-950/80 border border-red-800 text-red-300 text-xs sm:text-sm rounded-2xl p-4 flex items-center gap-2.5">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-400"></i>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        <!-- Form Tambah Mitra -->
        <div class="bg-[#141622] border border-slate-800 rounded-3xl p-6 shadow-sm">
            <h3 class="font-bold text-sm text-white mb-1 flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-4 h-4 text-red-500"></i>
                <span>Tambah Mitra Baru</span>
            </h3>
            <p class="text-xs text-slate-400 mb-5">Logo tampil di halaman utama website</p>

            <form action="mitra.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="action" value="tambah">

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="nama_perusahaan">Nama Perusahaan</label>
                    <input
                        type="text"
                        name="nama_perusahaan"
                        id="nama_perusahaan"
                        required
                        placeholder="Contoh: PT Maju Jaya"
                        class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white placeholder-slate-500 outline-none transition-all"
                    >
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="logo">Logo</label>
                    <input
                        type="file"
                        name="logo"
                        id="logo"
                        accept=".jpg,.jpeg,.png,.webp"
                        class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-slate-300 file:bg-slate-800 file:text-white file:border-0 file:rounded-lg file:px-3 file:py-1.5 file:text-xs file:mr-3 outline-none transition-all"
                    >
                    <p class="text-[11px] text-slate-500 mt-1.5">JPG / JPEG / PNG / WEBP, maks 5MB.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="link">Link Website (opsional)</label>
                    <input
                        type="url"
                        name="link"
                        id="link"
                        placeholder="https://contoh-perusahaan.com"
                        class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white placeholder-slate-500 outline-none transition-all"
                    >
                </div>

                <button
                    type="submit"
                    class="w-full bg-red-600 hover:bg-red-700 active:scale-95 text-white font-bold text-xs py-2.5 rounded-xl shadow transition-all flex items-center justify-center gap-2"
                >
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Simpan Mitra</span>
                </button>
            </form>
        </div>

        <!-- Tabel Daftar Mitra -->
        <div class="lg:col-span-2 bg-[#141622] border border-slate-800 rounded-3xl p-6 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                <div>
                    <h3 class="font-bold text-sm text-white">Daftar Mitra & Klien</h3>
                    <p class="text-xs text-slate-400">Klik toggle untuk tampil/sembunyikan di website</p>
                </div>
                <span class="text-xs text-slate-400 font-semibold bg-slate-800 px-3 py-1 rounded-full">
                    Total: <?= count($mitra) ?>
                </span>
            </div>

            <div class="overflow-x-auto mt-4">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 font-semibold text-[11px] uppercase tracking-wider">
                            <th class="py-3 px-3">#</th>
                            <th class="py-3 px-3">Logo</th>
                            <th class="py-3 px-3">Perusahaan</th>
                            <th class="py-3 px-3">Link</th>
                            <th class="py-3 px-3 text-center">Status</th>
                            <th class="py-3 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 text-slate-300">
                        <?php if (empty($mitra)): ?>
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-500">
                                    Belum ada mitra.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($mitra as $idx => $m): ?>
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="py-3 px-3 text-slate-500 font-mono text-[11px]">
                                    <?= $idx + 1 ?>
                                </td>
                                <td class="py-3 px-3">
                                    <?php if (!empty($m['logo']) && file_exists(__DIR__ . '/../assets/images/uploads/' . $m['logo'])): ?>
                                        <img src="../assets/images/uploads/<?= htmlspecialchars($m['logo']) ?>" alt="<?= htmlspecialchars($m['nama_perusahaan']) ?>" class="w-12 h-12 object-contain bg-white rounded-xl p-1">
                                    <?php else: ?>
                                        <div class="w-12 h-12 bg-slate-800 rounded-xl flex items-center justify-center">
                                            <i data-lucide="image-off" class="w-5 h-5 text-slate-600"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-3 font-bold text-white">
                                    <?= htmlspecialchars($m['nama_perusahaan']) ?>
                                </td>
                                <td class="py-3 px-3 text-xs max-w-[140px] truncate">
                                    <?php if (!empty($m['link'])): ?>
                                        <a href="<?= htmlspecialchars($m['link']) ?>" target="_blank" class="text-sky-400 hover:underline"><?= htmlspecialchars($m['link']) ?></a>
                                    <?php else: ?>
                                        <span class="text-slate-600">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <a
                                        href="mitra.php?action=toggle&id=<?= $m['id'] ?>"
                                        title="Klik untuk <?= $m['is_active'] ? 'nonaktifkan' : 'aktifkan' ?>"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold transition-all <?= $m['is_active'] ? 'bg-emerald-950/80 text-emerald-300 border border-emerald-800' : 'bg-slate-800 text-slate-500 border border-slate-700' ?>"
                                    >
                                        <span class="w-1.5 h-1.5 rounded-full <?= $m['is_active'] ? 'bg-emerald-400' : 'bg-slate-500' ?>"></span>
                                        <?= $m['is_active'] ? 'Aktif' : 'Off' ?>
                                    </a>
                                </td>
                                <td class="py-3 px-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button
                                            onclick="editMitra(<?= $m['id'] ?>, '<?= addslashes(htmlspecialchars($m['nama_perusahaan'])) ?>', '<?= addslashes(htmlspecialchars($m['link'] ?? '')) ?>')"
                                            class="p-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-lg transition-all"
                                            title="Edit Mitra"
                                        >
                                            <i data-lucide="edit-3" class="w-3.5 h-3.5 text-sky-400"></i>
                                        </button>
                                        <a
                                            href="mitra.php?action=hapus&id=<?= $m['id'] ?>"
                                            onclick="return confirm('Yakin ingin menghapus mitra <?= addslashes($m['nama_perusahaan']) ?>? Logo ikut terhapus.');"
                                            class="p-1.5 bg-slate-800 hover:bg-red-950/80 text-slate-400 hover:text-red-400 rounded-lg transition-all"
                                            title="Hapus Mitra"
                                        >
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5 text-red-400"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>

    </div>

</div>

<!-- Modal Edit Mitra -->
<div id="modal-edit-mitra" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-[#141622] border border-slate-800 rounded-3xl p-6 max-w-md w-full shadow-2xl">
        <h3 class="font-bold text-base text-white mb-1">Edit Mitra</h3>
        <p class="text-xs text-slate-400 mb-4">Ubah data perusahaan / ganti logo</p>

        <form action="mitra.php" method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="edit_id">

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Nama Perusahaan</label>
                <input
                    type="text"
                    name="nama_perusahaan"
                    id="edit_nama"
                    required
                    class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white outline-none"
                >
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Link Website</label>
                <input
                    type="url"
                    name="link"
                    id="edit_link"
                    placeholder="https://..."
                    class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white outline-none"
                >
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Ganti Logo (opsional)</label>
                <input
                    type="file"
                    name="logo"
                    accept=".jpg,.jpeg,.png,.webp"
                    class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-slate-300 file:bg-slate-800 file:text-white file:border-0 file:rounded-lg file:px-3 file:py-1.5 file:text-xs file:mr-3 outline-none"
                >
                <p class="text-[11px] text-slate-500 mt-1.5">Kosongkan jika logo tidak diganti.</p>
            </div>

            <div class="flex justify-end gap-2.5 pt-2">
                <button
                    type="button"
                    onclick="document.getElementById('modal-edit-mitra').classList.add('hidden')"
                    class="px-4 py-2 rounded-xl border border-slate-700 text-xs font-semibold text-slate-300 hover:bg-slate-800"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white font-bold text-xs px-5 py-2 rounded-xl shadow"
                >
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function editMitra(id, nama, link) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_nama').value = nama;
    document.getElementById('edit_link').value = link;
    document.getElementById('modal-edit-mitra').classList.remove('hidden');
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
