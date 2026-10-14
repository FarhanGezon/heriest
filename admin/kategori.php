<?php
$page_title = "Kategori Produk";
require_once __DIR__ . '/auth.php';

$db = getDB();
$error = '';
$msg = $_GET['msg'] ?? '';

// Handle Tambah Kategori
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'tambah') {
    $nama_kategori = trim($_POST['nama_kategori'] ?? '');
    if (empty($nama_kategori)) {
        $error = 'Nama kategori tidak boleh kosong.';
    } else {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $nama_kategori)));
        try {
            $stmt = $db->prepare("INSERT INTO kategori (nama_kategori, slug) VALUES (?, ?)");
            $stmt->execute([$nama_kategori, $slug]);
            header('Location: kategori.php?msg=created');
            exit;
        } catch (Exception $e) {
            $error = 'Kategori sudah ada atau terjadi kesalahan: ' . $e->getMessage();
        }
    }
}

// Handle Update Kategori
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = (int)($_POST['id'] ?? 0);
    $nama_kategori = trim($_POST['nama_kategori'] ?? '');
    if ($id > 0 && !empty($nama_kategori)) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $nama_kategori)));
        try {
            $stmt = $db->prepare("UPDATE kategori SET nama_kategori = ?, slug = ? WHERE id = ?");
            $stmt->execute([$nama_kategori, $slug, $id]);
            header('Location: kategori.php?msg=updated');
            exit;
        } catch (Exception $e) {
            $error = 'Gagal memperbarui kategori: ' . $e->getMessage();
        }
    }
}

// Handle Hapus Kategori
if (isset($_GET['action']) && $_GET['action'] === 'hapus') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0) {
        try {
            $stmt = $db->prepare("DELETE FROM kategori WHERE id = ?");
            $stmt->execute([$id]);
            header('Location: kategori.php?msg=deleted');
            exit;
        } catch (Exception $e) {
            $error = 'Gagal menghapus kategori.';
        }
    }
}

// Ambil list kategori dengan jumlah produk
$categories = [];
if ($db) {
    $categories = $db->query("
        SELECT k.*, COUNT(p.id) as total_produk
        FROM kategori k
        LEFT JOIN produk p ON k.id = p.kategori_id
        GROUP BY k.id
        ORDER BY k.id ASC
    ")->fetchAll();
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="space-y-6">
    
    <!-- Flash Messages -->
    <?php if ($msg === 'created'): ?>
        <div class="bg-emerald-950/80 border border-emerald-800 text-emerald-300 text-xs sm:text-sm rounded-2xl p-4 flex items-center gap-2.5">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-400"></i>
            <span>Kategori baru berhasil ditambahkan!</span>
        </div>
    <?php elseif ($msg === 'updated'): ?>
        <div class="bg-sky-950/80 border border-sky-800 text-sky-300 text-xs sm:text-sm rounded-2xl p-4 flex items-center gap-2.5">
            <i data-lucide="check-circle" class="w-5 h-5 text-sky-400"></i>
            <span>Kategori berhasil diperbarui!</span>
        </div>
    <?php elseif ($msg === 'deleted'): ?>
        <div class="bg-red-950/80 border border-red-800 text-red-300 text-xs sm:text-sm rounded-2xl p-4 flex items-center gap-2.5">
            <i data-lucide="trash-2" class="w-5 h-5 text-red-400"></i>
            <span>Kategori berhasil dihapus.</span>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="bg-red-950/80 border border-red-800 text-red-300 text-xs sm:text-sm rounded-2xl p-4 flex items-center gap-2.5">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-400"></i>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        <!-- Form Tambah Kategori -->
        <div class="bg-[#141622] border border-slate-800 rounded-3xl p-6 shadow-sm">
            <h3 class="font-bold text-sm text-white mb-1 flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-4 h-4 text-red-500"></i>
                <span>Tambah Kategori Baru</span>
            </h3>
            <p class="text-xs text-slate-400 mb-5">Grup kategori untuk memfilter produk di web</p>

            <form action="kategori.php" method="POST" class="space-y-4">
                <input type="hidden" name="action" value="tambah">
                
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="nama_kategori">Nama Kategori</label>
                    <input 
                        type="text" 
                        name="nama_kategori" 
                        id="nama_kategori" 
                        required
                        placeholder="Contoh: Digital Printing" 
                        class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white placeholder-slate-500 outline-none transition-all"
                    >
                </div>

                <button 
                    type="submit" 
                    class="w-full bg-red-600 hover:bg-red-700 active:scale-95 text-white font-bold text-xs py-2.5 rounded-xl shadow transition-all flex items-center justify-center gap-2"
                >
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Simpan Kategori</span>
                </button>
            </form>
        </div>

        <!-- Tabel Daftar Kategori -->
        <div class="lg:col-span-2 bg-[#141622] border border-slate-800 rounded-3xl p-6 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                <div>
                    <h3 class="font-bold text-sm text-white">Daftar Kategori</h3>
                    <p class="text-xs text-slate-400">Semua kategori layanan yang terdaftar</p>
                </div>
                <span class="text-xs text-slate-400 font-semibold bg-slate-800 px-3 py-1 rounded-full">
                    Total: <?= count($categories) ?>
                </span>
            </div>

            <div class="overflow-x-auto mt-4">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 font-semibold text-[11px] uppercase tracking-wider">
                            <th class="py-3 px-3">#</th>
                            <th class="py-3 px-3">Nama Kategori</th>
                            <th class="py-3 px-3">Slug URL</th>
                            <th class="py-3 px-3 text-center">Jumlah Produk</th>
                            <th class="py-3 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 text-slate-300">
                        <?php if (empty($categories)): ?>
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-500">
                                    Belum ada kategori.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($categories as $idx => $cat): ?>
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="py-3 px-3 text-slate-500 font-mono text-[11px]">
                                    <?= $idx + 1 ?>
                                </td>
                                <td class="py-3 px-3 font-bold text-white">
                                    <?= htmlspecialchars($cat['nama_kategori']) ?>
                                </td>
                                <td class="py-3 px-3 font-mono text-xs text-slate-400">
                                    <?= htmlspecialchars($cat['slug']) ?>
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <span class="bg-slate-800 text-slate-300 px-2.5 py-0.5 rounded-full text-xs font-semibold">
                                        <?= (int)$cat['total_produk'] ?> Item
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button 
                                            onclick="editKategori(<?= $cat['id'] ?>, '<?= addslashes(htmlspecialchars($cat['nama_kategori'])) ?>')"
                                            class="p-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-lg transition-all" 
                                            title="Edit Kategori"
                                        >
                                            <i data-lucide="edit-3" class="w-3.5 h-3.5 text-sky-400"></i>
                                        </button>
                                        <a 
                                            href="kategori.php?action=hapus&id=<?= $cat['id'] ?>" 
                                            onclick="return confirm('Yakin ingin menghapus kategori <?= addslashes($cat['nama_kategori']) ?>? Produk terkait tidak akan terhapus.');" 
                                            class="p-1.5 bg-slate-800 hover:bg-red-950/80 text-slate-400 hover:text-red-400 rounded-lg transition-all" 
                                            title="Hapus Kategori"
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

<!-- Modal Edit Kategori JS -->
<div id="modal-edit-kategori" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-[#141622] border border-slate-800 rounded-3xl p-6 max-w-md w-full shadow-2xl">
        <h3 class="font-bold text-base text-white mb-1">Edit Kategori</h3>
        <p class="text-xs text-slate-400 mb-4">Ubah nama kategori layanan</p>

        <form action="kategori.php" method="POST" class="space-y-4">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="edit_id">

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Nama Kategori</label>
                <input 
                    type="text" 
                    name="nama_kategori" 
                    id="edit_nama_kategori" 
                    required 
                    class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white outline-none"
                >
            </div>

            <div class="flex justify-end gap-2.5 pt-2">
                <button 
                    type="button" 
                    onclick="document.getElementById('modal-edit-kategori').classList.add('hidden')"
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

<!-- Fungsi editKategori() kini di assets/js/admin.js -->

<?php require_once __DIR__ . '/includes/footer.php'; ?>
