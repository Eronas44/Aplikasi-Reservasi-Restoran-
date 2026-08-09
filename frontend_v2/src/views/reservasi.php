<?php
// reservasi.php — Halaman Form & Detail Reservasi dengan Session Storage
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}

// 1. Tangkap restoran yang aktif dari URL
$selected_resto = isset($_GET['resto']) ? strtoupper($_GET['resto']) : 'A';
if (!in_array($selected_resto, ['A', 'B'])) {
    $selected_resto = 'A';
}

// 2. Mapping nama restoran lengkap
$daftar_resto = [
    'A' => 'RESTO A - Cabang Utama',
    'B' => 'RESTO B - Cabang Boulevard'
];

// Cek jika user ingin membuat reservasi baru (Reset session)
if (isset($_GET['action']) && $_GET['action'] === 'reset') {
    unset($_SESSION['current_reservation']);
    header("Location: " . route('reservasi', ['resto' => $selected_resto]));
    exit;
}

// 3. Cek apakah form dikirim via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['current_reservation'] = [
        'resto'       => $_POST['resto'] ?? $selected_resto,
        'nama'        => $_POST['nama'] ?? '',
        'acara'       => $_POST['acara'] ?? '-',
        'tanggal'     => $_POST['tanggal'] ?? '',
        'waktu'       => $_POST['waktu'] ?? '',
        'jumlah_tamu' => $_POST['jumlah_tamu'] ?? '',
        'catatan'     => $_POST['catatan'] ?? '-',
        'area'        => $_POST['area'] ?? 'indoor'
    ];
    
    // Redirect menggunakan PRG (Post-Redirect-Get) pattern
    header("Location: " . route('reservasi', ['resto' => $_SESSION['current_reservation']['resto']]));
    exit;
}

// Periksa apakah sudah ada data reservasi aktif di session
$has_reservation = isset($_SESSION['current_reservation']);
$data_reservasi = $has_reservation ? $_SESSION['current_reservation'] : null;

include LAYOUTS_PATH . '/header.php';
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- Sidebar Navigasi Kiri (Lengkap dengan Ikon SVG) -->
        <div class="lg:col-span-1 space-y-3">
            <div class="bg-white/80 border border-[#eadfd4] rounded-2xl p-4 shadow-sm space-y-2">
                
                <!-- Pilih Restoran -->
                <a href="<?= route('dashboard') ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-[#5e392e] hover:bg-[#efebe4] font-medium text-sm transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    Pilih Restoran
                </a>

                <!-- Reservasi Restoran -->
                <a href="<?= route('reservasi', ['resto' => $selected_resto]) ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-[#5e392e] text-white font-medium text-sm transition shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Reservasi Restoran
                </a>

                <!-- Menu -->
                <a href="<?= route('menu', ['resto' => $selected_resto]) ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-[#5e392e] hover:bg-[#efebe4] font-medium text-sm transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    Menu
                </a>

            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-3">
            <div class="bg-white/90 border border-[#eadfd4] rounded-3xl p-8 md:p-10 shadow-sm backdrop-blur space-y-8">
                
                <?php if ($has_reservation): ?>
                    <!-- KONDISI: TAMPILAN DETAIL RESERVASI BERHASIL -->
                    <div class="text-center space-y-3 border-b border-[#eadfd4] pb-6">
                        <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-green-100 text-green-700 mb-2">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h1 class="font-display text-2xl md:text-3xl font-bold text-[#201913] uppercase tracking-wide">Reservasi Berhasil!</h1>
                        <p class="text-sm text-stone-600">Terima kasih, berikut adalah detail reservasi Anda:</p>
                    </div>

                    <div class="bg-[#f9f6f0] border border-[#eadfd4] rounded-2xl p-6 space-y-4 text-sm text-[#201913]">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <span class="block text-xs font-bold uppercase tracking-wider text-stone-500">Restoran</span>
                                <span class="font-semibold text-[#5e392e]"><?= e($daftar_resto[$data_reservasi['resto']] ?? $data_reservasi['resto']) ?></span>
                            </div>
                            <div>
                                <span class="block text-xs font-bold uppercase tracking-wider text-stone-500">Nama Pemesan</span>
                                <span class="font-semibold"><?= e($data_reservasi['nama']) ?></span>
                            </div>
                            <div>
                                <span class="block text-xs font-bold uppercase tracking-wider text-stone-500">Acara</span>
                                <span class="font-semibold"><?= e($data_reservasi['acara']) ?></span>
                            </div>
                            <div>
                                <span class="block text-xs font-bold uppercase tracking-wider text-stone-500">Tanggal & Waktu</span>
                                <span class="font-semibold"><?= e($data_reservasi['tanggal']) ?> pukul <?= e($data_reservasi['waktu']) ?></span>
                            </div>
                            <div>
                                <span class="block text-xs font-bold uppercase tracking-wider text-stone-500">Jumlah Tamu</span>
                                <span class="font-semibold"><?= e($data_reservasi['jumlah_tamu']) ?> Orang</span>
                            </div>
                            <div>
                                <span class="block text-xs font-bold uppercase tracking-wider text-stone-500">Area</span>
                                <span class="font-semibold uppercase"><?= e($data_reservasi['area']) ?></span>
                            </div>
                        </div>

                        <?php if (!empty($data_reservasi['catatan'])): ?>
                            <div class="pt-2 border-t border-[#eadfd4]">
                                <span class="block text-xs font-bold uppercase tracking-wider text-stone-500">Catatan</span>
                                <span class="text-stone-700"><?= e($data_reservasi['catatan']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="pt-4 flex items-center justify-between border-t border-[#eadfd4]">
                        <a href="<?= route('reservasi', ['resto' => $selected_resto, 'action' => 'reset']) ?>" class="px-5 py-2.5 rounded-xl border border-[#eadfd4] text-stone-600 hover:bg-stone-50 text-xs font-bold transition">
                            Buat Reservasi Baru
                        </a>
                        <a href="<?= route('menu', ['resto' => $selected_resto]) ?>" class="bg-[#5e392e] hover:bg-[#4a2c24] text-white text-xs font-bold py-2.5 px-6 rounded-xl transition shadow-sm">
                            Lihat Menu Restoran
                        </a>
                    </div>

                <?php else: ?>
                    <!-- KONDISI: FORM INPUT AWAL -->
                    <div class="border-b border-[#eadfd4] pb-6 text-center">
                        <h1 class="font-display text-2xl md:text-3xl font-bold text-[#201913] uppercase tracking-wide">Reservasi Restoran</h1>
                    </div>

                    <form action="<?= route('reservasi', ['resto' => $selected_resto]) ?>" method="POST" class="space-y-6">
                        
                        <input type="hidden" name="resto" value="<?= e($selected_resto) ?>">

                        <!-- 1. Pilih Restoran -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#5e392e]">Pilih Restoran</label>
                            <div class="relative">
                                <select name="resto" required class="w-full bg-[#f9f6f0] border border-[#eadfd4] rounded-xl px-4 py-3 text-sm text-[#201913] focus:outline-none focus:border-[#5e392e] appearance-none cursor-pointer">
                                    <option value="A" <?= $selected_resto === 'A' ? 'selected' : '' ?>>RESTO A - Cabang Utama</option>
                                    <option value="B" <?= $selected_resto === 'B' ? 'selected' : '' ?>>RESTO B - Cabang Boulevard</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-stone-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Nama Pemesan -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#5e392e]">Nama Pemesan</label>
                            <input type="text" name="nama" required class="w-full bg-[#f9f6f0] border border-[#eadfd4] rounded-xl px-4 py-3 text-sm text-[#201913] placeholder-stone-400 focus:outline-none focus:border-[#5e392e]" placeholder="Nama Pemesan">
                        </div>

                        <!-- 3. Acara -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#5e392e]">Acara</label>
                            <input type="text" name="acara" class="w-full bg-[#f9f6f0] border border-[#eadfd4] rounded-xl px-4 py-3 text-sm text-[#201913] placeholder-stone-400 focus:outline-none focus:border-[#5e392e]" placeholder="Birthday, meeting, DLL">
                        </div>

                        <!-- 4. Tanggal & Waktu -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold uppercase tracking-wider text-[#5e392e]">Tanggal</label>
                                <input type="date" name="tanggal" required class="w-full bg-[#f9f6f0] border border-[#eadfd4] rounded-xl px-4 py-3 text-sm text-[#201913] focus:outline-none focus:border-[#5e392e]">
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold uppercase tracking-wider text-[#5e392e]">Waktu</label>
                                <input type="time" name="waktu" required class="w-full bg-[#f9f6f0] border border-[#eadfd4] rounded-xl px-4 py-3 text-sm text-[#201913] focus:outline-none focus:border-[#5e392e]">
                            </div>
                        </div>

                        <!-- 5. Jumlah Tamu -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#5e392e]">Jumlah Tamu</label>
                            <select name="jumlah_tamu" class="w-full bg-[#f9f6f0] border border-[#eadfd4] rounded-xl px-4 py-3 text-sm text-[#201913] focus:outline-none focus:border-[#5e392e]">
                                <option value="1">1 Orang (Single)</option>
                                <option value="2">2 Orang (Couple)</option>
                                <option value="4">4 Orang (Family Small)</option>
                                <option value="6">6+ Orang (Group)</option>
                            </select>
                        </div>

                        <!-- 6. Catatan -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#5e392e]">Catatan</label>
                            <textarea name="catatan" rows="2" class="w-full bg-[#f9f6f0] border border-[#eadfd4] rounded-xl px-4 py-3 text-sm text-[#201913] placeholder-stone-400 focus:outline-none focus:border-[#5e392e]" placeholder="Catatan tambahan (opsional)"></textarea>
                        </div>

                        <!-- 7. Pilih Area -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#5e392e]">Pilih Area :</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <label class="cursor-pointer relative block">
                                    <input type="radio" name="area" value="indoor" class="peer sr-only" checked>
                                    <div class="bg-[#f9f6f0] border-2 border-[#eadfd4] peer-checked:border-[#5e392e] peer-checked:bg-[#efebe4] rounded-2xl p-3 text-center transition flex flex-col items-center justify-center space-y-2 overflow-hidden">
                                        <div class="w-full h-28 rounded-xl overflow-hidden bg-stone-200 border border-[#eadfd4]">
                                            <img src="img/indoroutdor/indor.jpg" alt="Area Indoor" class="w-full h-full object-cover">
                                        </div>
                                        <span class="text-xs font-bold text-[#5e392e]">Area Indoor</span>
                                    </div>
                                </label>

                                <label class="cursor-pointer relative block">
                                    <input type="radio" name="area" value="outdoor" class="peer sr-only">
                                    <div class="bg-[#f9f6f0] border-2 border-[#eadfd4] peer-checked:border-[#5e392e] peer-checked:bg-[#efebe4] rounded-2xl p-3 text-center transition flex flex-col items-center justify-center space-y-2 overflow-hidden">
                                        <div class="w-full h-28 rounded-xl overflow-hidden bg-stone-200 border border-[#eadfd4]">
                                            <img src="img/indoroutdor/outdor.jpg" alt="Area Outdoor" class="w-full h-full object-cover">
                                        </div>
                                        <span class="text-xs font-bold text-[#5e392e]">Area Outdoor</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="pt-4 flex items-center justify-end gap-3 border-t border-[#eadfd4]">
                            <a href="<?= route('menu', ['resto' => $selected_resto]) ?>" class="px-5 py-2.5 rounded-xl border border-[#eadfd4] text-stone-600 hover:bg-stone-50 text-xs font-bold transition">
                                Lihat Menu Lagi
                            </a>
                            <button type="submit" class="bg-[#5e392e] hover:bg-[#4a2c24] text-white text-xs font-bold py-2.5 px-6 rounded-xl transition shadow-sm">
                                Konfirmasi Reservasi
                            </button>
                        </div>

                    </form>
                <?php endif; ?>

            </div>
        </div>

    </div>
</div>

<?php 
include LAYOUTS_PATH . '/footer.php'; 
?>