<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}

$restaurantName = 'Kafiber';
$isLoggedIn = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;

include LAYOUTS_PATH . '/header.php';
?>

<section class="mx-auto max-w-7xl px-6 py-12 lg:px-10">
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

  <!-- Tombol Tab Filter -->
  <div class="flex flex-wrap justify-center gap-3 mb-10">
    <button onclick="filterGaleri('semua')" id="btn-semua" class="tab-btn px-5 py-2 rounded-full text-xs font-semibold bg-[#8a5d49] text-white transition shadow-sm">Semua</button>
    <button onclick="filterGaleri('ruangan')" id="btn-ruangan" class="tab-btn px-5 py-2 rounded-full text-xs font-semibold bg-[#efebe4] text-[#66574b] hover:bg-[#decbbd] transition">Ruangan Utama</button>
    <button onclick="filterGaleri('privat')" id="btn-privat" class="tab-btn px-5 py-2 rounded-full text-xs font-semibold bg-[#efebe4] text-[#66574b] hover:bg-[#decbbd] transition">Area Privat</button>
    <button onclick="filterGaleri('malam')" id="btn-malam" class="tab-btn px-5 py-2 rounded-full text-xs font-semibold bg-[#efebe4] text-[#66574b] hover:bg-[#decbbd] transition">Suasana Malam</button>
  </div>

  <!-- Grid Galeri Foto (Total 12 Foto: 4 Ruangan, 4 Privat, 4 Malam) -->
  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6" id="galeri-container">
    
    <!-- === 4 FOTO: RUANGAN UTAMA === -->
    <div class="galeri-item ruangan group relative rounded-3xl overflow-hidden border border-[#eadfd4] bg-white shadow-sm h-72">
      <img src="img/ruangan_utama/ruanganutama1.jpg" alt="Ruangan Utama 1" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
      <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex items-end p-6">
        <p class="text-white text-sm font-medium">Ruangan Utama 1</p>
      </div>
    </div>
    <div class="galeri-item ruangan group relative rounded-3xl overflow-hidden border border-[#eadfd4] bg-white shadow-sm h-72">
      <img src="img/ruangan_utama/ruanganutama2.jpg" alt="Ruangan Utama 2" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
      <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex items-end p-6">
        <p class="text-white text-sm font-medium">Ruangan Utama 2</p>
      </div>
    </div>
    <div class="galeri-item ruangan group relative rounded-3xl overflow-hidden border border-[#eadfd4] bg-white shadow-sm h-72">
      <img src="img/ruangan_utama/ruanganutama3.jpg" alt="Ruangan Utama 3" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
      <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex items-end p-6">
        <p class="text-white text-sm font-medium">Ruangan Utama 3</p>
      </div>
    </div>
    <div class="galeri-item ruangan group relative rounded-3xl overflow-hidden border border-[#eadfd4] bg-white shadow-sm h-72">
      <img src="img/ruangan_utama/ruanganutama4.jpg" alt="Ruangan Utama 4" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
      <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex items-end p-6">
        <p class="text-white text-sm font-medium">Ruangan Utama 4</p>
      </div>
    </div>


    <!-- === 4 FOTO: AREA PRIVAT === -->
    <div class="galeri-item privat group relative rounded-3xl overflow-hidden border border-[#eadfd4] bg-white shadow-sm h-72" style="display: none;">
      <img src="img/area_privat/areaprivat1.jpg" alt="Area Privat 1" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
      <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex items-end p-6">
        <p class="text-white text-sm font-medium">Area Privat 1</p>
      </div>
    </div>
    <div class="galeri-item privat group relative rounded-3xl overflow-hidden border border-[#eadfd4] bg-white shadow-sm h-72" style="display: none;">
      <img src="img/area_privat/areaprivat2.jpg" alt="Area Privat 2" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
      <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex items-end p-6">
        <p class="text-white text-sm font-medium">Area Privat 2</p>
      </div>
    </div>
    <div class="galeri-item privat group relative rounded-3xl overflow-hidden border border-[#eadfd4] bg-white shadow-sm h-72" style="display: none;">
      <img src="img/area_privat/areaprivat3.jpg" alt="Area Privat 3" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
      <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex items-end p-6">
        <p class="text-white text-sm font-medium">Area Privat 3</p>
      </div>
    </div>
    <div class="galeri-item privat group relative rounded-3xl overflow-hidden border border-[#eadfd4] bg-white shadow-sm h-72" style="display: none;">
      <img src="img/area_privat/areaprivat4.jpg" alt="Area Privat 4" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
      <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex items-end p-6">
        <p class="text-white text-sm font-medium">Area Privat 4</p>
      </div>
    </div>


    <!-- === 4 FOTO: SUASANA MALAM === -->
    <div class="galeri-item malam group relative rounded-3xl overflow-hidden border border-[#eadfd4] bg-white shadow-sm h-72" style="display: none;">
      <img src="img/suasana_malam/suasanamalam1.jpg" alt="Suasana Malam 1" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
      <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex items-end p-6">
        <p class="text-white text-sm font-medium">Suasana Malam 1</p>
      </div>
    </div>
    <div class="galeri-item malam group relative rounded-3xl overflow-hidden border border-[#eadfd4] bg-white shadow-sm h-72" style="display: none;">
      <img src="img/suasana_malam/suasanamalam2.jpg" alt="Suasana Malam 2" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
      <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex items-end p-6">
        <p class="text-white text-sm font-medium">Suasana Malam 2</p>
      </div>
    </div>
    <div class="galeri-item malam group relative rounded-3xl overflow-hidden border border-[#eadfd4] bg-white shadow-sm h-72" style="display: none;">
      <img src="img/suasana_malam/suasanamalam3.jpg" alt="Suasana Malam 3" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
      <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex items-end p-6">
        <p class="text-white text-sm font-medium">Suasana Malam 3</p>
      </div>
    </div>
    <div class="galeri-item malam group relative rounded-3xl overflow-hidden border border-[#eadfd4] bg-white shadow-sm h-72" style="display: none;">
      <img src="img/suasana_malam/suasanamalam4.jpg" alt="Suasana Malam 4" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
      <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex items-end p-6">
        <p class="text-white text-sm font-medium">Suasana Malam 4</p>
      </div>
    </div>

  </div>
</section>

<!-- Script JavaScript Filter -->
<script>
function filterGaleri(kategori) {
    const items = document.querySelectorAll('.galeri-item');
    const buttons = document.querySelectorAll('.tab-btn');

    // Ubah warna tombol aktif
    buttons.forEach(btn => {
        btn.classList.remove('bg-[#8a5d49]', 'text-white', 'shadow-sm');
        btn.classList.add('bg-[#efebe4]', 'text-[#66574b]');
    });
    document.getElementById('btn-' + kategori).classList.remove('bg-[#efebe4]', 'text-[#66574b]');
    document.getElementById('btn-' + kategori).classList.add('bg-[#8a5d49]', 'text-white', 'shadow-sm');

    // Tampilkan / Sembunyikan item berdasarkan kategori
    items.forEach(item => {
        if (kategori === 'semua' || item.classList.contains(kategori)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}
</script>

<?php include LAYOUTS_PATH . '/footer.php'; ?>