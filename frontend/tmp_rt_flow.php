<?php
session_start();
require __DIR__ . '/src/config/api.config.php';
require __DIR__ . '/src/utils/api.php';

// Simulasikan login seperti login.php
$login = api_login('budi.santoso@example.com', 'password');
set_frontend_session_from_user($login['data']['data'] ?? []);
echo 'login user_id=' . var_export($_SESSION['user_id'], true) . PHP_EOL;

// Ambil meja tersedia seperti pilih_meja.php
$tables = api_get(API_TABLES . '?restaurant_id=1&status=available');
$raw = $tables['data']['data'] ?? [];
$trow = ($raw['data'] ?? $raw)[0] ?? [];
$tid = (int) ($trow['table_id'] ?? 0);

// Buat 2 reservasi persis seperti pembayaran.php
$codes = [];
foreach (['2030-03-15', '2030-03-16'] as $i => $date) {
    $code = 'KB-' . strtoupper(bin2hex(random_bytes(3)));
    $payload = [
        'user_id'          => (int) $_SESSION['user_id'],
        'table_id'         => $tid,
        'booking_code'     => $code,
        'reservation_date' => $date,
        'reservation_time' => '19:00:00',
        'number_of_guest'  => 2,
        'total_price'      => 0,
        'deposit_amount'   => 0,
        'status'           => 'confirmed',
    ];
    $res = api_post(API_RESERVATIONS, $payload);
    echo $code . ' create ok=' . var_export($res['ok'], true) . PHP_EOL;
    $codes[] = $code;
}

// Render halaman riwayat persis seperti pengguna melihatnya
$_GET = ['page' => 'riwayat_reservasi'];
$_SERVER['REQUEST_METHOD'] = 'GET';
ob_start();
require __DIR__ . '/index.php';
$html = ob_get_clean();

// Hitung baris tabel riwayat
preg_match_all('~status-badge~', $html, $mBadge);
echo 'rows in table (status badges)=' . count($mBadge[0]) . PHP_EOL;
foreach ($codes as $c) {
    echo 'shown ' . $c . ' = ' . var_export(strpos($html, $c) !== false, true) . PHP_EOL;
}
