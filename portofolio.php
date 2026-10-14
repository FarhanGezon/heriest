<?php
$page_title = "Portofolio Hasil Karya";
require_once __DIR__ . '/config/database.php';
include 'includes/header.php';

$wa_utama = getSetting('whatsapp', '082264356885');
$wa_link = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $wa_utama));

// Ambil portofolio dari database (fallback contoh jika kosong)
$db = getDB();
$db_rows = [];
if ($db) {
    try {
        $db_rows = $db->query(
            "SELECT pf.*, k.nama_kategori, k.slug
             FROM portofolio pf
             LEFT JOIN kategori k ON pf.kategori_id = k.id
             WHERE pf.is_active = 1
             ORDER BY pf.id DESC"
        )->fetchAll();
    } catch (Exception $e) { $db_rows = []; }
}

$grad_map = [
    'neon-box' => 'from-rose-500 to-orange-400',
    'cetak-banner' => 'from-sky-500 to-cyan-400',
    'huruf-timbul' => 'from-amber-500 to-yellow-400',
    'merchandise' => 'from-emerald-500 to-teal-400',
    'sablon-kaos' => 'from-violet-500 to-purple-400',
];

if (!empty($db_rows)) {
    $semua_karya = array_map(function ($r) use ($grad_map) {
        $slug = $r['slug'] ?? 'lainnya';
        $img = $r['gambar'] ?? '';
        $img_ok = !empty($img) && file_exists(__DIR__ . '/assets/images/uploads/' . $img);
        return [
            'judul' => $r['judul'],
            'klien' => $r['klien'] !== '' ? $r['klien'] : '-',
            'kategori' => $r['nama_kategori'] ?? 'Lainnya',
            'slug' => $slug,
            'lokasi' => $r['lokasi'] ?? '',
            'tahun' => $r['tahun'] ?? '',
            'deskripsi' => $r['deskripsi'] ?? '',
            'grad' => $grad_map[$slug] ?? 'from-slate-500 to-slate-400',
            'icon' => 'briefcase',
            'gambar' => $img_ok ? 'assets/images/uploads/' . $img : '',
            'id' => (int)$r['id'],
        ];
    }, $db_rows);
} else {
// Contoh data portofolio (tampil hanya jika database masih kosong)
$semua_karya = [
    [
        'judul' => 'Neon Box Bulat 80cm',
        'klien' => 'Kafe Aroma',
        'kategori' => 'Neon Box',
        'slug' => 'neon-box',
        'lokasi' => 'Karanganyar',
        'tahun' => '2026',
        'deskripsi' => 'Neon box 2 sisi akrilik + LED hemat energi untuk plang cafe.',
        'grad' => 'from-rose-500 to-orange-400',
        'icon' => 'lightbulb',
    ],
    [
        'judul' => 'Spanduk Grand Opening 4x1m',
        'klien' => 'Toko Berkah Jaya',
        'kategori' => 'Cetak Banner',
        'slug' => 'cetak-banner',
        'lokasi' => 'Solo',
        'tahun' => '2025',
        'deskripsi' => 'Spanduk flexi Korea 440gsm warna tajam anti pudar.',
        'grad' => 'from-sky-500 to-cyan-400',
        'icon' => 'printer',
    ],
    [
        'judul' => 'Huruf Timbul Stainless Mirror',
        'klien' => 'Kantor Notaris',
        'kategori' => 'Huruf Timbul',
        'slug' => 'huruf-timbul',
        'lokasi' => 'Madiun',
        'tahun' => '2025',
        'deskripsi' => 'Huruf timbul stainless kilap mirror, tinggi 40cm.',
        'grad' => 'from-amber-500 to-yellow-400',
        'icon' => 'box',
    ],
    [
        'judul' => 'X-Banner Event Kuliner',
        'klien' => 'Festival Kuliner',
        'kategori' => 'Cetak Banner',
        'slug' => 'cetak-banner',
        'lokasi' => 'Ngawi',
        'tahun' => '2024',
        'deskripsi' => 'Paket 10 X-banner albatros + tiang fiber untuk event.',
        'grad' => 'from-violet-500 to-purple-400',
        'icon' => 'flag',
    ],
    [
        'judul' => 'Kaos Sablon Komunitas 100pcs',
        'klien' => 'Komunitas Motor',
        'kategori' => 'Merchandise',
        'slug' => 'merchandise',
        'lokasi' => 'Karanganyar',
        'tahun' => '2025',
        'deskripsi' => 'Kaos cotton combed 30s sablon DTF warna tajam.',
        'grad' => 'from-emerald-500 to-teal-400',
        'icon' => 'shirt',
    ],
    [
        'judul' => 'Roll Up Banner Pameran UMKM',
        'klien' => 'Dinas Koperasi UMKM',
        'kategori' => 'Cetak Banner',
        'slug' => 'cetak-banner',
        'lokasi' => 'Solo',
        'tahun' => '2024',
        'deskripsi' => 'Roll up banner 60x160 kaki aluminium untuk pameran.',
        'grad' => 'from-blue-500 to-indigo-400',
        'icon' => 'presentation',
    ],
];
}

$filter = $_GET['cat'] ?? 'semua';
$karya = ($filter === '' || $filter === 'semua')
    ? $semua_karya
    : array_values(array_filter($semua_karya, fn($k) => $k['slug'] === $filter));

$kategori_filter = ['semua' => 'Semua'];
foreach ($semua_karya as $kk) {
    $ks = $kk['slug'] ?? 'lainnya';
    if (!isset($kategori_filter[$ks])) $kategori_filter[$ks] = $kk['kategori'];
}
?>

<!-- HERO PORTOFOLIO -->
<section class="relative overflow-hidden py-12 sm:py-20 text-center">
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-rose-200/40 dark:bg-rose-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute top-1/4 -right-24 w-96 h-96 bg-sky-200/40 dark:bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6">
        <div class="inline-block mb-3 sm:mb-4">
            <span class="border border-rose-300 dark:border-rose-400/80 text-rose-500 dark:text-rose-400 bg-white/90 dark:bg-[#141622] px-5 py-1.5 rounded-full text-xs font-bold tracking-wide shadow-2xs">
                Portofolio kami
            </span>
        </div>

        <h1 class="text-2xl sm:text-4xl md:text-5xl font-black text-slate-900 dark:text-white tracking-tight uppercase leading-tight max-w-3xl mx-auto mb-4">
            HASIL KARYA <span class="text-sky-500 dark:text-sky-400">HERIEST</span><br>
            UNTUK KLIEN KAMI
        </h1>
        <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm max-w-xl mx-auto mb-8">
            Contoh proyek yang sudah kami kerjakan. Tertarik buat yang serupa? Langsung konsultasi gratis.
        </p>

        <!-- Filter Kategori -->
        <div class="flex items-center gap-2 sm:gap-3 overflow-x-auto py-2 px-1 mb-8 sm:mb-12 justify-start sm:justify-center px-4">
            <?php foreach ($kategori_filter as $slug => $nama): ?>
                <a
                    href="portofolio.php<?= $slug !== 'semua' ? '?cat=' . urlencode($slug) : '' ?>"
                    class="whitespace-nowrap px-4 sm:px-5 py-1.5 rounded-full text-xs font-semibold transition-all shrink-0
                    <?= ($filter === $slug || (empty($filter) && $slug === 'semua'))
                        ? 'border border-rose-400 text-rose-500 dark:text-rose-400 bg-white dark:bg-[#181a28] shadow-2xs'
                        : 'border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 bg-white/70 dark:bg-[#141622]/80 hover:border-rose-300 hover:text-rose-500 dark:hover:text-white'
                    ?>">
                    <?= htmlspecialchars($nama) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Grid Karya -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 text-left">
            <?php if (empty($karya)): ?>
                <div class="col-span-full text-center py-12 text-slate-400">
                    <i data-lucide="inbox" class="w-10 h-10 mx-auto mb-3 text-slate-600"></i>
                    <p class="text-sm font-semibold">Belum ada karya di kategori ini.</p>
                </div>
            <?php else: ?>
                <?php foreach ($karya as $k): ?>
                <div class="group bg-white dark:bg-[#141622] border border-slate-200/90 dark:border-slate-800/90 rounded-2xl sm:rounded-3xl p-3 sm:p-4 shadow-2xs hover:shadow-lg dark:hover:border-slate-700 transition-all flex flex-col justify-between">
                    <!-- Visual Contoh -->
                    <div class="relative w-full aspect-[4/3] rounded-xl sm:rounded-2xl overflow-hidden mb-3 sm:mb-4 bg-gradient-to-br <?= $k['grad'] ?> flex items-center justify-center">
                        <?php if (!empty($k['gambar'] ?? '')): ?>
                            <img src="<?= htmlspecialchars($k['gambar']) ?>" alt="<?= htmlspecialchars($k['judul']) ?>" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent pointer-events-none"></div>
                        <?php else: ?>
                            <div class="absolute inset-0 opacity-20 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:14px_14px]"></div>
                            <i data-lucide="<?= $k['icon'] ?>" class="w-14 h-14 sm:w-16 sm:h-16 text-white/90 group-hover:scale-110 transition-transform duration-300"></i>
                        <?php endif; ?>
                        <span class="absolute top-2.5 left-2.5 bg-black/50 backdrop-blur text-white text-[10px] font-bold px-2.5 py-1 rounded-full">
                            <?= htmlspecialchars($k['kategori']) ?>
                        </span>
                        <span class="absolute bottom-2.5 right-2.5 bg-black/50 backdrop-blur text-white text-[10px] font-semibold px-2.5 py-1 rounded-full">
                            <?= htmlspecialchars($k['tahun']) ?>
                        </span>
                    </div>

                    <div>
                        <h3 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white" title="<?= htmlspecialchars($k['judul']) ?>">
                            <?= htmlspecialchars($k['judul']) ?>
                        </h3>
                        <p class="text-[10px] sm:text-xs text-slate-400 mt-0.5 flex items-center gap-1">
                            <i data-lucide="building-2" class="w-3 h-3 shrink-0"></i>
                            <span class="truncate"><?= htmlspecialchars($k['klien']) ?> • <?= htmlspecialchars($k['lokasi']) ?></span>
                        </p>
                        <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">
                            <?= htmlspecialchars($k['deskripsi']) ?>
                        </p>
                    </div>

                    <div class="mt-3">
                        <a
                            href="https://wa.me/<?= $wa_link ?>?text=<?= urlencode('Halo admin, saya mau buat seperti: ' . $k['judul'] . ' (' . $k['klien'] . ')') ?>"
                            target="_blank"
                            data-wa-sumber="portofolio" data-wa-ref="<?= (int)($k['id'] ?? 0) ?>"
                            class="w-full bg-emerald-500 hover:bg-emerald-600 active:scale-95 text-white font-bold text-[10px] sm:text-xs py-2 sm:py-2.5 px-2 rounded-lg sm:rounded-xl shadow-xs flex items-center justify-center gap-1.5 transition-all"
                        >
                            <i data-lucide="message-circle" class="w-3.5 h-3.5 fill-current shrink-0"></i>
                            <span class="truncate uppercase tracking-wider">Saya mau seperti ini</span>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- CTA Bawah -->
        <div class="mt-10 sm:mt-14 bg-white dark:bg-[#141622] border border-slate-200 dark:border-slate-800 rounded-2xl sm:rounded-3xl p-6 sm:p-8 max-w-2xl mx-auto">
            <h2 class="font-extrabold text-base sm:text-xl text-slate-900 dark:text-white mb-1.5">Punya proyek impian?</h2>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mb-5">Ceritakan kebutuhanmu, kami bantu wujudkan dengan harga kompetitif.</p>
            <a href="https://wa.me/<?= $wa_link ?>?text=<?= urlencode('Halo admin, saya ingin konsultasi kebutuhan advertising') ?>" target="_blank"
                data-wa-sumber="portofolio_cta"
                class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 active:scale-95 text-white font-bold text-xs sm:text-sm px-7 py-2.5 rounded-full shadow-md transition-all">
                <i data-lucide="message-circle" class="w-4 h-4"></i>
                <span>Konsultasi gratis</span>
            </a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
