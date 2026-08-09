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
