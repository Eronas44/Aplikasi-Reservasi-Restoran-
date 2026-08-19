<?php
// pages/jadwal_hari_ini.php — Jadwal Reservasi Hari Ini (Staff)
// Terhubung ke backend: GET /reservations (filter tanggal hari ini)

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

$today = date('Y-m-d');

$jadwal = [];
$resResult = api_get(API_RESERVATIONS . '?limit=200');
if ($resResult['ok']) {
    $raw = $resResult['data']['data'] ?? [];
    $all = $raw['data'] ?? $raw;
    foreach ($all as $r) {
        if (substr((string) ($r['reservation_date'] ?? ''), 0, 10) === $today) {
            $jadwal[] = $r;
        }
    }
}

$statusMap = [
    'pending'   => ['Pending', 'status-pending'],
    'confirmed' => ['Confirmed', 'status-confirmed'],
    'completed' => ['Completed', 'status-completed'],
    'no_show'   => ['No Show', 'status-cancelled'],
    'cancelled' => ['Cancelled', 'status-cancelled'],
];
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        <?php $sidebarRole = 'staff'; $sidebarActive = 'jadwal_hari_ini'; include __DIR__ . '/../components/dashboard-sidebar.php'; ?>

        <div class="lg:col-span-3">
            <div class="bg-white/90 border border-[#eadfd4] rounded-3xl p-8 md:p-10 shadow-sm space-y-6">

                <div class="border-b border-[#eadfd4] pb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <span class="text-xs uppercase tracking-widest text-[#8a5d49] font-bold">Operasional Staf</span>
                        <h1 class="font-display text-3xl font-bold text-[#201913] mt-1">Jadwal Reservasi Hari Ini</h1>
                        <p class="text-sm text-[#66574b] mt-1"><?= date('l, d F Y') ?></p>
                    </div>
                    <a href="<?= route('checkin') ?>" class="bg-[#5e392e] hover:bg-[#4a2c24] text-white text-xs font-bold py-2.5 px-6 rounded-xl transition shadow-sm">
                        Validasi Kedatangan →
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table data-paginate class="w-full text-sm text-left text-[#4f4338]">
                        <thead>
                            <tr class="border-b border-[#eadfd4] text-[#8a5d49] text-xs uppercase tracking-wider">
                                <th class="py-3 pr-4">Waktu</th>
                                <th class="py-3 pr-4">Nama</th>
                                <th class="py-3 pr-4">Restoran</th>
                                <th class="py-3 pr-4">Kode</th>
                                <th class="py-3 pr-4">Tamu</th>
                                <th class="py-3 pr-4">Meja</th>
                                <th class="py-3 pr-4">No. Telp</th>
                                <th class="py-3 pr-4">Status</th>
                                <th class="py-3 pr-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jadwal as $j): ?>
                                <?php $st = $statusMap[$j['status'] ?? 'pending'] ?? $statusMap['pending']; ?>
                                <?php $kode = $j['booking_code'] ?? '-'; ?>
                                <tr class="border-b border-[#eadfd4] hover:bg-[#fcfaf7]">
                                    <td class="py-3 pr-4 font-bold text-[#201913]"><?= e(substr((string) ($j['reservation_time'] ?? ''), 0, 5)) ?></td>
                                    <td class="py-3 pr-4"><?= e($j['user']['name'] ?? 'Tamu') ?></td>
                                    <td class="py-3 pr-4 font-bold text-[#8a5d49]"><?= e($j['table']['restaurant']['name'] ?? '-') ?></td>
                                    <td class="py-3 pr-4 font-mono text-xs"><?= e($kode) ?></td>
                                    <td class="py-3 pr-4"><?= (int) ($j['number_of_guest'] ?? 0) ?> org</td>
                                    <td class="py-3 pr-4 font-bold text-[#8a5d49]"><?= e($j['table']['table_number'] ?? '-') ?></td>
                                    <td class="py-3 pr-4 font-mono text-xs"><?= e($j['user']['phone_number'] ?? '-') ?></td>
                                    <td class="py-3 pr-4"><span class="status-badge <?= $st[1] ?>"><?= e($st[0]) ?></span></td>
                                    <td class="py-3 pr-4">
                                        <?php if (($j['reservation_id'] ?? 0) > 0): ?>
                                            <a href="<?= route('checkin', ['kode' => $kode]) ?>" class="inline-block text-[11px] font-bold text-[#8a5d49] hover:underline">Check-in</a>
                                        <?php else: ?>
                                            <span class="text-[11px] text-[#a39a8f]">Demo</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
