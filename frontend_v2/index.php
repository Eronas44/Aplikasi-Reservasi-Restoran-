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
    'detail_restoran'    => 'detail_restoran.php',
];

// 6. Proteksi Akses (Auth Guard)
$authRequiredPages = ['dashboard', 'reservasi', 'reservations', 'reservation-form', 'proses_reservasi'];
$guestOnlyPages    = ['login', 'register'];

if (in_array($page, $authRequiredPages, true) && !is_user_logged_in()) {
    header('Location: ' . route('login'));
    exit;
}

if (in_array($page, $guestOnlyPages, true) && is_user_logged_in()) {
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