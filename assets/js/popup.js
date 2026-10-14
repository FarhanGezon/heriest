/* Popup promo/pengumuman: tampil sekali per tab, tutup via X / backdrop / Esc
   (dipindah dari includes/footer.php). */
function closePromoPopup() {
    var p = document.getElementById('promo-popup');
    if (p) { p.classList.add('hidden'); p.classList.remove('flex'); }
    try { sessionStorage.setItem('heriest_popup_closed', '1'); } catch (e) {}
}

(function () {
    var closed = false;
    try { closed = sessionStorage.getItem('heriest_popup_closed') === '1'; } catch (e) {}
    if (closed) return;
    var p = document.getElementById('promo-popup');
    if (!p) return;
    setTimeout(function () {
        p.classList.remove('hidden');
        p.classList.add('flex');
    }, 800);
    p.addEventListener('click', closePromoPopup);
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closePromoPopup();
    });
})();
