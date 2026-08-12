<?php
/**
 * components/dashboard-sidebar.php — Sidebar Navigasi Dashboard (Customer / Staff / Admin)
 *
 * Penggunaan: set variabel $sidebarRole = 'customer' | 'staff' | 'admin'
 * dan $sidebarActive = slug halaman aktif sebelum include file ini.
 */
$sidebarRole   = $sidebarRole   ?? 'customer';
$sidebarActive = $sidebarActive ?? '';
$userRoleLabel = ucfirst($sidebarRole);

$customerMenu = [
    'dashboard'         => ['Dashboard', 'home'],
    'preview_restoran'  => ['Preview Restoran', 'dashboard'],
    'riwayat_reservasi' => ['Riwayat Reservasi', 'riwayat_reservasi'],
    'menu'              => ['Menu', 'menu'],
];

$staffMenu = [
    'dashboard_staff'   => ['Dashboard Operasional Staf', 'dashboard_staff'],
    'denah_meja'        => ['Denah Meja Real-Time', 'denah_meja'],
    'jadwal_hari_ini'   => ['Jadwal Hari Ini', 'jadwal_hari_ini'],
    'checkin'           => ['Check-in Tamu', 'checkin'],
    'walkin'            => ['Walk-in / Waiting List', 'walkin'],
];

$adminMenu = [
    'dashboard_admin'   => ['Dashboard Admin', 'dashboard_admin'],
    'kelola_meja'       => ['Kelola Meja & Layout', 'kelola_meja'],
    'kelola_menu'       => ['Kelola Menu & Kategori', 'kelola_menu'],
    'kelola_restoran'   => ['Kelola Restoran', 'kelola_restoran'],
    'kelola_reservasi'  => ['Kelola Reservasi', 'kelola_reservasi'],
    'jam_operasional'   => ['Jam Operasional', 'jam_operasional'],
    'kebijakan'         => ['Deposit & Refund', 'kebijakan'],
    'kelola_staf'       => ['Kelola Akun Staf', 'kelola_staf'],
    'laporan'           => ['Laporan & Analitik', 'laporan'],
];

$menuItems = match ($sidebarRole) {
    'staff' => $staffMenu,
    'admin' => $adminMenu,
    default => $customerMenu,
};
?>

<aside class="lg:col-span-1 space-y-3">
    <div class="bg-white/80 border border-[#eadfd4] rounded-2xl p-4 shadow-sm space-y-2">
        <div class="px-4 py-2 mb-1">
            <span class="text-[10px] uppercase tracking-widest font-bold text-[#8a5d49]">Menu <?= e($userRoleLabel) ?></span>
        </div>

        <?php foreach ($menuItems as $slug => [$label, $icon]): ?>
            <?php
            $active = $sidebarActive === $slug;
            $class  = $active
                ? 'bg-[#5e392e] text-white shadow-sm'
                : 'text-[#5e392e] hover:bg-[#efebe4]';
            $href   = route($slug);
            ?>
            <a href="<?= e($href) ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-sm transition <?= $class ?>">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <?php if ($icon === 'home'): ?>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    <?php elseif ($icon === 'dashboard'): ?>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    <?php elseif ($icon === 'reservasi' || $icon === 'kelola_reservasi'): ?>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    <?php elseif ($icon === 'riwayat_reservasi'): ?>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    <?php elseif ($icon === 'menu' || $icon === 'kelola_menu'): ?>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    <?php elseif ($icon === 'denah_meja' || $icon === 'kelola_meja'): ?>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h2m4 0h2m-8 4h6a4 4 0 004-4v-1a4 4 0 00-4-4H7a4 4 0 00-4 4v1a4 4 0 004 4z"/>
                    <?php elseif ($icon === 'jadwal_hari_ini'): ?>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    <?php elseif ($icon === 'checkin'): ?>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    <?php elseif ($icon === 'walkin'): ?>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-4-4 4 4 0 004 4z"/>
                    <?php elseif ($icon === 'jam_operasional'): ?>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    <?php elseif ($icon === 'kebijakan'): ?>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    <?php elseif ($icon === 'kelola_staf'): ?>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87m-10-1.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-4-4 4 4 0 004 4z"/>
                    <?php elseif ($icon === 'laporan'): ?>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    <?php elseif ($icon === 'kelola_restoran'): ?>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M4 21V9l8-6 8 6v12M9 21v-6h6v6"/>
                    <?php endif; ?>
                </svg>
                <?= e($label) ?>
            </a>
        <?php endforeach; ?>
    </div>
</aside>
