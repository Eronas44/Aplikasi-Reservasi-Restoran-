<?php
// pages/pembayaran.php — Halaman Pembayaran / Deposit Reservasi
// Terhubung ke backend: GET /policies, GET /tables, POST /reservations, POST /payments

require_once __DIR__ . '/../src/config/api.config.php';
require_once __DIR__ . '/../src/utils/api.php';

$data_reservasi = $_SESSION['current_reservation'] ?? null;
if (!$data_reservasi) {
    header('Location: ' . route('reservasi'));
    exit;
}

$metode = [
    'bank_transfer' => 'Transfer Bank BCA',
    'ewallet'       => 'E-Wallet (OVO / GoPay / DANA)',
    'qris'          => 'QRIS',
    'cod'           => 'Bayar di Restoran (Langsung)',
];

// Metode pembayaran backend (cod -> cash)
$metodeApiMap = [
    'bank_transfer' => 'bank_transfer',
    'ewallet'       => 'ewallet',
    'qris'          => 'qris',
    'cod'           => 'cash',
];

$totalEstimasi = (int) ($data_reservasi['total_estimasi'] ?? 750000);
$jumlahTamu    = (int) ($data_reservasi['jumlah_tamu'] ?? 2);
$restaurantId  = (int) ($_SESSION['restaurant_id'] ?? 1);

// Ambil kebijakan deposit dari backend (fallback: 20% / min 50000)
$depositPercent = 20;
$depositMin     = 50000;
$policyResult = api_get(API_POLICIES);
if ($policyResult['ok']) {
    $raw = $policyResult['data']['data'] ?? [];
    $items = $raw['data'] ?? $raw;
    foreach ($items as $p) {
        if ($p['is_active'] ?? false) {
            $depositPercent = (float) ($p['deposit_percent'] ?? 20);
            $depositMin     = (int) ($p['deposit_min_amount'] ?? 50000);
            break;
        }
    }
}
$depositNominal = max($depositMin, (int) round($totalEstimasi * $depositPercent / 100));

$bayar_terpilih = $_POST['metode_bayar'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $bayar_terpilih !== '') {
    $_SESSION['current_reservation']['metode_bayar'] = $bayar_terpilih;
    $_SESSION['current_reservation']['deposit'] = (string) $depositNominal;

    // Cari meja tersedia yang muat jumlah tamu (alur flowchart: sistem cari meja)
    $tableId = null;
    $tablesResult = api_get(API_TABLES . '?limit=100');
    if ($tablesResult['ok']) {
        $raw = $tablesResult['data']['data'] ?? [];
        foreach (($raw['data'] ?? $raw) as $t) {
            if (($t['status'] ?? '') === 'available' && (int) ($t['capacity'] ?? 0) >= $jumlahTamu) {
                $tableId = (int) ($t['table_id'] ?? 0);
                break;
            }
        }
    }

    if ($tableId !== null && !empty($data_reservasi['tanggal']) && !empty($data_reservasi['waktu'])) {
        // Buat reservasi di backend
        $bookingCode = 'KB-' . strtoupper(bin2hex(random_bytes(3)));
        $waktu = date('H:i:s', strtotime($data_reservasi['waktu']));
        $payload = [
            'user_id'          => (int) ($_SESSION['user_id'] ?? 1),
            'table_id'         => $tableId,
            'booking_code'     => $bookingCode,
            'reservation_date' => $data_reservasi['tanggal'],
            'reservation_time' => $waktu,
            'number_of_guest'  => $jumlahTamu,
            'total_price'      => $totalEstimasi,
            'deposit_amount'   => $depositNominal,
            'status'           => 'confirmed',
            'special_request'  => $data_reservasi['catatan'] ?? null,
        ];
        $resResult = api_request('POST', API_RESERVATIONS, $payload);

        if ($resResult['ok'] && isset($resResult['data']['data']['reservation_id'])) {
            $reservationId = (int) $resResult['data']['data']['reservation_id'];
            // Catat pembayaran deposit
            $payResult = api_request('POST', API_PAYMENTS, [
                'reservation_id'   => $reservationId,
                'type'             => 'deposit',
                'amount'           => $depositNominal,
                'method'           => $metodeApiMap[$bayar_terpilih] ?? 'bank_transfer',
                'transaction_code' => $bookingCode . '-DP',
                'gateway'          => $bayar_terpilih === 'qris' ? 'qris' : ($bayar_terpilih === 'ewallet' ? 'ewallet' : 'bank'),
            ]);

            $_SESSION['current_reservation']['kode_booking']   = $bookingCode;
            $_SESSION['current_reservation']['reservation_id'] = $reservationId;
        } else {
            // Backend menolak -> fallback simpan kode lokal saja
            $_SESSION['current_reservation']['kode_booking']   = $bookingCode;
        }
    } else {
        // Backend tidak tersedia / meja tidak ditemukan -> kode booking lokal
        $_SESSION['current_reservation']['kode_booking'] = 'KB-' . strtoupper(bin2hex(random_bytes(3)));
    }

    header('Location: ' . route('sukses_reservasi'));
    exit;
}

$depositSelesai = $data_reservasi['metode_bayar'] ?? '';
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <?php $sidebarRole = 'customer'; $sidebarActive = 'reservasi'; include __DIR__ . '/../components/dashboard-sidebar.php'; ?>

        <div class="lg:col-span-3">
            <div class="bg-white/90 border border-[#eadfd4] rounded-3xl p-8 md:p-10 shadow-sm space-y-8">

                <div class="border-b border-[#eadfd4] pb-6">
                    <span class="text-xs uppercase tracking-widest text-[#8a5d49] font-bold">Pembayaran Aman</span>
                    <h1 class="font-display text-3xl font-bold text-[#201913] mt-1">Metode Pembayaran & Deposit</h1>
                    <p class="text-sm text-[#66574b] mt-1">Pilih metode pembayaran untuk mengamankan jadwal reservasi Anda.</p>
                </div>

                <?php if ($depositSelesai !== ''): ?>
                    <div class="p-5 rounded-2xl bg-green-50 border border-green-200 text-green-800 text-sm">
                        <strong>Deposit berhasil diproses!</strong> Metode: <?= e($metode[$depositSelesai] ?? $depositSelesai) ?>.
                        Lanjut ke halaman konfirmasi untuk melihat kode booking & QR.
                    </div>
                <?php endif; ?>

                <form action="<?= route('pembayaran') ?>" method="POST" class="space-y-6">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-5">
                            <span class="text-xs font-bold uppercase tracking-wider text-[#8a5d49]">Tanggal</span>
                            <p class="font-display text-lg font-bold text-[#201913] mt-1"><?= e($data_reservasi['tanggal'] ?? '-') ?></p>
                        </div>
                        <div class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-5">
                            <span class="text-xs font-bold uppercase tracking-wider text-[#8a5d49]">Waktu</span>
                            <p class="font-display text-lg font-bold text-[#201913] mt-1"><?= e($data_reservasi['waktu'] ?? '-') ?> WIB</p>
                        </div>
                        <div class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-5">
                            <span class="text-xs font-bold uppercase tracking-wider text-[#8a5d49]">Jumlah Tamu</span>
                            <p class="font-display text-lg font-bold text-[#201913] mt-1"><?= e($jumlahTamu) ?> Orang</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-3">Pilih Metode Pembayaran</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php foreach ($metode as $key => $label): ?>
                                <label class="relative flex items-center p-4 rounded-xl border border-[#eadfd4] bg-white cursor-pointer hover:border-[#8a5d49] transition">
                                    <input type="radio" name="metode_bayar" value="<?= e($key) ?>" required class="text-[#8a5d49] focus:ring-[#8a5d49]">
                                    <div class="ml-3">
                                        <span class="block text-sm font-bold text-[#201913]"><?= e($label) ?></span>
                                        <span class="text-xs font-bold text-[#5e392e]">Proses otomatis & instan</span>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-6 space-y-3">
                        <div class="flex justify-between text-sm text-[#4f4338]">
                            <span>Estimasi Total Pesanan</span>
                            <span class="font-bold">Rp <?= number_format($totalEstimasi, 0, ',', '.') ?></span>
                        </div>
                        <div class="flex justify-between text-sm text-[#4f4338]">
                            <span>Deposit Wajib (20%)</span>
                            <span class="font-bold text-[#8a5d49]">Rp <?= number_format($depositNominal, 0, ',', '.') ?></span>
                        </div>
                        <div class="flex justify-between border-t border-[#eadfd4] pt-3 font-bold text-[#201913]">
                            <span>Total yang Dibayar Sekarang</span>
                            <span>Rp <?= number_format($depositNominal, 0, ',', '.') ?></span>
                        </div>
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-3 border-t border-[#eadfd4]">
                        <a href="<?= route('reservasi', ['resto' => $data_reservasi['resto'] ?? 'A', 'action' => 'reset']) ?>" class="px-5 py-2.5 rounded-xl border border-[#eadfd4] text-stone-600 hover:bg-stone-50 text-xs font-bold transition">
                            Kembali Ubah Data
                        </a>
                        <button type="submit" class="bg-[#5e392e] hover:bg-[#4a2c24] text-white text-xs font-bold py-2.5 px-6 rounded-xl transition shadow-sm">
                            Bayar Deposit & Konfirmasi
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
