<?php
// pages/checkin.php — Validasi Kedatangan Tamu (Scan QR / Input Kode Booking)
// Terhubung ke backend: GET /reservations (cari kode), POST /reservations/{id}/check-in

$isLoggedIn = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
$role = $_SESSION['role'] ?? 'customer';
if (!$isLoggedIn) {
    header('Location: ' . route('login'));
    exit;
}
if (!in_array($role, ['staff', 'admin'], true)) {
    header('Location: ' . route('dashboard'));
    exit;
}

require_once __DIR__ . '/../src/config/api.config.php';
require_once __DIR__ . '/../src/utils/api.php';

$resultMessage = '';
$resultType    = ''; // success | error
$scannedData   = null;

$inputKode = trim($_POST['kode_booking'] ?? ($_GET['kode'] ?? ''));

if (($inputKode !== '') && ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['kode']))) {
    $found = null;
    $resResult = api_get(API_RESERVATIONS . '?limit=200');
    if ($resResult['ok']) {
        $raw = $resResult['data']['data'] ?? [];
        foreach (($raw['data'] ?? $raw) as $r) {
            if (strcasecmp((string) ($r['booking_code'] ?? ''), $inputKode) === 0) {
                $found = $r;
                break;
            }
        }
    } else {
        // Fallback demo bila backend tidak tersedia
        $demo = [
            ['booking_code' => 'KB-000005', 'user' => ['name' => 'Rina Kartika'], 'reservation_date' => date('Y-m-d'), 'reservation_time' => '18:00'],
            ['booking_code' => 'KB-000007', 'user' => ['name' => 'Bima Pratama'], 'reservation_date' => date('Y-m-d'), 'reservation_time' => '19:00'],
        ];
        foreach ($demo as $d) {
            if (strcasecmp($d['booking_code'], $inputKode) === 0) {
                $found = $d;
                break;
            }
        }
    }

    if ($found !== null && ($found['reservation_id'] ?? 0) > 0) {
        $action = isset($_POST['aksi']) && $_POST['aksi'] === 'no_show' ? 'no_show' : 'completed';
        $result = api_request('POST', API_RESERVATIONS . '/' . (int) $found['reservation_id'] . '/check-in', ['status' => $action]);

        if ($result['ok']) {
            $resultType = 'success';
            $resultMessage = $action === 'no_show'
                ? 'Tamu tidak hadir (no-show). Reservasi dibatalkan dan meja dilepas, deposit dikembalikan (sesuai aturan refund).'
                : 'Kode valid! Tamu berhasil di-check-in. Status reservasi = "Tamu Hadir", Status meja = "Terisi".';
            $scannedData = [
                'kode'   => strtoupper($found['booking_code']),
                'nama'   => $found['user']['name'] ?? 'Tamu',
                'tanggal' => $found['reservation_date'] ?? date('Y-m-d'),
                'waktu'  => $found['reservation_time'] ?? date('H:i'),
            ];
        } else {
            $resultType = 'error';
            $resultMessage = api_error_message($result, 'Gagal memproses check-in.');
        }
    } elseif ($found !== null) {
        // Backend tidak tersedia / data demo -> simulasi sukses
        $resultType = 'success';
        $resultMessage = 'Kode valid! Tamu berhasil di-check-in. (Mode demo — backend tidak terhubung.)';
        $scannedData = [
            'kode'   => strtoupper($found['booking_code']),
            'nama'   => $found['user']['name'] ?? 'Tamu',
            'tanggal' => $found['reservation_date'] ?? date('Y-m-d'),
            'waktu'  => $found['reservation_time'] ?? date('H:i'),
        ];
    } else {
        $resultType = 'error';
        $resultMessage = 'Kode booking tidak ditemukan. Periksa kembali atau tanyakan QR pada tamu.';
    }
}
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        <?php $sidebarRole = 'staff'; $sidebarActive = 'checkin'; include __DIR__ . '/../components/dashboard-sidebar.php'; ?>

        <div class="lg:col-span-3">
            <div class="bg-white/90 border border-[#eadfd4] rounded-3xl p-8 md:p-10 shadow-sm space-y-8">

                <div class="border-b border-[#eadfd4] pb-6">
                    <span class="text-xs uppercase tracking-widest text-[#8a5d49] font-bold">Validasi Kedatangan</span>
                    <h1 class="font-display text-3xl font-bold text-[#201913] mt-1">Check-in Tamu</h1>
                    <p class="text-sm text-[#66574b] mt-1">Scan QR atau masukkan kode booking untuk memvalidasi kedatangan tamu.</p>
                </div>

                <?php if ($resultMessage !== ''): ?>
                    <div class="p-5 rounded-2xl <?= $resultType === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-700' ?> border text-sm">
                        <?= e($resultMessage) ?>
                        <?php if ($scannedData): ?>
                            <ul class="mt-3 space-y-1 text-xs">
                                <li><strong>Kode:</strong> <?= e($scannedData['kode']) ?></li>
                                <li><strong>Nama:</strong> <?= e($scannedData['nama']) ?></li>
                                <li><strong>Tanggal:</strong> <?= e($scannedData['tanggal']) ?> &middot; <strong>Waktu:</strong> <?= e($scannedData['waktu']) ?></li>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                    <!-- Input Manual -->
                    <div class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-6 space-y-4">
                        <h2 class="font-display text-lg font-bold text-[#201913]">Input Kode Booking</h2>
                        <form action="<?= route('checkin') ?>" method="POST" class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Kode Booking</label>
                                <input type="text" name="kode_booking" required value="<?= e($inputKode) ?>"
                                       placeholder="KB-XXXXXX"
                                       class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Nama Tamu</label>
                                <input type="text" name="nama" placeholder="Nama pemesan"
                                       class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
                            </div>
                            <div class="flex items-center gap-6 pt-1">
                                <label class="flex items-center gap-2 text-xs font-semibold text-[#4f4338]">
                                    <input type="radio" name="aksi" value="completed" checked class="accent-[#5e392e]">
                                    Tamu Hadir
                                </label>
                                <label class="flex items-center gap-2 text-xs font-semibold text-[#4f4338]">
                                    <input type="radio" name="aksi" value="no_show" class="accent-[#b91c1c]">
                                    No-show (Tidak Hadir)
                                </label>
                            </div>
                            <button type="submit" class="w-full bg-[#5e392e] hover:bg-[#4a2c24] text-white text-xs font-bold py-3 rounded-xl transition shadow-sm">
                                Validasi & Check-in
                            </button>
                        </form>
                    </div>

                    <!-- Simulasi Scan QR -->
                    <div class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-6 space-y-4 text-center">
                        <h2 class="font-display text-lg font-bold text-[#201913]">Scan QR Code</h2>
                        <div class="mx-auto w-44 h-44 border-4 border-dashed border-[#8a5d49]/40 rounded-2xl flex items-center justify-center bg-white">
                            <svg class="w-16 h-16 text-[#8a5d49]/60" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h2v2h-2zM17 14h3v3h-3zM14 17h2v3h-2zM19 17h1v1h-1zM9 9h2v2H9zM13 9h2v2h-2z"/>
                            </svg>
                        </div>
                        <p class="text-xs text-[#66574b]">Arahkan kamera perangkat ke QR code tamu (simulasi — koneksikan dengan library scanner pada produksi).</p>
                        <a href="<?= route('checkin', ['kode' => 'KB-000005']) ?>"
                           class="inline-block px-5 py-2.5 rounded-xl border border-[#eadfd4] text-stone-600 hover:bg-stone-50 text-xs font-bold transition">
                            Simulasikan Scan
                        </a>
                    </div>

                </div>

                <div class="flex flex-wrap gap-3 pt-2 border-t border-[#eadfd4]">
                    <a href="<?= route('jadwal_hari_ini') ?>" class="px-5 py-2.5 rounded-xl border border-[#eadfd4] text-stone-600 hover:bg-stone-50 text-xs font-bold transition">
                        ← Kembali ke Jadwal
                    </a>
                    <a href="<?= route('denah_meja') ?>" class="bg-[#5e392e] hover:bg-[#4a2c24] text-white text-xs font-bold py-2.5 px-6 rounded-xl transition shadow-sm">
                        Lihat Denah Meja
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
