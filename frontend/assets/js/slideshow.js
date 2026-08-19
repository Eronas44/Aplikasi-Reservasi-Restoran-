/**
 * slideshow.js — Slideshow ringan (tanpa dependensi) untuk gambar restoran.
 *
 * Cara pakai di halaman PHP:
 *   <div class="js-slideshow relative w-full h-44 overflow-hidden bg-[#f4ece1]" data-interval="3500">
 *     <div class="js-slideshow-track flex h-full w-full transition-transform duration-500">
 *       <img src="..." class="js-slide w-full h-full object-cover shrink-0" alt="...">
 *       <img src="..." class="js-slide w-full h-full object-cover shrink-0" alt="...">
 *     </div>
 *     <button type="button" class="js-slideshow-prev ...">‹</button>
 *     <button type="button" class="js-slideshow-next ...">›</button>
 *     <div class="js-slideshow-dots ..."></div>
 *   </div>
 *
 * Jika hanya ada 1 gambar, kontrol (panah/dot) otomatis disembunyikan
 * dan tidak ada auto-play.
 */
(function () {
    'use strict';

    function initSlideshow(el) {
        var slides = Array.prototype.slice.call(el.querySelectorAll('.js-slide'));
        if (slides.length <= 1) {
            return;
        }

        var track = el.querySelector('.js-slideshow-track');
        var dotsWrap = el.querySelector('.js-slideshow-dots');
        var index = 0;
        var timer = null;
        var interval = parseInt(el.getAttribute('data-interval') || '3500', 10);

        if (dotsWrap) {
            dotsWrap.innerHTML = '';
            slides.forEach(function (_, i) {
                var d = document.createElement('button');
                d.type = 'button';
                d.setAttribute('aria-label', 'Slide ' + (i + 1));
                d.className = 'js-dot w-2 h-2 rounded-full transition bg-white/40';
                d.addEventListener('click', function () { goTo(i); restart(); });
                dotsWrap.appendChild(d);
            });
        }

        function goTo(i) {
            index = (i + slides.length) % slides.length;
            if (track) {
                track.style.transform = 'translateX(-' + (index * 100) + '%)';
            }
            updateDots();
        }

        function updateDots() {
            var all = el.querySelectorAll('.js-dot');
            Array.prototype.forEach.call(all, function (d, i) {
                d.className = 'js-dot w-2 h-2 rounded-full transition ' + (i === index ? 'bg-white' : 'bg-white/40');
            });
        }

        function next() { goTo(index + 1); }
        function prev() { goTo(index - 1); }

        function start() {
            stop();
            timer = setInterval(next, interval);
        }
        function stop() {
            if (timer) { clearInterval(timer); timer = null; }
        }
        function restart() { start(); }

        var prevBtn = el.querySelector('.js-slideshow-prev');
        var nextBtn = el.querySelector('.js-slideshow-next');
        if (prevBtn) {
            prevBtn.addEventListener('click', function (e) { e.preventDefault(); prev(); restart(); });
        }
        if (nextBtn) {
            nextBtn.addEventListener('click', function (e) { e.preventDefault(); next(); restart(); });
        }

        el.addEventListener('mouseenter', stop);
        el.addEventListener('mouseleave', start);

        updateDots();
        start();
    }

    function initAll() {
        Array.prototype.forEach.call(document.querySelectorAll('.js-slideshow'), initSlideshow);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }
})();
