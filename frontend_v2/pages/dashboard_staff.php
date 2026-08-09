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

$today = date('d M Y');

// Data statistik & jadwal demo (akan diganti dengan data API)
$stats = [
    ['label' => 'Reservasi Hari Ini', 'value' => 24, 'icon' => 'calendar'],
    ['label' => 'Tamu Sudah Check-in', 'value' => 9, 'icon' => 'check'],
    ['label' => 'Meja Terisi', 'value' => 11, 'icon' => 'table'],
    ['label' => 'Waiting List', 'value' => 3, 'icon' => 'users'],
];

$jadwalHariIni = [
    ['12:00', 'Budi Santoso', 4, 'T12', 'confirmed'],
    ['12:30', 'Siti Rahma', 2, 'T05', 'confirmed'],
    ['13:00', 'Andi Wijaya', 6, 'T08', 'pending'],
    ['18:00', 'Rina Kartika', 8, 'VIP1', 'confirmed'],
    ['18:30', 'Dewi Lestari', 3, 'T03', 'pending'],
    ['19:00', 'Bima Pratama', 5, 'T10', 'confirmed'],
];
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        <?php $sidebarRole = 'staff'; $sidebarActive = 'dashboard_staff'; include __DIR__ . '/../components/dashboard-sidebar.php'; ?>

        <div class="lg:col-span-3 space-y-6">

            <div class="bg-[#5e392e] rounded-3xl p-8 text-white shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <span class="text-xs uppercase tracking-widest text-[#e8c39e] font-bold">Dashboard Operasional</span>
                    <h1 class="font-display text-3xl font-bold mt-1">Selamat Datang, <?= e($_SESSION['user_name'] ?? 'Staff') ?></h1>
                    <p class="text-sm text-[#e8c39e] mt-1">Ringkasan operasional restoran hari ini &middot; <?= e($today) ?></p>
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
                    <table class="w-full text-sm text-left text-[#4f4338]">
                        <thead>
                            <tr class="border-b border-[#eadfd4] text-[#8a5d49] text-xs uppercase tracking-wider">
                                <th class="py-3 pr-4">Waktu</th>
                                <th class="py-3 pr-4">Nama</th>
                                <th class="py-3 pr-4">Tamu</th>
                                <th class="py-3 pr-4">Meja</th>
                                <th class="py-3 pr-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jadwalHariIni as $j): ?>
                                <tr class="border-b border-[#eadfd4]">
                                    <td class="py-3 pr-4 font-bold text-[#201913]"><?= e($j[0]) ?></td>
                                    <td class="py-3 pr-4"><?= e($j[1]) ?></td>
                                    <td class="py-3 pr-4"><?= e($j[2]) ?> org</td>
                                    <td class="py-3 pr-4 font-bold text-[#8a5d49]"><?= e($j[3]) ?></td>
                                    <td class="py-3 pr-4">
                                        <span class="status-badge <?= $j[4] === 'confirmed' ? 'status-confirmed' : 'status-pending' ?>">
                                            <?= e(ucfirst($j[4])) ?>
                                        </span>
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
