<?php
/**
 * ==========================================
 * ROUTER TERPUSAT - APLIKASI RESERVASI RESTORAN
 * ==========================================
 * File ini adalah gerbang utama (entry point) aplikasi
 * Semua request diproses melalui file ini
 *
 * Cara Penggunaan:
 * - index.php                  (Homepage default/dashboard)
 * - index.php?page=login       (Halaman login)
 * - index.php?page=register    (Halaman register)
 * - index.php?page=dashboard   (Dashboard user)
 * - index.php?page=menu        (Menu restoran - data dari backend)
 * - index.php?page=galeri      (Galeri restoran)
 * - index.php?page=story       (Story about restoran)
 * - index.php?page=reservations      (Daftar reservasi user)
 * - index.php?page=reservation-form  (Form buat reservasi)
 * - index.php?page=logout      (Keluar sistem)
 *
 * Daftar route juga didefinisikan di src/config/routes.js sebagai
 * manifest frontend (sumber kebenaran). Pastikan keduanya sinkron.
 */

// ==========================================
// KONFIGURASI & INISIALISASI
// ==========================================

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define constants
define('BASE_PATH', __DIR__);
define('VIEWS_PATH', BASE_PATH . '/src/views');
define('LAYOUTS_PATH', BASE_PATH . '/src/layouts');
define('COMPONENTS_PATH', BASE_PATH . '/src/components');
define('ASSETS_PATH', BASE_PATH . '/public');

// Helper function untuk HTML escape
if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

// Helper function untuk generate URL (mengikuti manifest di src/config/routes.js)
function route($page = '', $params = []) {
    $query = [];

    if (!empty($page)) {
        $query['page'] = $page;
    }

    if (is_array($params) && count($params) > 0) {
        foreach ($params as $key => $value) {
            if ($value !== null && $value !== '') {
                $query[$key] = $value;
            }
        }
    }

    if (count($query) === 0) {
        return 'index.php';
    }

    return 'index.php?' . http_build_query($query);
}

// Helper function untuk check active page
function isActive($page) {
    $current = isset($_GET['page']) ? $_GET['page'] : 'home';
    return $current === $page ? 'active' : '';
}

// ==========================================
// AMBIL PARAMETER PAGE
// ==========================================

/**
 * Sanitize input - mencegah directory traversal attacks
 * Hanya allow alphanumeric, underscore, dan hyphen
 */
function sanitize($input) {
    return preg_replace('/[^a-zA-Z0-9_-]/', '', (string) $input);
}

// Default page adalah 'home'
$page = isset($_GET['page']) ? sanitize($_GET['page']) : 'home';

// ==========================================
// MAPPING ROUTES KE FILES
// ==========================================

$routes = [
    'home'             => 'home.php',
    'login'            => 'login.php',
    'register'         => 'register.php',
    'dashboard'        => 'dashboard_user.php',
    'galeri'           => 'galeri.php',
    'menu'             => 'menu.php',
    'story'            => 'story.php',
    'reservations'     => 'reservations.php',
    'reservation-form' => 'reservation_form.php',
    'detail_restoran'  => 'detail_restoran.php',
];

// Proteksi akses (sinkron dengan src/config/routes.js)
$authRequiredPages = ['dashboard', 'galeri', 'reservations', 'reservation-form', 'detail_restoran'];
$guestOnlyPages    = ['login', 'register'];

// ==========================================
// LOGOUT
// ==========================================

if ($page === 'logout') {
    require_once __DIR__ . '/src/config/api.config.php';
    require_once __DIR__ . '/src/utils/api.php';
    frontend_logout();
    header('Location: ' . route('login'));
    exit;
}

// ==========================================
// VALIDASI AKSES
// ==========================================

if (array_key_exists($page, $routes)) {
    // Halaman yang wajib login
    if (in_array($page, $authRequiredPages, true) && !is_user_logged_in()) {
        header('Location: ' . route('login'));
        exit;
    }

    // Halaman yang hanya untuk tamu (login/register)
    if (in_array($page, $guestOnlyPages, true) && is_user_logged_in()) {
        header('Location: ' . route('dashboard'));
        exit;
    }
}

// ==========================================
// VALIDASI & INCLUDE FILE
// ==========================================

if (array_key_exists($page, $routes)) {
    // File ada di mapping
    $file = VIEWS_PATH . '/' . $routes[$page];

    if (file_exists($file)) {
        include $file;
    } else {
        // File tidak ditemukan (error server)
        show_404("File halaman tidak ditemukan: {$file}");
    }
} else {
    // Route tidak ditemukan (404)
    show_404("Halaman '{$page}' tidak ditemukan");
}

/**
 * Cek apakah user sudah login (sesi frontend).
 */
function is_user_logged_in() {
    return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
}

/**
 * Fungsi untuk menampilkan halaman 404
 */
function show_404($message = '', $page = null) {
    http_response_code(404);
    if ($page === null) {
        $page = isset($_GET['page']) ? sanitize($_GET['page']) : '';
    }
    include LAYOUTS_PATH . '/header.php';
    ?>

    <div class="min-h-screen bg-[#f4ece1] flex items-center justify-center px-6">
        <div class="text-center max-w-md">
            <!-- 404 Error Image/Icon -->
            <div class="mb-8">
                <svg class="w-24 h-24 mx-auto text-[#8a5d49]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <!-- Error Code -->
            <h1 class="font-display text-6xl font-bold text-[#201913] mb-4">404</h1>

            <!-- Error Title -->
            <h2 class="font-display text-2xl font-bold text-[#201913] mb-2">Halaman Tidak Ditemukan</h2>

            <!-- Error Message -->
            <p class="text-[#66574b] mb-8">
                Maaf, halaman yang Anda cari tidak tersedia.
                <?php if (!empty($message)): ?>
                    <br><small class="text-[#8a5d49]"><?= e($message) ?></small>
                <?php endif; ?>
            </p>

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-4 justify-center">
                <a href="<?= route('home') ?>" class="inline-block bg-[#8a5d49] hover:bg-[#734d3d] text-white font-bold py-3 px-6 rounded-full transition">
                    ← Kembali ke Beranda
                </a>
                <a href="<?= route('dashboard') ?>" class="inline-block border border-[#8a5d49] text-[#8a5d49] hover:bg-[#8a5d49] hover:text-white font-bold py-3 px-6 rounded-full transition">
                    Dashboard →
                </a>
            </div>

            <!-- Debug Info (hanya di development) -->
            <?php if ((isset($_ENV['APP_DEBUG']) && $_ENV['APP_DEBUG'] === true) || false): ?>
                <div class="mt-8 text-left bg-white border border-[#eadfd4] rounded-lg p-4">
                    <p class="text-xs font-mono text-[#66574b]">
                        <strong>Debug Info:</strong><br>
                        Page: <?= e($page) ?><br>
                        Available Routes: home, login, register, dashboard, galeri, menu, story, reservations, reservation-form, logout
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php
    include LAYOUTS_PATH . '/footer.php';
    exit;
}
