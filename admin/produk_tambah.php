<?php
$page_title = "Tambah Produk Baru";
require_once __DIR__ . '/auth.php';

$db = getDB();
$error = '';

// Ambil list kategori
$kategori_list = [];
if ($db) {
    $kategori_list = $db->query("SELECT * FROM kategori ORDER BY nama_kategori ASC")->fetchAll();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_produk = trim($_POST['nama_produk'] ?? '');
    $kategori_id = !empty($_POST['kategori_id']) ? (int)$_POST['kategori_id'] : null;
    $harga = (int)($_POST['harga'] ?? 0);
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $wa_text = trim($_POST['wa_text'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $gambar_nama = null;

    if (empty($nama_produk)) {
        $error = 'Nama produk wajib diisi.';
    } elseif ($harga < 0) {
        $error = 'Harga tidak boleh negatif.';
    } else {
        // Upload Gambar jika ada
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
                $gambar_nama = 'produk_' . time() . '_' . rand(100, 999) . '.' . $ext;
                $upload_dir = __DIR__ . '/../assets/images/uploads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                move_uploaded_file($file_tmp, $upload_dir . $gambar_nama);
            }
        }

        // Simpan ke database jika tidak ada error
        if (empty($error)) {
            if (empty($wa_text)) {
                $wa_text = "Halo admin, saya tertarik ingin memesan produk: " . $nama_produk;
            }

            try {
                $stmt = $db->prepare("
                    INSERT INTO produk (kategori_id, nama_produk, deskripsi, harga, gambar, wa_text, is_active)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$kategori_id, $nama_produk, $deskripsi, $harga, $gambar_nama, $wa_text, $is_active]);

                header('Location: produk.php?msg=created');
                exit;
            } catch (Exception $e) {
                $error = 'Gagal menyimpan produk: ' . $e->getMessage();
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
            <h2 class="text-lg sm:text-xl font-black text-white">Tambah Produk Baru</h2>
            <p class="text-xs text-slate-400">Isi formulir di bawah untuk menambahkan item baru ke katalog</p>
        </div>
        <a href="produk.php" class="inline-flex items-center gap-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold px-4 py-2 rounded-xl transition-all">
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
    <form action="produk_tambah.php" method="POST" enctype="multipart/form-data" class="bg-[#141622] border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Nama Produk -->
            <div class="space-y-1.5 md:col-span-2">
                <label class="block text-xs font-semibold text-slate-300" for="nama_produk">
                    Nama Produk <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="nama_produk" 
                    name="nama_produk" 
                    required 
                    placeholder="Contoh: Neon Box Akrilik Bulat 60cm" 
                    value="<?= htmlspecialchars($_POST['nama_produk'] ?? '') ?>"
                    class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white placeholder-slate-500 outline-none transition-all"
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
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach ($kategori_list as $kat): ?>
                        <option value="<?= $kat['id'] ?>" <?= (isset($_POST['kategori_id']) && $_POST['kategori_id'] == $kat['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($kat['nama_kategori']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Harga -->
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-300" for="harga">
                    Harga (Rp) <span class="text-red-500">*</span>
                </label>
                <div class="relative flex items-center">
                    <span class="absolute left-4 text-xs font-bold text-slate-500">Rp</span>
                    <input 
                        type="number" 
                        id="harga" 
                        name="harga" 
                        required 
                        min="0"
                        step="1000"
                        placeholder="50000" 
                        value="<?= htmlspecialchars($_POST['harga'] ?? '50000') ?>"
                        class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl pl-11 pr-4 py-2.5 text-xs sm:text-sm text-white placeholder-slate-500 outline-none"
                    >
                </div>
            </div>

            <!-- Deskripsi Singkat -->
            <div class="space-y-1.5 md:col-span-2">
                <label class="block text-xs font-semibold text-slate-300" for="deskripsi">
                    Deskripsi Singkat / Spesifikasi
                </label>
                <textarea 
                    id="deskripsi" 
                    name="deskripsi" 
                    rows="3" 
                    placeholder="Contoh: Bahan akrilik susu 3mm dengan lampu LED modul hemat daya & trafo waterproof." 
                    class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl p-4 text-xs sm:text-sm text-white placeholder-slate-500 outline-none"
                ><?= htmlspecialchars($_POST['deskripsi'] ?? '') ?></textarea>
            </div>

            <!-- Pesan WhatsApp Default -->
            <div class="space-y-1.5 md:col-span-2">
                <label class="block text-xs font-semibold text-slate-300" for="wa_text">
                    Teks Pesan WhatsApp Otomatis (Opsional)
                </label>
                <input 
                    type="text" 
                    id="wa_text" 
                    name="wa_text" 
                    placeholder="Halo admin, saya ingin pesan produk ini..." 
                    value="<?= htmlspecialchars($_POST['wa_text'] ?? '') ?>"
                    class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white placeholder-slate-500 outline-none"
                >
                <p class="text-[11px] text-slate-500">Pesan ini otomatis muncul saat calon pembeli mengklik tombol "PESAN VIA WA".</p>
            </div>

            <!-- Upload Foto Produk -->
            <div class="space-y-1.5 md:col-span-2">
                <label class="block text-xs font-semibold text-slate-300">
                    Foto Produk (Format: JPG, PNG, WEBP max 5MB)
                </label>
                <div class="border-2 border-dashed border-slate-700/80 hover:border-red-500/80 rounded-2xl p-6 text-center bg-[#0d0f17] transition-all">
                    <i data-lucide="upload-cloud" class="w-8 h-8 text-slate-500 mx-auto mb-2"></i>
                    <p class="text-xs text-slate-300 font-semibold mb-1">Pilih foto produk dari komputer</p>
                    <p class="text-[11px] text-slate-500 mb-3">Jika dikosongkan, akan menggunakan template mockup default.</p>
                    <input 
                        type="file" 
                        name="gambar" 
                        accept=".jpg,.jpeg,.png,.webp"
                        class="text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-white hover:file:bg-slate-700 cursor-pointer"
                    >
                </div>
            </div>

            <!-- Switch Status Aktif -->
            <div class="md:col-span-2 flex items-center gap-3 pt-2">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" class="sr-only peer" checked>
                    <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                </label>
                <div>
                    <span class="text-xs font-bold text-white block">Publikasikan Langsung</span>
                    <span class="text-[11px] text-slate-400">Jika dicentang, produk akan langsung tayang di katalog halaman web.</span>
                </div>
            </div>

        </div>

        <!-- Submit Button -->
        <div class="pt-6 border-t border-slate-800 flex justify-end gap-3">
            <a href="produk.php" class="px-5 py-2.5 rounded-xl border border-slate-700 text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-800 transition-all">
                Batal
            </a>
            <button 
                type="submit" 
                class="bg-red-600 hover:bg-red-700 active:scale-95 text-white font-bold text-xs sm:text-sm px-7 py-2.5 rounded-xl shadow-lg transition-all flex items-center gap-2"
            >
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Simpan Produk</span>
            </button>
        </div>

    </form>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
