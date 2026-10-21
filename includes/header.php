<!DOCTYPE html>
<html lang="id" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . " - HERIEST ADVERTISING" : "HERIEST CV. Advertising"; ?></title>
    
    <!-- Google Fonts: Plus Jakarta Sans & Kalam (Font Brush Tulis Tangan) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kalam:wght@700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="assets/js/tailwind-site.js"></script>

    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Theme Initializer (Persist Dark/Light Mode) -->
    <script src="assets/js/theme-init.js"></script>

    <link rel="stylesheet" href="assets/css/site.css">
</head>
<body class="font-sans bg-aurora text-slate-800 dark:text-slate-100 antialiased min-h-screen flex flex-col justify-between transition-colors duration-300">

    <!-- HEADER NAVIGATION (HITAM PRESISI GAMBAR) -->
    <header class="bg-darkHeader text-white sticky top-0 z-50 border-b border-slate-800/80">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-14">
                
                <!-- Logo Kiri Presisi -->
                <a href="index.php" class="flex items-center">
                    <img src="assets/images/logo-heriest.png" alt="Heriest Logo" class="h-10 w-auto object-contain">
                </a>

                <!-- Navigasi Tengah -->
                <nav class="hidden md:flex items-center gap-6 text-[11px] font-medium text-slate-300">
                    <a href="index.php#beranda" class="hover:text-white transition-colors">Beranda</a>
                    <a href="index.php#layanan" class="hover:text-white transition-colors">Layanan Kami</a>
                    <a href="index.php#produk" class="hover:text-white transition-colors">Produk</a>
                    <a href="portofolio.php" class="hover:text-white transition-colors">Portofolio</a>
                    <a href="index.php#tentang" class="hover:text-white transition-colors">Tentang Kami</a>
                    <a href="index.php#pemesanan" class="hover:text-white transition-colors">Cara pemesanan</a>
                    <a href="faq.php" class="hover:text-white transition-colors">FAQ</a>
                </nav>

                <!-- Aksi Kanan (Search & Icons) -->
                <div class="flex items-center gap-2">
                    <!-- Search Input Box -->
                    <div class="relative hidden sm:block">
                        <input type="text" placeholder="cari produk" class="bg-[#181a20] border border-slate-700/60 rounded-full text-[10px] px-3 py-1 pl-6 w-32 focus:outline-none text-slate-300 placeholder-slate-500">
                        <i data-lucide="search" class="w-3 h-3 text-slate-500 absolute left-2 top-1.5"></i>
                    </div>
                    
                    <!-- Search Red Icon Button -->
                    <button class="w-6 h-6 bg-red-600 hover:bg-red-700 rounded-full flex items-center justify-center text-white transition-colors">
                        <i data-lucide="search" class="w-3 h-3"></i>
                    </button>
                    
                    <!-- Bendera ID & Dark/Light Toggle -->
                    <div class="flex items-center gap-2 pl-2 border-l border-slate-800">
                        <div class="w-4 h-2.5 bg-red-600 rounded-sm overflow-hidden relative border border-white/20">
                            <div class="bg-white h-1/2 w-full absolute bottom-0"></div>
                        </div>
                        <button id="theme-toggle" type="button" aria-label="Toggle Mode Gelap/Terang" class="text-slate-400 hover:text-white transition-colors focus:outline-none cursor-pointer">
                            <i id="theme-icon" data-lucide="moon" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </header>

    <main class="grow">