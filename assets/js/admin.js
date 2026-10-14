/* Skrip panel admin: sidebar, efek tekan layar sentuh, toggle tema, modal kategori
   (gabungan dari admin/includes/footer.php + admin/kategori.php). */

// Init Lucide Icons
lucide.createIcons();

// Mobile Sidebar Drawer Toggle
const openBtn = document.getElementById('open-sidebar-btn');
const closeBtn = document.getElementById('close-sidebar-btn');
const sidebar = document.getElementById('sidebar');
const backdrop = document.getElementById('sidebar-backdrop');

function toggleSidebar(show) {
    if (!sidebar || !backdrop) return;
    if (show) {
        sidebar.classList.remove('-translate-x-full');
        backdrop.classList.remove('hidden');
    } else {
        sidebar.classList.add('-translate-x-full');
        backdrop.classList.add('hidden');
    }
}

if (openBtn) openBtn.addEventListener('click', () => toggleSidebar(true));
if (closeBtn) closeBtn.addEventListener('click', () => toggleSidebar(false));
if (backdrop) backdrop.addEventListener('click', () => toggleSidebar(false));

// Efek tekan saat disentuh/ditahan (khusus layar sentuh):
// tampil setelah tahan ~150ms, hilang saat jari diangkat.
(function () {
    if (!window.matchMedia('(hover: none)').matches) return;
    let holdTimer = null;
    const HOLD_MS = 150;
    const clearHold = (el) => {
        if (holdTimer) { clearTimeout(holdTimer); holdTimer = null; }
        if (el) el.classList.remove('is-press');
    };
    document.querySelectorAll('body a, body button')
        .forEach((el) => {
            el.addEventListener('touchstart', () => {
                clearHold(el);
                holdTimer = setTimeout(() => el.classList.add('is-press'), HOLD_MS);
            }, { passive: true });
            ['touchend', 'touchcancel', 'touchmove'].forEach((evt) =>
                el.addEventListener(evt, () => clearHold(el), { passive: true }));
        });
})();

// Theme Toggle Terang/Gelap (preferensi sama dengan halaman utama)
function updateAdminThemeIcon() {
    const icon = document.getElementById('theme-icon');
    if (!icon) return;
    icon.setAttribute('data-lucide', document.documentElement.classList.contains('dark') ? 'sun' : 'moon');
    lucide.createIcons();
}

const themeToggleBtn = document.getElementById('theme-toggle');
if (themeToggleBtn) {
    updateAdminThemeIcon();
    themeToggleBtn.addEventListener('click', function () {
        if (document.documentElement.classList.contains('dark')) {
            document.documentElement.classList.remove('dark');
            try { localStorage.setItem('theme', 'light'); } catch (e) {}
        } else {
            document.documentElement.classList.add('dark');
            try { localStorage.setItem('theme', 'dark'); } catch (e) {}
        }
        updateAdminThemeIcon();
    });
}

// Modal edit kategori (dipakai admin/kategori.php)
function editKategori(id, nama) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_nama_kategori').value = nama;
    document.getElementById('modal-edit-kategori').classList.remove('hidden');
}
