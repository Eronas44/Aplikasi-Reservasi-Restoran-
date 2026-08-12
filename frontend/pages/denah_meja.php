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

// Pilih restoran (query ?resto=, fallback ke sesi / restoran pertama)
$restoId = (int) ($_GET['resto'] ?? $_SESSION['selected_resto_id'] ?? 0);

// Daftar restoran untuk dropdown pilihan
$restoList = [];
$restoListResult = api_get(API_RESTAURANTS . '?limit=100');
if ($restoListResult['ok']) {
    $raw = $restoListResult['data']['data'] ?? [];
    $restoList = $raw['data'] ?? (is_array($raw) && isset($raw[0]) ? $raw : []);
}
if ($restoId <= 0) {
    $restoId = (int) ($restoList[0]['restaurant_id'] ?? 1);
}
$_SESSION['selected_resto_id'] = $restoId;

$restoNama = 'Restoran';
$detail = api_get(API_RESTAURANTS . '/' . $restoId);
if ($detail['ok']) {
    $restoNama = $detail['data']['data']['name'] ?? $restoNama;
}

$tables = [];
$tablesResult = api_get(API_TABLES . '?restaurant_id=' . $restoId . '&limit=100');
if ($tablesResult['ok']) {
    $raw = $tablesResult['data']['data'] ?? [];
    $tables = $raw['data'] ?? $raw;
}

// Gunakan posisi yang tersimpan. Data lama tanpa posisi tetap mendapat
// posisi sementara supaya tetap tampil sampai diperbarui admin.
foreach ($tables as $i => $t) {
    $tables[$i]['pos_row'] = max(1, (int) ($t['layout_row'] ?? (intdiv($i, 4) + 1)));
    $tables[$i]['pos_col'] = min(8, max(1, (int) ($t['layout_column'] ?? (($i % 4) * 2 + 1))));
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
                        <p class="text-sm text-[#66574b] mt-1"><?= e($restoNama) ?></p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($statusInfo as $key => [$label, $cls]): ?>
                            <span class="status-badge <?= $cls ?>"><?= e($label) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Pilih Restoran -->
                <form method="GET" action="index.php" onchange="this.submit()" class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-4 flex flex-col md:flex-row md:items-center gap-4">
                    <input type="hidden" name="page" value="denah_meja">
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49]">Restoran</label>
                    <select name="resto" class="flex-1 px-4 py-2.5 rounded-xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
                        <?php foreach ($restoList as $r): ?>
                            <option value="<?= (int) ($r['restaurant_id'] ?? 0) ?>" <?= (int) ($r['restaurant_id'] ?? 0) === $restoId ? 'selected' : '' ?>>
                                <?= e($r['name'] ?? 'Restoran') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="bg-[#5e392e] hover:bg-[#4a2c24] text-white text-xs font-bold py-2.5 px-5 rounded-xl transition shadow-sm">
                        Muat Denah
                    </button>
                </form>

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
                        <div style="grid-column-start: <?= (int) ($t['pos_col'] ?? 1) ?>; grid-row-start: <?= (int) ($t['pos_row'] ?? 1) ?>;">
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
                    <button type="button" onclick="window.location.reload()" class="px-5 py-2.5 rounded-xl border border-[#eadfd4] text-stone-600 hover:bg-stone-50 text-xs font-bold transition">
                        Refresh Sekarang
                    </button>
                </div>

                <p class="text-[11px] text-[#66574b] pt-1">Denah memuat status meja dari database dan diperbarui otomatis setiap 30 detik.</p>

            </div>
        </div>
    </div>
</div>

<script>
// Perbarui otomatis agar status meja selalu real-time (data dari database)
(function () {
    setTimeout(function () {
        window.location.reload();
    }, 30000);
})();
</script>
