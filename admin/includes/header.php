<?php
require_once __DIR__ . '/../auth.php';

$current_page = basename($_SERVER['PHP_SELF']);
$admin_nama = $_SESSION['admin_nama'] ?? 'Administrator';
?>
<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) . ' - Admin HERIEST' : 'Admin Panel - HERIEST ADVERTISING' ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kalam:wght@700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../assets/js/tailwind-admin.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <?php require_once __DIR__ . '/theme.php'; ?>
</head>
<body class="font-sans bg-[#08090d] text-slate-100 min-h-screen flex flex-col antialiased">

    <div class="flex flex-1 min-h-screen">
        
        <!-- SIDEBAR (Desktop) -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-[#0f111a] border-r border-slate-800/80 flex flex-col justify-between transition-transform duration-300 -translate-x-full lg:translate-x-0 lg:static">
            
            <div>
                <!-- Brand Logo Sidebar -->
                <div class="h-16 flex items-center justify-between px-6 border-b border-slate-800/80">
                    <a href="index.php" class="flex items-center gap-2">
                        <img src="../assets/images/logo-heriest.png" alt="HERIEST CV. ADVERTISING" class="h-10 w-auto object-contain">
                    </a>
                    
                    <!-- Close button on mobile -->
                    <button id="close-sidebar-btn" class="lg:hidden text-slate-400 hover:text-white">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Navigation Links -->
                <div class="p-4 space-y-1.5">
                    <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-2">Menu Utama</p>

                    <a href="index.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all <?= $current_page === 'index.php' ? 'bg-red-600 text-white shadow-md' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' ?>">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="produk.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all <?= in_array($current_page, ['produk.php', 'produk_tambah.php', 'produk_edit.php']) ? 'bg-red-600 text-white shadow-md' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' ?>">
                        <i data-lucide="box" class="w-4 h-4"></i>
                        <span>Kelola Produk</span>
                    </a>

                    <a href="kategori.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all <?= $current_page === 'kategori.php' ? 'bg-red-600 text-white shadow-md' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' ?>">
                        <i data-lucide="tags" class="w-4 h-4"></i>
                        <span>Kategori Produk</span>
                    </a>

                    <a href="portofolio.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all <?= in_array($current_page, ['portofolio.php', 'portofolio_tambah.php', 'portofolio_edit.php']) ? 'bg-red-600 text-white shadow-md' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' ?>">
                        <i data-lucide="briefcase" class="w-4 h-4"></i>
                        <span>Kelola Portofolio</span>
                    </a>

                    <a href="banner.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all <?= $current_page === 'banner.php' ? 'bg-red-600 text-white shadow-md' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' ?>">
                        <i data-lucide="megaphone" class="w-4 h-4"></i>
                        <span>Banner & Promo</span>
                    </a>

                    <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 pt-4 mb-2">Konten Web</p>

                    <a href="mitra.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all <?= $current_page === 'mitra.php' ? 'bg-red-600 text-white shadow-md' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' ?>">
                        <i data-lucide="handshake" class="w-4 h-4"></i>
                        <span>Mitra & Klien</span>
                    </a>

                    <a href="testimoni.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all <?= $current_page === 'testimoni.php' ? 'bg-red-600 text-white shadow-md' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' ?>">
                        <i data-lucide="messages-square" class="w-4 h-4"></i>
                        <span>Testimoni & FAQ</span>
                    </a>

                    <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 pt-4 mb-2">Konfigurasi</p>

                    <a href="pengaturan.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all <?= $current_page === 'pengaturan.php' ? 'bg-red-600 text-white shadow-md' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' ?>">
                        <i data-lucide="sliders" class="w-4 h-4"></i>
                        <span>Pengaturan Web</span>
                    </a>

                    <a href="admin.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all <?= $current_page === 'admin.php' ? 'bg-red-600 text-white shadow-md' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' ?>">
                        <i data-lucide="shield-check" class="w-4 h-4"></i>
                        <span>Kelola Admin</span>
                    </a>

                    <a href="../index.php" target="_blank" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-slate-400 hover:text-cyan-400 hover:bg-slate-800/60 transition-all">
                        <i data-lucide="external-link" class="w-4 h-4"></i>
                        <span>Lihat Website</span>
                    </a>
                </div>
            </div>

            <!-- User Info & Logout Button -->
            <div class="p-4 border-t border-slate-800/80">
                <div class="flex items-center justify-between mb-3 px-2">
                    <div class="flex items-center gap-2.5 overflow-hidden">
                        <div class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-xs font-bold text-red-500">
                            <?= strtoupper(substr($admin_nama, 0, 1)) ?>
                        </div>
                        <div class="truncate">
                            <p class="text-xs font-bold text-white truncate"><?= htmlspecialchars($admin_nama) ?></p>
                            <p class="text-[10px] text-emerald-400 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Online
                            </p>
                        </div>
                    </div>
                </div>

                <a href="logout.php" onclick="return confirm('Apakah Anda yakin ingin logout?');" class="w-full flex items-center justify-center gap-2 bg-slate-800/80 hover:bg-red-950/80 text-slate-300 hover:text-red-400 border border-slate-700/80 hover:border-red-800/80 text-xs font-semibold py-2 rounded-xl transition-all">
                    <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                    <span>Keluar / Logout</span>
                </a>
            </div>

        </aside>

        <!-- BACKDROP OVERLAY ON MOBILE -->
        <div id="sidebar-backdrop" class="fixed inset-0 z-40 bg-black/60 backdrop-blur-xs hidden lg:hidden"></div>

        <!-- MAIN CONTENT AREA -->
        <div class="flex-1 flex flex-col min-w-0">
            
            <!-- TOP BAR HEADER -->
            <header id="topbar" class="h-16 bg-[#0f111a]/80 backdrop-blur-md border-b border-slate-800/80 sticky top-0 z-30 flex items-center justify-between px-4 sm:px-6 lg:px-8">
                
                <div class="flex items-center gap-3">
                    <!-- Mobile Hamburger Button -->
                    <button id="open-sidebar-btn" class="lg:hidden p-2 rounded-lg bg-slate-800/80 text-slate-300 hover:text-white">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>

                    <h2 class="text-sm sm:text-base font-bold text-white">
                        <?= isset($page_title) ? htmlspecialchars($page_title) : 'Dashboard' ?>
                    </h2>
                </div>

                <div class="flex items-center gap-3">
                    <button id="theme-toggle" type="button" aria-label="Ganti Mode Terang/Gelap" class="p-2 rounded-lg bg-slate-800/80 text-slate-300 hover:text-amber-400 transition-colors" title="Mode Terang/Gelap">
                        <i id="theme-icon" data-lucide="sun" class="w-4 h-4"></i>
                    </button>

                    <a href="produk_tambah.php" class="hidden sm:inline-flex items-center gap-1.5 bg-red-600 hover:bg-red-700 active:scale-95 text-white font-bold text-xs px-3.5 py-1.5 rounded-xl shadow transition-all">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        <span>Tambah Produk</span>
                    </a>

                    <a href="../index.php" target="_blank" class="p-2 rounded-lg bg-slate-800/80 text-slate-300 hover:text-cyan-400 transition-colors" title="Buka Website Utama">
                        <i data-lucide="globe" class="w-4 h-4"></i>
                    </a>
                </div>

            </header>

            <!-- PAGE CONTENT CONTAINER -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
