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

$stats = [
    ['label' => 'Total Pendapatan', 'value' => 'Rp 48,7jt', 'icon' => 'money'],
    ['label' => 'Reservasi Hari Ini', 'value' => '24', 'icon' => 'calendar'],
    ['label' => 'Occupancy Rate', 'value' => '72%', 'icon' => 'table'],
    ['label' => 'Tingkat No-show', 'value' => '8%', 'icon' => 'alert'],
];

$recentReservations = [
    ['KB-0001', 'Budi Santoso', '2026-08-09', '18:00', '4', 'confirmed'],
    ['KB-0002', 'Siti Rahma', '2026-08-09', '19:30', '2', 'completed'],
    ['KB-0003', 'Andi Wijaya', '2026-08-10', '12:00', '6', 'pending'],
    ['KB-0004', 'Rina Kartika', '2026-08-10', '20:00', '8', 'confirmed'],
];
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

            <!-- Menu Kelola Cepat -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <?php
                $quickActions = [
                    ['kelola_meja', 'Kelola Meja & Layout'],
                    ['kelola_menu', 'Kelola Menu & Kategori'],
                    ['kelola_reservasi', 'Kelola Reservasi'],
                    ['kelola_staf', 'Kelola Akun Staf'],
                    ['jam_operasional', 'Jam Operasional'],
                    ['kebijakan', 'Deposit & Refund'],
                    ['laporan', 'Laporan & Analitik'],
                ];
                ?>
                <?php foreach ($quickActions as [$slug, $label]): ?>
                    <a href="<?= route($slug) ?>" class="bg-white/80 border border-[#eadfd4] rounded-2xl p-5 shadow-sm hover:border-[#8a5d49] transition">
                        <span class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-1">Atur</span>
                        <span class="font-display font-bold text-[#201913]"><?= e($label) ?></span>
                        <span class="block mt-2 text-xs font-bold text-[#8a5d49]">Buka →</span>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Reservasi Terbaru -->
            <div class="bg-white/90 border border-[#eadfd4] rounded-3xl p-6 md:p-8 shadow-sm space-y-4">
                <h2 class="font-display text-2xl font-bold text-[#201913]">Reservasi Terbaru</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-[#4f4338]">
                        <thead>
                            <tr class="border-b border-[#eadfd4] text-[#8a5d49] text-xs uppercase tracking-wider">
                                <th class="py-3 pr-4">Kode</th>
                                <th class="py-3 pr-4">Nama</th>
                                <th class="py-3 pr-4">Tanggal</th>
                                <th class="py-3 pr-4">Waktu</th>
                                <th class="py-3 pr-4">Tamu</th>
                                <th class="py-3 pr-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentReservations as $r): ?>
                                <tr class="border-b border-[#eadfd4]">
                                    <td class="py-3 pr-4 font-mono text-xs font-bold text-[#201913]"><?= e($r[0]) ?></td>
                                    <td class="py-3 pr-4"><?= e($r[1]) ?></td>
                                    <td class="py-3 pr-4"><?= e($r[2]) ?></td>
                                    <td class="py-3 pr-4"><?= e($r[3]) ?></td>
                                    <td class="py-3 pr-4"><?= e($r[4]) ?></td>
                                    <td class="py-3 pr-4">
                                        <span class="status-badge status-<?= $r[5] === 'completed' ? 'completed' : ($r[5] === 'pending' ? 'pending' : 'confirmed') ?>">
                                            <?= e(ucfirst($r[5])) ?>
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
