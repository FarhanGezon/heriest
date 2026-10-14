<?php
$page_title = "Edit Portofolio";
require_once __DIR__ . '/auth.php';

$db = getDB();
$id = (int)($_GET['id'] ?? 0);
$error = '';

if ($id <= 0) {
    header('Location: portofolio.php');
    exit;
}

// Ambil data portofolio
$stmt = $db->prepare("SELECT * FROM portofolio WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$portofolio = $stmt->fetch();

if (!$portofolio) {
    header('Location: portofolio.php');
    exit;
}

// Ambil list kategori
$kategori_list = [];
if ($db) {
    $kategori_list = $db->query("SELECT * FROM kategori ORDER BY nama_kategori ASC")->fetchAll();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul'] ?? '');
    $klien = trim($_POST['klien'] ?? '');
    $kategori_id = !empty($_POST['kategori_id']) ? (int)$_POST['kategori_id'] : null;
    $lokasi = trim($_POST['lokasi'] ?? '');
    $tahun = trim($_POST['tahun'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $hapus_gambar = isset($_POST['hapus_gambar']) ? true : false;
    $gambar_nama = $portofolio['gambar'];

    if (empty($judul)) {
        $error = 'Judul portofolio wajib diisi.';
    } elseif (!empty($tahun) && !preg_match('/^\d{4}$/', $tahun)) {
        $error = 'Tahun harus 4 digit angka (contoh: 2026).';
    } else {
        $upload_dir = __DIR__ . '/../assets/images/uploads/';

        // Hapus gambar jika dicentang
        if ($hapus_gambar && !empty($gambar_nama)) {
            if (file_exists($upload_dir . $gambar_nama)) {
                unlink($upload_dir . $gambar_nama);
            }
            $gambar_nama = null;
        }

        // Upload Gambar Baru jika ada
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['gambar']['tmp_name'];
            $file_name = $_FILES['gambar']['name'];
            $file_size = $_FILES['gambar']['size'];
            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];

            if (!in_array($ext, $allowed_exts)) {
                $error = 'Format gambar hanya boleh JPG, JPEG, PNG, atau WEBP.';
            } elseif ($file_size > 5 * 1024 * 1024) {
                $error = 'Ukuran gambar maksimal 5MB.';
            } else {
                // Hapus gambar lama jika ada
                if (!empty($portofolio['gambar']) && file_exists($upload_dir . $portofolio['gambar'])) {
                    unlink($upload_dir . $portofolio['gambar']);
                }

                $gambar_nama = 'portofolio_' . time() . '_' . rand(100, 999) . '.' . $ext;
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                move_uploaded_file($file_tmp, $upload_dir . $gambar_nama);
            }
        }

        if (empty($error)) {
            try {
                $stmt = $db->prepare("
                    UPDATE portofolio
                    SET judul = ?, klien = ?, kategori_id = ?, lokasi = ?, tahun = ?, deskripsi = ?, gambar = ?, is_active = ?
                    WHERE id = ?
                ");
                $stmt->execute([$judul, $klien ?: null, $kategori_id, $lokasi ?: null, $tahun ?: null, $deskripsi ?: null, $gambar_nama, $is_active, $id]);

                header('Location: portofolio.php?msg=updated');
                exit;
            } catch (Exception $e) {
                $error = 'Gagal memperbarui portofolio: ' . $e->getMessage();
            }
        }
    }
}
require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header Page -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg sm:text-xl font-black text-white">Edit Portofolio</h2>
            <p class="text-xs text-slate-400">Perbarui data portofolio: <strong><?= htmlspecialchars($portofolio['judul']) ?></strong></p>
        </div>
        <a href="portofolio.php" class="inline-flex items-center gap-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold px-4 py-2 rounded-xl transition-all">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali</span>
        </a>
    </div>

    <!-- Error Alert -->
    <?php if (!empty($error)): ?>
        <div class="bg-red-950/80 border border-red-800 text-red-300 text-xs sm:text-sm rounded-2xl p-4 flex items-center gap-2.5">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-400 shrink-0"></i>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <!-- Form Container -->
    <form action="portofolio_edit.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data" class="bg-[#141622] border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Judul -->
            <div class="space-y-1.5 md:col-span-2">
                <label class="block text-xs font-semibold text-slate-300" for="judul">
                    Judul Portofolio <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="judul"
                    name="judul"
                    required
                    value="<?= htmlspecialchars($_POST['judul'] ?? $portofolio['judul']) ?>"
                    class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white outline-none transition-all"
                >
            </div>

            <!-- Klien -->
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-300" for="klien">
                    Nama Klien
                </label>
                <input
                    type="text"
                    id="klien"
                    name="klien"
                    value="<?= htmlspecialchars($_POST['klien'] ?? $portofolio['klien']) ?>"
                    class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white outline-none transition-all"
                >
            </div>

            <!-- Kategori -->
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-300" for="kategori_id">
                    Kategori Layanan
                </label>
                <select
                    id="kategori_id"
                    name="kategori_id"
                    class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white outline-none"
                >
                    <option value="">-- Tanpa Kategori --</option>
                    <?php foreach ($kategori_list as $kat): ?>
                        <?php
                            $selected = (isset($_POST['kategori_id']) && $_POST['kategori_id'] == $kat['id']) || ((!isset($_POST['kategori_id'])) && $portofolio['kategori_id'] == $kat['id']) ? 'selected' : '';
                        ?>
                        <option value="<?= $kat['id'] ?>" <?= $selected ?>>
                            <?= htmlspecialchars($kat['nama_kategori']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Lokasi -->
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-300" for="lokasi">
                    Lokasi Proyek
                </label>
                <input
                    type="text"
                    id="lokasi"
                    name="lokasi"
                    value="<?= htmlspecialchars($_POST['lokasi'] ?? $portofolio['lokasi']) ?>"
                    class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white outline-none transition-all"
                >
            </div>

            <!-- Tahun -->
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-300" for="tahun">
                    Tahun Pengerjaan
                </label>
                <input
                    type="text"
                    id="tahun"
                    name="tahun"
                    inputmode="numeric"
                    maxlength="4"
                    pattern="\d{4}"
                    placeholder="Contoh: 2026"
                    value="<?= htmlspecialchars($_POST['tahun'] ?? $portofolio['tahun']) ?>"
                    class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white outline-none transition-all"
                >
            </div>

            <!-- Deskripsi -->
            <div class="space-y-1.5 md:col-span-2">
                <label class="block text-xs font-semibold text-slate-300" for="deskripsi">
                    Deskripsi Proyek
                </label>
                <textarea
                    id="deskripsi"
                    name="deskripsi"
                    rows="3"
                    class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl p-4 text-xs sm:text-sm text-white outline-none"
                ><?= htmlspecialchars($_POST['deskripsi'] ?? $portofolio['deskripsi']) ?></textarea>
            </div>

            <!-- Foto Portofolio & Ganti Foto -->
            <div class="space-y-3 md:col-span-2">
                <label class="block text-xs font-semibold text-slate-300">
                    Foto Hasil Kerja
                </label>

                <?php if (!empty($portofolio['gambar']) && file_exists(__DIR__ . '/../assets/images/uploads/' . $portofolio['gambar'])): ?>
                    <div class="flex items-center gap-4 p-4 bg-[#0d0f17] border border-slate-800 rounded-2xl">
                        <img src="../assets/images/uploads/<?= htmlspecialchars($portofolio['gambar']) ?>" alt="" class="w-16 h-16 rounded-xl object-cover border border-slate-700">
                        <div class="space-y-1">
                            <p class="text-xs text-white font-semibold">Foto Saat Ini: <?= htmlspecialchars($portofolio['gambar']) ?></p>
                            <label class="inline-flex items-center gap-2 text-xs text-red-400 cursor-pointer hover:text-red-300">
                                <input type="checkbox" name="hapus_gambar" value="1" class="rounded bg-slate-800 border-slate-700 text-red-600 focus:ring-0">
                                <span>Hapus foto saat ini</span>
                            </label>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="border-2 border-dashed border-slate-700/80 hover:border-red-500/80 rounded-2xl p-5 text-center bg-[#0d0f17] transition-all">
                    <p class="text-xs text-slate-300 font-semibold mb-1">Pilih foto baru untuk mengganti</p>
                    <p class="text-[11px] text-slate-500 mb-2">Format: JPG, JPEG, PNG, WEBP (Max 5MB)</p>
                    <input
                        type="file"
                        name="gambar"
                        accept=".jpg,.jpeg,.png,.webp"
                        class="text-xs text-slate-400 file:mr-3 file:py-1.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-white hover:file:bg-slate-700 cursor-pointer"
                    >
                </div>
            </div>

            <!-- Switch Status Aktif -->
            <div class="md:col-span-2 flex items-center gap-3 pt-2">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" class="sr-only peer" <?= $portofolio['is_active'] ? 'checked' : '' ?>>
                    <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                </label>
                <div>
                    <span class="text-xs font-bold text-white block">Publikasikan Langsung</span>
                    <span class="text-[11px] text-slate-400">Jika dicentang, portofolio akan muncul di galeri publik.</span>
                </div>
            </div>

        </div>

        <!-- Submit Button -->
        <div class="pt-6 border-t border-slate-800 flex justify-end gap-3">
            <a href="portofolio.php" class="px-5 py-2.5 rounded-xl border border-slate-700 text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-800 transition-all">
                Batal
            </a>
            <button
                type="submit"
                class="bg-red-600 hover:bg-red-700 active:scale-95 text-white font-bold text-xs sm:text-sm px-7 py-2.5 rounded-xl shadow-lg transition-all flex items-center gap-2"
            >
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Simpan Perubahan</span>
            </button>
        </div>

    </form>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
