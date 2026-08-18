<?php
// pages/sukses_reservasi.php — Halaman Sukses: Kode Booking + QR Code
// Menampilkan status sesuai pembayaran: confirmed (sudah bayar) atau
// pending (Bayar di Restoran / menunggu konfirmasi kasir).

$data_reservasi = $_SESSION['current_reservation'] ?? null;
if (!$data_reservasi) {
    header('Location: ' . route('reservasi'));
    exit;
}

// Generate kode booking bila belum ada (set oleh halaman pembayaran / fallback)
if (empty($data_reservasi['kode_booking'])) {
    $data_reservasi['kode_booking'] = 'KB-' . strtoupper(bin2hex(random_bytes(3)));
    $_SESSION['current_reservation'] = $data_reservasi;
}

$kodeBooking = $data_reservasi['kode_booking'];
$paymentStatus = $data_reservasi['payment_status'] ?? 'paid';
$paymentMethod = $data_reservasi['payment_method'] ?? '';
$deposit = (float) ($data_reservasi['deposit'] ?? 0);
$isPaid = $paymentStatus === 'paid';

// Data QR (string yang nanti di-scan oleh staf)
$qrPayload = json_encode([
    'kode_booking' => $kodeBooking,
    'nama'         => $data_reservasi['nama'] ?? '',
    'tanggal'      => $data_reservasi['tanggal'] ?? '',
    'waktu'        => $data_reservasi['waktu'] ?? '',
], JSON_UNESCAPED_UNICODE);
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-10">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white/90 border border-[#eadfd4] rounded-3xl p-8 md:p-10 shadow-sm text-center space-y-8">

            <?php if ($isPaid): ?>
                <div class="space-y-2">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 text-green-700 mb-2">
                        <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h1 class="font-display text-3xl font-bold text-[#201913]">Reservasi Berhasil Dibuat!</h1>
                    <p class="text-sm text-[#66574b]">Pembayaran deposit berhasil. Tunjukkan kode berikut saat tiba di restoran.</p>
                </div>
            <?php else: ?>
                <div class="space-y-2">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-orange-100 text-orange-700 mb-2">
                        <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h1 class="font-display text-3xl font-bold text-[#201913]">Reservasi Menunggu Pembayaran</h1>
                    <p class="text-sm text-[#66574b]">Pembayaran <?= $paymentMethod === 'cod' ? 'akan dilakukan di kasir restoran' : 'belum dikonfirmasi' ?>. Reservasi hangus jika tidak dibayar dalam tenggat waktu.</p>
                </div>
            <?php endif; ?>

            <!-- Kartu Kode Booking + QR -->
            <div class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-6 md:p-8 space-y-6 shadow-inner max-w-md mx-auto">
                <div class="flex items-center justify-between border-b border-[#eadfd4] pb-4">
                    <div class="text-left">
                        <span class="text-xs font-bold uppercase tracking-wider text-[#8a5d49]">Kode Booking Anda</span>
                        <h3 class="font-display text-2xl font-bold tracking-widest text-[#201913] mt-1"><?= e($kodeBooking) ?></h3>
                    </div>
                    <?php if ($isPaid): ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">Confirmed</span>
                    <?php else: ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-800">Pending</span>
                    <?php endif; ?>
                </div>

                <?php if ($isPaid): ?>
                    <!-- QR Code (digambar via library qrcodejs lokal) -->
                    <div class="mx-auto w-48 h-48 bg-white border border-[#eadfd4] rounded-2xl p-2">
                        <div id="qr-wrap" class="w-full h-full flex items-center justify-center"></div>
                    </div>
                    <p class="text-xs text-[#66574b]">Staf akan memindai QR ini saat check-in.</p>
                <?php else: ?>
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm text-[#4f4338]">
                            <span>Total Deposit</span>
                            <span class="font-bold text-[#201913]">Rp <?= number_format($deposit, 0, ',', '.') ?></span>
                        </div>
                        <p class="text-xs text-[#66574b]">
                            Silakan bayar <?= $paymentMethod === 'cod' ? 'di kasir restoran' : 'sesuai instruksi pembayaran' ?>.
                            Staf akan memindai QR ini saat check-in dan mengonfirmasi pembayaran.
                        </p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="pt-2 flex flex-wrap gap-4 justify-center">
                <a href="<?= route('riwayat_reservasi') ?>" class="bg-[#5e392e] hover:bg-[#4a2c24] text-white text-xs font-bold py-2.5 px-6 rounded-xl transition shadow-sm">
                    Lihat Riwayat Reservasi →
                </a>
                <a href="<?= route('home') ?>" class="px-5 py-2.5 rounded-xl border border-[#eadfd4] text-stone-600 hover:bg-stone-50 text-xs font-bold transition">
                    Kembali ke Beranda
                </a>
            </div>

        </div>
    </div>
</div>

<?php if ($isPaid): ?>
<script src="assets/js/qrcode.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const qrWrap = document.getElementById("qr-wrap");
    const payload = <?= json_encode($qrPayload) ?>;
    if (window.QRCode && qrWrap) {
        new QRCode(qrWrap, {
            text: payload,
            width: 180,
            height: 180,
            colorDark: "#201913",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.M
        });
    }
});
</script>
<?php endif; ?>