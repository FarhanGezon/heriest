/* Inisialisasi tema anti-kedip (dipakai situs + admin).
   Gelap = default; terang hanya jika pernah disimpan.
   Key 'theme' dipakai bersama, jadi ikut sinkron. */
(function () {
    try {
        if (localStorage.getItem('theme') === 'light') {
            document.documentElement.classList.remove('dark');
        } else {
            document.documentElement.classList.add('dark');
        }
    } catch (e) {
        document.documentElement.classList.add('dark');
    }
})();
