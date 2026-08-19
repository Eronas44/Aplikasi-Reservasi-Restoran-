<?php
// pages/dashboard_admin.php — Dashboard Admin Restoran

$isLoggedIn = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
$role = $_SESSION['role'] ?? 'customer';
if (!$isLoggedIn) {
    header('Location: ' . route('login'));
    exit;
}
if ($role !== 'admin') {
    header('Location: ' . route('dashboard'));
    exit;
}

require_once __DIR__ . '/../src/config/api.config.php';
require_once __DIR__ . '/../src/utils/api.php';

// Ambil semua reservasi dari backend
$allReservations = [];
$resResult = api_get(API_RESERVATIONS . '?limit=500');
if ($resResult['ok']) {
    // Response paginated: { data: { data: [...], current_page, ... } }
    $outer = $resResult['data']['data'] ?? [];
    $allReservations = $outer['data'] ?? (is_array($outer) && !isset($outer['data']) ? $outer : []);
}

// Ambil data meja
$allTables = [];
$tablesResult = api_get(API_TABLES . '?limit=200');
if ($tablesResult['ok']) {
    // Response paginated: { data: { data: [...], ... } }
    $outer = $tablesResult['data']['data'] ?? [];
    $allTables = $outer['data'] ?? (is_array($outer) && !isset($outer['data']) ? $outer : []);
}

$today = date('Y-m-d');

// Hitung statistik
$totalRevenue = 0;
$reservasiHariIni = 0;
$totalReservations = count($allReservations);
$totalNoShow = 0;
$totalCompleted = 0;

foreach ($allReservations as $res) {
    // Hitung total pendapatan dari reservasi yang completed
    if (($res['status'] ?? '') === 'completed') {
        $totalRevenue += (float) ($res['total_price'] ?? 0);
        $totalCompleted++;
    }
    
    // Hitung reservasi hari ini
    $resDate = substr((string) ($res['reservation_date'] ?? ''), 0, 10);
    if ($resDate === $today) {
        $reservasiHariIni++;
    }
    
    // Hitung no-show
    if (($res['status'] ?? '') === 'no_show') {
        $totalNoShow++;
    }
}

// Hitung occupancy rate
$totalTables = count($allTables);
$occupiedTables = 0;
foreach ($allTables as $table) {
    if (in_array(($table['status'] ?? ''), ['occupied', 'reserved'], true)) {
        $occupiedTables++;
    }
}
$occupancyRate = $totalTables > 0 ? round(($occupiedTables / $totalTables) * 100) : 0;

// Hitung tingkat no-show (persentase dari total reservasi)
$noShowRate = $totalReservations > 0 ? round(($totalNoShow / $totalReservations) * 100) : 0;

// Format total pendapatan
$revenueFormatted = 'Rp ' . number_format($totalRevenue / 1000000, 1, ',', '.') . 'jt';

$stats = [
    ['label' => 'Total Pendapatan', 'value' => $revenueFormatted, 'icon' => 'money'],
    ['label' => 'Reservasi Hari Ini', 'value' => (string) $reservasiHariIni, 'icon' => 'calendar'],
    ['label' => 'Occupancy Rate', 'value' => $occupancyRate . '%', 'icon' => 'table'],
    ['label' => 'Tingkat No-show', 'value' => $noShowRate . '%', 'icon' => 'alert'],
];

// Ambil 5 reservasi terbaru (berdasarkan created_at atau reservation_id)
$recentReservations = [];
$sortedReservations = $allReservations;

// Sort berdasarkan reservation_id descending (terbaru dulu)
usort($sortedReservations, function($a, $b) {
    return ($b['reservation_id'] ?? 0) - ($a['reservation_id'] ?? 0);
});

// Ambil 5 teratas
$recentReservations = array_slice($sortedReservations, 0, 5);
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        <?php $sidebarRole = 'admin'; $sidebarActive = 'dashboard_admin'; include __DIR__ . '/../components/dashboard-sidebar.php'; ?>

        <div class="lg:col-span-3 space-y-6">

            <div class="bg-[#5e392e] rounded-3xl p-8 text-white shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <span class="text-xs uppercase tracking-widest text-[#e8c39e] font-bold">Dashboard Admin</span>
                    <h1 class="font-display text-3xl font-bold mt-1">Selamat Datang, <?= e($_SESSION['user_name'] ?? 'Admin') ?></h1>
                    <p class="text-sm text-[#e8c39e] mt-1">Kelola seluruh operasional restoran dari satu tempat.</p>
                </div>
                <a href="<?= route('laporan') ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white text-[#5e392e] font-bold text-xs shadow-sm transition hover:bg-[#efebe4]">
                    Lihat Laporan & Analitik
                </a>
            </div>

            <!-- Stat Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <?php foreach ($stats as $s): ?>
                    <div class="bg-white/80 border border-[#eadfd4] rounded-2xl p-5 shadow-sm">
                        <span class="text-[10px] uppercase tracking-widest font-bold text-[#8a5d49]"><?= e($s['label']) ?></span>
                        <p class="font-display text-2xl font-bold text-[#201913] mt-2"><?= e($s['value']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Reservasi Terbaru -->
            <div class="bg-white/90 border border-[#eadfd4] rounded-3xl p-6 md:p-8 shadow-sm space-y-4">
                <h2 class="font-display text-2xl font-bold text-[#201913]">Reservasi Terbaru</h2>
                <div class="overflow-x-auto">
                    <table data-paginate class="w-full text-sm text-left text-[#4f4338]">
                        <thead>
                            <tr class="border-b border-[#eadfd4] text-[#8a5d49] text-xs uppercase tracking-wider">
                                <th class="py-3 pr-4">Kode</th>
                                <th class="py-3 pr-4">Nama</th>
                                <th class="py-3 pr-4">Restoran</th>
                                <th class="py-3 pr-4">Tanggal</th>
                                <th class="py-3 pr-4">Waktu</th>
                                <th class="py-3 pr-4">Tamu</th>
                                <th class="py-3 pr-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentReservations as $r): ?>
                                <tr class="border-b border-[#eadfd4]">
                                    <td class="py-3 pr-4 font-mono text-xs font-bold text-[#201913]"><?= e($r['booking_code'] ?? '-') ?></td>
                                    <td class="py-3 pr-4"><?= e($r['user']['name'] ?? 'Tamu') ?></td>
                                    <td class="py-3 pr-4 font-bold text-[#8a5d49]"><?= e($r['table']['restaurant']['name'] ?? '-') ?></td>
                                    <td class="py-3 pr-4"><?= e(substr((string) ($r['reservation_date'] ?? ''), 0, 10)) ?></td>
                                    <td class="py-3 pr-4"><?= e(substr((string) ($r['reservation_time'] ?? ''), 0, 5)) ?></td>
                                    <td class="py-3 pr-4"><?= e((int) ($r['number_of_guest'] ?? 0)) ?></td>
                                    <td class="py-3 pr-4">
                                        <?php 
                                        $status = $r['status'] ?? 'pending';
                                        $statusClass = 'pending';
                                        if ($status === 'completed') $statusClass = 'completed';
                                        elseif ($status === 'confirmed') $statusClass = 'confirmed';
                                        elseif (in_array($status, ['cancelled', 'no_show'], true)) $statusClass = 'cancelled';
                                        ?>
                                        <span class="status-badge status-<?= $statusClass ?>">
                                            <?= e(ucfirst(str_replace('_', ' ', $status))) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($recentReservations)): ?>
                                <tr>
                                    <td colspan="7" class="py-6 text-center text-sm text-[#8a5d49]">Belum ada reservasi</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
