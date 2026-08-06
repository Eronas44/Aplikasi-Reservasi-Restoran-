<?php
// reservations.php — Daftar Reservasi User Kafiber
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: ' . route('login'));
    exit;
}

$userId = $_SESSION['user_id'] ?? null;
$userName = $_SESSION['user_name'] ?? 'Pelanggan';

require_once dirname(__DIR__) . '/config/api.config.php';
require_once dirname(__DIR__) . '/utils/api.php';

$reservations = [];
$apiError = '';

if ($userId !== null) {
    $result = api_get(API_RESERVATIONS . '?per_page=100');
    if ($result['ok']) {
        $list = $result['data']['data']['data'] ?? $result['data']['data'] ?? [];
        foreach ($list as $reservation) {
            if ((int) ($reservation['user_id'] ?? 0) === (int) $userId) {
                $reservations[] = $reservation;
            }
        }
    } else {
        $apiError = api_error_message($result, '');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reservasi Saya — Kafiber Restoran</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="src/styles/style.css">
</head>
<body class="bg-[#f4ece1] font-sans antialiased min-h-screen flex">

    <!-- SIDEBAR -->
    <aside class="w-72 bg-[#faf8f5] border-r border-[#eadfd4] flex flex-col justify-between p-6 shadow-sm">
        <div>
            <a href="<?= route('home') ?>" class="flex items-center gap-3 mb-10 no-underline">
                <div class="w-10 h-10 rounded-full bg-[#5e392e] text-white flex items-center justify-center font-display italic font-bold text-lg shadow">K</div>
                <span class="font-display text-lg font-semibold italic text-[#201913]">Kafiber Restoran</span>
            </a>
            <nav class="space-y-4">
                <a href="<?= route('dashboard') ?>" class="flex items-center px-6 py-3.5 rounded-full bg-white border border-[#eadfd4] text-[#201913] text-sm font-semibold shadow-sm hover:border-[#8a5d49] transition">Dashboard</a>
                <a href="<?= route('reservations') ?>" class="flex items-center px-6 py-3.5 rounded-full bg-[#5e392e] text-white text-sm font-semibold shadow-sm transition">Reservasi Restoran</a>
                <a href="<?= route('menu') ?>" class="flex items-center px-6 py-3.5 rounded-full bg-white border border-[#eadfd4] text-[#201913] text-sm font-semibold shadow-sm hover:border-[#8a5d49] transition">Menu Kuliner</a>
            </nav>
        </div>
        <div class="pt-6 border-t border-[#eadfd4]">
            <p class="text-sm font-bold text-[#201913] truncate mb-4"><?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?></p>
            <a href="<?= route('logout') ?>" class="block w-full text-center bg-red-50 hover:bg-red-100 text-red-600 text-xs font-bold py-3 rounded-2xl transition">Keluar Sistem</a>
        </div>
    </aside>

    <!-- KONTEN -->
    <main class="flex-1 p-10 overflow-y-auto">
        <header class="mb-8 flex justify-between items-center flex-wrap gap-4">
            <div>
                <h1 class="font-display text-3xl font-semibold text-[#201913]">Reservasi Saya</h1>
                <p class="text-sm text-[#66574b] mt-1">Semua reservasi Anda di Kafiber Restoran.</p>
            </div>
            <a href="<?= route('reservation-form') ?>" class="bg-[#8a5d49] hover:bg-[#734d3d] text-white text-xs font-bold px-6 py-3.5 rounded-full shadow transition">+ Buat Reservasi Baru</a>
        </header>

        <?php if (!empty($apiError)): ?>
            <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-600 text-sm font-medium"><?= htmlspecialchars($apiError, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['flash_success'])): ?>
            <div class="mb-6 p-4 rounded-2xl bg-green-50 border border-green-200 text-green-700 text-sm font-medium">
                <?= htmlspecialchars($_SESSION['flash_success'], ENT_QUOTES, 'UTF-8') ?>
            </div>
            <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>

        <?php if (empty($reservations)): ?>
            <div class="bg-[#faf8f5] border border-[#eadfd4] rounded-3xl p-12 text-center shadow-sm">
                <p class="text-sm text-[#66574b] mb-4">Belum ada reservasi ditemukan.</p>
                <a href="<?= route('reservation-form') ?>" class="inline-block bg-[#8a5d49] hover:bg-[#734d3d] text-white text-xs font-bold px-6 py-3 rounded-full shadow transition">Buat Reservasi</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach ($reservations as $reservation): ?>
                    <div class="bg-[#faf8f5] border border-[#eadfd4] rounded-3xl p-6 shadow-sm hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full <?= in_array($reservation['status'] ?? '', ['cancelled', 'no_show'], true) ? 'bg-red-100 text-red-600' : (($reservation['status'] ?? '') === 'confirmed' ? 'bg-green-100 text-green-700' : 'bg-[#efe0d5] text-[#8a5d49]') ?>">
                                <?= htmlspecialchars($reservation['status'] ?? 'pending', ENT_QUOTES, 'UTF-8') ?>
                            </span>
                            <span class="text-xs text-[#66574b] font-mono"><?= htmlspecialchars($reservation['booking_code'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <h3 class="font-display text-xl font-semibold text-[#201913]">Meja <?= htmlspecialchars($reservation['table']['table_number'] ?? '-', ENT_QUOTES, 'UTF-8') ?></h3>
                        <div class="mt-3 space-y-1 text-sm text-[#66574b]">
                            <p>📅 <?= htmlspecialchars($reservation['reservation_date'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                            <p>🕒 <?= htmlspecialchars($reservation['reservation_time'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                            <p>👥 <?= (int) ($reservation['number_of_guest'] ?? 0) ?> tamu</p>
                        </div>
                        <?php if (!empty($reservation['special_request'])): ?>
                            <p class="mt-3 text-xs text-[#8a5d49] italic">"<?= htmlspecialchars($reservation['special_request'], ENT_QUOTES, 'UTF-8') ?>"</p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

</body>
</html>
