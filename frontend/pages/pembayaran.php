<?php
// pages/pembayaran.php — Langkah 4: Review Detail & Pembayaran Deposit
// Membuat reservasi + item pre-order + pembayaran di backend.

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: ' . route('login'));
    exit;
}

require_once __DIR__ . '/../src/config/api.config.php';
require_once __DIR__ . '/../src/utils/api.php';

$data = $_SESSION['current_reservation'] ?? null;
$meja = $_SESSION['current_reservation']['meja'] ?? null;
if (!$data || empty($data['tanggal']) || !$meja) {
    header('Location: ' . route('reservasi', ['resto' => (int) ($data['resto'] ?? 1)]));
    exit;
}

$restoId   = (int) ($data['resto'] ?? 1);
$jumlahTamu = (int) ($data['jumlah_tamu'] ?? 1);

$metode = [
    'bank_transfer' => 'Transfer Bank BCA',
    'ewallet'       => 'E-Wallet (OVO / GoPay / DANA)',
    'qris'          => 'QRIS',
    'cod'           => 'Bayar di Restoran (Langsung)',
];
$metodeApiMap = [
    'bank_transfer' => 'bank_transfer',
    'ewallet'       => 'ewallet',
    'qris'          => 'qris',
    'cod'           => 'cash',
];

// Total pre-order dari keranjang (session)
$cart = $_SESSION['cart'] ?? [];
$totalPreOrder = 0.0;
$cartItems = [];
foreach ($cart as $cid => $c) {
    $line = (float) $c['price'] * (int) $c['qty'];
    $totalPreOrder += $line;
    $cartItems[] = [
        'menu_id' => (int) $cid,
        'qty'     => (int) $c['qty'],
        'name'    => $c['name'],
        'price'   => (float) $c['price'],
        'subtotal'=> $line,
    ];
}

// Kebijakan deposit dari restoran (melalui endpoint publik detail restoran)
$depositPercent = 20;
$depositMin = 50000;
$bankConfig = null; // konfigurasi merchant transfer BCA per restoran
$detail = api_get(API_RESTAURANTS . '/' . $restoId);
if ($detail['ok']) {
    $resto = $detail['data']['data'] ?? [];
    foreach (($resto['policies'] ?? []) as $p) {
        if (!empty($p['is_active'])) {
            $depositPercent = (float) ($p['deposit_percent'] ?? 20);
            $depositMin = (int) ($p['deposit_min_amount'] ?? 50000);
            break;
        }
    }
    foreach (($resto['payment_methods'] ?? []) as $pm) {
        if (($pm['method'] ?? '') === 'bank_transfer' && !empty($pm['is_active'])) {
            $bankConfig = $pm;
            break;
        }
    }
}
$totalEstimasi = $totalPreOrder; // kenaikan via pre-order; tanpa pre-order = 0

// Deposit dasar (20% dari pre-order). Bayar di Restoran memakai nilai ini;
// pembayaran online dikenai deposit minimum restoran.
$depositBase = $totalEstimasi > 0
    ? max($depositMin, (int) round($totalEstimasi * $depositPercent / 100))
    : 0;
$depositOnline = max($depositBase, $depositMin);

$error = '';
$bayarTerpilih = '';

// Proses pembayaran
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bayarTerpilih = (string) ($_POST['metode_bayar'] ?? '');
    if (!array_key_exists($bayarTerpilih, $metode)) {
        $error = 'Silakan pilih metode pembayaran yang valid.';
    }

    if ($error === '') {
        // Siapkan data reservasi
        $bookingCode = 'KB-' . strtoupper(bin2hex(random_bytes(3)));
        $waktu = date('H:i:s', strtotime($data['waktu']));

        // Bayar di Restoran = deposit dasar (boleh Rp 0 bila tanpa pre-order);
        // metode online dikenakan deposit minimum restoran.
        $payNow = $bayarTerpilih === 'cod' ? $depositBase : $depositOnline;

        // Detail pembayaran per metode
        $paymentDetails = $bayarTerpilih === 'bank_transfer'
            ? [
                'bank' => 'BCA',
                'account_name' => $bankConfig['account_name'] ?? 'Kafiber Resto',
            ]
            : null;

        $payData = [
            'amount'           => round($payNow, 2),
            'method'           => $metodeApiMap[$bayarTerpilih] ?? 'bank_transfer',
            'gateway'          => $bayarTerpilih === 'qris' ? 'qris' : ($bayarTerpilih === 'ewallet' ? 'ewallet' : 'bank'),
            'va_number'        => $bayarTerpilih === 'bank_transfer' ? ($bankConfig['account_number'] ?? null) : null,
            'payment_details'  => $paymentDetails,
            'expires_at'       => $bayarTerpilih === 'cod' ? date('Y-m-d H:i:s', strtotime('+2 hours')) : null,
        ];

        // Cek reservasi pending milik sendiri pada slot yang sama (mis. saat
        // pelanggan "Kembali ke Review" lalu mengganti metode pembayaran).
        // Bila ada, gunakan kembali agar tidak menabrak unique slot (409).
        $existingId    = (int) ($_SESSION['current_reservation']['reservation_id'] ?? 0);
        $existingPayId = (int) ($_SESSION['current_reservation']['payment_id'] ?? 0);
        $reuseId = 0;
        $existingResData = [];
        if ($existingId > 0) {
            $chk = api_get(API_RESERVATIONS . '/' . $existingId);
            if ($chk['ok']) {
                $existingResData = $chk['data']['data'] ?? [];
                $sameSlot = (int) ($existingResData['table_id'] ?? 0) === (int) $meja['table_id']
                    && substr((string) ($existingResData['reservation_date'] ?? ''), 0, 10) === $data['tanggal']
                    && substr((string) ($existingResData['reservation_time'] ?? ''), 0, 5) === substr($waktu, 0, 5);
                if ($sameSlot && in_array($existingResData['status'] ?? '', ['pending', 'confirmed'], true)) {
                    $reuseId = $existingId;
                }
            }
        }

        if ($reuseId > 0) {
            // === GUNAKAN KEMBALI reservasi yang sudah ada (hindari duplikat slot) ===
            $reservationId = $reuseId;

            // Item pre-order hanya dibuat bila reservasi belum punya item
            if (!empty($cartItems) && empty($existingResData['items'] ?? [])) {
                foreach ($cartItems as $item) {
                    api_post(API_RESERVATION_ITEMS, [
                        'reservation_id' => $reservationId,
                        'menu_id'        => $item['menu_id'],
                        'quantity'       => $item['qty'],
                        'subtotal_price' => round($item['subtotal'], 2),
                    ]);
                }
            }

            // Perbarui metode pembayaran bila payment pending masih ada; jika
            // sudah sukses (tidak bisa diubah), buat catatan pembayaran baru.
            $paymentId = 0;
            if ($existingPayId > 0) {
                $upd = api_request('PUT', API_PAYMENTS . '/' . $existingPayId, $payData);
                if ($upd['ok']) {
                    $paymentId = $existingPayId;
                }
            }
            if ($paymentId === 0) {
                $payResult = api_post(API_PAYMENTS, array_merge([
                    'reservation_id'   => $reservationId,
                    'type'             => 'deposit',
                    'status'           => 'pending',
                    'transaction_code' => $bookingCode . '-DP',
                ], $payData));
                $paymentId = (int) ($payResult['data']['data']['payment_id'] ?? 0);
            }

            $_SESSION['current_reservation']['kode_booking']   = $existingResData['booking_code'] ?? $bookingCode;
            $_SESSION['current_reservation']['reservation_id'] = $reservationId;
            $_SESSION['current_reservation']['payment_id']     = $paymentId;
            $_SESSION['current_reservation']['payment_method'] = $bayarTerpilih;
            $_SESSION['current_reservation']['payment_status'] = 'pending';
            $_SESSION['current_reservation']['total']          = $totalEstimasi;
            $_SESSION['current_reservation']['deposit']        = $payNow;

            header('Location: ' . route('instruksi_pembayaran'));
            exit;
        }

        $payload = [
            'user_id'          => (int) ($_SESSION['user_id'] ?? 0),
            'table_id'         => (int) $meja['table_id'],
            'booking_code'     => $bookingCode,
            'reservation_date' => $data['tanggal'],
            'reservation_time' => $waktu,
            'number_of_guest'  => $jumlahTamu,
            'total_price'      => round($totalEstimasi, 2),
            'deposit_amount'   => round($payNow, 2),
            'status'           => 'pending',
            'payment_status'   => 'unpaid',
            'special_request'  => ($data['catatan'] ?? '-') !== '-' ? $data['catatan'] : null,
        ];

        $resResult = api_post(API_RESERVATIONS, $payload);

        if (!$resResult['ok']) {
            $error = 'Gagal membuat reservasi di backend. ' .
                (isset($resResult['data']['message']) ? $resResult['data']['message'] : 'Meja mungkin sudah dipesan orang lain pada slot yang sama.');
        } else {
            $reservationId = (int) ($resResult['data']['data']['reservation_id'] ?? 0);

            // Simpan item pre-order
            if ($reservationId > 0) {
                foreach ($cartItems as $item) {
                    api_post(API_RESERVATION_ITEMS, [
                        'reservation_id' => $reservationId,
                        'menu_id'        => $item['menu_id'],
                        'quantity'       => $item['qty'],
                        'subtotal_price' => round($item['subtotal'], 2),
                    ]);
                }
            }

            // Catat pembayaran deposit (status pending sampai diverifikasi backend).
            $paymentId = 0;
            if ($reservationId > 0) {
                $payResult = api_post(API_PAYMENTS, array_merge([
                    'reservation_id'   => $reservationId,
                    'type'             => 'deposit',
                    'status'           => 'pending',
                    'transaction_code' => $bookingCode . '-DP',
                ], $payData));
                $paymentId = (int) ($payResult['data']['data']['payment_id'] ?? 0);
            }

            // Simpan data sukses ke session
            $_SESSION['current_reservation']['kode_booking']   = $bookingCode;
            $_SESSION['current_reservation']['reservation_id'] = $reservationId;
            $_SESSION['current_reservation']['payment_id']     = $paymentId;
            $_SESSION['current_reservation']['payment_method'] = $bayarTerpilih;
            $_SESSION['current_reservation']['payment_status'] = 'pending';
            $_SESSION['current_reservation']['total']          = $totalEstimasi;
            $_SESSION['current_reservation']['deposit']        = $payNow;

            header('Location: ' . route('instruksi_pembayaran'));
            exit;
        }
    }
}
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-10">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white/90 border border-[#eadfd4] rounded-3xl p-8 md:p-10 shadow-sm space-y-8">

            <?php $step = 4; include __DIR__ . '/../components/reservation-stepper.php'; ?>

                <div class="border-b border-[#eadfd4] pb-6">
                    <span class="text-xs uppercase tracking-widest text-[#8a5d49] font-bold">Langkah 4 dari 4</span>
                    <h1 class="font-display text-3xl font-bold text-[#201913] mt-1">Review Detail Reservasi & Pembayaran</h1>
                    <p class="text-sm text-[#66574b] mt-1">Pastikan detail di bawah sudah benar sebelum mengonfirmasi.</p>
                </div>

                <?php if ($error !== ''): ?>
                    <div class="p-5 rounded-2xl bg-red-50 border border-red-200 text-red-700 text-sm">
                        <strong>Kesalahan:</strong> <?= e($error) ?>
                    </div>
                <?php endif; ?>

                <!-- Detail Reservasi -->
                <div class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-6 space-y-4 shadow-inner">
                    <h2 class="font-display text-xl font-bold text-[#201913]">Detail Reservasi</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="block text-xs uppercase tracking-[0.15em] font-bold text-[#8a5d49] mb-1">Restoran</span>
                            <span class="font-bold text-[#201913]"><?= e($data['resto_nama'] ?? 'Restoran') ?></span>
                        </div>
                        <div>
                            <span class="block text-xs uppercase tracking-[0.15em] font-bold text-[#8a5d49] mb-1">Nama Pemesan</span>
                            <span class="font-bold text-[#201913]"><?= e($data['nama'] ?? 'Tamu') ?></span>
                        </div>
                        <div>
                            <span class="block text-xs uppercase tracking-[0.15em] font-bold text-[#8a5d49] mb-1">Tanggal & Waktu</span>
                            <span class="font-bold text-[#201913]"><?= e($data['tanggal'] ?? '-') ?> &middot; <?= e($data['waktu'] ?? '-') ?> WIB</span>
                        </div>
                        <div>
                            <span class="block text-xs uppercase tracking-[0.15em] font-bold text-[#8a5d49] mb-1">Jumlah Tamu</span>
                            <span class="font-bold text-[#201913]"><?= $jumlahTamu ?> Orang</span>
                        </div>
                        <div>
                            <span class="block text-xs uppercase tracking-[0.15em] font-bold text-[#8a5d49] mb-1">Meja</span>
                            <span class="font-bold text-[#201913]">Meja <?= e($meja['table_number'] ?? '-') ?> (<?= e(strtoupper($meja['area'] ?? $data['area'] ?? 'indoor')) ?>)</span>
                        </div>
                        <div>
                            <span class="block text-xs uppercase tracking-[0.15em] font-bold text-[#8a5d49] mb-1">Acara</span>
                            <span class="font-bold text-[#201913]"><?= e($data['acara'] ?? '-') ?></span>
                        </div>
                    </div>
                </div>

                <!-- Pre-order Menu -->
                <div class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-6 space-y-3 shadow-inner">
                    <div class="flex items-center justify-between">
                        <h2 class="font-display text-xl font-bold text-[#201913]">Pre-order Menu</h2>
                        <a href="<?= route('menu', ['resto' => $restoId]) ?>" class="text-xs font-bold text-[#8a5d49] hover:underline">+ Tambah Menu</a>
                    </div>

                    <?php if (empty($cartItems)): ?>
                        <p class="text-sm text-[#66574b]">Tidak ada pre-order. Anda dapat memesan langsung di restoran.</p>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table data-paginate class="w-full text-sm text-left text-[#4f4338]">
                                <thead>
                                    <tr class="border-b border-[#eadfd4] text-[#8a5d49] text-xs uppercase tracking-wider">
                                        <th class="py-2 pr-4">Menu</th>
                                        <th class="py-2 pr-4">Qty</th>
                                        <th class="py-2 pr-4 text-right">Harga</th>
                                        <th class="py-2 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cartItems as $item): ?>
                                        <tr class="border-b border-[#eadfd4]">
                                            <td class="py-2 pr-4 font-bold text-[#201913]"><?= e($item['name']) ?></td>
                                            <td class="py-2 pr-4"><?= $item['qty'] ?>x</td>
                                            <td class="py-2 pr-4 text-right">Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                                            <td class="py-2 text-right font-bold">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Form Pembayaran -->
                <form action="<?= route('pembayaran') ?>" method="POST" class="space-y-6">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-3">Pilih Metode Pembayaran</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php foreach ($metode as $key => $label): ?>
                                <label class="relative flex items-center p-4 rounded-xl border border-[#eadfd4] bg-white cursor-pointer hover:border-[#8a5d49] transition">
                                    <input type="radio" name="metode_bayar" value="<?= e($key) ?>" required class="text-[#8a5d49] focus:ring-[#8a5d49]">
                                    <div class="ml-3">
                                        <span class="block text-sm font-bold text-[#201913]"><?= e($label) ?></span>
                                        <span class="text-xs font-bold text-[#5e392e]">Proses otomatis &amp; instan</span>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-6 space-y-3">
                        <div class="flex justify-between text-sm text-[#4f4338]">
                            <span>Total Pre-order</span>
                            <span class="font-bold">Rp <?= number_format($totalEstimasi, 0, ',', '.') ?></span>
                        </div>
                        <div class="flex justify-between text-sm text-[#4f4338]">
                            <span>Deposit <?= (int) $depositPercent ?>%</span>
                            <span class="font-bold text-[#8a5d49]">Rp <span id="deposit-value"><?= number_format($depositBase, 0, ',', '.') ?></span></span>
                        </div>
                        <div class="flex justify-between border-t border-[#eadfd4] pt-3 font-bold text-[#201913]">
                            <span>Total yang Dibayar Sekarang</span>
                            <span>Rp <span id="paynow-value"><?= number_format($depositBase, 0, ',', '.') ?></span></span>
                        </div>
                        <p id="deposit-note" class="text-xs text-[#8a5d49]">
                            <?php if ($totalEstimasi <= 0): ?>
                                Tanpa pre-order, Bayar di Restoran tidak dikenakan deposit. Metode online dikenakan deposit minimum Rp <?= number_format($depositMin, 0, ',', '.') ?>.
                            <?php else: ?>
                                Bayar di Restoran memakai deposit <?= (int) $depositPercent ?>% dari total; metode online dikenakan minimal Rp <?= number_format($depositMin, 0, ',', '.') ?>.
                            <?php endif; ?>
                        </p>
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-3 border-t border-[#eadfd4]">
                        <a href="<?= route('pilih_meja') ?>" class="px-5 py-2.5 rounded-xl border border-[#eadfd4] text-stone-600 hover:bg-stone-50 text-xs font-bold transition">
                            ← Kembali
                        </a>
                        <button type="submit" class="bg-[#5e392e] hover:bg-[#4a2c24] text-white text-xs font-bold py-2.5 px-6 rounded-xl transition shadow-sm">
                            Konfirmasi & Simpan Reservasi →
                        </button>
                    </div>
                </form>
            </div>
        </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const depositBase = <?= (int) $depositBase ?>;
    const depositOnline = <?= (int) $depositOnline ?>;
    const fmt = n => "Rp " + n.toLocaleString("id-ID");
    const radios = document.querySelectorAll('input[name="metode_bayar"]');

    function refresh() {
        const isCod = document.querySelector('input[name="metode_bayar"]:checked')?.value === "cod";
        const val = isCod ? depositBase : depositOnline;
        document.getElementById("deposit-value").textContent = fmt(val);
        document.getElementById("paynow-value").textContent = fmt(val);
    }
    radios.forEach(r => r.addEventListener("change", refresh));
    refresh();
});
</script>