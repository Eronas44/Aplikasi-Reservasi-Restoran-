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
    'menu_view'         => ['Preview Menu', 'menu'],
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

<aside id="dashboard-sidebar" class="lg:col-span-1 space-y-3">
    <div class="bg-white/80 border border-[#eadfd4] rounded-2xl p-4 shadow-sm space-y-2">
        <div class="flex items-center justify-between gap-2 px-2 pt-1 pb-3 mb-2 border-b border-[#eadfd4]">
            <a href="<?= e($sidebarRole === 'customer' ? route('dashboard') : route('home')) ?>" class="flex items-center gap-3 min-w-0">
                <img src="assets/images/kafiber.png" alt="Logo Kafiber" class="w-11 h-11 object-cover rounded-full bg-white border border-[#eadfd4]">
                <span class="font-display italic font-bold text-lg text-[#5e392e] leading-none">Kafiber</span>
            </a>
            <button type="button" class="sidebar-toggle-btn" onclick="kafiberToggleSidebar()" title="Sembunyikan sidebar" aria-label="Sembunyikan sidebar">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
        </div>
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
                    <?php elseif ($icon === 'dashboard' || $icon === 'dashboard_staff' || $icon === 'dashboard_admin'): ?>
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

<button type="button" id="dashboard-sidebar-show" class="sidebar-show-btn" onclick="kafiberToggleSidebar()" title="Tampilkan sidebar" aria-label="Tampilkan sidebar">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
    </svg>
</button>

<style>
    .sidebar-toggle-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        flex-shrink: 0;
        border-radius: 10px;
        border: 1px solid #eadfd4;
        background: #f4ece1;
        color: #5e392e;
        cursor: pointer;
        transition: background .15s ease, transform .15s ease;
    }
    .sidebar-toggle-btn:hover { background: #eadfd4; }
    .sidebar-toggle-btn:active { transform: scale(.95); }
    .sidebar-show-btn {
        position: fixed;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        z-index: 50;
        display: none;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 12px;
        border: 0;
        background: #5e392e;
        color: #fff;
        cursor: pointer;
        box-shadow: 0 6px 16px rgba(32, 25, 19, .25);
        transition: background .15s ease, transform .15s ease;
    }
    .sidebar-show-btn:hover { background: #4a2c24; }
    .sidebar-show-btn:active { transform: translateY(-50%) scale(.95); }
</style>

<script>
(function () {
    function contentCol(grid) {
        for (var i = 0; i < grid.children.length; i++) {
            var child = grid.children[i];
            if (child.classList && child.classList.contains('lg:col-span-3')) {
                return child;
            }
        }
        return null;
    }

    function kafiberInitSidebar() {
        var aside = document.getElementById('dashboard-sidebar');
        if (!aside) return;
        var grid = aside.closest('.grid');
        if (!grid) return;

        // Kolom sidebar = child langsung grid (aside itu sendiri, atau wrapper pembungkusnya)
        var sidebarCol = aside.parentElement === grid ? aside : aside.parentElement;
        // Konten diambil saat seluruh dokumen sudah ter-parse (DOM ready),
        // karena div konten berada SETELAH komponen sidebar pada HTML.
        var content = contentCol(grid);
        var showBtn = document.getElementById('dashboard-sidebar-show');

        // Pastikan tombol "tampilkan" menjadi child langsung grid (fixed, tanpa efek layout)
        if (showBtn && showBtn.parentElement !== grid) {
            grid.appendChild(showBtn);
        }

        window.kafiberToggleSidebar = function () {
            var hidden = sidebarCol.style.display === 'none';
            sidebarCol.style.display = hidden ? '' : 'none';
            if (content) {
                content.style.gridColumn = hidden ? '' : '1 / -1';
            }
            if (showBtn) {
                showBtn.style.display = hidden ? 'none' : 'flex';
            }
            try { localStorage.setItem('kafiber_sidebar_collapsed', hidden ? '0' : '1'); } catch (e) {}
        };

        var collapsed = false;
        try { collapsed = localStorage.getItem('kafiber_sidebar_collapsed') === '1'; } catch (e) {}
        if (collapsed) {
            sidebarCol.style.display = 'none';
            if (content) {
                content.style.gridColumn = '1 / -1';
            }
            if (showBtn) {
                showBtn.style.display = 'flex';
            }
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', kafiberInitSidebar);
    } else {
        kafiberInitSidebar();
    }
})();
</script>
