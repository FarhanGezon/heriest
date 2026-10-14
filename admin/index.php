<?php
$page_title = "Dashboard";
require_once __DIR__ . '/includes/header.php';

$db = getDB();

// Hitung data statistik
$total_produk = 0;
$total_kategori = 0;
$total_aktif = 0;
$total_portofolio = 0;
$total_leads = 0;
$leads_7hari = 0;
$recent_products = [];
$recent_leads = [];

if ($db) {
    try {
        $total_produk = $db->query("SELECT COUNT(*) FROM produk")->fetchColumn();
        $total_kategori = $db->query("SELECT COUNT(*) FROM kategori")->fetchColumn();
        $total_aktif = $db->query("SELECT COUNT(*) FROM produk WHERE is_active = 1")->fetchColumn();
        try { $total_portofolio = $db->query("SELECT COUNT(*) FROM portofolio")->fetchColumn(); } catch (Exception $e) {}
        try {
            $total_leads = $db->query("SELECT COUNT(*) FROM wa_clicks")->fetchColumn();
            $leads_7hari = $db->query("SELECT COUNT(*) FROM wa_clicks WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();
            $stmt = $db->query("SELECT * FROM wa_clicks ORDER BY id DESC LIMIT 5");
            $recent_leads = $stmt->fetchAll();
        } catch (Exception $e) {}

        // Ambil 5 produk terbaru
        $stmt = $db->query("
            SELECT p.*, k.nama_kategori 
            FROM produk p 
            LEFT JOIN kategori k ON p.kategori_id = k.id 
            ORDER BY p.id DESC 
            LIMIT 5
        ");
        $recent_products = $stmt->fetchAll();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<div class="space-y-6">
    
    <!-- Welcome Banner -->
    <div id="welcome-banner" class="relative overflow-hidden bg-gradient-to-r from-red-950/80 via-[#141622] to-[#141622] border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl">
        <div class="relative z-10 max-w-2xl">
            <span class="text-xs font-bold uppercase tracking-wider text-red-400 bg-red-950/60 border border-red-900/60 px-3 py-1 rounded-full inline-block mb-3">
                Selamat Datang Kembali
            </span>
            <h1 class="text-xl sm:text-2xl font-black text-white">
                Halo, <?= htmlspecialchars($admin_nama) ?>! 👋
            </h1>
            <p class="text-xs sm:text-sm text-slate-300 mt-2 leading-relaxed">
                Kelola katalog produk promosi, kategori, dan pengaturan website <strong>HERIEST ADVERTISING</strong> dengan mudah dan cepat di sini.
            </p>
            <div class="mt-5 flex flex-wrap gap-3">
                <a href="produk_tambah.php" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 active:scale-95 text-white font-bold text-xs px-5 py-2.5 rounded-xl shadow transition-all">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Tambah Produk Baru</span>
                </a>
                <a href="kategori.php" class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs px-5 py-2.5 rounded-xl border border-slate-700 transition-all">
                    <i data-lucide="tags" class="w-4 h-4"></i>
                    <span>Kelola Kategori</span>
                </a>
            </div>
        </div>
        
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-red-600/10 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
        
        <!-- Stat 1: Total Produk -->
        <div class="bg-[#141622] border border-slate-800/90 rounded-2xl p-5 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Produk</p>
                <h3 class="text-2xl font-black text-white mt-1"><?= number_format($total_produk) ?></h3>
                <p class="text-[11px] text-slate-500 mt-1">Item di katalog</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-red-950/60 border border-red-900/50 text-red-400 flex items-center justify-center">
                <i data-lucide="box" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- Stat 2: Produk Aktif -->
        <div class="bg-[#141622] border border-slate-800/90 rounded-2xl p-5 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Produk Tayang</p>
                <h3 class="text-2xl font-black text-emerald-400 mt-1"><?= number_format($total_aktif) ?></h3>
                <p class="text-[11px] text-slate-500 mt-1">Muncul di website</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-950/60 border border-emerald-900/50 text-emerald-400 flex items-center justify-center">
                <i data-lucide="check-circle-2" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- Stat 3: Total Kategori -->
        <div class="bg-[#141622] border border-slate-800/90 rounded-2xl p-5 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Kategori</p>
                <h3 class="text-2xl font-black text-sky-400 mt-1"><?= number_format($total_kategori) ?></h3>
                <p class="text-[11px] text-slate-500 mt-1">Kategori layanan</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-sky-950/60 border border-sky-900/50 text-sky-400 flex items-center justify-center">
                <i data-lucide="tags" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- Stat 4: WhatsApp CS -->
        <div class="bg-[#141622] border border-slate-800/90 rounded-2xl p-5 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">WhatsApp Utama</p>
                <h3 class="text-sm font-bold text-amber-400 mt-1 truncate max-w-[140px]"><?= htmlspecialchars(getSetting('whatsapp', '082264356885')) ?></h3>
                <a href="pengaturan.php" class="text-[11px] text-slate-400 hover:text-white underline mt-1 inline-block">Ubah nomor</a>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-950/60 border border-amber-900/50 text-amber-400 flex items-center justify-center">
                <i data-lucide="message-circle" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- Stat 5: Klik WhatsApp (Leads) -->
        <div class="bg-[#141622] border border-slate-800/90 rounded-2xl p-5 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Klik WhatsApp (Leads)</p>
                <h3 class="text-2xl font-black text-emerald-400 mt-1"><?= number_format($total_leads) ?></h3>
                <p class="text-[11px] text-slate-500 mt-1">+<?= number_format($leads_7hari) ?> dalam 7 hari</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-950/60 border border-emerald-900/50 text-emerald-400 flex items-center justify-center">
                <i data-lucide="mouse-pointer-click" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- Stat 6: Total Portofolio -->
        <div class="bg-[#141622] border border-slate-800/90 rounded-2xl p-5 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Portofolio</p>
                <h3 class="text-2xl font-black text-violet-400 mt-1"><?= number_format($total_portofolio) ?></h3>
                <a href="portofolio.php" class="text-[11px] text-slate-400 hover:text-white underline mt-1 inline-block">Kelola portofolio</a>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-violet-950/60 border border-violet-900/50 text-violet-400 flex items-center justify-center">
                <i data-lucide="briefcase" class="w-6 h-6"></i>
            </div>
        </div>

    </div>

    <!-- Recent Products Table -->
    <div class="bg-[#141622] border border-slate-800/90 rounded-3xl p-5 sm:p-6 shadow-sm">
        
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3 pb-5 border-b border-slate-800">
            <div>
                <h3 class="font-bold text-base text-white">Produk Terbaru</h3>
                <p class="text-xs text-slate-400">Daftar produk yang baru saja ditambahkan</p>
            </div>
            <a href="produk.php" class="inline-flex items-center gap-1.5 text-xs font-semibold text-red-400 hover:text-red-300">
                <span>Lihat Semua Produk</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>

        <div class="overflow-x-auto mt-4">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 font-semibold text-[11px] uppercase tracking-wider">
                        <th class="py-3 px-3">Produk</th>
                        <th class="py-3 px-3">Kategori</th>
                        <th class="py-3 px-3">Harga</th>
                        <th class="py-3 px-3">Status</th>
                        <th class="py-3 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-slate-300">
                    <?php if (empty($recent_products)): ?>
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-500">
                                Belum ada produk yang ditambahkan.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recent_products as $p): ?>
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="py-3 px-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-[#0d0f17] border border-slate-800 shrink-0 overflow-hidden flex items-center justify-center">
                                        <?php if (!empty($p['gambar']) && file_exists(__DIR__ . '/../assets/images/uploads/' . $p['gambar'])): ?>
                                            <img src="../assets/images/uploads/<?= htmlspecialchars($p['gambar']) ?>" alt="" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <i data-lucide="image" class="w-4 h-4 text-slate-600"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <p class="font-bold text-white text-xs sm:text-sm line-clamp-1"><?= htmlspecialchars($p['nama_produk']) ?></p>
                                        <p class="text-[11px] text-slate-500 line-clamp-1"><?= htmlspecialchars($p['deskripsi']) ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-3">
                                <span class="bg-slate-800 text-slate-300 border border-slate-700 px-2.5 py-1 rounded-full text-[11px] font-medium">
                                    <?= htmlspecialchars($p['nama_kategori'] ?? 'Tanpa Kategori') ?>
                                </span>
                            </td>
                            <td class="py-3 px-3 font-bold text-amber-400">
                                <?= formatRupiah($p['harga']) ?>
                            </td>
                            <td class="py-3 px-3">
                                <?php if ($p['is_active']): ?>
                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-400 bg-emerald-950/60 border border-emerald-900/60 px-2.5 py-0.5 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Aktif
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-400 bg-slate-800 px-2.5 py-0.5 rounded-full">
                                        Draft
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="produk_edit.php?id=<?= $p['id'] ?>" class="p-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-lg transition-all" title="Edit Produk">
                                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                    </a>
                                    <a href="produk_hapus.php?id=<?= $p['id'] ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?');" class="p-1.5 bg-slate-800 hover:bg-red-950/80 text-slate-300 hover:text-red-400 rounded-lg transition-all" title="Hapus Produk">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
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

    <!-- Klik WA Terbaru -->
    <?php if (!empty($recent_leads)): ?>
    <div class="bg-[#141622] border border-slate-800/90 rounded-3xl p-5 sm:p-6 shadow-sm">
        <div class="pb-5 border-b border-slate-800">
            <h3 class="font-bold text-base text-white">Klik WhatsApp Terbaru</h3>
            <p class="text-xs text-slate-400">Asal tombol yang paling sering ditekan pengunjung</p>
        </div>
        <div class="overflow-x-auto mt-4">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 font-semibold text-[11px] uppercase tracking-wider">
                        <th class="py-3 px-3">Sumber Tombol</th>
                        <th class="py-3 px-3">Ref ID</th>
                        <th class="py-3 px-3 text-right">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-slate-300">
                    <?php foreach ($recent_leads as $l): ?>
                    <tr class="hover:bg-slate-800/30 transition-colors">
                        <td class="py-3 px-3">
                            <span class="bg-emerald-950/60 text-emerald-300 border border-emerald-900/60 px-2.5 py-1 rounded-full text-[11px] font-semibold">
                                <?= htmlspecialchars($l['sumber']) ?>
                            </span>
                        </td>
                        <td class="py-3 px-3 font-mono text-xs text-slate-400"><?= $l['ref_id'] ? (int)$l['ref_id'] : '-' ?></td>
                        <td class="py-3 px-3 text-right text-xs text-slate-400"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($l['created_at']))) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
