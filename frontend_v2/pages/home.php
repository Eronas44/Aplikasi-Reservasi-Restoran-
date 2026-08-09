<?php
// pages/home.php — Halaman Utama (Landing Page)
$restaurantName = 'Rasa & Cerita';
$restaurantPhone = '+62 829 6573 9824';
$restaurantAddress = 'Jl. Soekarno Hatta No.113, Lampung, Indonesia';
$openTime = '10:00';
$closeTime = '22:00';

$isLoggedIn = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;

$featuredCategories = [
    ['category_id' => 1, 'category_name' => 'Makanan Utama'],
    ['category_id' => 2, 'category_name' => 'Hidangan Pembuka'],
    ['category_id' => 3, 'category_name' => 'Makanan Penutup'],
    ['category_id' => 4, 'category_name' => 'Minuman'],
];

$featuredMenu = [];
?>

<style>
  .carousel-slide {
    transition: opacity 1s ease-in-out;
  }
</style>

<section class="hero-shell">
  <div class="mx-auto grid max-w-7xl gap-14 px-6 py-14 lg:grid-cols-[1.15fr_0.85fr] lg:px-10 lg:py-10">
    <div class="self-center">
      <span class="eyebrow">Sistem Reservasi Restoran</span>
      <h1 class="mt-5 max-w-3xl font-display text-5xl leading-[0.95] tracking-tight text-[#201913] md:text-6xl">
        Kemewahan Rasa di Setiap Sajian.
      </h1>
      <h2 class="mt-5 max-w-3xl font-display text-5xl leading-[0.95] tracking-tight text-[#201913] md:text-6xl">
        Good Food, Good Mood.
      </h2>
      <p class="mt-6 max-w-2xl text-lg leading-8 text-[#4f4338]">
        <?= e($restaurantName) ?> Dari meja reservasi hingga pesanan siap saji, menyatukan alur kerja restoran dalam satu genggaman. Tampil berkelas, layani lebih cepat.
      </p>
     
      <!-- Tombol Hero Dinamis -->
      <div class="mt-4 flex flex-wrap gap-4">
        <?php if ($isLoggedIn): ?>
          <a href="<?= route('reservasi') ?>" class="btn-primary">Mulai Pesan Sekarang</a>
          <a href="<?= route('dashboard') ?>" class="btn-secondary">Dashboard Saya</a>
        <?php else: ?>
          <a href="<?= route('register') ?>" class="btn-primary">Buat Akun</a>
          <a href="<?= route('login') ?>" class="btn-secondary">Masuk ke Sistem</a>
        <?php endif; ?>
      </div>
    </div>

    <!-- CAROUSEL BANNER / SUASANA RESTORAN -->
    <div class="relative overflow-hidden rounded-[2.5rem] shadow-2xl border-4 border-white/40 h-[480px]">
      <div id="carousel-slides" class="w-full h-full relative">
        <div class="carousel-slide absolute inset-0 opacity-100 transition-opacity duration-1000">
          <img src="assets/images/ruangan_utama/1.png" alt="Ruangan Utama 1" class="w-full h-full object-cover">
          <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
          <div class="absolute bottom-6 left-6 right-6 text-white">
            <span class="text-xs uppercase tracking-widest text-[#e8c39e] font-bold">Ruangan Utama</span>
            <h3 class="font-display text-2xl font-bold mt-1">Suasana Elegan & Nyaman</h3>
          </div>
        </div>

        <div class="carousel-slide absolute inset-0 opacity-0 transition-opacity duration-1000">
          <img src="assets/images/area_privat/2.png" alt="Area Privat 2" class="w-full h-full object-cover">
          <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
          <div class="absolute bottom-6 left-6 right-6 text-white">
            <span class="text-xs uppercase tracking-widest text-[#e8c39e] font-bold">Area VIP / Privat</span>
            <h3 class="font-display text-2xl font-bold mt-1">Pengalaman Santap Eksklusif</h3>
          </div>
        </div>

        <div class="carousel-slide absolute inset-0 opacity-0 transition-opacity duration-1000">
          <img src="assets/images/suasana_malam/3.png" alt="Suasana Malam 3" class="w-full h-full object-cover">
          <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
          <div class="absolute bottom-6 left-6 right-6 text-white">
            <span class="text-xs uppercase tracking-widest text-[#e8c39e] font-bold">Suasana Malam</span>
            <h3 class="font-display text-2xl font-bold mt-1">Cahaya Hangat Restoran</h3>
          </div>
        </div>

        <div class="carousel-slide absolute inset-0 opacity-0 transition-opacity duration-1000">
          <img src="assets/images/slide_makanan/4.png" alt="Slide Makanan 4" class="w-full h-full object-cover">
          <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
          <div class="absolute bottom-6 left-6 right-6 text-white">
            <span class="text-xs uppercase tracking-widest text-[#e8c39e] font-bold">Hidangan Spesial</span>
            <h3 class="font-display text-2xl font-bold mt-1">Cita Rasa Otentik</h3>
          </div>
        </div>
      </div>

      <!-- Indikator Carousel Dot -->
      <div class="absolute bottom-4 right-6 flex space-x-2 z-10">
        <button class="carousel-dot w-3 h-3 rounded-full bg-white/40 hover:bg-white transition" onclick="setSlide(0)"></button>
        <button class="carousel-dot w-3 h-3 rounded-full bg-white/40 hover:bg-white transition" onclick="setSlide(1)"></button>
        <button class="carousel-dot w-3 h-3 rounded-full bg-white/40 hover:bg-white transition" onclick="setSlide(2)"></button>
        <button class="carousel-dot w-3 h-3 rounded-full bg-white/40 hover:bg-white transition" onclick="setSlide(3)"></button>
      </div>
    </div>
  </div>
</section>

<!-- SCRIPT SLIDER CAROUSEL -->
<script>
  let currentSlide = 0;
  const slides = document.querySelectorAll('.carousel-slide');
  const dots = document.querySelectorAll('.carousel-dot');

  function showSlide(index) {
    slides.forEach((slide, i) => {
      if (i === index) {
        slide.classList.remove('opacity-0');
        slide.classList.add('opacity-100');
      } else {
        slide.classList.remove('opacity-100');
        slide.classList.add('opacity-0');
      }
    });

    dots.forEach((dot, i) => {
      if (i === index) {
        dot.classList.remove('bg-white/40');
        dot.classList.add('bg-white', 'w-6');
      } else {
        dot.classList.remove('bg-white', 'w-6');
        dot.classList.add('bg-white/40', 'w-3');
      }
    });
  }

  function setSlide(index) {
    currentSlide = index;
    showSlide(currentSlide);
  }

  setInterval(() => {
    currentSlide = (currentSlide + 1) % slides.length;
    showSlide(currentSlide);
  }, 4000);

  showSlide(0);
</script>

<section id="story" class="mx-auto max-w-7xl px-6 py-12 lg:px-10">
  <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center bg-white/80 p-8 md:p-12 rounded-3xl border border-[#eadfd4]">
    <div>
      <span class="eyebrow">Cerita Kami</span>
      <h2 class="mt-3 font-display text-4xl font-bold text-[#201913]">Otentik, Hangat, & Penuh Kenangan</h2>
      <p class="mt-4 text-[#4f4338] leading-relaxed">
        Berdiri sejak tahun 2026, Kafiber berkomitmen menyajikan kelezatan hidangan nusantara dan internasional dengan sentuhan modern. Kami percaya setiap hidangan membawa cerita dan momen berharga.
      </p>
    </div>
    <div class="rounded-2xl overflow-hidden shadow-lg border border-[#eadfd4]">
      <img src="assets/images/indoroutdor/1.png" alt="Outdoor View" class="w-full h-64 object-cover">
    </div>
  </div>
</section>

<!-- SECTION MENU KULINER -->
<section id="menu-kuliner" class="mx-auto max-w-7xl px-6 py-12 lg:px-10">
  <div class="text-center max-w-2xl mx-auto mb-10">
    <span class="eyebrow">Pilihan Terbaik</span>
    <h2 class="mt-3 font-display text-4xl font-bold text-[#201913]">Kategori Menu Kuliner</h2>
    <p class="mt-2 text-[#4f4338]">Temukan berbagai sajian pilihan lezat racikan koki terbaik kami.</p>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#eadfd4] hover:shadow-md transition">
      <span class="text-xs uppercase tracking-[0.24em] text-[#8a5d49] font-bold">Kategori 1</span>
      <h3 class="font-display text-xl font-bold mt-2 text-[#201913]">Makanan Utama</h3>
      <p class="text-xs text-[#66574b] mt-1">(Main Course)</p>
    </div>

    <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#eadfd4] hover:shadow-md transition">
      <span class="text-xs uppercase tracking-[0.24em] text-[#8a5d49] font-bold">Kategori 2</span>
      <h3 class="font-display text-xl font-bold mt-2 text-[#201913]">Hidangan Pembuka</h3>
      <p class="text-xs text-[#66574b] mt-1">(Appetizer)</p>
    </div>

    <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#eadfd4] hover:shadow-md transition">
      <span class="text-xs uppercase tracking-[0.24em] text-[#8a5d49] font-bold">Kategori 3</span>
      <h3 class="font-display text-xl font-bold mt-2 text-[#201913]">Makanan Penutup</h3>
      <p class="text-xs text-[#66574b] mt-1">(Dessert)</p>
    </div>

    <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#eadfd4] hover:shadow-md transition">
      <span class="text-xs uppercase tracking-[0.24em] text-[#8a5d49] font-bold">Kategori 4</span>
      <h3 class="font-display text-xl font-bold mt-2 text-[#201913]">Minuman</h3>
      <p class="text-xs text-[#66574b] mt-1">(Beverages)</p>
    </div>
  </div>
</section>

<section class="mx-auto max-w-7xl px-6 pb-16 pt-4 lg:px-10">
  <div class="cta-banner">
    <div>
      <span class="eyebrow">Mulai Sekarang</span>
      <h2 class="mt-4 font-display text-3xl text-[#201913]">Siapkan alur reservasi restoran yang benar-benar operasional.</h2>
      <p class="mt-4 max-w-2xl text-sm leading-6 text-[#5d4e42]">Langkah berikutnya tinggal menambah halaman reservasi, daftar reservasi pelanggan, dan dashboard admin/staff yang terhubung ke database yang sudah tersedia.</p>
    </div>
    <div class="flex flex-wrap gap-4">
      <?php if ($isLoggedIn): ?>
        <a href="<?= route('reservasi') ?>" class="btn-primary">Buat Pesanan</a>
        <a href="<?= route('dashboard') ?>" class="btn-secondary">Lihat Dashboard</a>
      <?php else: ?>
        <a href="<?= route('register') ?>" class="btn-primary">Mulai Daftar</a>
        <a href="<?= route('login') ?>" class="btn-secondary">Masuk ke Sistem</a>
      <?php endif; ?>
    </div>
  </div>
</section>
