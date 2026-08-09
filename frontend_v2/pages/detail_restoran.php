<?php
// pages/detail_restoran.php — Halaman Detail & Fasilitas Restoran

$restoId = isset($_GET['resto']) ? $_GET['resto'] : 'A';

$restaurants = [
    'A' => [
        'name' => 'Restoran A',
        'rating' => '4.9',
        'hours' => '10.00 - 22.00 WIB',
        'images' => [
            'assets/images/Resto/KidsCornerRestoA.jpg', 
            'assets/images/Resto/RestoA.jpg', 
            'assets/images/Resto/ViewRestoA.jpg', 
            'assets/images/Resto/LiveCookingRestoA.jpg'
        ],
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
        'images' => [
            'assets/images/Resto/ViewRestoB.jpg', 
            'assets/images/Resto/DjRestoB.jpg', 
            'assets/images/Resto/GamingRestoB.jpg', 
            'assets/images/Resto/BarRestoB.png'
        ],
        'facilities' => [
            'Rooftop Sky Lounge: Area tempat duduk di lantai atas dengan pemandangan kota terbuka.',
            'Live DJ Performance: Penampilan musik DJ langsung setiap akhir pekan malam.',
            'Family Booth Seating: Sofa besar khusus keluarga yang nyaman dan privat.',
            'Playstation & Gaming Corner: Sudut hiburan konsol game untuk anak-anak dan remaja.',
            'High-Speed Charging Station: Fasilitas pengisian daya gratis di setiap meja.',
            'Mushola & Clean Restroom: Fasilitas ibadah dan toilet yang bersih serta terawat.',
            'Valet Parking Service: Layanan parkir valet praktis untuk kendaraan pengunjung.',
            'Open Bar & Cocktail Counter: Konter minuman terbuka dengan barista profesional.'
        ]
    ],
    'C' => [
        'name' => 'Restoran C',
        'rating' => '4.7',
        'hours' => '10.00 - 22.00 WIB',
        'images' => [
            'assets/images/Resto/ViewRestoC.jpg', 
            'assets/images/Resto/birdparkRestoC.jpg', 
            'assets/images/Resto/dapurTerbukaRestoC.jpg', 
            'assets/images/Resto/gamelanrestoC.jpg'
        ],
        'facilities' => [
            'Garden Dining Area: Area makan outdoor dengan konsep taman hijau yang sejuk.',
            'Traditional Gamelan Music: Alunan musik tradisional gamelan live setiap malam Minggu.',
            'Meeting Hall (Ballroom): Ruangan serbaguna besar untuk acara formal, seminar, atau pernikahan.',
            'Mini Zoo / Bird Park: Area taman dengan satwa jinak ramah anak.',
            'Free Wi-Fi Access: Koneksi internet nirkabel gratis di seluruh area restoran.',
            'Mushola Representatif: Tempat sholat yang luas dan nyaman untuk jamaah.',
            'Spacious Car Park: Lahan parkir luas yang muat untuk banyak bus dan mobil.',
            'Traditional Kitchen Showcase: Pameran dapur terbuka bergaya tradisional tempo dulu.'
        ]
    ],
    'D' => [
        'name' => 'Restoran D',
        'rating' => '4.9',
        'hours' => '10.00 - 22.00 WIB',
        'images' => [
            'assets/images/Resto/artspacerestoD.jpg',
            'assets/images/Resto/KapsulD.jpg', 
            'assets/images/Resto/tapayakirestoD.jpg', 
            'assets/images/Resto/ViewRestoD.jpg'
        ],
        'facilities' => [
            'Waterfront Deck: Meja makan tepat di tepi kolam buatan atau danau.',
            'Jazz & Blues Night: Hiburan musik jazz lembut setiap hari Jumat dan Sabtu.',
            'Romantic Couple Pods: Gazebo privat berbentuk kapsul khusus untuk pasangan.',
            'Creative Art Space: Sudut melukis dan kerajinan tangan interaktif untuk anak-anak.',
            'High-Speed Wi-Fi: Jaringan internet cepat tanpa batasan kuota.',
            'Mushola & Ablution Area: Tempat wudhu dan mushola yang bersih terpisah.',
            'Secure Parking Area: Area parkir aman dengan penjagaan petugas khusus.',
            'Teppanyaki Grill Station: Atraksi memasak langsung di atas meja grill panas oleh koki.'
        ]
    ]
];

$currentResto = isset($restaurants[$restoId]) ? $restaurants[$restoId] : $restaurants['A'];
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-10">
  
  <div class="mb-6">
    <a href="<?= route('dashboard') ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#efebe4] hover:bg-[#decbbd] text-[#5e392e] text-xs font-bold transition shadow-sm">
      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
      </svg>
      Kembali ke Pilih Restoran
    </a>
  </div>

  <div class="bg-white/90 border border-[#eadfd4] rounded-3xl p-8 md:p-10 shadow-sm backdrop-blur space-y-8">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-[#eadfd4] pb-6 gap-4">
      <div>
        <span class="text-xs uppercase tracking-widest text-[#8a5d49] font-bold">Detail Restoran Mitra</span>
        <h1 class="font-display text-3xl md:text-4xl font-bold text-[#201913] mt-1"><?= e($currentResto['name']) ?></h1>
        <p class="text-sm text-[#66574b] mt-1">Jam Operasional: <strong><?= e($currentResto['hours']) ?></strong></p>
      </div>
      
      <div class="flex items-center gap-3">
        <span class="text-sm font-bold text-[#8a5d49] bg-[#efebe4] px-4 py-2 rounded-full border border-[#eadfd4]">
          ★ <?= e($currentResto['rating']) ?> / 5.0
        </span>
        <a href="<?= route('reservasi', ['resto' => $restoId]) ?>" class="bg-[#5e392e] hover:bg-[#4a2c24] text-white text-xs font-bold py-2.5 px-6 rounded-xl transition shadow-sm">
          Reservasi Sekarang →
        </a>
      </div>
    </div>

    <!-- Carousel Gambar Restoran -->
    <div class="w-full h-80 md:h-[420px] rounded-2xl overflow-hidden border border-[#eadfd4] relative detail-carousel" data-images='<?= json_encode($currentResto['images']) ?>'>
      <img src="<?= e($currentResto['images'][0]) ?>" alt="<?= e($currentResto['name']) ?>" class="w-full h-full object-cover transition-opacity duration-700 detail-img">
      <button onclick="nextDetailSlide()" class="absolute right-4 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-black/70 text-white p-3 rounded-full transition">
        ❯
      </button>
    </div>

    <!-- Fasilitas Restoran -->
    <div class="space-y-4">
      <h2 class="font-display text-2xl font-bold text-[#201913]">Fasilitas Utama Restoran</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <?php foreach ($currentResto['facilities'] as $facility): ?>
          <?php 
            $parts = explode(':', $facility, 2); 
            $title = $parts[0];
            $desc = isset($parts[1]) ? $parts[1] : '';
          ?>
          <div class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-4 flex gap-3 items-start shadow-inner">
            <div class="w-8 h-8 rounded-full bg-[#efebe4] flex items-center justify-center shrink-0 text-[#8a5d49] font-bold text-xs mt-0.5">
              ✓
            </div>
            <div>
              <strong class="block text-sm font-bold text-[#201913]"><?= e($title) ?></strong>
              <span class="text-xs text-[#66574b] mt-0.5 block"><?= e($desc) ?></span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

  </div>
</div>

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
