<?php
// pages/galeri.php — Galeri Suasana Restoran
$restaurantName = 'Kafiber';
$isLoggedIn = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
?>

<section class="mx-auto max-w-7xl px-6 py-12 lg:px-10">
  
  <!-- Tombol Kembali ke Home -->
  <div class="mb-6">
    <a href="<?= route('home') ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#efebe4] hover:bg-[#decbbd] text-[#5e392e] text-xs font-bold transition shadow-sm">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
      </svg>
      Kembali ke Beranda
    </a>
  </div>

  <!-- Judul Halaman -->
  <div class="text-center max-w-2xl mx-auto mb-12">
    <span class="eyebrow">Galeri & Suasana</span>
    <h1 class="mt-3 font-display text-4xl text-[#201913] md:text-5xl tracking-tight">
      Kehangatan di Setiap Sudut Ruangan
    </h1>
    <p class="mt-4 text-sm md:text-base text-[#66574b] leading-relaxed">
      Intip kenyamanan suasana restoran <?= e($restaurantName) ?>. Dirancang khusus untuk memberikan pengalaman bersantap yang intim, estetis, dan berkesan.
    </p>
  </div>

  <!-- Tombol Filter Kategori Suasana -->
  <div class="flex flex-wrap items-center justify-center gap-3 mb-10">
    <button onclick="filterGaleri('semua')" id="btn-semua" class="tab-btn px-5 py-2.5 rounded-full text-xs font-bold bg-[#8a5d49] text-white shadow-sm transition">
      Semua Suasana
    </button>
    <button onclick="filterGaleri('utama')" id="btn-utama" class="tab-btn px-5 py-2.5 rounded-full text-xs font-bold bg-[#efebe4] text-[#66574b] hover:bg-[#decbbd] transition">
      Ruangan Utama
    </button>
    <button onclick="filterGaleri('privat')" id="btn-privat" class="tab-btn px-5 py-2.5 rounded-full text-xs font-bold bg-[#efebe4] text-[#66574b] hover:bg-[#decbbd] transition">
      Area Privat / VIP
    </button>
    <button onclick="filterGaleri('malam')" id="btn-malam" class="tab-btn px-5 py-2.5 rounded-full text-xs font-bold bg-[#efebe4] text-[#66574b] hover:bg-[#decbbd] transition">
      Suasana Malam
    </button>
    <button onclick="filterGaleri('makanan')" id="btn-makanan" class="tab-btn px-5 py-2.5 rounded-full text-xs font-bold bg-[#efebe4] text-[#66574b] hover:bg-[#decbbd] transition">
      Sajian Kuliner
    </button>
    <button onclick="filterGaleri('indoroutdor')" id="btn-indoroutdor" class="tab-btn px-5 py-2.5 rounded-full text-xs font-bold bg-[#efebe4] text-[#66574b] hover:bg-[#decbbd] transition">
      Indoor / Outdoor
    </button>
  </div>

  <!-- Grid Galeri Foto -->
  <div id="galeri-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">

    <!-- Ruangan Utama -->
    <div class="galeri-item utama bg-white p-3 rounded-2xl border border-[#eadfd4] shadow-sm hover:shadow-md transition">
      <div class="overflow-hidden rounded-xl h-64 border border-[#eadfd4]">
        <img src="assets/images/ruangan_utama/1.png" alt="Ruangan Utama 1" class="w-full h-full object-cover hover:scale-105 transition duration-500">
      </div>
      <div class="p-3">
        <h3 class="font-display font-bold text-[#201913]">Ruangan Utama - Meja Tengah</h3>
        <p class="text-xs text-[#8a5d49]">Pencahayaan hangat untuk keluarga</p>
      </div>
    </div>

    <!-- Area Privat / VIP -->
    <div class="galeri-item privat bg-white p-3 rounded-2xl border border-[#eadfd4] shadow-sm hover:shadow-md transition">
      <div class="overflow-hidden rounded-xl h-64 border border-[#eadfd4]">
        <img src="assets/images/area_privat/2.png" alt="Area Privat 2" class="w-full h-full object-cover hover:scale-105 transition duration-500">
      </div>
      <div class="p-3">
        <h3 class="font-display font-bold text-[#201913]">Ruang Privat VIP</h3>
        <p class="text-xs text-[#8a5d49]">Eksklusif untuk rapat & acara penting</p>
      </div>
    </div>

    <!-- Suasana Malam -->
    <div class="galeri-item malam bg-white p-3 rounded-2xl border border-[#eadfd4] shadow-sm hover:shadow-md transition">
      <div class="overflow-hidden rounded-xl h-64 border border-[#eadfd4]">
        <img src="assets/images/suasana_malam/3.png" alt="Suasana Malam 3" class="w-full h-full object-cover hover:scale-105 transition duration-500">
      </div>
      <div class="p-3">
        <h3 class="font-display font-bold text-[#201913]">Suasana Malam Romantis</h3>
        <p class="text-xs text-[#8a5d49]">Lampu hangat untuk santap malam</p>
      </div>
    </div>

    <!-- Slide Makanan -->
    <div class="galeri-item makanan bg-white p-3 rounded-2xl border border-[#eadfd4] shadow-sm hover:shadow-md transition">
      <div class="overflow-hidden rounded-xl h-64 border border-[#eadfd4]">
        <img src="assets/images/slide_makanan/4.png" alt="Sajian Kuliner 4" class="w-full h-full object-cover hover:scale-105 transition duration-500">
      </div>
      <div class="p-3">
        <h3 class="font-display font-bold text-[#201913]">Hidangan Utama Spesial</h3>
        <p class="text-xs text-[#8a5d49]">Cita rasa khas koki utama</p>
      </div>
    </div>

    <!-- Indoor / Outdoor -->
    <div class="galeri-item indoroutdor bg-white p-3 rounded-2xl border border-[#eadfd4] shadow-sm hover:shadow-md transition">
      <div class="overflow-hidden rounded-xl h-64 border border-[#eadfd4]">
        <img src="assets/images/indoroutdor/1.png" alt="Indoor / Outdoor 1" class="w-full h-full object-cover hover:scale-105 transition duration-500">
      </div>
      <div class="p-3">
        <h3 class="font-display font-bold text-[#201913]">Area Semi-Outdoor</h3>
        <p class="text-xs text-[#8a5d49]">Udara segar dengan pemandangan taman</p>
      </div>
    </div>

  </div>
</section>

<!-- Script JavaScript Filter -->
<script>
function filterGaleri(kategori) {
    const items = document.querySelectorAll('.galeri-item');
    const buttons = document.querySelectorAll('.tab-btn');

    buttons.forEach(btn => {
        btn.classList.remove('bg-[#8a5d49]', 'text-white', 'shadow-sm');
        btn.classList.add('bg-[#efebe4]', 'text-[#66574b]');
    });
    document.getElementById('btn-' + kategori).classList.remove('bg-[#efebe4]', 'text-[#66574b]');
    document.getElementById('btn-' + kategori).classList.add('bg-[#8a5d49]', 'text-white', 'shadow-sm');

    items.forEach(item => {
        if (kategori === 'semua' || item.classList.contains(kategori)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}
</script>
