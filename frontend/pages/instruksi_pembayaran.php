<?php
// pages/instruksi_pembayaran.php — Instruksi pembayaran per metode + verifikasi (simulasi)
// Alur: tahap 4 submit -> reservasi pending -> halaman ini menampilkan VA / QRIS /
// e-wallet / tenggat Bayar di Restoran -> pelanggan "bayar" -> backend verify -> confirmed.

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: ' . route('login'));
    exit;
}

require_once __DIR__ . '/../src/config/api.config.php';
require_once __DIR__ . '/../src/utils/api.php';

$res = $_SESSION['current_reservation'] ?? null;
$paymentId = (int) ($res['payment_id'] ?? 0);
$reservationId = (int) ($res['reservation_id'] ?? 0);
$method = (string) ($res['payment_method'] ?? '');

if (!$reservationId || !$paymentId || !$method) {
    header('Location: ' . route('reservasi', ['resto' => (int) ($res['resto'] ?? 1)]));
    exit;
}

// Ambil instruksi pembayaran dari backend (VA / QRIS / e-wallet / tenggat)
$payment = null;
$instructions = [];
$insResult = api_get(API_PAYMENTS . '/' . $paymentId . '/instructions');
if ($insResult['ok']) {
    $payment = $insResult['data']['data']['payment'] ?? [];
    $instructions = $insResult['data']['data']['instructions'] ?? [];
}

$error = '';
$success = '';

// Verifikasi pembayaran (simulasi "Saya Sudah Bayar")
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $paymentId > 0) {
    $phone = trim((string) ($_POST['phone_number'] ?? ''));
    $verifyResult = api_post(API_PAYMENTS . '/' . $paymentId . '/verify', $phone !== '' ? ['phone_number' => $phone] : []);

    if ($verifyResult['ok']) {
        $_SESSION['current_reservation']['payment_status'] = 'paid';
        header('Location: ' . route('sukses_reservasi'));
        exit;
    }

    $error = api_error_message($verifyResult, 'Verifikasi pembayaran gagal. Silakan coba lagi.');
}

$kodeBooking = $res['kode_booking'] ?? '-';
$nominal = (float) ($res['deposit'] ?? ($payment['amount'] ?? 0));
$expiresAt = $instructions['expires_at'] ?? ($payment['expires_at'] ?? null);

// Payload QR untuk metode QRIS (bila tidak ada gambar QRIS dari restoran)
$qrPayload = json_encode([
    'kode_booking' => $kodeBooking,
    'metode'       => $method,
    'nominal'      => $nominal,
    'waktu'        => ($res['tanggal'] ?? '') . ' ' . ($res['waktu'] ?? ''),
], JSON_UNESCAPED_UNICODE);
$qrisImage = $instructions['qris_image'] ?? null;
$qrString = $instructions['qr_payload'] ?? $qrPayload;
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-10">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white/90 border border-[#eadfd4] rounded-3xl p-8 md:p-10 shadow-sm space-y-8">

            <?php $step = 4; include __DIR__ . '/../components/reservation-stepper.php'; ?>

            <div class="border-b border-[#eadfd4] pb-6">
                <span class="text-xs uppercase tracking-widest text-[#8a5d49] font-bold">Langkah 4 dari 4</span>
                <h1 class="font-display text-3xl font-bold text-[#201913] mt-1">Instruksi Pembayaran</h1>
                <p class="text-sm text-[#66574b] mt-1">Kode Booking: <strong class="font-mono text-[#201913]"><?= e($kodeBooking) ?></strong></p>
            </div>

            <?php if ($error !== ''): ?>
                <div class="p-5 rounded-2xl bg-red-50 border border-red-200 text-red-700 text-sm">
                    <strong>Kesalahan:</strong> <?= e($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($method === 'cod'): ?>

                <!-- BAYAR DI RESTORAN (pending, tanpa tombol bayar) -->
                <div class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-6 space-y-4 text-center">
                    <span class="status-badge status-pending">Menunggu Pembayaran di Kasir</span>
                    <div>
                        <p class="text-sm text-[#66574b]">Silakan membayar deposit di kasir restoran saat tiba.</p>
                        <p class="font-display text-4xl font-bold text-[#201913] mt-2">
                            Rp <?= number_format($nominal, 0, ',', '.') ?>
                        </p>
                        <?php if ($nominal <= 0): ?>
                            <p class="text-xs text-[#8a5d49] mt-1">Tanpa pre-order, tidak ada deposit yang perlu dibayar.</p>
                        <?php endif; ?>
                    </div>
                    <?php if ($expiresAt): ?>
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-orange-50 border border-orange-200 text-orange-800 text-xs font-bold">
                            Reservasi hangus bila tidak dibayar dalam
                            <span id="countdown" class="font-mono">--:--</span>
                        </div>
                    <?php endif; ?>
                    <p class="text-xs text-[#66574b]">Reservasi akan dibatalkan otomatis jika pembayaran melewati tenggat waktu. Kasir akan mengonfirmasi setelah Anda membayar.</p>
                    <div class="pt-2 flex flex-wrap gap-4 justify-center">
                        <a href="<?= route('riwayat_reservasi') ?>" class="bg-[#5e392e] hover:bg-[#4a2c24] text-white text-xs font-bold py-2.5 px-6 rounded-xl transition shadow-sm">
                            Lihat Kode Booking →
                        </a>
                    </div>
                </div>

            <?php elseif ($method === 'bank_transfer'): ?>

                <!-- TRANSFER BANK BCA -->
                <form method="POST" class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-[#8a5d49]">Transfer <?= e($instructions['bank'] ?? 'BCA') ?></span>
                            <h3 class="font-display text-xl font-bold text-[#201913]"><?= e($instructions['label'] ?? 'Transfer Bank BCA') ?></h3>
                        </div>
                        <span class="text-sm font-bold text-[#201913]">Rp <?= number_format($nominal, 0, ',', '.') ?></span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <span class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-1">Nama Penerima</span>
                            <span class="block font-bold text-[#201913]"><?= e($instructions['account_name'] ?? 'Kafiber Resto') ?></span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-1">Nomor Rekening / VA</span>
                            <div class="flex items-center gap-2">
                                <span id="va-number" class="font-mono text-lg font-bold text-[#201913] tracking-widest"><?= e($instructions['account_number'] ?? '-') ?></span>
                                <button type="button" onclick="copyVA()" class="text-xs font-bold text-[#8a5d49] hover:underline">Salin</button>
                            </div>
                        </div>
                    </div>

                    <ol class="text-sm text-[#4f4338] space-y-1 list-decimal list-inside">
                        <li>Transfer <?= e($instructions['bank'] ?? 'BCA') ?> ke nomor di atas sebesar <strong>Rp <?= number_format($nominal, 0, ',', '.') ?></strong>.</li>
                        <li>Setelah transfer, klik tombol <strong>“Saya Sudah Bayar”</strong> di bawah.</li>
                        <li>Reservasi langsung dikonfirmasi secara otomatis.</li>
                    </ol>

                    <p class="text-xs text-[#a39a8f] bg-white border border-[#eadfd4] rounded-xl p-3">
                        Mode simulasi (tanpa gateway bank): pembayaran dikonfirmasi otomatis saat tombol ditekan. Saat gateway asli terpasang, konfirmasi datang dari webhook bank.
                    </p>

                    <button type="submit" class="w-full bg-[#5e392e] hover:bg-[#4a2c24] text-white text-xs font-bold py-3 rounded-xl transition shadow-sm">
                        Saya Sudah Bayar →
                    </button>
                </form>

            <?php elseif ($method === 'ewallet'): ?>

                <!-- E-WALLET -->
                <form method="POST" class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-[#8a5d49]">E-Wallet</span>
                            <h3 class="font-display text-xl font-bold text-[#201913]"><?= e($instructions['label'] ?? 'E-Wallet (OVO / GoPay / DANA)') ?></h3>
                        </div>
                        <span class="text-sm font-bold text-[#201913]">Rp <?= number_format($nominal, 0, ',', '.') ?></span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Nomor HP yang Terhubung E-Wallet</label>
                        <input type="text" name="phone_number" required placeholder="Contoh: 081234567890"
                               value="<?= e($instructions['customer_phone'] ?? '') ?>"
                               class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
                    </div>

                    <ol class="text-sm text-[#4f4338] space-y-1 list-decimal list-inside">
                        <li>Masukkan nomor HP e-wallet Anda (OVO / GoPay / DANA).</li>
                        <li>Klik <strong>“Bayar Sekarang”</strong> untuk membuka aplikasi e-wallet.</li>
                        <li>Konfirmasi pembayaran setelah memasukkan PIN.</li>
                    </ol>

                    <p class="text-xs text-[#a39a8f] bg-white border border-[#eadfd4] rounded-xl p-3">
                        Mode simulasi: pembayaran langsung dikonfirmasi backend. Saat gateway asli terpasang, Anda diarahkan ke aplikasi e-wallet & verifikasi dari notifikasi merchant.
                    </p>

                    <button type="submit" class="w-full bg-[#5e392e] hover:bg-[#4a2c24] text-white text-xs font-bold py-3 rounded-xl transition shadow-sm">
                        Bayar Sekarang →
                    </button>
                </form>

            <?php elseif ($method === 'qris'): ?>

                <!-- QRIS -->
                <form method="POST" class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-6 space-y-4 text-center">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-[#8a5d49]">Scan dengan Aplikasi Bank / E-Wallet</span>
                        <h3 class="font-display text-xl font-bold text-[#201913]"><?= e($instructions['label'] ?? 'QRIS') ?></h3>
                        <p class="text-sm font-bold text-[#201913] mt-1">Rp <?= number_format($nominal, 0, ',', '.') ?></p>
                    </div>

                    <div class="mx-auto w-52 h-52 bg-white border border-[#eadfd4] rounded-2xl p-2">
                        <?php if ($qrisImage): ?>
                            <img src="<?= e(api_image_url($qrisImage)) ?>" alt="QRIS" class="w-full h-full object-contain">
                        <?php else: ?>
                            <div id="qris-wrap" class="w-full h-full flex items-center justify-center"></div>
                        <?php endif; ?>
                    </div>

                    <ol class="text-sm text-[#4f4338] space-y-1 list-decimal list-inside text-left">
                        <li>Buka aplikasi bank / e-wallet Anda, pilih menu <strong>QRIS</strong>.</li>
                        <li>Scan kode QR di atas dan verifikasi nominalnya.</li>
                        <li>Klik <strong>“Saya Sudah Bayar”</strong> setelah pembayaran berhasil.</li>
                    </ol>

                    <p class="text-xs text-[#a39a8f] bg-white border border-[#eadfd4] rounded-xl p-3">
                        Mode simulasi: QR berisi kode booking (bukan kode merchant QRIS asli). Dengan gateway asli, QRIS unik per transaksi & diverifikasi instan.
                    </p>

                    <button type="submit" class="w-full bg-[#5e392e] hover:bg-[#4a2c24] text-white text-xs font-bold py-3 rounded-xl transition shadow-sm">
                        Saya Sudah Bayar →
                    </button>
                </form>

            <?php else: ?>

                <div class="p-5 rounded-2xl bg-orange-50 border border-orange-200 text-orange-800 text-sm">
                    Metode pembayaran tidak dikenali. <a href="<?= route('riwayat_reservasi') ?>" class="font-bold underline">Lihat riwayat reservasi</a>.
                </div>

            <?php endif; ?>

            <div class="pt-2 flex items-center justify-between border-t border-[#eadfd4]">
                <a href="<?= route('pembayaran') ?>" class="text-xs font-bold text-[#8a5d49] hover:underline">← Kembali ke Review</a>
                <span class="text-xs text-[#a39a8f]">Bantuan? Hubungi restoran.</span>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Hitung mundur tenggat "Bayar di Restoran"
    const expiresAt = <?= $expiresAt ? json_encode($expiresAt) : 'null' ?>;
    const cd = document.getElementById("countdown");
    if (expiresAt && cd) {
        const target = new Date(expiresAt.replace(" ", "T"));
        const tick = () => {
            const diff = target.getTime() - Date.now();
            if (diff <= 0) { cd.textContent = "00:00"; window.location.reload(); return; }
            const m = Math.floor(diff / 60000);
            const s = Math.floor((diff % 60000) / 1000);
            cd.textContent = String(m).padStart(2, "0") + ":" + String(s).padStart(2, "0");
        };
        tick();
        setInterval(tick, 1000);
    }

    // QRIS: gambar QR dari payload bila tidak ada gambar merchant
    const qrisWrap = document.getElementById("qris-wrap");
    if (qrisWrap && window.QRCode) {
        new QRCode(qrisWrap, {
            text: <?= json_encode($qrString) ?>,
            width: 190,
            height: 190,
            colorDark: "#201913",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.M
        });
    }
});

function copyVA() {
    const va = document.getElementById("va-number");
    if (!va) return;
    const text = va.textContent.trim();
    (navigator.clipboard ? navigator.clipboard.writeText(text) : Promise.reject()).catch(() => {
        const ta = document.createElement("textarea");
        ta.value = text;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand("copy");
        ta.remove();
    });
}
</script>
<script src="assets/js/qrcode.min.js"></script>