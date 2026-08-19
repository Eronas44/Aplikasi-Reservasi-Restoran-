<?php
// pages/dashboard_staff.php — Dashboard Operasional Staf / Resepsionis

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
$todayFormatted = date('d M Y');

// Ambil semua reservasi
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

// Ambil waiting list
$waitingList = [];
$waitResult = api_get(API_WAITING_LIST);
if ($waitResult['ok']) {
    // Response bisa paginated atau plain array
    $outer = $waitResult['data']['data'] ?? [];
    $waitingList = $outer['data'] ?? (is_array($outer) ? $outer : []);
}

// Hitung statistik
$reservasiHariIni = 0;
$tamuCheckedIn = 0;
$jadwalHariIni = [];

foreach ($allReservations as $res) {
    $resDate = substr((string) ($res['reservation_date'] ?? ''), 0, 10);
    
    // Hitung reservasi hari ini
    if ($resDate === $today) {
        $reservasiHariIni++;
        
        // Tambahkan ke jadwal hari ini
        $jadwalHariIni[] = $res;
        
        // Hitung tamu yang sudah check-in (status completed)
        if (($res['status'] ?? '') === 'completed') {
            $tamuCheckedIn++;
        }
    }
}

// Sort jadwal berdasarkan waktu
usort($jadwalHariIni, function($a, $b) {
    return strcmp($a['reservation_time'] ?? '', $b['reservation_time'] ?? '');
});

// Hitung meja terisi
$mejaTerisi = 0;
foreach ($allTables as $table) {
    if (in_array(($table['status'] ?? ''), ['occupied', 'reserved'], true)) {
        $mejaTerisi++;
    }
}

// Hitung waiting list aktif
$waitingListCount = 0;
foreach ($waitingList as $w) {
    if (($w['status'] ?? '') === 'waiting') {
        $waitingListCount++;
    }
}

$stats = [
    ['label' => 'Reservasi Hari Ini', 'value' => $reservasiHariIni, 'icon' => 'calendar'],
    ['label' => 'Tamu Sudah Check-in', 'value' => $tamuCheckedIn, 'icon' => 'check'],
    ['label' => 'Meja Terisi', 'value' => $mejaTerisi, 'icon' => 'table'],
    ['label' => 'Waiting List', 'value' => $waitingListCount, 'icon' => 'users'],
];

// Batasi jadwal hanya 6 teratas
$jadwalHariIni = array_slice($jadwalHariIni, 0, 6);
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        <?php $sidebarRole = 'staff'; $sidebarActive = 'dashboard_staff'; include __DIR__ . '/../components/dashboard-sidebar.php'; ?>

        <div class="lg:col-span-3 space-y-6">

            <div class="bg-[#5e392e] rounded-3xl p-8 text-white shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <span class="text-xs uppercase tracking-widest text-[#e8c39e] font-bold">Dashboard Operasional</span>
                    <h1 class="font-display text-3xl font-bold mt-1">Selamat Datang, <?= e($_SESSION['user_name'] ?? 'Staff') ?></h1>
                    <p class="text-sm text-[#e8c39e] mt-1">Ringkasan operasional restoran hari ini &middot; <?= e($todayFormatted) ?></p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="<?= route('denah_meja') ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white text-[#5e392e] font-bold text-xs shadow-sm transition hover:bg-[#efebe4]">
                        Denah Meja
                    </a>
                    <a href="<?= route('checkin') ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-white/40 text-white font-bold text-xs transition hover:bg-white/10">
                        Check-in Tamu
                    </a>
                </div>
            </div>

            <!-- Stat Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <?php foreach ($stats as $s): ?>
                    <div class="bg-white/80 border border-[#eadfd4] rounded-2xl p-5 shadow-sm">
                        <span class="text-[10px] uppercase tracking-widest font-bold text-[#8a5d49]"><?= e($s['label']) ?></span>
                        <p class="font-display text-3xl font-bold text-[#201913] mt-2"><?= (int) $s['value'] ?></p>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Jadwal Hari Ini -->
            <div class="bg-white/90 border border-[#eadfd4] rounded-3xl p-6 md:p-8 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="font-display text-2xl font-bold text-[#201913]">Jadwal Reservasi Hari Ini</h2>
                    <a href="<?= route('jadwal_hari_ini') ?>" class="text-xs font-bold text-[#8a5d49] hover:underline">Lihat Semua →</a>
                </div>
                <div class="overflow-x-auto">
                    <table data-paginate class="w-full text-sm text-left text-[#4f4338]">
                        <thead>
                            <tr class="border-b border-[#eadfd4] text-[#8a5d49] text-xs uppercase tracking-wider">
                                <th class="py-3 pr-4">Waktu</th>
                                <th class="py-3 pr-4">Nama</th>
                                <th class="py-3 pr-4">Restoran</th>
                                <th class="py-3 pr-4">Tamu</th>
                                <th class="py-3 pr-4">Meja</th>
                                <th class="py-3 pr-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jadwalHariIni as $j): ?>
                                <tr class="border-b border-[#eadfd4]">
                                    <td class="py-3 pr-4 font-bold text-[#201913]"><?= e(substr((string) ($j['reservation_time'] ?? ''), 0, 5)) ?></td>
                                    <td class="py-3 pr-4"><?= e($j['user']['name'] ?? 'Tamu') ?></td>
                                    <td class="py-3 pr-4 font-bold text-[#8a5d49]"><?= e($j['table']['restaurant']['name'] ?? '-') ?></td>
                                    <td class="py-3 pr-4"><?= e((int) ($j['number_of_guest'] ?? 0)) ?> org</td>
                                    <td class="py-3 pr-4 font-bold text-[#8a5d49]"><?= e($j['table']['table_number'] ?? '-') ?></td>
                                    <td class="py-3 pr-4">
                                        <?php 
                                        $status = $j['status'] ?? 'pending';
                                        $statusClass = $status === 'confirmed' ? 'status-confirmed' : 'status-pending';
                                        if ($status === 'completed') $statusClass = 'status-completed';
                                        ?>
                                        <span class="status-badge <?= $statusClass ?>">
                                            <?= e(ucfirst(str_replace('_', ' ', $status))) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($jadwalHariIni)): ?>
                                <tr>
                                    <td colspan="6" class="py-6 text-center text-sm text-[#8a5d49]">Tidak ada jadwal reservasi hari ini</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
