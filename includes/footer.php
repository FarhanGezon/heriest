</main> <!-- End Main Content -->

    <!-- FOOTER HITAM PRESISI GAMBAR -->
    <?php
        $f_nama = function_exists('getSetting') ? getSetting('nama_toko', 'HERIEST CV. ADVERTISING') : 'HERIEST CV. ADVERTISING';
        $f_alamat = function_exists('getSetting') ? getSetting('alamat', 'Jl. kabupaten nomor mangunsari, rego, kabupaten, manandai, kabupaten, kabupaten karanganyar, Jawa Timur 63281') : 'Jl. kabupaten nomor mangunsari, rego, kabupaten, manandai, kabupaten, kabupaten karanganyar, Jawa Timur 63281';
        $f_wa = function_exists('getSetting') ? getSetting('whatsapp', '082264356885') : '082264356885';
        $f_email = function_exists('getSetting') ? getSetting('email', 'info@heriest.com') : 'info@heriest.com';
        $f_tiktok = function_exists('getSetting') ? getSetting('tiktok', '#') : '#';
        $f_ig = function_exists('getSetting') ? getSetting('instagram', '#') : '#';
        $f_fb = function_exists('getSetting') ? getSetting('facebook', '#') : '#';
        $f_yt = function_exists('getSetting') ? getSetting('youtube', '#') : '#';
    ?>
    <footer class="bg-black text-white pt-10 pb-8 border-t border-slate-900 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 pb-8">

                <!-- Col 1: Logo -->
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <img src="assets/images/logo-heriest.png" alt="<?= htmlspecialchars($f_nama) ?>" class="h-12 w-auto object-contain">
                    </div>
                </div>

                <!-- Col 2: Alamat -->
                <div class="space-y-2">
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider">Alamat</h4>
                    <p class="text-[11px] text-slate-400 leading-relaxed flex items-start gap-1.5">
                        <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400 shrink-0 mt-0.5"></i>
                        <span><?= htmlspecialchars($f_alamat) ?></span>
                    </p>
                </div>

                <!-- Col 3: Kontak -->
                <div class="space-y-2">
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider">kontak</h4>
                    <p class="text-[11px] text-slate-400 flex items-center gap-2">
                        <i data-lucide="phone" class="w-3.5 h-3.5"></i> <?= htmlspecialchars($f_wa) ?>
                    </p>
                    <p class="text-[11px] text-slate-400 flex items-center gap-2">
                        <i data-lucide="mail" class="w-3.5 h-3.5"></i> <?= htmlspecialchars($f_email) ?>
                    </p>
                </div>

                <!-- Col 4: Ikuti Kami -->
                <div class="space-y-2">
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider">Ikuti kami</h4>
                    <div class="flex items-center gap-3 text-slate-400 pt-1">
                        <a href="<?= htmlspecialchars($f_tiktok) ?>" target="_blank" rel="noopener" class="hover:text-white" title="TikTok"><i data-lucide="music-2" class="w-4 h-4"></i></a>
                        <a href="<?= htmlspecialchars($f_ig) ?>" target="_blank" rel="noopener" class="hover:text-white" title="Instagram"><i data-lucide="instagram" class="w-4 h-4"></i></a>
                        <a href="<?= htmlspecialchars($f_fb) ?>" target="_blank" rel="noopener" class="hover:text-white" title="Facebook"><i data-lucide="facebook" class="w-4 h-4"></i></a>
                        <a href="<?= htmlspecialchars($f_yt) ?>" target="_blank" rel="noopener" class="hover:text-white" title="YouTube"><i data-lucide="youtube" class="w-4 h-4"></i></a>
                    </div>
                </div>

            </div>
        </div>
    </footer>

    <!-- POPUP PROMO / PENGUMUMAN (atur via Admin > Pengaturan Website) -->
    <?php
        $popup_enabled = function_exists('getSetting') ? getSetting('popup_enabled', '0') : '0';
        $popup_image = function_exists('getSetting') ? getSetting('popup_image', '') : '';
        $popup_link = function_exists('getSetting') ? getSetting('popup_link', '') : '';
        $popup_path = __DIR__ . '/../assets/images/uploads/' . $popup_image;
        $show_popup = ($popup_enabled === '1' && !empty($popup_image) && file_exists($popup_path));
    ?>
    <?php if ($show_popup): ?>
    <div id="promo-popup" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
        <div class="relative max-w-md w-full bg-white dark:bg-[#141622] rounded-2xl overflow-hidden shadow-2xl border border-slate-200 dark:border-slate-700" onclick="event.stopPropagation()">
            <button type="button" onclick="closePromoPopup()" aria-label="Tutup popup"
                class="absolute top-2 right-2 z-10 w-8 h-8 rounded-full bg-black/60 hover:bg-red-600 text-white flex items-center justify-center transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
            <?php if (!empty($popup_link)): ?>
                <a href="<?= htmlspecialchars($popup_link) ?>" <?= (strpos($popup_link, 'http') === 0) ? 'target="_blank" rel="noopener"' : '' ?>>
                    <img src="assets/images/uploads/<?= htmlspecialchars($popup_image) ?>" alt="Promo terbaru" class="w-full max-h-[80vh] object-contain bg-black/5">
                </a>
            <?php else: ?>
                <img src="assets/images/uploads/<?= htmlspecialchars($popup_image) ?>" alt="Promo terbaru" class="w-full max-h-[80vh] object-contain bg-black/5">
            <?php endif; ?>
        </div>
    </div>
    <!-- Script Popup Promo (atur via Admin > Pengaturan Website) -->
    <script src="assets/js/popup.js"></script>
    <?php endif; ?>

    <!-- Script Utama Situs: ikon Lucide & toggle tema -->
    <script src="assets/js/site.js"></script>
    <!-- Pelacak Klik WhatsApp (leads dashboard) -->
    <script src="assets/js/wa-track.js"></script>
</body>
</html>