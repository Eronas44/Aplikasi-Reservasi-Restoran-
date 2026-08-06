<?php
session_start(); // Memulai sesi untuk mengecek status login pengguna

if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}

$restaurantName = 'Rasa & Cerita';
$restaurantPhone = '+62 829 6573 9824';
$restaurantAddress = 'Jl. Soekarno Hatta No.113, Lampung, Indonesia';
$openTime = '10:00';
$closeTime = '22:00';

// Cek status login pengguna (sesuaikan nama variabel session dengan sistem login Anda)
$isLoggedIn = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;

$featuredCategories = [
    ['category_id' => 1, 'category_name' => 'Makanan Utama'],
    ['category_id' => 2, 'category_name' => 'Hidangan Pembuka'],
    ['category_id' => 3, 'category_name' => 'Makanan Penutup'],
    ['category_id' => 4, 'category_name' => 'Minuman'],
];

$featuredMenu = [];

include LAYOUTS_PATH . '/header.php';
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
      
      <!-- Tombol Hero Dinamis Berdasarkan Status Login -->
      <div class="mt-4 flex flex-wrap gap-4">
        <?php if ($isLoggedIn): ?>
          <a href="<?= route('dashboard') ?>" class="btn-primary">Mulai Pesan Sekarang</a>
          <a href="<?= route('dashboard') ?>" class="btn-secondary">Dashboard Saya</a>
        <?php else: ?>
          <a href="<?= route('register') ?>" class="btn-primary">Buat Akun</a>
          <a href="<?= route('login') ?>" class="btn-secondary">Masuk ke Sistem</a>
        <?php endif; ?>
      </div>
    </div>
    <div class="relative">
      <div class="hero-card">
        <div class="hero-orb hero-orb-1"></div>
        <div class="hero-orb hero-orb-2"></div>
        <div class="rounded-[2rem] border border-white/60 bg-white/90 p-4 shadow-2xl backdrop-blur relative overflow-hidden h-[360px] md:h-[420px] flex items-center justify-center group">
          <!-- Carousel Slides Container -->
          <div class="carousel-container relative w-full h-full rounded-2xl overflow-hidden">
            <!-- Slides -->
            <div class="carousel-slide absolute inset-0 opacity-100 transition-opacity duration-1000 ease-in-out">
              <img src="img/slide_makanan/Makanan 1.jpg" alt="Makanan 1" class="w-full h-full object-cover">
            </div>
            <div class="carousel-slide absolute inset-0 opacity-0 transition-opacity duration-1000 ease-in-out">
              <img src="img/slide_makanan/makanan 2.jpg" alt="Makanan 2" class="w-full h-full object-cover">
            </div>
            <div class="carousel-slide absolute inset-0 opacity-0 transition-opacity duration-1000 ease-in-out">
              <img src="img/slide_makanan/makanan 3.jpg" alt="Makanan 3" class="w-full h-full object-cover">
            </div>
            <div class="carousel-slide absolute inset-0 opacity-0 transition-opacity duration-1000 ease-in-out">
              <img src="img/slide_makanan/makanan 4.jpg" alt="Makanan 4" class="w-full h-full object-cover">
            </div>
            <div class="carousel-slide absolute inset-0 opacity-0 transition-opacity duration-1000 ease-in-out">
              <img src="img/slide_makanan/makanan 5.jpg" alt="Makanan 5" class="w-full h-full object-cover">
            </div>
          </div>
          
          <!-- Navigation Buttons -->
          <button onclick="prevSlide()" class="absolute left-6 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/80 hover:bg-white text-stone-900 border border-stone-200/50 rounded-full flex items-center justify-center shadow-md transition opacity-0 group-hover:opacity-100 focus:outline-none z-10">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
          </button>
          <button onclick="nextSlide()" class="absolute right-6 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/80 hover:bg-white text-stone-900 border border-stone-200/50 rounded-full flex items-center justify-center shadow-md transition opacity-0 group-hover:opacity-100 focus:outline-none z-10">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
          </button>

          <!-- Indicators -->
          <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2 z-10">
            <span onclick="setSlide(0)" class="carousel-indicator w-2.5 h-2.5 rounded-full bg-white/85 cursor-pointer transition shadow-sm"></span>
            <span onclick="setSlide(1)" class="carousel-indicator w-2.5 h-2.5 rounded-full bg-white/35 cursor-pointer transition shadow-sm"></span>
            <span onclick="setSlide(2)" class="carousel-indicator w-2.5 h-2.5 rounded-full bg-white/35 cursor-pointer transition shadow-sm"></span>
            <span onclick="setSlide(3)" class="carousel-indicator w-2.5 h-2.5 rounded-full bg-white/35 cursor-pointer transition shadow-sm"></span>
            <span onclick="setSlide(4)" class="carousel-indicator w-2.5 h-2.5 rounded-full bg-white/35 cursor-pointer transition shadow-sm"></span>
          </div>
        </div>

        <script>
          let currentSlide = 0;
          const slides = document.querySelectorAll('.carousel-slide');
          const indicators = document.querySelectorAll('.carousel-indicator');
          let autoSlideInterval;

          function showSlide(index) {
            slides.forEach((slide, i) => {
              if (i === index) {
                slide.classList.remove('opacity-0');
                slide.classList.add('opacity-100');
                indicators[i].classList.remove('bg-white/35');
                indicators[i].classList.add('bg-white/85');
              } else {
                slide.classList.add('opacity-0');
                slide.classList.remove('opacity-100');
                indicators[i].classList.remove('bg-white/85');
                indicators[i].classList.add('bg-white/35');
              }
            });
            currentSlide = index;
          }

          function nextSlide() {
            let next = (currentSlide + 1) % slides.length;
            showSlide(next);
            resetAutoSlide();
          }

          function prevSlide() {
            let prev = (currentSlide - 1 + slides.length) % slides.length;
            showSlide(prev);
            resetAutoSlide();
          }

          function setSlide(index) {
            showSlide(index);
            resetAutoSlide();
          }

          function startAutoSlide() {
            autoSlideInterval = setInterval(() => {
              let next = (currentSlide + 1) % slides.length;
              showSlide(next);
            }, 4000);
          }

          function resetAutoSlide() {
            clearInterval(autoSlideInterval);
            startAutoSlide();
          }

          showSlide(0);
          startAutoSlide();
        </script>
      </div>
    </div>
  </div>
</section>

<section id="fitur" class="mx-auto max-w-7xl px-6 py-4 lg:px-10">
  <div class="grid gap-6 md:grid-cols-3">
    
    <!-- Feature 1: Kapasitas -->
    <div class="feature-card flex flex-col items-center text-center p-8 bg-white/70 border border-[#eadfd4] rounded-3xl shadow-sm">
      <div class="w-20 h-20 bg-[#efe0d5] border border-[#decbbd] rounded-2xl flex items-center justify-center mb-5 shadow-sm">
        <svg viewBox="0 0 64 64" class="w-12 h-12 text-[#8a5d49]" fill="none" stroke="currentColor" stroke-width="2.5">
          <circle cx="32" cy="20" r="8" />
          <path d="M12 50c0-10 10-14 20-14s20 4 20 14" stroke-linecap="round" />
          <circle cx="16" cy="24" r="6" />
          <path d="M4 52c0-8 6-12 12-12" stroke-linecap="round" />
          <circle cx="48" cy="24" r="6" />
          <path d="M60 52c0-8-6-12-12-12" stroke-linecap="round" />
        </svg>
      </div>
      <p class="feature-kicker text-xs uppercase tracking-[0.24em] text-[#8a5d49] font-bold">Kapasitas</p>
      <h3 class="feature-title font-display text-xl font-bold mt-2 text-[#201913]">Hingga 120 Tamu</h3>
      <p class="feature-text text-sm text-[#66574b] mt-3 leading-relaxed">Ruang utama & area privat untuk acara Anda.</p>
    </div>

    <!-- Feature 2: Suasana -->
    <div class="feature-card flex flex-col items-center text-center p-8 bg-white/70 border border-[#eadfd4] rounded-3xl shadow-sm">
      <div class="w-20 h-20 bg-[#efe0d5] border border-[#decbbd] rounded-2xl flex items-center justify-center mb-5 shadow-sm">
        <svg viewBox="0 0 64 64" class="w-12 h-12 text-[#8a5d49]" fill="none" stroke="currentColor" stroke-width="2.5">
          <path d="M10 36h44M18 36v20M46 36v20" stroke-linecap="round"/>
          <path d="M14 26H8v28M50 26h6v28" stroke-linecap="round"/>
        </svg>
      </div>
      <p class="feature-kicker text-xs uppercase tracking-[0.24em] text-[#8a5d49] font-bold">Suasana</p>
      <h3 class="feature-title font-display text-xl font-bold mt-2 text-[#201913]">Meja & Suasana</h3>
      <p class="feature-text text-sm text-[#66574b] mt-3 leading-relaxed">Pilih meja sesuai suasana yang Anda inginkan.</p>
    </div>

    <!-- Feature 3: Ulasan -->
    <div class="feature-card flex flex-col items-center text-center p-8 bg-white/70 border border-[#eadfd4] rounded-3xl shadow-sm">
      <div class="w-20 h-20 bg-[#efe0d5] border border-[#decbbd] rounded-2xl flex items-center justify-center mb-5 shadow-sm">
        <svg viewBox="0 0 64 64" class="w-12 h-12 text-[#8a5d49]" fill="currentColor" stroke="none">
          <path d="M18 24l3.5 7.5 8 1-6 5.5 1.5 8-7-4.5-7 4.5 1.5-8-6-5.5 8-1z" opacity="0.8"/>
          <path d="M32 14l4.5 9.5 10 1.5-7.5 7 2 10-9-5.5-9 5.5 2-10-7.5-7 10-1.5z" />
          <path d="M46 24l3.5 7.5 8 1-6 5.5 1.5 8-7-4.5-7 4.5 1.5-8-6-5.5 8-1z" opacity="0.8"/>
        </svg>
      </div>
      <p class="feature-kicker text-xs uppercase tracking-[0.24em] text-[#8a5d49] font-bold">Ulasan</p>
      <h3 class="feature-title font-display text-xl font-bold mt-2 text-[#201913]">Rating 4.9 / 5</h3>
      <p class="feature-text text-sm text-[#66574b] mt-3 leading-relaxed">Dari lebih dari 800 ulasan tamu kami.</p>
    </div>

  </div>
</section>

<section id="menu" class="mx-auto max-w-7xl px-6 py-12 lg:px-10">
  <div class="flex items-end justify-between gap-6 mb-8">
    <div>
      <span class="eyebrow">Kategori Menu</span>
      <h2 class="mt-4 font-display text-3xl text-[#201913] md:text-4xl">Pilihan hidangan utama restoran</h2>
    </div>
    <a href="<?= $isLoggedIn ? route('dashboard') : route('login') . '#login' ?>" class="hidden text-sm font-semibold text-[#8a5d49] md:inline">Kelola menu dari dashboard</a>
  </div>

  <div class="bg-[#efebe4]/80 border border-[#eadfd4] rounded-3xl p-8 flex flex-col md:flex-row justify-between gap-6 text-center md:text-left items-center md:items-stretch shadow-sm">
    <div class="flex-1 flex flex-col justify-center py-2">
      <span class="text-xs uppercase tracking-[0.24em] text-[#8a5d49] font-bold">Kategori 1</span>
      <h3 class="font-display text-xl font-bold mt-1 text-[#201913]">Makanan Utama</h3>
      <p class="text-xs text-[#66574b] mt-1">(Asian & Western Cuisine)</p>
    </div>
    <div class="hidden md:block w-px bg-[#eadfd4]"></div>
    
    <div class="flex-1 flex flex-col justify-center py-2">
      <span class="text-xs uppercase tracking-[0.24em] text-[#8a5d49] font-bold">Kategori 2</span>
      <h3 class="font-display text-xl font-bold mt-1 text-[#201913]">Hidangan Pembuka</h3>
      <p class="text-xs text-[#66574b] mt-1">(Appetizer)</p>
    </div>
    <div class="hidden md:block w-px bg-[#eadfd4]"></div>
    
    <div class="flex-1 flex flex-col justify-center py-2">
      <span class="text-xs uppercase tracking-[0.24em] text-[#8a5d49] font-bold">Kategori 3</span>
      <h3 class="font-display text-xl font-bold mt-1 text-[#201913]">Makanan Penutup</h3>
      <p class="text-xs text-[#66574b] mt-1">(Dessert)</p>
    </div>
    <div class="hidden md:block w-px bg-[#eadfd4]"></div>
    
    <div class="flex-1 flex flex-col justify-center py-2">
      <span class="text-xs uppercase tracking-[0.24em] text-[#8a5d49] font-bold">Kategori 4</span>
      <h3 class="font-display text-xl font-bold mt-1 text-[#201913]">Minuman</h3>
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
        <a href="<?= route('dashboard') ?>" class="btn-primary">Buat Pesanan</a>
        <a href="<?= route('dashboard') ?>" class="btn-secondary">Lihat Dashboard</a>
      <?php else: ?>
        <a href="<?= route('register') ?>" class="btn-primary">Mulai Daftar</a>
        <a href="<?= route('dashboard') ?>" class="btn-secondary">Lihat Dashboard</a>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php include LAYOUTS_PATH . '/footer.php'; ?>
