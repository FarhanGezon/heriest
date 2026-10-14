/* Pelacak klik tombol WhatsApp (leads): catat ke wa_track.php tanpa
   menghambat navigasi (sendBeacon + fallback fetch keepalive). */
(function () {
    document.addEventListener('click', function (e) {
        var a = e.target.closest ? e.target.closest('a[href*="wa.me"]') : null;
        if (!a) return;
        var sumber = a.getAttribute('data-wa-sumber') || 'umum';
        var ref = a.getAttribute('data-wa-ref') || '0';
        var url = 'wa_track.php?sumber=' + encodeURIComponent(sumber)
            + '&ref_id=' + encodeURIComponent(ref);
        try {
            if (navigator.sendBeacon) {
                navigator.sendBeacon(url);
                return;
            }
        } catch (err) { /* lanjut ke fetch */ }
        try {
            fetch(url, { method: 'GET', keepalive: true, credentials: 'same-origin' });
        } catch (err) { /* abaikan: tracking tak boleh ganggu UX */ }
    }, true);
})();
