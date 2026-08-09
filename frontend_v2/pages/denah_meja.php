<?php
// pages/denah_meja.php — Denah Meja Real-Time (FR-009, terhubung ke GET /tables)

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

$tables = [];
$tablesResult = api_get(API_TABLES . '?limit=100');
if ($tablesResult['ok']) {
    $raw = $tablesResult['data']['data'] ?? [];
    $tables = $raw['data'] ?? $raw;
}
// Fallback demo bila backend tidak tersedia
if (empty($tables)) {
    $demo = [
        ['table_number' => 'T01', 'capacity' => 2, 'location_area' => 'indoor',   'status' => 'available'],
        ['table_number' => 'T02', 'capacity' => 2, 'location_area' => 'indoor',   'status' => 'occupied'],
        ['table_number' => 'T03', 'capacity' => 4, 'location_area' => 'indoor',   'status' => 'reserved'],
        ['table_number' => 'T04', 'capacity' => 4, 'location_area' => 'indoor',   'status' => 'available'],
        ['table_number' => 'T05', 'capacity' => 2, 'location_area' => 'indoor',   'status' => 'available'],
        ['table_number' => 'T06', 'capacity' => 6, 'location_area' => 'indoor',   'status' => 'occupied'],
        ['table_number' => 'T07', 'capacity' => 4, 'location_area' => 'indoor',   'status' => 'maintenance'],
        ['table_number' => 'T08', 'capacity' => 6, 'location_area' => 'outdoor',  'status' => 'reserved'],
        ['table_number' => 'T09', 'capacity' => 2, 'location_area' => 'outdoor',  'status' => 'available'],
        ['table_number' => 'T10', 'capacity' => 4, 'location_area' => 'outdoor',  'status' => 'occupied'],
        ['table_number' => 'T11', 'capacity' => 8, 'location_area' => 'vip',      'status' => 'reserved'],
        ['table_number' => 'T12', 'capacity' => 8, 'location_area' => 'vip',      'status' => 'available'],
        ['table_number' => 'T13', 'capacity' => 4, 'location_area' => 'smoking',  'status' => 'available'],
        ['table_number' => 'T14', 'capacity' => 4, 'location_area' => 'smoking',  'status' => 'occupied'],
        ['table_number' => 'T15', 'capacity' => 6, 'location_area' => 'outdoor',  'status' => 'available'],
        ['table_number' => 'T16', 'capacity' => 6, 'location_area' => 'outdoor',  'status' => 'maintenance'],
    ];
    foreach ($demo as $i => $d) {
        $tables[] = array_merge($d, ['table_id' => 0, 'pos_row' => intdiv($i, 4) + 1, 'pos_col' => (($i % 4) * 2) + 1]);
    }
} else {
    // Susun posisi grid otomatis dari urutan daftar
    foreach ($tables as $i => $t) {
        $tables[$i]['pos_row'] = intdiv($i, 4) + 1;
        $tables[$i]['pos_col'] = (($i % 4) * 2) + 1;
    }
}

$statusInfo = [
    'available'   => ['Kosong', 'bg-green-100 text-green-700 border-green-300'],
    'reserved'    => ['Dipesan', 'bg-amber-100 text-amber-700 border-amber-300'],
    'occupied'    => ['Terisi', 'bg-red-100 text-red-700 border-red-300'],
    'maintenance' => ['Dibersihkan', 'bg-blue-100 text-blue-700 border-blue-300'],
];

$zonaInfo = [
    'indoor'  => ['Indoor', 'text-[#5e392e]'],
    'outdoor' => ['Outdoor', 'text-[#8a5d49]'],
    'vip'     => ['VIP', 'text-[#b45309]'],
    'smoking' => ['Smoking', 'text-[#6b7280]'],
];
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        <?php $sidebarRole = 'staff'; $sidebarActive = 'denah_meja'; include __DIR__ . '/../components/dashboard-sidebar.php'; ?>

        <div class="lg:col-span-3">
            <div class="bg-white/90 border border-[#eadfd4] rounded-3xl p-8 md:p-10 shadow-sm space-y-6">

                <div class="border-b border-[#eadfd4] pb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <span class="text-xs uppercase tracking-widest text-[#8a5d49] font-bold">Real-Time Floor Plan</span>
                        <h1 class="font-display text-3xl font-bold text-[#201913] mt-1">Denah Meja Restoran</h1>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($statusInfo as $key => [$label, $cls]): ?>
                            <span class="status-badge <?= $cls ?>"><?= e($label) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Legenda Zona -->
                <div class="flex flex-wrap gap-3 text-xs">
                    <?php foreach ($zonaInfo as $key => [$label, $cls]): ?>
                        <span class="font-bold <?= $cls ?>">■ <?= e($label) ?></span>
                    <?php endforeach; ?>
                </div>

                <!-- Grid Denah -->
                <div class="grid grid-cols-8 gap-4">
                    <?php foreach ($tables as $t): ?>
                        <?php
                        $st = $statusInfo[$t['status']] ?? $statusInfo['available'];
                        $zn = $zonaInfo[$t['location_area']] ?? ['', ''];
                        ?>
                        <div class="col-start-<?= (int) ($t['pos_col'] ?? 1) ?> col-span-1 row-start-<?= (int) ($t['pos_row'] ?? 1) ?>">
                            <div class="rounded-2xl border-2 <?= $st[1] ?> p-3 text-center shadow-sm h-full flex flex-col items-center justify-center gap-0.5">
                                <span class="text-lg font-extrabold text-[#201913]"><?= e($t['table_number']) ?></span>
                                <span class="text-[10px] font-bold <?= $zn[1] ?>"><?= e($zn[0]) ?></span>
                                <span class="text-[10px] text-[#66574b]"><?= (int) ($t['capacity'] ?? 0) ?> org</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="flex flex-wrap gap-3 pt-2">
                    <a href="<?= route('checkin') ?>" class="bg-[#5e392e] hover:bg-[#4a2c24] text-white text-xs font-bold py-2.5 px-6 rounded-xl transition shadow-sm">
                        Check-in Tamu
                    </a>
                    <a href="<?= route('walkin') ?>" class="px-5 py-2.5 rounded-xl border border-[#eadfd4] text-stone-600 hover:bg-stone-50 text-xs font-bold transition">
                        Alokasikan Meja Walk-in
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
