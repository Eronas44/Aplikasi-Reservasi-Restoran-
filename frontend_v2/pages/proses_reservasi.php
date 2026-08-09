<?php
// pages/proses_reservasi.php — Halaman Konfirmasi / Hasil Reservasi Restoran

$restaurantName = 'Rasa & Cerita';
$isLoggedIn = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;

// Tangkap data yang dikirim dari form reservasi (Metode POST)
$restoran      = isset($_POST['restoran']) ? $_POST['restoran'] : 'A';
$nama_pemesan  = isset($_POST['nama_pemesan']) ? $_POST['nama_pemesan'] : (isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Tamu');
$acara         = isset($_POST['acara']) && !empty($_POST['acara']) ? $_POST['acara'] : 'Reguler / Bersantap';
$tanggal       = isset($_POST['tanggal']) ? $_POST['tanggal'] : date('Y-m-d');
$waktu         = isset($_POST['waktu']) ? $_POST['waktu'] : '17:00';
$jumlah_tamu   = isset($_POST['jumlah_tamu']) ? $_POST['jumlah_tamu'] : '1';
$catatan       = isset($_POST['catatan']) ? $_POST['catatan'] : '-';
$area          = isset($_POST['area']) ? $_POST['area'] : 'indoor';

// Mapping nama restoran agar tampil lebih jelas
$daftar_resto = [
    'A' => 'Restoran A - Cabang Utama',
    'B' => 'Restoran B',
    'C' => 'Restoran C',
    'D' => 'Restoran D'
];
$nama_restoran_lengkap = isset($daftar_resto[$restoran]) ? $daftar_resto[$restoran] : 'Restoran A';
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- Sidebar Menu Navigasi Kiri -->
        <div class="lg:col-span-1 space-y-3">
            <div class="bg-white/80 border border-[#eadfd4] rounded-2xl p-4 shadow-sm space-y-2">
                <a href="<?= route('dashboard') ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-[#5e392e] hover:bg-[#efebe4] font-medium text-sm transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Preview Restoran
                </a>
                <a href="<?= route('reservasi') ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-[#5e392e] text-white font-medium text-sm transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Reservasi Restoran
                </a>
                <a href="<?= route('menu') ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-[#5e392e] hover:bg-[#efebe4] font-medium text-sm transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    Menu
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-3">
            <div class="bg-white/90 border border-[#eadfd4] rounded-3xl p-8 md:p-10 shadow-sm backdrop-blur text-center space-y-6">
                
                <!-- Icon Sukses -->
                <div class="w-16 h-16 bg-[#efebe4] text-[#5e392e] rounded-full flex items-center justify-center mx-auto shadow-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>

                <!-- Header Informasi Sukses -->
                <div>
                    <span class="eyebrow">Berhasil Dipesan</span>
                    <h1 class="mt-2 font-display text-2xl md:text-3xl font-bold text-[#201913]">Reservasi Anda Telah Diterima!</h1>
                    <p class="mt-1 text-sm text-[#66574b]">Terima kasih, <strong class="text-[#201913]"><?= e($nama_pemesan) ?></strong>. Meja Anda di <?= e($restaurantName) ?> berhasil dicatat.</p>
                </div>

                <!-- Detail Ringkasan Reservasi -->
                <div class="bg-[#f9f6f0] border border-[#eadfd4] rounded-2xl p-6 text-left space-y-4 text-sm text-[#3b3028]">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-b border-[#eadfd4] pb-4">
                        <div>
                            <span class="block text-xs uppercase tracking-[0.15em] font-bold text-[#8a5d49] mb-1">Restoran Tujuan</span>
                            <span class="font-medium text-[#201913]"><?= e($nama_restoran_lengkap) ?></span>
                        </div>
                        <div>
                            <span class="block text-xs uppercase tracking-[0.15em] font-bold text-[#8a5d49] mb-1">Jenis Acara</span>
                            <span class="font-medium text-[#201913]"><?= e($acara) ?></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 border-b border-[#eadfd4] pb-4">
                        <div>
                            <span class="block text-xs uppercase tracking-[0.15em] font-bold text-[#8a5d49] mb-1">Tanggal</span>
                            <span class="font-medium text-[#201913]"><?= e($tanggal) ?></span>
                        </div>
                        <div>
                            <span class="block text-xs uppercase tracking-[0.15em] font-bold text-[#8a5d49] mb-1">Waktu</span>
                            <span class="font-medium text-[#201913]"><?= e($waktu) ?> WIB</span>
                        </div>
                        <div>
                            <span class="block text-xs uppercase tracking-[0.15em] font-bold text-[#8a5d49] mb-1">Jumlah Tamu</span>
                            <span class="font-medium text-[#201913]"><?= e($jumlah_tamu) ?> Orang</span>
                        </div>
                    </div>

                    <div>
                        <span class="block text-xs uppercase tracking-[0.15em] font-bold text-[#8a5d49] mb-1">Area Duduk & Catatan</span>
                        <span class="font-medium text-[#201913] uppercase">[<?= e($area) ?>]</span>
                        <span class="text-xs text-[#66574b] block mt-0.5"><?= e($catatan) ?></span>
                    </div>
                </div>

                <div class="pt-4 flex flex-wrap gap-4 justify-center">
                    <a href="<?= route('home') ?>" class="btn-primary">Kembali ke Beranda</a>
                    <a href="<?= route('reservasi') ?>" class="btn-secondary">Reservasi Lagi</a>
                </div>

            </div>
        </div>

    </div>
</div>
