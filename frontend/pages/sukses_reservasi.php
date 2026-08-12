<?php
// pages/sukses_reservasi.php — Halaman Sukses: Kode Booking + QR Code

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

                <div class="space-y-2">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 text-green-700 mb-2">
                        <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h1 class="font-display text-3xl font-bold text-[#201913]">Reservasi Berhasil Dibuat!</h1>
                    <p class="text-sm text-[#66574b]">Pembayaran deposit berhasil. Tunjukkan kode berikut saat tiba di restoran.</p>
                </div>

                <!-- Kartu Kode Booking + QR -->
                <div class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-6 md:p-8 space-y-6 shadow-inner max-w-md mx-auto">
                    <div class="flex items-center justify-between border-b border-[#eadfd4] pb-4">
                        <div class="text-left">
                            <span class="text-xs font-bold uppercase tracking-wider text-[#8a5d49]">Kode Booking Anda</span>
                            <h3 class="font-display text-2xl font-bold tracking-widest text-[#201913] mt-1"><?= e($kodeBooking) ?></h3>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">Confirmed</span>
                    </div>

                    <!-- QR Code (Canvas digambar via JS) -->
                    <div class="mx-auto w-48 h-48 bg-white border border-[#eadfd4] rounded-2xl p-2">
                        <canvas id="qr-canvas" class="w-full h-full" width="180" height="180"></canvas>
                    </div>
                    <p class="text-xs text-[#66574b]">Staf akan memindai QR ini saat check-in.</p>
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

<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const canvas = document.getElementById("qr-canvas");
    const payload = <?= json_encode($qrPayload) ?>;
    if (window.QRCode) {
        QRCode.toCanvas(canvas, payload, { width: 180, margin: 2 }, function (error) {
            if (error) console.error(error);
        });
    }
});
</script>
