<?php
// dashboard_user.php — Halaman Dashboard User / Pesan
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fallback aman untuk menghindari warning/error undefined constant LAYOUTS_PATH
if (!defined('LAYOUTS_PATH')) {
    define('LAYOUTS_PATH', __DIR__ . '/../layouts'); // Sesuaikan path folder layouts Anda jika diperlukan
}

// Pastikan user sudah login
$isLoggedIn = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
if (!$isLoggedIn) {
    header('Location: index.php?page=login');
    exit;
}

include LAYOUTS_PATH . '/header.php';
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-10">
  <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
    
    <!-- Sidebar Menu Dashboard -->
    <div class="lg:col-span-1 space-y-3">
      <div class="bg-white/80 border border-[#eadfd4] rounded-2xl p-4 shadow-sm space-y-2">
        <a href="<?= route('dashboard') ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-[#5e392e] text-white font-medium text-sm transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
          Preview Restoran
        </a>
        <a href="<?= route('reservations') ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-[#5e392e] hover:bg-[#efebe4] font-medium text-sm transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          Reservasi Restoran
        </a>
        <a href="<?= route('menu') ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-[#5e392e] hover:bg-[#efebe4] font-medium text-sm transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
          Menu
        </a>
      </div>
    </div>

    <!-- Main Content: Daftar Restoran / Preview -->
    <div class="lg:col-span-3">
      <div class="mb-6">
        <h1 class="font-display text-2xl font-bold text-[#201913]">Pilih Restoran</h1>
        <p class="text-sm text-[#66574b] mt-1">Silakan pilih restoran mitra kami untuk melihat preview dan melakukan reservasi.</p>
      </div>

      <!-- Grid Restoran (4 Kartu) -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- Restoran A -->
        <a href="<?= route('detail_restoran', ['resto' => 'A']) ?>" class="bg-white/80 border border-[#eadfd4] rounded-3xl p-5 shadow-sm flex flex-col hover:border-[#8a5d49] transition group">
          <div class="w-full h-48 rounded-2xl overflow-hidden mb-4 border border-[#eadfd4] relative carousel-container" data-images='["img/Resto/KidsCornerRestoA.jpg", "img/Resto/RestoA.jpg", "img/Resto/ViewRestoA.jpg", "img/Resto/LiveCookingRestoA.jpg"]'>
            <img src="img/Resto/KidsCornerRestoA.jpg" alt="Restoran A" class="w-full h-full object-cover transition-opacity duration-700 carousel-img">
          </div>
          <div class="flex items-center justify-between mt-auto">
            <h3 class="font-display text-lg font-bold text-[#201913] group-hover:text-[#8a5d49] transition">Restoran A</h3>
            <span class="text-sm font-semibold text-[#8a5d49] bg-[#efebe4] px-3 py-1 rounded-full">Rating 4.9</span>
          </div>
        </a>

        <!-- Restoran B -->
        <a href="<?= route('detail_restoran', ['resto' => 'B']) ?>" class="bg-white/80 border border-[#eadfd4] rounded-3xl p-5 shadow-sm flex flex-col hover:border-[#8a5d49] transition group">
          <div class="w-full h-48 rounded-2xl overflow-hidden mb-4 border border-[#eadfd4] relative carousel-container" data-images='["img/Resto/ViewRestoB.jpg", "img/Resto/DjRestoB.jpg", "img/Resto/GamingRestoB.jpg", "img/Resto/BarRestoB.png"]'>
            <img src="img/Resto/ViewRestoB.jpg" alt="Restoran B" class="w-full h-full object-cover transition-opacity duration-700 carousel-img">
          </div>
          <div class="flex items-center justify-between mt-auto">
            <h3 class="font-display text-lg font-bold text-[#201913] group-hover:text-[#8a5d49] transition">Restoran B</h3>
            <span class="text-sm font-semibold text-[#8a5d49] bg-[#efebe4] px-3 py-1 rounded-full">Rating 4.8</span>
          </div>
        </a>

        <!-- Restoran C -->
        <a href="<?= route('detail_restoran', ['resto' => 'C']) ?>" class="bg-white/80 border border-[#eadfd4] rounded-3xl p-5 shadow-sm flex flex-col hover:border-[#8a5d49] transition group">
          <div class="w-full h-48 rounded-2xl overflow-hidden mb-4 border border-[#eadfd4] relative carousel-container" data-images='["img/Resto/ViewRestoC.jpg", "img/Resto/birdparkRestoC.jpg", "img/Resto/dapurTerbukaRestoC.jpg", "img/Resto/gamelanrestoC.jpg"]'>
            <img src="img/Resto/ViewRestoC.jpg" alt="Restoran C" class="w-full h-full object-cover transition-opacity duration-700 carousel-img">
          </div>
          <div class="flex items-center justify-between mt-auto">
            <h3 class="font-display text-lg font-bold text-[#201913] group-hover:text-[#8a5d49] transition">Restoran C</h3>
            <span class="text-sm font-semibold text-[#8a5d49] bg-[#efebe4] px-3 py-1 rounded-full">Rating 4.7</span>
          </div>
        </a>

        <!-- Restoran D -->
        <a href="<?= route('detail_restoran', ['resto' => 'D']) ?>" class="bg-white/80 border border-[#eadfd4] rounded-3xl p-5 shadow-sm flex flex-col hover:border-[#8a5d49] transition group">
          <div class="w-full h-48 rounded-2xl overflow-hidden mb-4 border border-[#eadfd4] relative carousel-container" data-images='["img/Resto/KapsulD.jpg", "img/Resto/tapayakirestoD.jpg", "img/Resto/ViewRestoD.jpg"]'>
            <img src="img/Resto/KapsulD.jpg" alt="Restoran D" class="w-full h-full object-cover transition-opacity duration-700 carousel-img">
          </div>
          <div class="flex items-center justify-between mt-auto">
            <h3 class="font-display text-lg font-bold text-[#201913] group-hover:text-[#8a5d49] transition">Restoran D</h3>
            <span class="text-sm font-semibold text-[#8a5d49] bg-[#efebe4] px-3 py-1 rounded-full">Rating 4.9</span>
          </div>
        </a>

      </div>
    </div>

  </div>
</div>

<!-- Script untuk Slide Otomatis -->
<script>
(function() {
    const carousels = document.querySelectorAll(".carousel-container");

    carousels.forEach(container => {
        const imgElement = container.querySelector(".carousel-img");
        const images = JSON.parse(container.getAttribute("data-images"));
        let currentIndex = 0;

        if (images.length > 1) {
            setInterval(() => {
                currentIndex = (currentIndex + 1) % images.length;
                
                // Efek fade out/in sederhana
                imgElement.style.opacity = 0;
                setTimeout(() => {
                    imgElement.src = images[currentIndex];
                    imgElement.style.opacity = 1;
                }, 300);

            }, 3500); // Ganti gambar setiap 3.5 detik
        }
    });
})();
</script>

<?php include LAYOUTS_PATH . '/footer.php'; ?>