<?php
// pages/riwayat_reservasi.php — Halaman Riwayat Reservasi Pelanggan

$isLoggedIn = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
if (!$isLoggedIn) {
    header('Location: ' . route('login'));
    exit;
}

// Coba ambil data dari backend (jika tersedia)
$riwayat_api = [];
if (file_exists(__DIR__ . '/../src/config/api.config.php')) {
    require_once __DIR__ . '/../src/config/api.config.php';
}
if (file_exists(__DIR__ . '/../src/utils/api.php') && function_exists('api_get')) {
    // Minta reservasi milik pengguna yang sedang login (backend memfilter user_id)
    $uid = (int) ($_SESSION['user_id'] ?? 0);
    $result = api_get(API_RESERVATIONS . '?user_id=' . $uid . '&limit=100');
    if ($result['ok'] && isset($result['data']['data'])) {
        $paginated = $result['data']['data'];
        $riwayat_api = is_array($paginated) && isset($paginated['data'])
            ? $paginated['data']
            : $paginated;
    }
}

$currentReservation = $_SESSION['current_reservation'] ?? null;

$statusMap = [
    'pending'   => ['Pending', 'status-pending'],
    'confirmed' => ['Confirmed', 'status-confirmed'],
    'completed' => ['Completed', 'status-completed'],
    'cancelled' => ['Cancelled', 'status-cancelled'],
    'no_show'   => ['No Show', 'status-no_show'],
];
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        <?php $sidebarRole = 'customer'; $sidebarActive = 'riwayat_reservasi'; include __DIR__ . '/../components/dashboard-sidebar.php'; ?>

        <div class="lg:col-span-3">
            <div class="bg-white/90 border border-[#eadfd4] rounded-3xl p-8 md:p-10 shadow-sm space-y-6">

                <div class="border-b border-[#eadfd4] pb-6">
                    <span class="text-xs uppercase tracking-widest text-[#8a5d49] font-bold">Riwayat Saya</span>
                    <h1 class="font-display text-3xl font-bold text-[#201913] mt-1">Riwayat Reservasi</h1>
                    <p class="text-sm text-[#66574b] mt-1">Daftar seluruh reservasi yang pernah Anda buat.</p>
                </div>

                <!-- Reservasi Aktif di Session (paling baru) -->
                <?php if ($currentReservation): ?>
                    <div class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-[#8a5d49]">Reservasi Terbaru</span>
                            <h3 class="font-display text-xl font-bold text-[#201913] mt-1">
                                <?= e($currentReservation['nama'] ?? 'Tamu') ?> — <?= e($currentReservation['tanggal'] ?? '-') ?>
                            </h3>
                            <p class="text-sm text-[#66574b]">
                                <?= e($currentReservation['waktu'] ?? '-') ?> WIB &middot;
                                <?= e($currentReservation['jumlah_tamu'] ?? '-') ?> Orang &middot;
                                <?= e(strtoupper($currentReservation['area'] ?? 'indoor')) ?>
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="status-badge status-confirmed"><?= e($currentReservation['kode_booking'] ?? 'Confirmed') ?></span>
                            <a href="<?= route('sukses_reservasi') ?>" class="bg-[#5e392e] hover:bg-[#4a2c24] text-white text-xs font-bold py-2.5 px-5 rounded-xl transition shadow-sm">
                                Lihat QR
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Data dari Backend API -->
                <?php if (!empty($riwayat_api)): ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-[#4f4338]">
                            <thead>
                                <tr class="border-b border-[#eadfd4] text-[#8a5d49] text-xs uppercase tracking-wider">
                                    <th class="py-3 pr-4">Kode</th>
                                    <th class="py-3 pr-4">Tanggal</th>
                                    <th class="py-3 pr-4">Waktu</th>
                                    <th class="py-3 pr-4">Tamu</th>
                                    <th class="py-3 pr-4">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($riwayat_api as $r): ?>
                                    <?php
                                    $st = $statusMap[$r['status'] ?? 'pending'] ?? ['Pending', 'status-pending'];
                                    ?>
                                    <tr class="border-b border-[#eadfd4]">
                                        <td class="py-3 pr-4 font-bold text-[#201913]"><?= e($r['booking_code'] ?? '-') ?></td>
                                        <td class="py-3 pr-4"><?= e($r['reservation_date'] ?? '-') ?></td>
                                        <td class="py-3 pr-4"><?= e($r['reservation_time'] ?? '-') ?></td>
                                        <td class="py-3 pr-4"><?= e($r['number_of_guest'] ?? '-') ?></td>
                                        <td class="py-3 pr-4"><span class="status-badge <?= $st[1] ?>"><?= e($st[0]) ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-10">
                        <p class="text-sm text-[#66574b]">Belum ada riwayat reservasi dari database. Buat reservasi baru untuk melihatnya di sini.</p>
                        <a href="<?= route('reservasi') ?>" class="inline-block mt-4 bg-[#5e392e] hover:bg-[#4a2c24] text-white text-xs font-bold py-2.5 px-6 rounded-xl transition shadow-sm">
                            Buat Reservasi Baru
                        </a>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>
