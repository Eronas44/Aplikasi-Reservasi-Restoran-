<?php
/**
 * ============================================
 * API CONFIGURATION - BACKEND LARAVEL
 * ============================================
 * Konfigurasi alamat backend & daftar endpoint API.
 * Frontend mengakses backend melalui proxy server-side
 * (lihat src/utils/api.php) sehingga tidak terkendala CORS.
 */

// Base URL backend. Bisa di-override lewat environment:
//   - API_URL       -> URL internal docker (http://backend:8000)
//   - API_BASE_URL  -> URL untuk akses dari luar (http://localhost:8080)
// Prioritas pertama API_URL agar ketika frontend berjalan di dalam container
// Docker, ia memanggil backend melalui jaringan internal docker network.
$apiBaseUrl = getenv('API_URL') ?: getenv('API_BASE_URL') ?: 'http://localhost:8080';

if (!defined('API_BASE_URL')) {
    define('API_BASE_URL', rtrim($apiBaseUrl, '/') . '/api/v1');
}

// Base URL publik backend yang bisa dijangkau browser pengguna (untuk menampilkan
// gambar). Berbeda dengan API_BASE_URL yang internal Docker (http://backend:8000).
//   - API_PUBLIC_URL -> contoh: http://localhost:8080 (mapping host backend)
$apiPublicUrl = getenv('API_PUBLIC_URL') ?: getenv('API_BASE_URL') ?: 'http://localhost:8080';

if (!defined('API_IMAGE_BASE')) {
    define('API_IMAGE_BASE', rtrim($apiPublicUrl, '/'));
}

if (!function_exists('api_image_url')) {
    /**
     * Bangun URL gambar lengkap dari path yang disimpan backend
     * (contoh: /media/menu_images/xxx.jpg).
     */
    function api_image_url($path) {
        $path = ltrim((string) ($path ?? ''), '/');
        return $path === '' ? '' : API_IMAGE_BASE . '/' . $path;
    }
}

if (!function_exists('api_resto_image')) {
    /**
     * URL gambar restoran. Jika belum ada image_url (belum diupload),
     * gunakan gambar default dari aset frontend, dipilih deterministik
     * berdasarkan restaurant_id agar tiap restoran tampil berbeda.
     */
    function api_resto_image($path, $restoId = 0) {
        if (!empty($path)) {
            return api_image_url($path);
        }
        $defaults = ['RestoA.jpg', 'ViewrestoB.jpg', 'ViewRestoC.jpg', 'ViewRestoD.jpg'];
        $idx = ((int) $restoId % count($defaults));
        return '/assets/images/Resto/' . $defaults[$idx];
    }
}

if (!function_exists('api_menu_image')) {
    /**
     * URL gambar menu. Jika belum ada image_url (belum diupload),
     * gunakan gambar default dari aset frontend.
     */
    function api_menu_image($path, $menuId = 0) {
        if (!empty($path)) {
            return api_image_url($path);
        }
        $defaults = ['makanan1.jpg', 'drink1.jpg', 'dessert1.jpg', 'appetizer1.jpg'];
        $idx = ((int) $menuId % count($defaults));
        return '/assets/images/MenuRestoA/' . $defaults[$idx];
    }
}

if (!defined('API_AUTH_LOGIN')) {
    define('API_AUTH_LOGIN', '/auth/login');
}
if (!defined('API_AUTH_REGISTER')) {
    define('API_AUTH_REGISTER', '/auth/register');
}
if (!defined('API_AUTH_ME')) {
    define('API_AUTH_ME', '/auth/me');
}
if (!defined('API_AUTH_LOGOUT')) {
    define('API_AUTH_LOGOUT', '/auth/logout');
}
if (!defined('API_MENUS')) {
    define('API_MENUS', '/menus');
}
if (!defined('API_CATEGORIES')) {
    define('API_CATEGORIES', '/categories');
}
if (!defined('API_TABLES')) {
    define('API_TABLES', '/tables');
}
if (!defined('API_RESERVATIONS')) {
    define('API_RESERVATIONS', '/reservations');
}
if (!defined('API_RESERVATION_ITEMS')) {
    define('API_RESERVATION_ITEMS', '/reservation-items');
}
if (!defined('API_RESTAURANTS')) {
    define('API_RESTAURANTS', '/restaurants');
}
if (!defined('API_OPENING_HOURS')) {
    define('API_OPENING_HOURS', '/opening-hours');
}
if (!defined('API_POLICIES')) {
    define('API_POLICIES', '/policies');
}
if (!defined('API_PAYMENTS')) {
    define('API_PAYMENTS', '/payments');
}
if (!defined('API_WAITING_LIST')) {
    define('API_WAITING_LIST', '/waiting-list');
}
if (!defined('API_USERS')) {
    define('API_USERS', '/users');
}
