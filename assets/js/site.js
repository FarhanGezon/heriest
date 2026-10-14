/* Skrip utama halaman situs: ikon + toggle tema (dipindah dari includes/footer.php). */
lucide.createIcons();

// Theme Toggle Controller
const themeToggleBtn = document.getElementById('theme-toggle');
const themeIcon = document.getElementById('theme-icon');

function updateThemeIcon() {
    if (!themeIcon) return;
    if (document.documentElement.classList.contains('dark')) {
        themeIcon.setAttribute('data-lucide', 'sun');
    } else {
        themeIcon.setAttribute('data-lucide', 'moon');
    }
    lucide.createIcons();
}

if (themeToggleBtn) {
    updateThemeIcon();
    themeToggleBtn.addEventListener('click', function () {
        if (document.documentElement.classList.contains('dark')) {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        } else {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        }
        updateThemeIcon();
    });
}
