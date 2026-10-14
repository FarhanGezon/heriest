<?php
$page_title = "Banner & Promo";
require_once __DIR__ . '/auth.php';

$db = getDB();
$error = '';
$msg = $_GET['msg'] ?? '';

function handleBannerUpload($field = 'gambar', $old = null) {
    global $error;
    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
        return $old;
    }
    if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        $error = 'Gagal mengunggah gambar.';
        return false;
    }
    $file_tmp = $_FILES[$field]['tmp_name'];
    $file_name = $_FILES[$field]['name'];
    $file_size = $_FILES[$field]['size'];
    $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($ext, $allowed_exts)) {
        $error = 'Format gambar hanya boleh JPG, JPEG, PNG, atau WEBP.';
        return false;
    }
    if ($file_size > 5 * 1024 * 1024) {
        $error = 'Ukuran gambar maksimal 5MB.';
        return false;
    }
    $gambar_nama = 'banner_' . time() . '_' . rand(100, 999) . '.' . $ext;
    $upload_dir = __DIR__ . '/../assets/images/uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    if (!move_uploaded_file($file_tmp, $upload_dir . $gambar_nama)) {
        $error = 'Gagal menyimpan gambar.';
        return false;
    }
    if (!empty($old) && file_exists($upload_dir . $old)) {
        unlink($upload_dir . $old);
    }
    return $gambar_nama;
}

// Handle Tambah Banner
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'tambah') {
    $headline = trim($_POST['headline'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $cta_text = trim($_POST['cta_text'] ?? 'Cek Promo Sekarang');
    $cta_link = trim($_POST['cta_link'] ?? '#produk');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    if (empty($headline)) {
        $error = 'Headline tidak boleh kosong.';
    } else {
        $gambar_nama = handleBannerUpload('gambar', null);
        if ($gambar_nama !== false && empty($error)) {
            try {
                $stmt = $db->prepare("INSERT INTO banner_promo (headline, deskripsi, cta_text, cta_link, gambar, is_active) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$headline, $deskripsi, $cta_text, $cta_link, $gambar_nama, $is_active]);
                header('Location: banner.php?msg=created');
                exit;
            } catch (Exception $e) {
                $error = 'Gagal menambah banner: ' . $e->getMessage();
            }
        }
    }
}

// Handle Edit Banner
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = (int)($_POST['id'] ?? 0);
    $headline = trim($_POST['headline'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $cta_text = trim($_POST['cta_text'] ?? 'Cek Promo Sekarang');
    $cta_link = trim($_POST['cta_link'] ?? '#produk');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    if ($id > 0 && !empty($headline)) {
        try {
            $old = $db->prepare("SELECT gambar FROM banner_promo WHERE id = ?");
            $old->execute([$id]);
            $old_gambar = $old->fetchColumn();
            $gambar_nama = handleBannerUpload('gambar', $old_gambar);
            if ($gambar_nama !== false && empty($error)) {
                $stmt = $db->prepare("UPDATE banner_promo SET headline = ?, deskripsi = ?, cta_text = ?, cta_link = ?, gambar = ?, is_active = ? WHERE id = ?");
                $stmt->execute([$headline, $deskripsi, $cta_text, $cta_link, $gambar_nama, $is_active, $id]);
                header('Location: banner.php?msg=updated');
                exit;
            }
        } catch (Exception $e) {
            $error = 'Gagal memperbarui banner: ' . $e->getMessage();
        }
    } else {
        $error = 'Data banner tidak valid.';
    }
}

// Handle Toggle Aktif
if (isset($_GET['action']) && $_GET['action'] === 'toggle') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0) {
        try {
            $stmt = $db->prepare("UPDATE banner_promo SET is_active = 1 - is_active WHERE id = ?");
            $stmt->execute([$id]);
            header('Location: banner.php?msg=toggled');
            exit;
        } catch (Exception $e) {
            $error = 'Gagal mengubah status banner.';
        }
    }
}

// Handle Hapus Banner
if (isset($_GET['action']) && $_GET['action'] === 'hapus') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0) {
        try {
            $stmt = $db->prepare("SELECT gambar FROM banner_promo WHERE id = ?");
            $stmt->execute([$id]);
            $old_gambar = $stmt->fetchColumn();
            $stmt = $db->prepare("DELETE FROM banner_promo WHERE id = ?");
            $stmt->execute([$id]);
            if (!empty($old_gambar)) {
                $path = __DIR__ . '/../assets/images/uploads/' . $old_gambar;
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            header('Location: banner.php?msg=deleted');
            exit;
        } catch (Exception $e) {
            $error = 'Gagal menghapus banner.';
        }
    }
}

// Ambil list banner
$banners = [];
if ($db) {
    $banners = $db->query("SELECT * FROM banner_promo ORDER BY id DESC")->fetchAll();
}
require_once __DIR__ . '/includes/header.php';
?>

<div class="space-y-6">

    <!-- Flash Messages -->
    <?php if ($msg === 'created'): ?>
        <div class="bg-emerald-950/80 border border-emerald-800 text-emerald-300 text-xs sm:text-sm rounded-2xl p-4 flex items-center gap-2.5">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-400"></i>
            <span>Banner baru berhasil ditambahkan!</span>
        </div>
    <?php elseif ($msg === 'updated'): ?>
        <div class="bg-sky-950/80 border border-sky-800 text-sky-300 text-xs sm:text-sm rounded-2xl p-4 flex items-center gap-2.5">
            <i data-lucide="check-circle" class="w-5 h-5 text-sky-400"></i>
            <span>Banner berhasil diperbarui!</span>
        </div>
    <?php elseif ($msg === 'deleted'): ?>
        <div class="bg-red-950/80 border border-red-800 text-red-300 text-xs sm:text-sm rounded-2xl p-4 flex items-center gap-2.5">
            <i data-lucide="trash-2" class="w-5 h-5 text-red-400"></i>
            <span>Banner berhasil dihapus.</span>
        </div>
    <?php elseif ($msg === 'toggled'): ?>
        <div class="bg-amber-950/80 border border-amber-800 text-amber-300 text-xs sm:text-sm rounded-2xl p-4 flex items-center gap-2.5">
            <i data-lucide="power" class="w-5 h-5 text-amber-400"></i>
            <span>Status banner berhasil diubah.</span>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="bg-red-950/80 border border-red-800 text-red-300 text-xs sm:text-sm rounded-2xl p-4 flex items-center gap-2.5">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-400"></i>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <!-- Hero fallback note -->
    <div class="bg-slate-900/60 border border-slate-800 text-slate-400 text-xs sm:text-sm rounded-2xl p-4 flex items-start gap-2.5">
        <i data-lucide="info" class="w-5 h-5 text-sky-400 shrink-0 mt-0.5"></i>
        <span>Catatan: jika tidak ada banner aktif, hero homepage pakai teks default (fallback statis di <span class="font-mono text-slate-300">index.php</span>). Aktifkan minimal 1 banner agar section promo tampil dinamis.</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        <!-- Form Tambah Banner -->
        <div class="bg-[#141622] border border-slate-800 rounded-3xl p-6 shadow-sm">
            <h3 class="font-bold text-sm text-white mb-1 flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-4 h-4 text-red-500"></i>
                <span>Tambah Banner Baru</span>
            </h3>
            <p class="text-xs text-slate-400 mb-5">Banner tampil di section promo homepage</p>

            <form action="banner.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="action" value="tambah">

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="headline">Headline</label>
                    <input
                        type="text"
                        name="headline"
                        id="headline"
                        required
                        maxlength="200"
                        placeholder="Contoh: Diskon 50% Neon Box"
                        class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white placeholder-slate-500 outline-none transition-all"
                    >
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="deskripsi">Deskripsi</label>
                    <textarea
                        name="deskripsi"
                        id="deskripsi"
                        rows="3"
                        placeholder="Deskripsi singkat promo..."
                        class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white placeholder-slate-500 outline-none transition-all"
                    ></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="cta_text">Teks CTA</label>
                        <input
                            type="text"
                            name="cta_text"
                            id="cta_text"
                            maxlength="100"
                            value="Cek Promo Sekarang"
                            class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white placeholder-slate-500 outline-none transition-all"
                        >
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="cta_link">Link CTA</label>
                        <input
                            type="text"
                            name="cta_link"
                            id="cta_link"
                            maxlength="255"
                            value="#produk"
                            class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white placeholder-slate-500 outline-none transition-all"
                        >
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="gambar">Gambar (JPG/PNG/WEBP, max 5MB)</label>
                    <input
                        type="file"
                        name="gambar"
                        id="gambar"
                        accept=".jpg,.jpeg,.png,.webp"
                        class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-slate-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-slate-800 file:text-slate-200 file:text-xs outline-none transition-all"
                    >
                </div>

                <label class="flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 accent-red-600">
                    <span>Aktif / tampilkan di homepage</span>
                </label>

                <button
                    type="submit"
                    class="w-full bg-red-600 hover:bg-red-700 active:scale-95 text-white font-bold text-xs py-2.5 rounded-xl shadow transition-all flex items-center justify-center gap-2"
                >
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Simpan Banner</span>
                </button>
            </form>
        </div>

        <!-- Tabel Daftar Banner -->
        <div class="lg:col-span-2 bg-[#141622] border border-slate-800 rounded-3xl p-6 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                <div>
                    <h3 class="font-bold text-sm text-white">Daftar Banner & Promo</h3>
                    <p class="text-xs text-slate-400">Semua banner promo yang terdaftar</p>
                </div>
                <span class="text-xs text-slate-400 font-semibold bg-slate-800 px-3 py-1 rounded-full">
                    Total: <?= count($banners) ?>
                </span>
            </div>

            <div class="overflow-x-auto mt-4">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 font-semibold text-[11px] uppercase tracking-wider">
                            <th class="py-3 px-3">#</th>
                            <th class="py-3 px-3">Gambar</th>
                            <th class="py-3 px-3">Headline</th>
                            <th class="py-3 px-3">CTA</th>
                            <th class="py-3 px-3 text-center">Status</th>
                            <th class="py-3 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 text-slate-300">
                        <?php if (empty($banners)): ?>
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-500">
                                    Belum ada banner. Hero homepage pakai teks default.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($banners as $idx => $b): ?>
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="py-3 px-3 text-slate-500 font-mono text-[11px]">
                                    <?= $idx + 1 ?>
                                </td>
                                <td class="py-3 px-3">
                                    <?php if (!empty($b['gambar']) && file_exists(__DIR__ . '/../assets/images/uploads/' . $b['gambar'])): ?>
                                        <img src="../assets/images/uploads/<?= htmlspecialchars($b['gambar']) ?>" alt="" class="w-14 h-10 rounded-lg object-cover border border-slate-700">
                                    <?php else: ?>
                                        <span class="text-slate-600 text-[11px]">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-3">
                                    <p class="font-bold text-white text-xs sm:text-sm"><?= htmlspecialchars($b['headline']) ?></p>
                                    <?php if (!empty($b['deskripsi'])): ?>
                                        <p class="text-[11px] text-slate-500 truncate max-w-[220px]"><?= htmlspecialchars($b['deskripsi']) ?></p>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-3 text-[11px]">
                                    <span class="text-slate-300"><?= htmlspecialchars($b['cta_text']) ?></span>
                                    <span class="block font-mono text-slate-500"><?= htmlspecialchars($b['cta_link']) ?></span>
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <a
                                        href="banner.php?action=toggle&id=<?= $b['id'] ?>"
                                        title="Toggle aktif"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold transition-all <?= (int)$b['is_active'] === 1 ? 'bg-emerald-950/80 text-emerald-300 border border-emerald-800 hover:bg-emerald-900' : 'bg-slate-800 text-slate-400 border border-slate-700 hover:bg-slate-700' ?>"
                                    >
                                        <span class="w-1.5 h-1.5 rounded-full <?= (int)$b['is_active'] === 1 ? 'bg-emerald-400' : 'bg-slate-500' ?>"></span>
                                        <?= (int)$b['is_active'] === 1 ? 'Aktif' : 'Nonaktif' ?>
                                    </a>
                                </td>
                                <td class="py-3 px-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button
                                            onclick='editBanner(<?= (int)$b['id'] ?>, <?= json_encode($b['headline'], JSON_HEX_APOS | JSON_HEX_QUOT) ?>, <?= json_encode($b['deskripsi'] ?? '', JSON_HEX_APOS | JSON_HEX_QUOT) ?>, <?= json_encode($b['cta_text'], JSON_HEX_APOS | JSON_HEX_QUOT) ?>, <?= json_encode($b['cta_link'], JSON_HEX_APOS | JSON_HEX_QUOT) ?>, <?= (int)$b['is_active'] ?>)'
                                            class="p-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-lg transition-all"
                                            title="Edit Banner"
                                        >
                                            <i data-lucide="edit-3" class="w-3.5 h-3.5 text-sky-400"></i>
                                        </button>
                                        <a
                                            href="banner.php?action=hapus&id=<?= $b['id'] ?>"
                                            onclick="return confirm('Yakin ingin menghapus banner ini?');"
                                            class="p-1.5 bg-slate-800 hover:bg-red-950/80 text-slate-400 hover:text-red-400 rounded-lg transition-all"
                                            title="Hapus Banner"
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

<!-- Modal Edit Banner -->
<div id="modal-edit-banner" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-[#141622] border border-slate-800 rounded-3xl p-6 max-w-md w-full shadow-2xl max-h-[90vh] overflow-y-auto">
        <h3 class="font-bold text-base text-white mb-1">Edit Banner</h3>
        <p class="text-xs text-slate-400 mb-4">Ubah konten banner promo</p>

        <form action="banner.php" method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="edit_id">

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Headline</label>
                <input
                    type="text"
                    name="headline"
                    id="edit_headline"
                    required
                    maxlength="200"
                    class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white outline-none"
                >
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Deskripsi</label>
                <textarea
                    name="deskripsi"
                    id="edit_deskripsi"
                    rows="3"
                    class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white outline-none"
                ></textarea>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Teks CTA</label>
                    <input
                        type="text"
                        name="cta_text"
                        id="edit_cta_text"
                        maxlength="100"
                        class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white outline-none"
                    >
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Link CTA</label>
                    <input
                        type="text"
                        name="cta_link"
                        id="edit_cta_link"
                        maxlength="255"
                        class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white outline-none"
                    >
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Ganti Gambar (opsional)</label>
                <input
                    type="file"
                    name="gambar"
                    accept=".jpg,.jpeg,.png,.webp"
                    class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-slate-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-slate-800 file:text-slate-200 file:text-xs outline-none"
                >
                <p class="text-[11px] text-slate-500 mt-1">Kosongkan jika tidak ganti gambar. JPG/PNG/WEBP max 5MB.</p>
            </div>

            <label class="flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="w-4 h-4 accent-red-600">
                <span>Aktif / tampilkan di homepage</span>
            </label>

            <div class="flex justify-end gap-2.5 pt-2">
                <button
                    type="button"
                    onclick="document.getElementById('modal-edit-banner').classList.add('hidden')"
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
function editBanner(id, headline, deskripsi, cta_text, cta_link, is_active) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_headline').value = headline;
    document.getElementById('edit_deskripsi').value = deskripsi;
    document.getElementById('edit_cta_text').value = cta_text;
    document.getElementById('edit_cta_link').value = cta_link;
    document.getElementById('edit_is_active').checked = (is_active == 1);
    document.getElementById('modal-edit-banner').classList.remove('hidden');
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
