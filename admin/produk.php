<?php
$page_title = "Kelola Produk";
require_once __DIR__ . '/includes/header.php';

$db = getDB();
$search = trim($_GET['q'] ?? '');
$filter_kategori = trim($_GET['kategori'] ?? '');
$msg = $_GET['msg'] ?? '';

// Ambil data kategori untuk filter
$kategori_list = [];
if ($db) {
    $kategori_list = $db->query("SELECT * FROM kategori ORDER BY nama_kategori ASC")->fetchAll();
}

// Query Produk
$where_clauses = [];
$params = [];

if (!empty($search)) {
    $where_clauses[] = "(p.nama_produk LIKE ? OR p.deskripsi LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

if (!empty($filter_kategori)) {
    $where_clauses[] = "p.kategori_id = ?";
    $params[] = $filter_kategori;
}

$where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";

$sql = "
    SELECT p.*, k.nama_kategori 
    FROM produk p 
    LEFT JOIN kategori k ON p.kategori_id = k.id 
    {$where_sql}
    ORDER BY p.id DESC
";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$produk_list = $stmt->fetchAll();
?>

<div class="space-y-6">
    
    <!-- Flash Messages -->
    <?php if ($msg === 'created'): ?>
        <div class="bg-emerald-950/80 border border-emerald-800 text-emerald-300 text-xs sm:text-sm rounded-2xl p-4 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-400"></i>
                <span>Produk baru berhasil ditambahkan ke katalog!</span>
            </div>
        </div>
    <?php elseif ($msg === 'updated'): ?>
        <div class="bg-sky-950/80 border border-sky-800 text-sky-300 text-xs sm:text-sm rounded-2xl p-4 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <i data-lucide="check-circle" class="w-5 h-5 text-sky-400"></i>
                <span>Data produk berhasil diperbarui!</span>
            </div>
        </div>
    <?php elseif ($msg === 'deleted'): ?>
        <div class="bg-red-950/80 border border-red-800 text-red-300 text-xs sm:text-sm rounded-2xl p-4 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <i data-lucide="trash-2" class="w-5 h-5 text-red-400"></i>
                <span>Produk berhasil dihapus dari database.</span>
            </div>
        </div>
    <?php endif; ?>

    <!-- Header Actions & Search -->
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 bg-[#141622] border border-slate-800 rounded-3xl p-5 shadow-sm">
        
        <!-- Search & Filter Form -->
        <form action="produk.php" method="GET" class="flex flex-wrap sm:flex-nowrap items-center gap-2.5 w-full sm:w-auto">
            
            <div class="relative flex-1 sm:w-64">
                <i data-lucide="search" class="w-4 h-4 text-slate-500 absolute left-3.5 top-3"></i>
                <input 
                    type="text" 
                    name="q" 
                    placeholder="Cari nama produk..." 
                    value="<?= htmlspecialchars($search) ?>"
                    class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl pl-10 pr-4 py-2 text-xs sm:text-sm text-white placeholder-slate-500 outline-none"
                >
            </div>

            <select 
                name="kategori" 
                onchange="this.form.submit()"
                class="bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-3 py-2 text-xs sm:text-sm text-slate-300 outline-none"
            >
                <option value="">Semua Kategori</option>
                <?php foreach ($kategori_list as $kat): ?>
                    <option value="<?= $kat['id'] ?>" <?= $filter_kategori == $kat['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($kat['nama_kategori']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-slate-200 px-3.5 py-2 rounded-xl text-xs font-semibold">
                Filter
            </button>

            <?php if (!empty($search) || !empty($filter_kategori)): ?>
                <a href="produk.php" class="text-xs text-red-400 hover:text-red-300 underline py-2">
                    Reset
                </a>
            <?php endif; ?>

        </form>

        <!-- Add Button -->
        <a href="produk_tambah.php" class="inline-flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 active:scale-95 text-white font-bold text-xs sm:text-sm px-5 py-2.5 rounded-xl shadow-lg transition-all shrink-0">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Tambah Produk</span>
        </a>

    </div>

    <!-- Products Table Card -->
    <div class="bg-[#141622] border border-slate-800 rounded-3xl overflow-hidden shadow-sm">
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead>
                    <tr class="bg-[#0d0f17] border-b border-slate-800 text-slate-400 font-semibold text-[11px] uppercase tracking-wider">
                        <th class="py-4 px-4">Info Produk</th>
                        <th class="py-4 px-4">Kategori</th>
                        <th class="py-4 px-4">Harga Satuan</th>
                        <th class="py-4 px-4">Status</th>
                        <th class="py-4 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-slate-300">
                    <?php if (empty($produk_list)): ?>
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-500">
                                <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 text-slate-600"></i>
                                Tidak ada produk yang ditemukan.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($produk_list as $p): ?>
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            
                            <!-- Foto & Nama -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-12 h-12 rounded-xl bg-[#0d0f17] border border-slate-800 shrink-0 overflow-hidden flex items-center justify-center">
                                        <?php if (!empty($p['gambar']) && file_exists(__DIR__ . '/../assets/images/uploads/' . $p['gambar'])): ?>
                                            <img src="../assets/images/uploads/<?= htmlspecialchars($p['gambar']) ?>" alt="" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <!-- SVG Mockup Placeholder -->
                                            <div class="w-6 h-6 text-slate-600">
                                                <i data-lucide="image" class="w-full h-full"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <p class="font-bold text-white text-xs sm:text-sm"><?= htmlspecialchars($p['nama_produk']) ?></p>
                                        <p class="text-[11px] text-slate-400 line-clamp-1 max-w-xs"><?= htmlspecialchars($p['deskripsi'] ?? '-') ?></p>
                                    </div>
                                </div>
                            </td>

                            <!-- Kategori -->
                            <td class="py-3.5 px-4">
                                <span class="bg-slate-800 text-slate-300 border border-slate-700/80 px-2.5 py-1 rounded-full text-[11px] font-medium whitespace-nowrap">
                                    <?= htmlspecialchars($p['nama_kategori'] ?? 'Tanpa Kategori') ?>
                                </span>
                            </td>

                            <!-- Harga -->
                            <td class="py-3.5 px-4 font-black text-amber-400 whitespace-nowrap">
                                <?= formatRupiah($p['harga']) ?>
                            </td>

                            <!-- Status -->
                            <td class="py-3.5 px-4">
                                <?php if ($p['is_active']): ?>
                                    <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-emerald-400 bg-emerald-950/60 border border-emerald-900/60 px-2.5 py-1 rounded-full whitespace-nowrap">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Tayang
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-slate-400 bg-slate-800 border border-slate-700 px-2.5 py-1 rounded-full whitespace-nowrap">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span> Draft
                                    </span>
                                <?php endif; ?>
                            </td>

                            <!-- Aksi -->
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <a 
                                        href="produk_edit.php?id=<?= $p['id'] ?>" 
                                        class="inline-flex items-center gap-1 bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-all"
                                    >
                                        <i data-lucide="edit-3" class="w-3.5 h-3.5 text-sky-400"></i>
                                        <span>Edit</span>
                                    </a>
                                    
                                    <a 
                                        href="produk_hapus.php?id=<?= $p['id'] ?>" 
                                        onclick="return confirm('Yakin ingin menghapus produk <?= addslashes($p['nama_produk']) ?>?');" 
                                        class="inline-flex items-center gap-1 bg-slate-800 hover:bg-red-950/80 text-slate-400 hover:text-red-400 px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-all"
                                    >
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5 text-red-400"></i>
                                        <span>Hapus</span>
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

<?php require_once __DIR__ . '/includes/footer.php'; ?>
