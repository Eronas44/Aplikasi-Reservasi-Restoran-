<?php
/**
 * ==========================================
 * ROUTER TERPUSAT - APLIKASI RESERVASI RESTORAN
 * ==========================================
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

// Helper function untuk generate URL
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

function sanitize($input) {
    return preg_replace('/[^a-zA-Z0-9_-]/', '', (string) $input);
}

// Default page adalah 'home'
$page = isset($_GET['page']) ? sanitize($_GET['page']) : 'home';

// ==========================================
// MAPPING ROUTES KE FILES (DISINKRONKAN)
// ==========================================

$routes = [
    'home'               => 'home.php',
    'login'              => 'login.php',
    'register'           => 'register.php',
    'dashboard'          => 'dashboard_user.php',
    'galeri'             => 'galeri.php',
    'menu'               => 'menu.php',
    'story'              => 'story.php',
    'reservasi'          => 'reservasi.php', // <-- DITAMBAHKAN AGAR TIDAK 404
    'reservations'       => 'reservasi.php',
    'reservation-form'   => 'reservasi.php', 
    'proses_reservasi'   => 'proses_reservasi.php', 
    'detail_restoran'    => 'detail_restoran.php',
];

// Proteksi akses (ditambahkan 'reservasi' agar aman)
$authRequiredPages = ['dashboard', 'galeri', 'reservasi', 'reservations', 'reservation-form', 'detail_restoran'];
$guestOnlyPages    = ['login', 'register'];

// ==========================================
// LOGOUT
// ==========================================

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
    $file = VIEWS_PATH . '/' . $routes[$page];

    if (file_exists($file)) {
        include $file;
    } else {
        show_404("File halaman tidak ditemukan: {$file}");
    }
} else {
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
    
    $layout_header = LAYOUTS_PATH . '/header.php';
    $layout_footer = LAYOUTS_PATH . '/footer.php';

    if (file_exists($layout_header)) include $layout_header;
    ?>

    <div class="min-h-screen bg-[#f4ece1] flex items-center justify-center px-6">
        <div class="text-center max-w-md">
            <div class="mb-8">
                <svg class="w-24 h-24 mx-auto text-[#8a5d49]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <h1 class="font-display text-6xl font-bold text-[#201913] mb-4">404</h1>
            <h2 class="font-display text-2xl font-bold text-[#201913] mb-2">Halaman Tidak Ditemukan</h2>

            <p class="text-[#66574b] mb-8">
                Maaf, halaman yang Anda cari tidak tersedia.
                <?php if (!empty($message)): ?>
                    <br><small class="text-[#8a5d49]"><?= e($message) ?></small>
                <?php endif; ?>
            </p>

            <div class="flex flex-wrap gap-4 justify-center">
                <a href="<?= route('home') ?>" class="inline-block bg-[#8a5d49] hover:bg-[#734d3d] text-white font-bold py-3 px-6 rounded-full transition">
                    ← Kembali ke Beranda
                </a>
                <a href="<?= route('dashboard') ?>" class="inline-block border border-[#8a5d49] text-[#8a5d49] hover:bg-[#8a5d49] hover:text-white font-bold py-3 px-6 rounded-full transition">
                    Dashboard →
                </a>
            </div>
        </div>
    </div>

    <?php
    if (file_exists($layout_footer)) include $layout_footer;
    exit;
}