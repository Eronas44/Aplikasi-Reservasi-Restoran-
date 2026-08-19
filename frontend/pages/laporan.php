<?php
// pages/laporan.php — Laporan & Analitik (Admin)
// Terhubung ke backend: GET /reservations, /payments, /tables

$isLoggedIn = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
$role = $_SESSION['role'] ?? 'customer';
if (!$isLoggedIn || $role !== 'admin') {
    header('Location: ' . route('login'));
    exit;
}

require_once __DIR__ . '/../src/config/api.config.php';
require_once __DIR__ . '/../src/utils/api.php';

$filterFrom = $_GET['from'] ?? date('Y-m-d', strtotime('-30 days'));
$filterTo = $_GET['to'] ?? date('Y-m-d');
if (isset($_GET['range']) && $_GET['range'] === '7d') {
    $filterFrom = date('Y-m-d', strtotime('-7 days'));
} elseif (isset($_GET['range']) && $_GET['range'] === 'today') {
    $filterFrom = date('Y-m-d');
}

// Ambil daftar restoran untuk filter
$restaurants = [];
$restoResult = api_get(API_RESTAURANTS . '?limit=200');
if ($restoResult['ok']) {
    $raw = $restoResult['data']['data'] ?? [];
    $restaurants = $raw['data'] ?? $raw;
}
$restoFilter = (int) ($_GET['restaurant_id'] ?? 0);
$selectedRestoName = 'Semua Restoran';
foreach ($restaurants as $resto) {
    if ((int) ($resto['restaurant_id'] ?? 0) === $restoFilter) {
        $selectedRestoName = $resto['name'] ?? 'Restoran';
        break;
    }
}

// Ambil data reservasi, pembayaran, dan tabel dari backend
$reservations = [];
$resResult = api_get(API_RESERVATIONS . '?limit=500');
if ($resResult['ok']) {
    $raw = $resResult['data']['data'] ?? [];
    $reservations = $raw['data'] ?? $raw;
}

$payments = [];
$payResult = api_get(API_PAYMENTS . '?limit=500');
if ($payResult['ok']) {
    $raw = $payResult['data']['data'] ?? [];
    $payments = $raw['data'] ?? $raw;
}

$totalTables = 20;
$tablesResult = api_get(API_TABLES . '?limit=500');
if ($tablesResult['ok']) {
    $raw = $tablesResult['data']['data'] ?? [];
    $tableList = $raw['data'] ?? $raw;
    if (!empty($tableList)) {
        $totalTables = count($tableList);
    }
}

// Filter sesuai rentang tanggal & restoran
$filtered = array_filter($reservations, function ($r) use ($filterFrom, $filterTo, $restoFilter) {
    $date = substr((string) ($r['reservation_date'] ?? ''), 0, 10);
    if ($date < $filterFrom || $date > $filterTo) {
        return false;
    }
    if ($restoFilter > 0 && (int) ($r['table']['restaurant']['restaurant_id'] ?? 0) !== $restoFilter) {
        return false;
    }
    return true;
});

$filteredPayments = array_filter($payments, function ($p) use ($filterFrom, $filterTo) {
    $date = substr((string) ($p['paid_at'] ?? $p['created_at'] ?? ''), 0, 10);
    return $date === '' || ($date >= $filterFrom && $date <= $filterTo);
});

// Metrik
$totalReservations = count($filtered);
$totalPaid = count(array_filter($filtered, fn ($r) => ($r['payment_status'] ?? '') === 'paid'));
$noShow = count(array_filter($filtered, fn ($r) => ($r['status'] ?? '') === 'no_show'));
$completed = count(array_filter($filtered, fn ($r) => ($r['status'] ?? '') === 'completed'));

$revenue = 0.0;
foreach ($filtered as $r) {
    if (($r['payment_status'] ?? '') === 'paid') {
        $revenue += (float) ($r['deposit_amount'] ?? 0);
    }
}
foreach ($filteredPayments as $p) {
    if (($p['status'] ?? '') === 'success' || ($p['status'] ?? '') === 'paid') {
        $revenue += (float) ($p['amount'] ?? 0);
    }
}

// Peak hours
$hourCounts = [];
foreach ($filtered as $r) {
    $t = (string) ($r['reservation_time'] ?? '');
    $hour = substr($t, 0, 5);
    if ($hour !== '') {
        $hourCounts[$hour] = ($hourCounts[$hour] ?? 0) + 1;
    }
}
arsort($hourCounts);
$peakHour = $hourCounts ? key($hourCounts) . ' - ' . (date('H:i', strtotime(key($hourCounts) . ':00') + 7200)) : '--';
$peakLabel = $hourCounts ? 'Slot paling ramai' : 'Belum ada data';

$daysSpan = max(1, (int) ((strtotime($filterTo) - strtotime($filterFrom)) / 86400) + 1);
$occupancy = $totalReservations > 0 && $totalTables > 0
    ? round((($totalReservations / $daysSpan) / $totalTables) * 100, 1)
    : 0;
$noShowRate = $totalReservations > 0 ? round(($noShow / $totalReservations) * 100, 1) : 0;

$revenueText = 'Rp ' . number_format($revenue, 0, ',', '.');
$occupancyText = $occupancy . '%';
$noShowText = $noShowRate . '%';

// Data grafik: jumlah reservasi per 10 hari terakhir
$chartDays = [];
for ($i = 9; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime($filterTo . " -$i days"));
    $count = count(array_filter($filtered, fn ($r) => substr((string) ($r['reservation_date'] ?? ''), 0, 10) === $d));
    $chartDays[] = ['label' => date('d M', strtotime($d)), 'count' => $count];
}
$maxCount = max(1, max(array_column($chartDays, 'count')));
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        <?php $sidebarRole = 'admin'; $sidebarActive = 'laporan'; include __DIR__ . '/../components/dashboard-sidebar.php'; ?>

        <div class="lg:col-span-3 space-y-6">
            <div class="bg-white/90 border border-[#eadfd4] rounded-3xl p-8 md:p-10 shadow-sm space-y-6">

                <div class="border-b border-[#eadfd4] pb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <span class="text-xs uppercase tracking-widest text-[#8a5d49] font-bold">Insight Restoran</span>
                        <h1 class="font-display text-3xl font-bold text-[#201913] mt-1">Laporan & Analitik</h1>
                        <p class="text-sm text-[#66574b] mt-1">Ringkasan performa berdasarkan data reservasi & pembayaran (FR-012).</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="export_laporan.php?type=pdf&from=<?= urlencode($filterFrom) ?>&to=<?= urlencode($filterTo) ?>&restaurant_id=<?= $restoFilter ?>" class="px-4 py-2 rounded-xl bg-[#5e392e] hover:bg-[#4a2c24] text-white text-xs font-bold transition shadow-sm">Export PDF</a>
                        <a href="export_laporan.php?type=excel&from=<?= urlencode($filterFrom) ?>&to=<?= urlencode($filterTo) ?>&restaurant_id=<?= $restoFilter ?>" class="px-4 py-2 rounded-xl border border-[#eadfd4] text-stone-600 hover:bg-stone-50 text-xs font-bold transition">Export Excel</a>
                    </div>
                </div>

                <!-- Pilih Rentang Tanggal -->
                <form method="GET" action="<?= route('laporan') ?>" class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-5 flex flex-col md:flex-row md:items-end gap-4">
                    <!-- Browser mengganti query string pada action saat form GET
                         dikirim. Simpan page secara eksplisit agar tidak kembali
                         ke halaman default/dashboard. -->
                    <input type="hidden" name="page" value="laporan">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Restoran</label>
                        <select name="restaurant_id" onchange="this.form.submit()" class="px-4 py-2.5 rounded-xl border border-[#eadfd4] bg-white text-sm outline-none focus:border-[#8a5d49] transition">
                            <option value="">Semua Restoran</option>
                            <?php foreach ($restaurants as $resto): ?>
                                <?php $rid = (int) ($resto['restaurant_id'] ?? 0); ?>
                                <option value="<?= $rid ?>" <?= $rid === $restoFilter ? 'selected' : '' ?>>
                                    <?= e($resto['name'] ?? $resto['restaurant_name'] ?? 'Restoran') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Dari</label>
                        <input type="date" name="from" value="<?= e($filterFrom) ?>" class="px-4 py-2.5 rounded-xl border border-[#eadfd4] bg-white text-sm outline-none focus:border-[#8a5d49] transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Sampai</label>
                        <input type="date" name="to" value="<?= e($filterTo) ?>" class="px-4 py-2.5 rounded-xl border border-[#eadfd4] bg-white text-sm outline-none focus:border-[#8a5d49] transition">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="bg-[#5e392e] hover:bg-[#4a2c24] text-white text-xs font-bold py-2.5 px-6 rounded-xl transition shadow-sm">Tampilkan</button>
                        <a href="<?= route('laporan', ['range' => '7d', 'restaurant_id' => $restoFilter ?: null]) ?>" class="px-4 py-2.5 rounded-xl border border-[#eadfd4] text-stone-600 hover:bg-stone-50 text-xs font-bold transition">7 Hari</a>
                    </div>
                </form>

                <!-- Metrik -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-5">
                        <span class="text-[10px] uppercase tracking-widest font-bold text-[#8a5d49]">Pendapatan Deposit</span>
                        <p class="font-display text-2xl font-bold text-[#201913] mt-1"><?= e($revenueText) ?></p>
                        <span class="text-xs font-semibold text-green-700"><?= $totalPaid ?> pembayaran lunas</span>
                    </div>
                    <div class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-5">
                        <span class="text-[10px] uppercase tracking-widest font-bold text-[#8a5d49]">Peak Hours</span>
                        <p class="font-display text-2xl font-bold text-[#201913] mt-1"><?= e($peakHour) ?></p>
                        <span class="text-xs font-semibold text-green-700"><?= e($peakLabel) ?></span>
                    </div>
                    <div class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-5">
                        <span class="text-[10px] uppercase tracking-widest font-bold text-[#8a5d49]">Occupancy Rate</span>
                        <p class="font-display text-2xl font-bold text-[#201913] mt-1"><?= e($occupancyText) ?></p>
                        <span class="text-xs font-semibold text-green-700"><?= $totalReservations ?> reservasi dalam rentang</span>
                    </div>
                    <div class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-5">
                        <span class="text-[10px] uppercase tracking-widest font-bold text-[#8a5d49]">Tingkat No-show</span>
                        <p class="font-display text-2xl font-bold text-[#201913] mt-1"><?= e($noShowText) ?></p>
                        <span class="text-xs font-semibold text-green-700"><?= $noShow ?> no-show, <?= $completed ?> kedatangan</span>
                    </div>
                </div>

                <!-- Grafik -->
                <div class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-6">
                    <h2 class="font-display text-lg font-bold text-[#201913] mb-4">Tren Reservasi — 10 Hari Terakhir</h2>
                    <div class="flex items-end gap-2 h-40">
                        <?php foreach ($chartDays as $d): ?>
                            <div class="flex-1 flex flex-col items-center justify-end gap-1 h-full">
                                <span class="text-[10px] font-bold text-[#8a5d49]"><?= $d['count'] ?></span>
                                <div class="w-full bg-gradient-to-t from-[#5e392e] to-[#8a5d49] rounded-t-lg" style="height: <?= max(4, round(($d['count'] / $maxCount) * 100)) ?>%"></div>
                                <span class="text-[9px] text-[#a39a8f] truncate"><?= e($d['label']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
