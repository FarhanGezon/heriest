<?php 
$page_title = "Solusi Cetak & Advertising";
require_once __DIR__ . '/config/database.php';
include 'includes/header.php'; 

// Fetch Pengaturan dari Database
$wa_utama = getSetting('whatsapp', '082264356885');
$wa_link = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $wa_utama));
$wa_template = getSetting('wa_template', 'Halo admin, saya ingin konsultasi kebutuhan advertising');
$hero_badge = getSetting('hero_badge', 'SOLUSI ADVERTISING TERBAIK UNTUK BISNIS ANDA');
$hero_headline = getSetting('hero_headline', 'TINGKATKAN VISIBILITAS BISNIS ANDA BERSAMA KAMI!');
$hero_sub = getSetting('hero_subdeskripsi', 'Solusi cetak, neon box, dan bliboard profesional untuk segala kebutuhan promosi anda');
$hero_cta_text = getSetting('hero_cta_text', 'cek promo sekarang');
$hero_cta_link = getSetting('hero_cta_link', '#produk');
$hero_words = preg_split('/\s+/', trim($hero_headline));
$hero_line1 = implode(' ', array_slice($hero_words, 0, (int)ceil(count($hero_words) / 2)));
$hero_line2 = implode(' ', array_slice($hero_words, (int)ceil(count($hero_words) / 2)));
?>

<!-- 1. HERO SECTION -->
<section id="beranda" class="pt-8 pb-12 sm:pt-16 sm:pb-20 text-center">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        
        <!-- Badge Atas -->
        <p class="text-xs sm:text-xs font-bold text-yellow-500 dark:text-yellow-400 uppercase tracking-widest mb-3">
            <?= htmlspecialchars($hero_badge) ?>
        </p>

        <!-- Headline Utama Responsif -->
        <h1 class="text-2xl sm:text-4xl md:text-5xl font-black text-slate-900 dark:text-white tracking-tight leading-tight uppercase max-w-2xl mx-auto">
            <?= htmlspecialchars($hero_line1) ?><br class="hidden sm:inline">
            <?= htmlspecialchars($hero_line2) ?>
        </h1>

        <!-- Brush Highlight Text -->
        <div class="my-3 sm:my-4 inline-block relative">
            <span class="bg-yellow-400 text-slate-950 font-brush text-2xl sm:text-3xl md:text-4xl px-4 py-1 rounded-md -rotate-1 inline-block shadow-md">
                <?= htmlspecialchars(getSetting('tagline', 'We Touch With Colours.')) ?>
            </span>
        </div>

        <!-- Sub-description -->
        <p class="text-slate-600 dark:text-slate-300 text-xs sm:text-base font-normal max-w-lg mx-auto mt-2 mb-6 sm:mb-8 leading-relaxed">
            <?= htmlspecialchars($hero_sub) ?>
        </p>

        <!-- CTA Buttons -->
        <div class="flex flex-col xs:flex-row sm:flex-row justify-center items-center gap-3.5 max-w-sm sm:max-w-none mx-auto">
            <a href="<?= htmlspecialchars($hero_cta_link) ?>" class="w-full sm:w-auto text-center bg-red-600 hover:bg-red-700 active:scale-95 text-white font-bold text-xs sm:text-sm px-7 py-3 rounded-xl shadow-md transition-all">
                <?= htmlspecialchars($hero_cta_text) ?>
            </a>
            <a href="https://wa.me/<?= $wa_link ?>?text=<?= urlencode($wa_template) ?>" target="_blank" data-wa-sumber="hero_konsultasi" class="w-full sm:w-auto text-center bg-emerald-500 hover:bg-emerald-600 active:scale-95 text-white font-bold text-xs sm:text-sm px-7 py-3 rounded-xl shadow-md flex items-center justify-center gap-2 transition-all">
                <span>konsultasi</span>
            </a>
        </div>

    </div>
</section>

<!-- 2. SECTION BANNER PROMO (dinamis dari admin/banner.php) -->
<?php
$banner_list = [];
$bdb = function_exists('getDB') ? getDB() : null;
if ($bdb) {
    try {
        $banner_list = $bdb->query("SELECT * FROM banner_promo WHERE is_active = 1 ORDER BY id DESC")->fetchAll();
    } catch (Exception $e) { $banner_list = []; }
}
?>
<?php if (!empty($banner_list)): ?>
<section id="promo" class="py-8 sm:py-12">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="grid grid-cols-1 <?= count($banner_list) > 1 ? 'md:grid-cols-2' : 'max-w-3xl mx-auto' ?> gap-4 sm:gap-6">
            <?php foreach ($banner_list as $b): ?>
            <?php
                $b_img_path = __DIR__ . '/assets/images/uploads/' . ($b['gambar'] ?? '');
                $b_has_img = !empty($b['gambar']) && file_exists($b_img_path);
                $b_link = !empty($b['cta_link']) ? $b['cta_link'] : '#produk';
                $b_is_external = strpos($b_link, 'http') === 0;
            ?>
            <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl border border-slate-200 dark:border-slate-800/80 shadow-md group">
                <?php if ($b_has_img): ?>
                    <img src="assets/images/uploads/<?= htmlspecialchars($b['gambar']) ?>" alt="<?= htmlspecialchars($b['headline']) ?>"
                        class="w-full h-52 sm:h-64 object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                <?php else: ?>
                    <div class="w-full h-52 sm:h-64 bg-gradient-to-br from-red-600 via-rose-600 to-orange-500 flex items-center justify-center">
                        <i data-lucide="megaphone" class="w-12 h-12 text-white/70"></i>
                    </div>
                <?php endif; ?>
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent pointer-events-none"></div>
                <div class="absolute bottom-0 inset-x-0 p-5 sm:p-6 text-left">
                    <h3 class="font-black text-base sm:text-xl text-white leading-tight mb-1.5 drop-shadow">
                        <?= htmlspecialchars($b['headline']) ?>
                    </h3>
                    <?php if (!empty($b['deskripsi'])): ?>
                    <p class="text-[11px] sm:text-xs text-slate-200 leading-relaxed mb-3.5 line-clamp-2">
                        <?= htmlspecialchars($b['deskripsi']) ?>
                    </p>
                    <?php endif; ?>
                    <a href="<?= htmlspecialchars($b_link) ?>" <?= $b_is_external ? 'target="_blank" rel="noopener"' : '' ?> data-wa-sumber="banner_promo" data-wa-ref="<?= (int)$b['id'] ?>"
                        class="inline-flex items-center gap-1.5 bg-white hover:bg-red-600 hover:text-white active:scale-95 text-slate-900 font-bold text-[11px] sm:text-xs px-5 py-2 rounded-full shadow transition-all">
                        <span><?= htmlspecialchars($b['cta_text']) ?></span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 3. SECTION DI PERCAYA OLEH -->
<?php
$mitra_list = [];
$mdb = function_exists('getDB') ? getDB() : null;
if ($mdb) {
    try {
        $mitra_list = $mdb->query("SELECT * FROM mitra WHERE is_active = 1 ORDER BY id ASC")->fetchAll();
    } catch (Exception $e) { $mitra_list = []; }
}
?>
<section class="py-5 sm:py-6 border-y border-slate-200/60 dark:border-slate-800/80 bg-white/40 dark:bg-black/20 backdrop-blur-sm">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <p class="text-xs font-bold text-slate-700 dark:text-white uppercase tracking-wider mb-3">Di Percaya Oleh</p>
        <div class="flex flex-wrap justify-center items-center gap-3 sm:gap-6">
            <?php if (empty($mitra_list)): ?>
            <div class="border border-slate-300 dark:border-slate-800 bg-white dark:bg-[#13151b] rounded-lg px-4 py-2 font-bold text-xs text-slate-800 dark:text-white flex items-center gap-2 shadow-2xs">
                <i data-lucide="building-2" class="w-3.5 h-3.5 text-slate-500 dark:text-slate-400"></i> BRI
            </div>
            <div class="border border-slate-300 dark:border-slate-800 bg-white dark:bg-[#13151b] rounded-lg px-4 py-2 font-bold text-xs text-slate-800 dark:text-white shadow-2xs">
                deeRka
            </div>
            <?php else: ?>
                <?php foreach ($mitra_list as $m): ?>
                    <?php
                        $logo_path = __DIR__ . '/assets/images/uploads/' . ($m['logo'] ?? '');
                        $has_logo = !empty($m['logo']) && file_exists($logo_path);
                    ?>
                    <?php if (!empty($m['link']) && $m['link'] !== '#'): ?>
                    <a href="<?= htmlspecialchars($m['link']) ?>" target="_blank" rel="noopener" title="<?= htmlspecialchars($m['nama_perusahaan']) ?>"
                        class="border border-slate-300 dark:border-slate-800 bg-white dark:bg-[#13151b] rounded-lg px-4 py-2 font-bold text-xs text-slate-800 dark:text-white flex items-center gap-2 shadow-2xs hover:shadow-md transition-shadow">
                    <?php else: ?>
                    <div title="<?= htmlspecialchars($m['nama_perusahaan']) ?>"
                        class="border border-slate-300 dark:border-slate-800 bg-white dark:bg-[#13151b] rounded-lg px-4 py-2 font-bold text-xs text-slate-800 dark:text-white flex items-center gap-2 shadow-2xs">
                    <?php endif; ?>
                        <?php if ($has_logo): ?>
                            <img src="assets/images/uploads/<?= htmlspecialchars($m['logo']) ?>" alt="<?= htmlspecialchars($m['nama_perusahaan']) ?>" class="h-6 w-auto object-contain">
                        <?php else: ?>
                            <i data-lucide="building-2" class="w-3.5 h-3.5 text-slate-500 dark:text-slate-400"></i> <?= htmlspecialchars($m['nama_perusahaan']) ?>
                        <?php endif; ?>
                    <?php if (!empty($m['link']) && $m['link'] !== '#'): ?>
                    </a>
                    <?php else: ?>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- 4. SECTION SOLUSI LENGKAP MEDIA PROMOSI -->
<section id="layanan" class="py-12 sm:py-16 text-center">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        
        <span class="text-xs text-red-600 bg-white font-bold px-3.5 py-1 rounded-full inline-block mb-3 shadow-xs">
            Layanan unggulan
        </span>
        
        <h2 class="text-xl sm:text-3xl font-extrabold text-slate-900 dark:text-white mb-1.5">Solusi Lengkap Media Promosi</h2>
        <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm max-w-xl mx-auto mb-8 sm:mb-12">Pilihan material terbaik, awet, terlengkap dan presisi tinggi dikerjakan dengan mesin modern</p>

        <!-- 4 Grid Cards Services -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-5 text-left">
            
            <!-- Card 1 -->
            <div class="bg-white dark:bg-[#141622] border border-slate-200 dark:border-slate-800/90 rounded-2xl sm:rounded-3xl p-5 shadow-xs hover:shadow-lg dark:hover:border-slate-700 transition-all flex sm:flex-col items-start gap-3.5 sm:gap-0">
                <div class="w-10 h-10 sm:w-11 sm:h-11 shrink-0 bg-rose-100 text-rose-600 dark:bg-rose-950/60 dark:text-rose-400 dark:border dark:border-rose-900/40 rounded-xl flex items-center justify-center sm:mb-4">
                    <i data-lucide="map-pin" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white mb-1">Neon Box Custom</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Bentuk bulat, kotak, atau custom logo. Lampu LED terang & hemat energi dengan garansi pengerjaan rapi.</p>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="bg-white dark:bg-[#141622] border border-slate-200 dark:border-slate-800/90 rounded-2xl sm:rounded-3xl p-5 shadow-xs hover:shadow-lg dark:hover:border-slate-700 transition-all flex sm:flex-col items-start gap-3.5 sm:gap-0">
                <div class="w-10 h-10 sm:w-11 sm:h-11 shrink-0 bg-sky-100 text-sky-600 dark:bg-sky-950/60 dark:text-sky-400 dark:border dark:border-sky-900/40 rounded-xl flex items-center justify-center sm:mb-4">
                    <i data-lucide="printer" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white mb-1">Cetak Banner & Spanduk</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Spanduk Flexi Korea, Jerman, Albatros, X-Banner & Roll-Banner tajam resolusi tinggi.</p>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="bg-white dark:bg-[#141622] border border-slate-200 dark:border-slate-800/90 rounded-2xl sm:rounded-3xl p-5 shadow-xs hover:shadow-lg dark:hover:border-slate-700 transition-all flex sm:flex-col items-start gap-3.5 sm:gap-0">
                <div class="w-10 h-10 sm:w-11 sm:h-11 shrink-0 bg-amber-100 text-amber-600 dark:bg-amber-950/60 dark:text-amber-400 dark:border dark:border-amber-900/40 rounded-xl flex items-center justify-center sm:mb-4">
                    <i data-lucide="box" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white mb-1">Huruf Timbul 3D</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Stainless steel elegan, galvanis berkilau presisi, serta akrilik dengan lampu backlight modern.</p>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="bg-white dark:bg-[#141622] border border-slate-200 dark:border-slate-800/90 rounded-2xl sm:rounded-3xl p-5 shadow-xs hover:shadow-lg dark:hover:border-slate-700 transition-all flex sm:flex-col items-start gap-3.5 sm:gap-0">
                <div class="w-10 h-10 sm:w-11 sm:h-11 shrink-0 bg-purple-100 text-purple-600 dark:bg-purple-950/60 dark:text-purple-400 dark:border dark:border-purple-900/40 rounded-xl flex items-center justify-center sm:mb-4">
                    <i data-lucide="gift" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white mb-1">Merchandise & Sablon</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Kaos seragam komunitas karyawan, tumbler custom, mug cetak, plakat, dan souvenir promosi.</p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- 5. SECTION PRODUK (DYNAMIC MYSQL) -->
<?php
$db = getDB();
$db_categories = [];
$db_products = [];
$filter_kategori_name = $_GET['cat'] ?? '';
$search_query = trim($_GET['cari'] ?? '');

if ($db) {
    try {
        $db_categories = $db->query("SELECT * FROM kategori ORDER BY id ASC")->fetchAll();
        
        $where = ["p.is_active = 1"];
        $params = [];

        if (!empty($search_query)) {
            $where[] = "(p.nama_produk LIKE ? OR p.deskripsi LIKE ?)";
            $params[] = "%{$search_query}%";
            $params[] = "%{$search_query}%";
        }

        if (!empty($filter_kategori_name) && $filter_kategori_name !== 'semua') {
            $where[] = "k.slug = ?";
            $params[] = $filter_kategori_name;
        }

        $where_str = implode(" AND ", $where);
        $stmt = $db->prepare("
            SELECT p.*, k.nama_kategori, k.slug as kat_slug
            FROM produk p 
            LEFT JOIN kategori k ON p.kategori_id = k.id 
            WHERE {$where_str} 
            ORDER BY p.id DESC
        ");
        $stmt->execute($params);
        $db_products = $stmt->fetchAll();
    } catch (Exception $e) {
        // Fallback jika terjadi error
    }
}
?>
<section id="produk" class="relative overflow-hidden py-12 sm:py-20 text-center">
    
    <!-- Background Ambient Glow (Halus dan Menawan) -->
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-rose-200/40 dark:bg-rose-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute top-1/4 -right-24 w-96 h-96 bg-sky-200/40 dark:bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 left-1/3 w-96 h-96 bg-amber-100/50 dark:bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6">
        
        <!-- 1. Pill Badge Galeri Produk -->
        <div class="inline-block mb-3 sm:mb-4">
            <span class="border border-rose-300 dark:border-rose-400/80 text-rose-500 dark:text-rose-400 bg-white/90 dark:bg-[#141622] px-5 py-1.5 rounded-full text-xs font-bold tracking-wide shadow-2xs">
                Galery produk
            </span>
        </div>

        <!-- 2. Headline Utama -->
        <h2 class="text-2xl sm:text-4xl md:text-5xl font-black text-slate-900 dark:text-white tracking-tight uppercase leading-tight max-w-3xl mx-auto mb-6 sm:mb-8">
            SOLUSI <span class="text-sky-500 dark:text-sky-400">VISUAL & MERCENDES</span><br>
            TERBAIK ANDA
        </h2>

        <!-- 3. Search Bar -->
        <div class="max-w-md mx-auto mb-6 sm:mb-8">
            <form action="#produk" method="GET" class="relative flex items-center bg-white dark:bg-[#141622] border border-rose-300 dark:border-rose-400/50 rounded-full p-1 sm:p-1.5 shadow-sm hover:border-rose-400 focus-within:border-rose-500 focus-within:ring-2 focus-within:ring-rose-200 dark:focus-within:ring-rose-900/50 transition-all">
                <?php if (!empty($filter_kategori_name)): ?>
                    <input type="hidden" name="cat" value="<?= htmlspecialchars($filter_kategori_name) ?>">
                <?php endif; ?>
                <input 
                    type="text" 
                    name="cari" 
                    value="<?= htmlspecialchars($search_query) ?>"
                    placeholder="cari produk" 
                    class="w-full pl-5 pr-12 py-1.5 sm:py-2 text-xs sm:text-sm text-slate-700 dark:text-white bg-transparent outline-none placeholder:text-slate-400 dark:placeholder:text-slate-500"
                >
                <button 
                    type="submit" 
                    aria-label="Cari Produk" 
                    class="absolute right-1.5 sm:right-2 w-8 h-8 rounded-full bg-red-600 hover:bg-red-700 active:scale-90 text-white flex items-center justify-center transition-all shadow-xs"
                >
                    <i data-lucide="search" class="w-4 h-4"></i>
                </button>
            </form>
        </div>

        <!-- 4. Category Filter (Dynamic dari DB) -->
        <div class="flex items-center gap-2 sm:gap-3 overflow-x-auto no-scrollbar py-2 px-1 mb-8 sm:mb-12 justify-start sm:justify-center -mx-4 sm:mx-0 px-4">
            <a 
                href="index.php#produk"
                class="whitespace-nowrap px-4 sm:px-5 py-1.5 rounded-full text-xs font-semibold transition-all shrink-0 
                <?= (empty($filter_kategori_name) || $filter_kategori_name === 'semua') 
                    ? 'border border-rose-400 text-rose-500 dark:text-rose-400 bg-white dark:bg-[#181a28] shadow-2xs' 
                    : 'border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 bg-white/70 dark:bg-[#141622]/80 hover:border-rose-300 hover:text-rose-500 dark:hover:text-white' 
                ?>">
                Semua
            </a>
            <?php foreach ($db_categories as $cat): ?>
                <a 
                    href="index.php?cat=<?= urlencode($cat['slug']) ?>#produk"
                    class="whitespace-nowrap px-4 sm:px-5 py-1.5 rounded-full text-xs font-semibold transition-all shrink-0 
                    <?= ($filter_kategori_name === $cat['slug']) 
                        ? 'border border-rose-400 text-rose-500 dark:text-rose-400 bg-white dark:bg-[#181a28] shadow-2xs' 
                        : 'border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 bg-white/70 dark:bg-[#141622]/80 hover:border-rose-300 hover:text-rose-500 dark:hover:text-white' 
                    ?>">
                    <?= htmlspecialchars($cat['nama_kategori']) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- 5. Grid Produk -->
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-6 text-left">
            <?php if (empty($db_products)): ?>
                <div class="col-span-2 sm:col-span-2 md:col-span-3 lg:col-span-4 text-center py-12 text-slate-400">
                    <i data-lucide="inbox" class="w-10 h-10 mx-auto mb-3 text-slate-600"></i>
                    <p class="text-sm font-semibold">Tidak ada produk ditemukan.</p>
                </div>
            <?php else: ?>
                <?php foreach ($db_products as $item): ?>
                <div class="group bg-white dark:bg-[#141622] border border-slate-200/90 dark:border-slate-800/90 rounded-2xl sm:rounded-3xl p-3 sm:p-4 shadow-2xs hover:shadow-lg dark:hover:border-slate-700 transition-all flex flex-col justify-between">
                    
                    <div>
                        <!-- Container Gambar Produk -->
                        <div class="relative w-full aspect-square rounded-xl sm:rounded-2xl bg-slate-50 dark:bg-[#0d0f17] overflow-hidden flex items-center justify-center border border-slate-100 dark:border-slate-800/80 mb-3 sm:mb-4">
                            
                            <!-- Pattern Titik-Titik Halus di Latar -->
                            <div class="absolute inset-0 opacity-40 dark:opacity-20 bg-[radial-gradient(#94a3b8_1px,transparent_1px)] [background-size:12px_12px]"></div>

                            <!-- Technical Corner Marks -->
                            <div class="absolute top-2 left-2 w-2 h-2 border-t-2 border-l-2 border-slate-400 dark:border-slate-600"></div>
                            <div class="absolute top-2 right-2 w-2 h-2 border-t-2 border-r-2 border-slate-400 dark:border-slate-600"></div>
                            <div class="absolute bottom-2 left-2 w-2 h-2 border-b-2 border-l-2 border-slate-400 dark:border-slate-600"></div>
                            <div class="absolute bottom-2 right-2 w-2 h-2 border-b-2 border-r-2 border-slate-400 dark:border-slate-600"></div>

                            <?php if (!empty($item['gambar']) && file_exists(__DIR__ . '/assets/images/uploads/' . $item['gambar'])): ?>
                                <!-- Gambar Asli Upload -->
                                <img src="assets/images/uploads/<?= htmlspecialchars($item['gambar']) ?>" alt="<?= htmlspecialchars($item['nama_produk']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <?php else: ?>
                                <!-- Mockup Baju Hitam Realistis -->
                                <div class="relative w-4/5 h-4/5 flex items-center justify-center drop-shadow-md group-hover:scale-105 transition-transform duration-300">
                                    <svg viewBox="0 0 200 200" class="w-full h-full" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M60 45 L80 55 C90 60 110 60 120 55 L140 45 L175 75 L155 100 L140 88 L140 170 C140 172 138 174 135 174 L65 174 C62 174 60 172 60 170 L60 88 L45 100 L25 75 Z" fill="#18181b"/>
                                        <path d="M80 55 C90 68 110 68 120 55 C110 60 90 60 80 55 Z" fill="#27272a"/>
                                        <path d="M60 88 L60 170" stroke="#27272a" stroke-width="2"/>
                                        <path d="M140 88 L140 170" stroke="#27272a" stroke-width="2"/>
                                    </svg>
                                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none text-center px-4 pt-3">
                                        <span class="text-[9px] sm:text-[11px] font-black text-white leading-tight tracking-wider uppercase drop-shadow-sm">
                                            DESAIN<br>KAMU<br>DISINI
                                        </span>
                                    </div>
                                </div>
                            <?php endif; ?>

                        </div>

                        <!-- Judul & Keterangan Produk -->
                        <h3 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white truncate" title="<?= htmlspecialchars($item['nama_produk']) ?>">
                            <?= htmlspecialchars($item['nama_produk']) ?>
                        </h3>
                        <p class="text-[10px] sm:text-xs text-slate-400 dark:text-slate-400 truncate mt-0.5">
                            <?= htmlspecialchars($item['deskripsi'] ?? 'Produk Promosi Berkualitas') ?>
                        </p>
                    </div>

                    <!-- Bagian Bawah: Divider, Harga & Tombol WA -->
                    <div class="mt-2.5 sm:mt-3">
                        <div class="border-t border-slate-100 dark:border-slate-800/80 pt-2 sm:pt-2.5">
                            <span class="block font-black text-xs sm:text-base text-amber-500 dark:text-amber-400 mb-2">
                                <?= formatRupiah($item['harga']) ?>
                            </span>
                        </div>

                        <?php 
                            $wa_pesan = !empty($item['wa_text']) ? $item['wa_text'] : 'Halo admin, saya ingin pesan ' . $item['nama_produk'];
                        ?>
                        <!-- Tombol Pesan WA -->
                        <a 
                            href="https://wa.me/<?= $wa_link ?>?text=<?= urlencode($wa_pesan) ?>" 
                            target="_blank" 
                            data-wa-sumber="produk" data-wa-ref="<?= (int)$item['id'] ?>"
                            class="w-full bg-emerald-500 hover:bg-emerald-600 active:scale-95 text-white font-bold text-[10px] sm:text-xs py-2 sm:py-2.5 px-2 rounded-lg sm:rounded-xl shadow-xs flex items-center justify-center gap-1.5 transition-all"
                        >
                            <i data-lucide="message-circle" class="w-3.5 h-3.5 fill-current shrink-0"></i>
                            <span class="truncate uppercase tracking-wider">PESAN VIA WA</span>
                        </a>
                    </div>

                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- 6. Tombol "Lihat semua produk" -->
        <div class="mt-10 sm:mt-14">
            <a 
                href="produk.php" 
                class="inline-block border border-rose-300 dark:border-rose-400/60 text-rose-500 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 active:scale-95 bg-white dark:bg-[#141622] font-bold text-xs sm:text-sm px-7 py-2.5 rounded-full shadow-2xs transition-all"
            >
                Lihat semua produk
            </a>
        </div>

    </div>
</section>

<!-- 6. SECTION 4 LANGKAH PEMESANAN -->
<section id="pemesanan" class="py-12 sm:py-16 text-center">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        
        <span class="text-xs text-slate-900 bg-white font-bold px-3.5 py-1 rounded-full inline-block mb-2.5 shadow-xs">
            Pemesanan
        </span>

        <h2 class="text-xl sm:text-3xl font-extrabold text-slate-900 dark:text-white mb-1.5">4 langkah untuk memesan di hariest advertising</h2>
        <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm max-w-lg mx-auto mb-8 sm:mb-12">Proses simpel tanpa ribet, mulai dari konsultasi hingga barang terpasang</p>

        <!-- 4 Step Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-5">
            
            <!-- Step 1 -->
            <div class="bg-white dark:bg-[#141622] border border-slate-200 dark:border-slate-800/80 rounded-2xl sm:rounded-3xl p-5 text-left sm:text-center shadow-xs flex sm:flex-col items-start sm:items-center gap-3.5 sm:gap-0">
                <div class="w-8 h-8 shrink-0 bg-emerald-500 text-white rounded-full flex items-center justify-center font-black text-xs sm:mb-3 shadow-xs">1</div>
                <div>
                    <h3 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white mb-1 sm:mb-1.5">Konsultasi via WA</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Hubungi kami untuk konsultasi jenis pesanan, ukuran, dan kebutuhan bisnis Anda.</p>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="bg-white dark:bg-[#141622] border border-slate-200 dark:border-slate-800/80 rounded-2xl sm:rounded-3xl p-5 text-left sm:text-center shadow-xs flex sm:flex-col items-start sm:items-center gap-3.5 sm:gap-0">
                <div class="w-8 h-8 shrink-0 bg-orange-500 text-white rounded-full flex items-center justify-center font-black text-xs sm:mb-3 shadow-xs">2</div>
                <div>
                    <h3 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white mb-1 sm:mb-1.5">Konsultasi via WA</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Hubungi kami untuk konsultasi jenis pesanan, ukuran, dan kebutuhan bisnis Anda.</p>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="bg-white dark:bg-[#141622] border border-slate-200 dark:border-slate-800/80 rounded-2xl sm:rounded-3xl p-5 text-left sm:text-center shadow-xs flex sm:flex-col items-start sm:items-center gap-3.5 sm:gap-0">
                <div class="w-8 h-8 shrink-0 bg-purple-600 text-white rounded-full flex items-center justify-center font-black text-xs sm:mb-3 shadow-xs">3</div>
                <div>
                    <h3 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white mb-1 sm:mb-1.5">Konsultasi via WA</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Hubungi kami untuk konsultasi jenis pesanan, ukuran, dan kebutuhan bisnis Anda.</p>
                </div>
            </div>

            <!-- Step 4 -->
            <div class="bg-white dark:bg-[#141622] border border-slate-200 dark:border-slate-800/80 rounded-2xl sm:rounded-3xl p-5 text-left sm:text-center shadow-xs flex sm:flex-col items-start sm:items-center gap-3.5 sm:gap-0">
                <div class="w-8 h-8 shrink-0 bg-blue-600 text-white rounded-full flex items-center justify-center font-black text-xs sm:mb-3 shadow-xs">4</div>
                <div>
                    <h3 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white mb-1 sm:mb-1.5">Konsultasi via WA</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Hubungi kami untuk konsultasi jenis pesanan, ukuran, dan kebutuhan bisnis Anda.</p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- 7. SECTION TENTANG KAMI -->
<section id="tentang" class="py-12 sm:py-16 text-center">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        
        <span class="text-xs text-slate-900 bg-white font-bold px-3.5 py-1 rounded-full inline-block mb-3 shadow-xs">
            Tentang Kami
        </span>

        <!-- Description Box -->
        <div class="bg-white/95 dark:bg-[#141622] border border-slate-200 dark:border-slate-800/80 rounded-2xl sm:rounded-3xl p-5 sm:p-7 shadow-xs mb-6 text-slate-700 dark:text-slate-300 text-xs sm:text-sm leading-relaxed text-left sm:text-center">
            HERIEST CV. Advertising adalah perusahaan yang bergerak di bidang jasa advertising, printing, dan pembuatan media promosi. Kami hadir untuk membantu bisnis Anda tampil lebih profesional dan dikenal lebih luas dengan hasil karya berkualitas, pelayanan cepat, dan harga yang kompetitif!
        </div>

        <!-- 3 Advantage Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-5 mb-8">
            
            <div class="bg-white dark:bg-[#141622] border border-slate-200 dark:border-slate-800/80 rounded-2xl p-4 sm:p-5 shadow-xs flex sm:flex-col items-center gap-3 sm:gap-1.5 text-left sm:text-center">
                <div class="w-9 h-9 shrink-0 flex items-center justify-center text-amber-500 sm:mb-1">
                    <i data-lucide="settings" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white">profesional</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Berpengalaman dan terpercaya</p>
                </div>
            </div>

            <div class="bg-white dark:bg-[#141622] border border-slate-200 dark:border-slate-800/80 rounded-2xl p-4 sm:p-5 shadow-xs flex sm:flex-col items-center gap-3 sm:gap-1.5 text-left sm:text-center">
                <div class="w-9 h-9 shrink-0 flex items-center justify-center text-cyan-400 sm:mb-1">
                    <i data-lucide="sparkles" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white">Kualitas Terbaik</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Menggunakan bahan terbaik</p>
                </div>
            </div>

            <div class="bg-white dark:bg-[#141622] border border-slate-200 dark:border-slate-800/80 rounded-2xl p-4 sm:p-5 shadow-xs flex sm:flex-col items-center gap-3 sm:gap-1.5 text-left sm:text-center">
                <div class="w-9 h-9 shrink-0 flex items-center justify-center text-pink-500 sm:mb-1">
                    <i data-lucide="clock" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white">Pelayanan cepat</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Tepat waktu dan ramah</p>
                </div>
            </div>

        </div>

        <!-- Button Selengkapnya -->
        <a href="#tentang-detail" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 active:scale-95 text-white font-bold text-xs sm:text-sm px-7 py-2.5 rounded-full shadow-md transition-all">
            <span>selengkapnya</span>
            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
        </a>

    </div>
</section>

<?php include 'includes/footer.php'; ?>