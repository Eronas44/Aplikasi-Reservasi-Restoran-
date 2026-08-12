<?php
// pages/reservasi.php — Langkah 1: Pilih Tanggal & Waktu Kunjungan
// Melakukan pencarian meja otomatis ke backend (GET /tables/available).

if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] !== true) {
    header('Location: ' . route('login'));
    exit;
}

require_once __DIR__ . '/../src/config/api.config.php';
require_once __DIR__ . '/../src/utils/api.php';

// Resolve restoran (int id dari database)
$restoId = (int) ($_GET['resto'] ?? $_SESSION['current_reservation']['resto'] ?? 0);
if ($restoId <= 0) {
    $list = api_get(API_RESTAURANTS . '?limit=1');
    $raw = $list['data']['data'] ?? [];
    $first = ($raw['data'] ?? $raw)[0] ?? null;
    $restoId = (int) ($first['restaurant_id'] ?? 1);
}

// Nama restoran dari database
$restoNama = 'Restoran';
$detail = api_get(API_RESTAURANTS . '/' . $restoId);
if ($detail['ok']) {
    $restoNama = $detail['data']['data']['name'] ?? $restoNama;
}

// Daftar restoran untuk dropdown pilihan
$restoList = [];
$restoListResult = api_get(API_RESTAURANTS . '?limit=100');
if ($restoListResult['ok']) {
    $raw = $restoListResult['data']['data'] ?? [];
    $restoList = $raw['data'] ?? (is_array($raw) && isset($raw[0]) ? $raw : []);
}

$error = '';
$mejaDitemukan = null;

// Proses submit form pencarian meja
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payload = [
        'resto'        => (int) ($_POST['resto'] ?? $restoId),
        'nama'         => $_POST['nama'] ?? ($_SESSION['user_name'] ?? ''),
        'acara'        => !empty($_POST['acara']) ? $_POST['acara'] : '-',
        'tanggal'      => $_POST['tanggal'] ?? '',
        'waktu'        => $_POST['waktu'] ?? '',
        'jumlah_tamu'  => (int) ($_POST['jumlah_tamu'] ?? 1),
        'catatan'      => !empty($_POST['catatan']) ? $_POST['catatan'] : '-',
        'area'         => $_POST['area'] ?? 'indoor',
        'resto_nama'   => $restoNama,
    ];

    // Validasi sederhana
    $today = date('Y-m-d');
    if (empty($payload['tanggal']) || $payload['tanggal'] < $today) {
        $error = 'Tanggal kunjungan harus di masa mendatang atau hari ini.';
    } elseif (!in_array($payload['area'], ['indoor', 'outdoor', 'smoking', 'vip'], true)) {
        $payload['area'] = 'indoor';
    }

    if ($error === '') {
        // Simpan draft ke session
        $_SESSION['current_reservation'] = $payload;
        unset($_SESSION['selected_table_id'], $_SESSION['cart']);

        // Sistem mencari meja yang tersedia (alur flowchart)
        $q = http_build_query([
            'restaurant_id' => $payload['resto'],
            'date'          => $payload['tanggal'],
            'time'          => $payload['waktu'],
            'guests'        => $payload['jumlah_tamu'],
            'area'          => $payload['area'],
        ]);
        $avail = api_get(API_TABLES . '/available?' . $q);

        if ($avail['ok']) {
            $raw = $avail['data']['data'] ?? [];
            $mejaDitemukan = $raw['data'] ?? $raw;
            if (is_array($mejaDitemukan) && count($mejaDitemukan) > 0) {
                header('Location: ' . route('pilih_meja'));
                exit;
            }
        }

        $error = 'Meja Tidak Tersedia. Tidak ada meja yang cocok dengan tanggal, jam, jumlah tamu, dan area yang dipilih. Silakan ubah pencarian Anda.';
    }
}

$draft = $_SESSION['current_reservation'] ?? [];
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-10">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white/90 border border-[#eadfd4] rounded-3xl p-8 md:p-10 shadow-sm space-y-8">

            <?php $step = 1; include __DIR__ . '/../components/reservation-stepper.php'; ?>

            <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-[#eadfd4] pb-6 gap-4">
                    <div>
                        <span class="text-xs uppercase tracking-widest text-[#8a5d49] font-bold">Langkah 1 dari 4</span>
                        <h1 class="font-display text-3xl font-bold text-[#201913] mt-1">Pilih Tanggal & Waktu Kunjungan</h1>
                        <p class="text-sm text-[#66574b] mt-1">Restoran: <strong class="text-[#201913]"><?= e($restoNama) ?></strong></p>
                    </div>
                    <a href="<?= route('preview_restoran') ?>" class="text-xs font-bold text-[#8a5d49] hover:underline">Ganti Restoran →</a>
                </div>

                <!-- Pilih Restoran (ganti langsung, konteks diteruskan ke halaman menu) -->
                <form method="GET" action="index.php" onchange="this.submit()" class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-5 flex flex-col md:flex-row md:items-end gap-4">
                    <input type="hidden" name="page" value="reservasi">
                    <div class="flex-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Pilih Restoran</label>
                        <select name="resto" class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
                            <?php foreach ($restoList as $r): ?>
                                <option value="<?= (int) ($r['restaurant_id'] ?? 0) ?>" <?= (int) ($r['restaurant_id'] ?? 0) === $restoId ? 'selected' : '' ?>>
                                    <?= e($r['name'] ?? 'Restoran') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <span class="text-xs text-[#66574b] md:mb-2">Menu &amp; meja yang ditampilkan mengikuti restoran yang dipilih.</span>
                </form>

                <?php if ($error !== ''): ?>
                    <div class="p-5 rounded-2xl bg-orange-50 border border-orange-200 text-orange-800 text-sm">
                        <strong>⚠ Meja Tidak Tersedia.</strong> <?= e($error) ?>
                    </div>
                <?php endif; ?>

                <!-- Form Pencarian Meja -->
                <form action="<?= route('reservasi', ['resto' => $restoId]) ?>" method="POST" class="space-y-6">
                    <input type="hidden" name="resto" value="<?= (int) $restoId ?>">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Nama Pemesan</label>
                            <input type="text" name="nama" required placeholder="Masukkan nama Anda"
                                   value="<?= e($draft['nama'] ?? $_SESSION['user_name'] ?? '') ?>"
                                   class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Acara (Opsional)</label>
                            <input type="text" name="acara" placeholder="Ulang Tahun, Rapat, Santap Keluarga..."
                                   value="<?= e($draft['acara'] ?? '') ?>"
                                   class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Tanggal Kunjungan</label>
                            <input type="date" name="tanggal" required min="<?= date('Y-m-d') ?>"
                                   value="<?= e($draft['tanggal'] ?? '') ?>"
                                   class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Waktu Kunjungan</label>
                            <select name="waktu" required class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
                                <option value="">Pilih Jam Kedatangan</option>
                                <?php foreach (['11:00', '12:00', '13:00', '14:00', '15:00', '17:00', '18:00', '19:00', '20:00'] as $jam): ?>
                                    <option value="<?= $jam ?>" <?= ($draft['waktu'] ?? '') === $jam ? 'selected' : '' ?>><?= $jam ?> WIB</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Jumlah Tamu (Orang)</label>
                            <input type="number" name="jumlah_tamu" required min="1" max="50" placeholder="Contoh: 4"
                                   value="<?= e($draft['jumlah_tamu'] ?? '2') ?>"
                                   class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Catatan Khusus</label>
                            <input type="text" name="catatan" placeholder="Misal: Perlu baby chair, alergi kacang..."
                                   value="<?= e($draft['catatan'] ?? '') ?>"
                                   class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
                        </div>

                    </div>

                    <!-- Preferensi Area -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-3">Preferensi Area Tempat Duduk</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <?php
                            $areas = [
                                'indoor'  => ['Indoor AC', 'Area bebas asap rokok'],
                                'outdoor' => ['Outdoor / Semi-Outdoor', 'Area dengan pemandangan taman'],
                                'vip'     => ['VIP / Private Room', 'Ruangan privat eksklusif'],
                                'smoking' => ['Smoking Area', 'Area untuk perokok'],
                            ];
                            $selectedArea = $draft['area'] ?? 'indoor';
                            foreach ($areas as $key => [$judul, $desc]):
                            ?>
                                <label class="relative flex items-center p-4 rounded-xl border border-[#eadfd4] bg-white cursor-pointer hover:border-[#8a5d49] transition">
                                    <input type="radio" name="area" value="<?= $key ?>" <?= $selectedArea === $key ? 'checked' : '' ?> class="text-[#8a5d49] focus:ring-[#8a5d49]">
                                    <div class="ml-3">
                                        <span class="block text-sm font-bold text-[#201913]"><?= e($judul) ?></span>
                                        <span class="text-xs font-bold text-[#5e392e]"><?= e($desc) ?></span>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="pt-4 flex items-center justify-end gap-3 border-t border-[#eadfd4]">
                        <a href="<?= route('menu', ['resto' => $restoId]) ?>" class="px-5 py-2.5 rounded-xl border border-[#eadfd4] text-stone-600 hover:bg-stone-50 text-xs font-bold transition">
                            Lihat Menu Dulu
                        </a>
                        <button type="submit" class="bg-[#5e392e] hover:bg-[#4a2c24] text-white text-xs font-bold py-2.5 px-6 rounded-xl transition shadow-sm">
                            Cari Meja Tersedia →
                        </button>
                    </div>

                </form>
            </div>
        </div>
</div>