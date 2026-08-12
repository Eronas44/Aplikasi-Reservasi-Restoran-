<?php
/**
 * ===================================================
 * SINGLE ENTRYPOINT ROUTER — FRONTEND_V2
 * ===================================================
 * Router terpusat untuk aplikasi reservasi restoran.
 * Menggunakan query parameter (?page=nama_halaman) dengan
 * sanitasi basename() untuk mencegah celah Directory Traversal.
 */

// 1. Inisialisasi Sesi PHP
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1b. Output buffering agar header('Location: ...') tetap terkirim meski
//     halaman dirender setelah HTML layout mulai dikeluarkan.
ob_start();

// 2. Helper Functions Global
if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

function sanitize($input) {
    return preg_replace('/[^a-zA-Z0-9_-]/', '', (string) $input);
}

function route($page = '', $params = []) {
    $query = [];
    if (!empty($page)) {
        $query['page'] = $page;
    }
    if (is_array($params) && count($params) > 0) {
        foreach ($params as $k => $v) {
            if ($v !== null && $v !== '') {
                $query[$k] = $v;
            }
        }
    }
    return 'index.php' . (!empty($query) ? '?' . http_build_query($query) : '');
}

function isActive($pageName) {
    $currentRaw = isset($_GET['page']) ? $_GET['page'] : 'home';
    $current = sanitize(basename($currentRaw));
    return $current === $pageName ? 'active-nav' : '';
}

function is_user_logged_in() {
    return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
}

function dashboard_route() {
    $role = $_SESSION['role'] ?? 'customer';
    if ($role === 'admin') {
        return route('dashboard_admin');
    }
    if ($role === 'staff') {
        return route('dashboard_staff');
    }
    return route('dashboard');
}

// 3. Tangkap & Sanitasi Parameter URL ?page= (Pencegahan Directory Traversal via basename)
$rawPage = isset($_GET['page']) ? $_GET['page'] : 'home';
$page = sanitize(basename($rawPage));
if (empty($page)) {
    $page = 'home';
}

// 4. Penanganan Fitur Logout
if ($page === 'logout') {
    if (file_exists(__DIR__ . '/src/config/api.config.php')) {
        require_once __DIR__ . '/src/config/api.config.php';
    }
    if (file_exists(__DIR__ . '/src/utils/api.php')) {
        require_once __DIR__ . '/src/utils/api.php';
    }
    if (function_exists('frontend_logout')) {
        frontend_logout();
    }
    session_destroy();
    header('Location: ' . route('login'));
    exit;
}

// 5. Mapping Alias Route ke File di Folder pages/
$routeMap = [
    'home'               => 'home.php',
    'login'              => 'login.php',
    'register'           => 'register.php',
    'dashboard'          => 'dashboard_user.php',
    'galeri'             => 'galeri.php',
    'menu'               => 'menu.php',
    'story'              => 'story.php',
    'reservasi'          => 'reservasi.php',
    'reservations'       => 'reservasi.php',
    'reservation-form'   => 'reservasi.php',
    'proses_reservasi'   => 'proses_reservasi.php',
    'preview_restoran'   => 'preview_restoran.php',
    'detail_restoran'    => 'detail_restoran.php',
    // Halaman Customer
    'pembayaran'         => 'pembayaran.php',
    'pilih_meja'         => 'pilih_meja.php',
    'sukses_reservasi'   => 'sukses_reservasi.php',
    'riwayat_reservasi'  => 'riwayat_reservasi.php',
    // Halaman Staff
    'dashboard_staff'    => 'dashboard_staff.php',
    'denah_meja'         => 'denah_meja.php',
    'jadwal_hari_ini'    => 'jadwal_hari_ini.php',
    'checkin'            => 'checkin.php',
    'walkin'             => 'walkin.php',
    // Halaman Admin
    'dashboard_admin'    => 'dashboard_admin.php',
    'kelola_meja'        => 'kelola_meja.php',
    'kelola_menu'        => 'kelola_menu.php',
    'kelola_restoran'    => 'kelola_restoran.php',
    'kelola_reservasi'   => 'kelola_reservasi.php',
    'jam_operasional'    => 'jam_operasional.php',
    'kebijakan'          => 'kebijakan.php',
    'kelola_staf'        => 'kelola_staf.php',
    'laporan'            => 'laporan.php',
];

// 6. Proteksi Akses (Auth Guard)
$authRequiredPages = [
    'dashboard', 'reservasi', 'reservations', 'reservation-form', 'proses_reservasi',
    'pembayaran', 'pilih_meja', 'sukses_reservasi', 'riwayat_reservasi',
    'dashboard_staff', 'denah_meja', 'jadwal_hari_ini', 'checkin', 'walkin',
    'dashboard_admin', 'kelola_meja', 'kelola_menu', 'kelola_restoran',
    'kelola_reservasi',
    'jam_operasional', 'kebijakan', 'kelola_staf', 'laporan',
];
$guestOnlyPages    = ['login', 'register'];

$staffOnlyPages = ['dashboard_staff', 'denah_meja', 'jadwal_hari_ini', 'checkin', 'walkin'];
$adminOnlyPages = [
    'dashboard_admin', 'kelola_meja', 'kelola_menu', 'kelola_restoran',
    'kelola_reservasi',
    'jam_operasional', 'kebijakan', 'kelola_staf', 'laporan',
];

$userRole = isset($_SESSION['role']) ? $_SESSION['role'] : 'customer';

if (in_array($page, $authRequiredPages, true) && !is_user_logged_in()) {
    header('Location: ' . route('login'));
    exit;
}

if (in_array($page, $guestOnlyPages, true) && is_user_logged_in()) {
    header('Location: ' . dashboard_route());
    exit;
}

// Role Guard: Redirect ?page=dashboard ke dashboard sesuai role
// (mengikuti flowchart: sistem menyeleksi email -> halaman pengguna/admin/staff)
if ($page === 'dashboard' && is_user_logged_in() && in_array($userRole, ['admin', 'staff'], true)) {
    header('Location: ' . dashboard_route());
    exit;
}

// Role Guard: Admin hanya
if (in_array($page, $adminOnlyPages, true) && $userRole !== 'admin') {
    // Staff dan customer sama-sama tidak boleh akses halaman admin
    if ($userRole === 'staff') {
        header('Location: ' . route('dashboard_staff'));
    } else {
        header('Location: ' . route('dashboard'));
    }
    exit;
}

// Role Guard: Customer tidak boleh akses halaman staff
if (in_array($page, $staffOnlyPages, true) && !in_array($userRole, ['staff', 'admin'], true)) {
    header('Location: ' . route('dashboard'));
    exit;
}

// 7. Tentukan File Halaman Target
$fileName = isset($routeMap[$page]) ? $routeMap[$page] : "{$page}.php";
$pageFile = __DIR__ . "/pages/{$fileName}";

// 8. Validasi Ketersediaan File (Fallback Otomatis ke 404.php)
if (!file_exists($pageFile)) {
    $pageFile = __DIR__ . "/pages/404.php";
}

// 9. Render Menggunakan Layout Global Utama
include __DIR__ . '/layouts/main-layout.php';

// 10. Flush buffer output (satu kali di akhir script)
ob_end_flush();