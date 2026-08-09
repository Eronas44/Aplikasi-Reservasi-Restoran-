<?php
// pages/reservasi.php — Halaman Form & Detail Reservasi dengan Session Storage

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
    
    header("Location: " . route('reservasi', ['resto' => $_SESSION['current_reservation']['resto']]));
    exit;
}

// Periksa apakah sudah ada data reservasi aktif di session
$has_reservation = isset($_SESSION['current_reservation']);
$data_reservasi = $has_reservation ? $_SESSION['current_reservation'] : null;
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- Sidebar Navigasi Kiri -->
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
                        <h1 class="font-display text-3xl font-bold text-[#201913]">Reservasi Berhasil Terjadwal!</h1>
                        <p class="text-sm text-[#66574b]">Terima kasih telah memesan meja di Kafiber Restoran. Berikut detail reservasi Anda:</p>
                    </div>

                    <!-- Kartu Detail Tiket Reservasi -->
                    <div class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-6 md:p-8 space-y-6 shadow-inner">
                        <div class="flex items-center justify-between border-b border-[#eadfd4] pb-4">
                            <div>
                                <span class="text-xs font-bold uppercase tracking-wider text-[#8a5d49]">Restoran Pilihan</span>
                                <h3 class="font-display text-xl font-bold text-[#201913] mt-0.5">
                                    <?= e($daftar_resto[$data_reservasi['resto']] ?? $data_reservasi['resto']) ?>
                                </h3>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                Konfirmasi Aktif
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <span class="text-xs font-semibold text-[#8a5d49]">Nama Pemesan</span>
                                <p class="text-base font-bold text-[#201913] mt-1"><?= e($data_reservasi['nama']) ?></p>
                            </div>
                            <div>
                                <span class="text-xs font-semibold text-[#8a5d49]">Acara / Acara Khusus</span>
                                <p class="text-base font-bold text-[#201913] mt-1"><?= e($data_reservasi['acara']) ?></p>
                            </div>
                            <div>
                                <span class="text-xs font-semibold text-[#8a5d49]">Tanggal Reservasi</span>
                                <p class="text-base font-bold text-[#201913] mt-1"><?= e($data_reservasi['tanggal']) ?></p>
                            </div>
                            <div>
                                <span class="text-xs font-semibold text-[#8a5d49]">Waktu Kedatangan</span>
                                <p class="text-base font-bold text-[#201913] mt-1"><?= e($data_reservasi['waktu']) ?> WIB</p>
                            </div>
                            <div>
                                <span class="text-xs font-semibold text-[#8a5d49]">Jumlah Tamu</span>
                                <p class="text-base font-bold text-[#201913] mt-1"><?= e($data_reservasi['jumlah_tamu']) ?> Orang</p>
                            </div>
                            <div>
                                <span class="text-xs font-semibold text-[#8a5d49]">Pilihan Area Meja</span>
                                <p class="text-base font-bold text-[#201913] mt-1 uppercase"><?= e($data_reservasi['area']) ?></p>
                            </div>
                        </div>

                        <?php if (!empty($data_reservasi['catatan']) && $data_reservasi['catatan'] !== '-'): ?>
                            <div class="border-t border-[#eadfd4] pt-4">
                                <span class="text-xs font-semibold text-[#8a5d49]">Catatan Tambahan</span>
                                <p class="text-sm text-[#4f4338] italic mt-1">"<?= e($data_reservasi['catatan']) ?>"</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex flex-wrap items-center justify-between gap-4 pt-4">
                        <a href="<?= route('reservasi', ['resto' => $selected_resto, 'action' => 'reset']) ?>" class="px-5 py-2.5 rounded-xl border border-[#8a5d49] text-[#8a5d49] hover:bg-[#8a5d49] hover:text-white text-xs font-bold transition">
                            + Buat Reservasi Baru
                        </a>
                        <a href="<?= route('menu', ['resto' => $selected_resto]) ?>" class="bg-[#5e392e] hover:bg-[#4a2c24] text-white text-xs font-bold py-2.5 px-6 rounded-xl transition shadow-sm">
                            Pilih Menu Makanan →
                        </a>
                    </div>

                <?php else: ?>
                    <!-- KONDISI: FORMULIR ISIAN RESERVASI -->
                    <div class="border-b border-[#eadfd4] pb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <span class="text-xs uppercase tracking-widest text-[#8a5d49] font-bold">Formulir Pendaftaran Meja</span>
                            <h1 class="font-display text-3xl font-bold text-[#201913] mt-1">Reservasi Restoran</h1>
                        </div>
                        
                        <!-- Pilihan Restoran -->
                        <div class="flex items-center gap-2 bg-[#efebe4] p-1.5 rounded-2xl border border-[#eadfd4]">
                            <a href="<?= route('reservasi', ['resto' => 'A']) ?>" class="px-4 py-2 rounded-xl text-xs font-bold transition <?= $selected_resto === 'A' ? 'bg-[#5e392e] text-white shadow-sm' : 'text-[#5e392e] hover:bg-[#e2dcd2]' ?>">
                                Resto A
                            </a>
                            <a href="<?= route('reservasi', ['resto' => 'B']) ?>" class="px-4 py-2 rounded-xl text-xs font-bold transition <?= $selected_resto === 'B' ? 'bg-[#5e392e] text-white shadow-sm' : 'text-[#5e392e] hover:bg-[#e2dcd2]' ?>">
                                Resto B
                            </a>
                        </div>
                    </div>

                    <!-- Form Isian -->
                    <form action="<?= route('reservasi', ['resto' => $selected_resto]) ?>" method="POST" class="space-y-6">
                        <input type="hidden" name="resto" value="<?= e($selected_resto) ?>">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <!-- Nama Pemesan -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Nama Pemesan</label>
                                <input type="text" name="nama" required placeholder="Masukkan nama Anda" value="<?= e($_SESSION['user_name'] ?? '') ?>"
                                       class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
                            </div>

                            <!-- Acara -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Acara (Opsional)</label>
                                <input type="text" name="acara" placeholder="Ulang Tahun, Rapat, Santap Keluarga..."
                                       class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
                            </div>

                            <!-- Tanggal -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Tanggal Reservasi</label>
                                <input type="date" name="tanggal" required min="<?= date('Y-m-d') ?>"
                                       class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
                            </div>

                            <!-- Waktu -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Waktu Kedatangan</label>
                                <select name="waktu" required class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
                                    <option value="">Pilih Jam Kedatangan</option>
                                    <option value="11:00">11:00 WIB (Makan Siang)</option>
                                    <option value="13:00">13:00 WIB</option>
                                    <option value="15:00">15:00 WIB</option>
                                    <option value="18:00">18:00 WIB (Makan Malam)</option>
                                    <option value="20:00">20:00 WIB</option>
                                </select>
                            </div>

                            <!-- Jumlah Tamu -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Jumlah Tamu (Orang)</label>
                                <input type="number" name="jumlah_tamu" required min="1" max="50" placeholder="Contoh: 4"
                                       class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
                            </div>

                            <!-- Catatan -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Catatan Khusus</label>
                                <input type="text" name="catatan" placeholder="Misal: Perlu baby chair, alergi kacang..."
                                       class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
                            </div>

                        </div>

                        <!-- Pilihan Area -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-3">Pilihan Area Tempat Duduk</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <label class="relative flex items-center p-4 rounded-xl border border-[#eadfd4] bg-white cursor-pointer hover:border-[#8a5d49] transition">
                                    <input type="radio" name="area" value="indoor" checked class="text-[#8a5d49] focus:ring-[#8a5d49]">
                                    <div class="ml-3">
                                        <span class="block text-sm font-bold text-[#201913]">Indoor AC</span>
                                        <span class="text-xs font-bold text-[#5e392e]">Area Bebas Asap Rokok</span>
                                    </div>
                                </label>

                                <label class="relative flex items-center p-4 rounded-xl border border-[#eadfd4] bg-white cursor-pointer hover:border-[#8a5d49] transition">
                                    <input type="radio" name="area" value="outdoor" class="text-[#8a5d49] focus:ring-[#8a5d49]">
                                    <div class="ml-3">
                                        <span class="block text-sm font-bold text-[#201913]">Outdoor / Semi-Outdoor</span>
                                        <span class="text-xs font-bold text-[#5e392e]">Area Outdoor Pemandangan Taman</span>
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
