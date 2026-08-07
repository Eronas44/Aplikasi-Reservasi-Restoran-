<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ambil parameter restoran yang diklik, default ke 'A'
$restoId = isset($_GET['resto']) ? $_GET['resto'] : 'A';

// Data dummy restoran berdasarkan pilihan
$restaurants = [
    'A' => [
        'name' => 'Restoran A',
        'rating' => '4.9',
        'hours' => '10.00 - 22.00 WIB',
        'images' => ['img/Resto/RestoA.jpg', 'img/Resto/ViewRestoA.jpg', 'img/slide_makanan/foto3.jpg'],
        'facilities' => [
            'Live Acoustic Music: Hiburan musik akustik setiap akhir pekan.',
            'Ruang VIP (Private Room): Ruangan ber-AC eksklusif untuk acara keluarga atau rapat.',
            'Kids Corner: Area bermain anak yang aman.',
            'Wi-Fi Berkecepatan Tinggi: Akses internet gratis untuk pengunjung.',
            'Mushola: Tempat ibadah yang bersih dan nyaman.',
            'Area Parkir Luas & Valet: Tersedia lahan parkir yang aman untuk mobil dan motor, lengkap dengan layanan parkir valet.',
            'Smoking Area & Non-Smoking Area: Pilihan area tempat duduk terpisah yang nyaman bagi perokok maupun bukan-perokok.',
            'Live Cooking Counter: Atraksi langsung dari koki saat menyiapkan hidangan tertentu di hadapan tamu.'
        ]
    ],
    'B' => [
        'name' => 'Restoran B',
        'rating' => '4.8',
        'hours' => '10.00 - 22.00 WIB',
        'images' => ['img/slide_makanan/makanan 2.jpg', 'img/slide_makanan/makanan 3.jpg'],
        'facilities' => [
            'Live Acoustic Music: Hiburan musik akustik setiap akhir pekan.',
            'Wi-Fi Berkecepatan Tinggi: Akses internet gratis untuk pengunjung.',
            'Mushola: Tempat ibadah yang bersih dan nyaman.',
            'Area Parkir Luas: Tersedia lahan parkir yang aman.'
        ]
    ],
    'C' => [
        'name' => 'Restoran C',
        'rating' => '4.7',
        'hours' => '10.00 - 22.00 WIB',
        'images' => ['img/slide_makanan/Makanan 1.jpg', 'img/slide_makanan/makanan 3.jpg'],
        'facilities' => [
            'Kids Corner: Area bermain anak yang aman.',
            'Wi-Fi Berkecepatan Tinggi: Akses internet gratis untuk pengunjung.',
            'Mushola: Tempat ibadah yang bersih dan nyaman.'
        ]
    ],
    'D' => [
        'name' => 'Restoran D',
        'rating' => '4.9',
        'hours' => '10.00 - 22.00 WIB',
        'images' => ['img/slide_makanan/makanan 4.jpg', 'img/slide_makanan/makanan 5.jpg'],
        'facilities' => [
            'Ruang VIP (Private Room): Ruangan ber-AC eksklusif.',
            'Live Cooking Counter: Atraksi langsung dari koki.'
        ]
    ]
];

$currentResto = isset($restaurants[$restoId]) ? $restaurants[$restoId] : $restaurants['A'];

include LAYOUTS_PATH . '/header.php';
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-10">
  <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 items-start">
    
    <!-- Kolom Kiri: Tombol Nama Restoran, Back, dan Jam Buka -->
    <div class="space-y-4 lg:col-span-1">
      <!-- Nama Restoran -->
      <div class="bg-white border border-[#eadfd4] rounded-2xl px-5 py-4 shadow-sm text-center font-display text-xl font-bold text-[#201913]">
        <?= $currentResto['name'] ?>
      </div>

      <!-- Tombol Back -->
      <a href="index.php?page=dashboard" class="flex items-center justify-center gap-2 bg-white border border-[#eadfd4] hover:bg-[#efebe4] rounded-2xl py-3 shadow-sm font-semibold text-[#5e392e] transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Back
      </a>

      <!-- Jam Buka / Tutup -->
      <div class="bg-white/60 border border-[#eadfd4] rounded-2xl p-5 shadow-sm text-[#5d4e42] text-sm leading-relaxed">
        <p class="font-bold mb-1 text-[#201913]">Jam buka / tutup:</p>
        <p><?= $currentResto['hours'] ?></p>
      </div>
    </div>

    <!-- Kolom Kanan: Preview Gambar, Rating, dan Daftar Fasilitas -->
    <div class="lg:col-span-3 bg-white border border-[#eadfd4] rounded-3xl p-6 shadow-sm">
      
      <!-- Container Gambar & Carousel -->
      <div class="relative w-full h-[350px] rounded-2xl overflow-hidden border border-[#eadfd4] bg-stone-100 flex items-center justify-center group detail-carousel" data-images='<?= json_encode($currentResto['images']) ?>'>
        <img src="<?= $currentResto['images'][0] ?>" alt="Preview Restoran" class="w-full h-full object-cover transition-opacity duration-700 detail-img">
        
        <!-- Tombol Panah Carousel Kanan -->
        <button onclick="nextDetailSlide()" class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/80 hover:bg-white text-stone-900 border border-stone-200 rounded-full flex items-center justify-center shadow transition opacity-80 group-hover:opacity-100 focus:outline-none">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </button>
      </div>

      <!-- Rating -->
      <div class="text-center my-4 font-display text-lg font-bold text-[#201913]">
        Rating <?= $currentResto['rating'] ?>
      </div>

      <!-- Kotak Fasilitas / Deskripsi bawah -->
      <div class="bg-[#d7cbc1]/50 border border-[#eadfd4] rounded-2xl p-6 space-y-2 text-[#3b3028] text-sm leading-relaxed">
        <?php foreach ($currentResto['facilities'] as $facility): ?>
          <p>• <?= $facility ?></p>
        <?php endforeach; ?>
      </div>

    </div>

  </div>
</div>

<!-- Script untuk Slider Gambar di Halaman Detail -->
<script>
let detailCurrentIndex = 0;
const detailCarousel = document.querySelector(".detail-carousel");
if (detailCarousel) {
    const detailImgElement = detailCarousel.querySelector(".detail-img");
    const detailImages = JSON.parse(detailCarousel.getAttribute("data-images"));

    function nextDetailSlide() {
        if (detailImages.length > 1) {
            detailCurrentIndex = (detailCurrentIndex + 1) % detailImages.length;
            detailImgElement.style.opacity = 0;
            setTimeout(() => {
                detailImgElement.src = detailImages[detailCurrentIndex];
                detailImgElement.style.opacity = 1;
            }, 300);
        }
    }
}
</script>

<?php include LAYOUTS_PATH . '/footer.php'; ?>