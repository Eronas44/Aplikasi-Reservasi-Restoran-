<?php
// dashboard_user.php — Dashboard Reservasi Pelanggan Kafiber
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Proteksi halaman: Pastikan sudah login
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: ' . route('login'));
    exit;
}

$userName = $_SESSION['user_name'] ?? 'Pelanggan';
$userRole = $_SESSION['role'] ?? 'customer';
$userId = $_SESSION['user_id'] ?? null;

// Ambil data reservasi dari backend
$reservations = [];
$apiError = '';
if ($userId !== null) {
    require_once dirname(__DIR__) . '/config/api.config.php';
    require_once dirname(__DIR__) . '/utils/api.php';

    $result = api_get(API_RESERVATIONS . '?per_page=50');
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
<title>Dashboard User — Kafiber Restoran</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="src/styles/style.css">
</head>
<body class="bg-[#f4ece1] font-sans antialiased min-h-screen flex">

    <!-- SIDEBAR KIRI -->
    <aside class="w-72 bg-[#faf8f5] border-r border-[#eadfd4] flex flex-col justify-between p-6 shadow-sm">
        <div>
            <!-- Logo & Brand -->
            <a href="<?= route('home') ?>" class="flex items-center gap-3 mb-10 no-underline">
                <div class="w-10 h-10 rounded-full bg-[#5e392e] text-white flex items-center justify-center font-display italic font-bold text-lg shadow">
                    K
                </div>
                <span class="font-display text-lg font-semibold italic text-[#201913]">Kafiber Restoran</span>
            </a>

            <!-- Tombol Navigasi -->
            <nav class="space-y-4">
                <a href="<?= route('dashboard') ?>" class="flex items-center px-6 py-3.5 rounded-full bg-[#5e392e] text-white text-sm font-semibold shadow-sm transition">
                    Dashboard
                </a>
                <a href="<?= route('reservations') ?>" class="flex items-center px-6 py-3.5 rounded-full bg-white border border-[#eadfd4] text-[#201913] text-sm font-semibold shadow-sm hover:border-[#8a5d49] transition">
                    Reservasi Restoran
                </a>
                <a href="<?= route('menu') ?>" class="flex items-center px-6 py-3.5 rounded-full bg-white border border-[#eadfd4] text-[#201913] text-sm font-semibold shadow-sm hover:border-[#8a5d49] transition">
                    Menu Kuliner
                </a>
            </nav>
        </div>

        <!-- Info User & Tombol Keluar -->
        <div class="pt-6 border-t border-[#eadfd4]">
            <p class="text-xs text-[#66574b] mb-1">Masuk sebagai:</p>
            <p class="text-sm font-bold text-[#201913] truncate mb-1"><?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?></p>
            <p class="text-xs text-[#8a5d49] uppercase tracking-wider font-bold mb-4"><?= htmlspecialchars($userRole, ENT_QUOTES, 'UTF-8') ?></p>
            <a href="<?= route('logout') ?>" class="block w-full text-center bg-red-50 hover:bg-red-100 text-red-600 text-xs font-bold py-3 rounded-2xl transition">
                Keluar Sistem
            </a>
        </div>
    </aside>

    <!-- KONTEN UTAMA KANAN -->
    <main class="flex-1 p-10 overflow-y-auto">
        <header class="mb-8 flex justify-between items-center flex-wrap gap-4">
            <div>
                <h1 class="font-display text-3xl font-semibold text-[#201913]">Selamat Datang, <?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?>!</h1>
                <p class="text-sm text-[#66574b] mt-1">Kelola reservasi dan nikmati kemudahan di Kafiber.</p>
            </div>
            <a href="<?= route('reservation-form') ?>" class="bg-[#8a5d49] hover:bg-[#734d3d] text-white text-xs font-bold px-6 py-3.5 rounded-full shadow transition">
                + Buat Reservasi Baru
            </a>
        </header>

        <?php if (!empty($apiError)): ?>
            <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-600 text-sm font-medium">
                <?= htmlspecialchars($apiError, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <!-- Daftar Reservasi -->
        <section class="mb-10">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-display text-2xl font-semibold text-[#201913]">Reservasi Saya</h2>
                <a href="<?= route('reservations') ?>" class="text-sm font-semibold text-[#8a5d49] hover:underline">Lihat Semua</a>
            </div>

            <?php if (empty($reservations)): ?>
                <div class="bg-[#faf8f5] border border-[#eadfd4] rounded-3xl p-10 text-center shadow-sm">
                    <p class="text-sm text-[#66574b] mb-4">Anda belum memiliki reservasi.</p>
                    <a href="<?= route('reservation-form') ?>" class="inline-block bg-[#8a5d49] hover:bg-[#734d3d] text-white text-xs font-bold px-6 py-3 rounded-full shadow transition">
                        Pesan Meja Sekarang
                    </a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php foreach (array_slice($reservations, 0, 4) as $reservation): ?>
                        <div class="bg-[#faf8f5] border border-[#eadfd4] rounded-3xl p-6 shadow-sm hover:shadow-md transition">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full bg-[#efe0d5] text-[#8a5d49]">
                                    <?= htmlspecialchars($reservation['status'] ?? 'pending', ENT_QUOTES, 'UTF-8') ?>
                                </span>
                                <span class="text-xs text-[#66574b] font-mono"><?= htmlspecialchars($reservation['booking_code'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <h3 class="font-display text-lg font-semibold text-[#201913]">
                                Meja <?= htmlspecialchars($reservation['table']['table_number'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                            </h3>
                            <p class="text-xs text-[#66574b] mt-1">
                                <?= htmlspecialchars($reservation['reservation_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                • <?= htmlspecialchars($reservation['reservation_time'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </p>
                            <p class="text-xs text-[#66574b] mt-1"><?= (int) ($reservation['number_of_guest'] ?? 0) ?> tamu</p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- Link Cepat -->
        <section>
            <h2 class="font-display text-2xl font-semibold text-[#201913] mb-4">Menu Cepat</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <a href="<?= route('menu') ?>" class="bg-[#faf8f5] border border-[#eadfd4] rounded-3xl p-6 shadow-sm hover:shadow-md transition no-underline">
                    <h3 class="font-display text-lg font-semibold text-[#201913]">Menu Kuliner</h3>
                    <p class="text-xs text-[#66574b] mt-1">Lihat daftar hidangan restoran.</p>
                </a>
                <a href="<?= route('reservations') ?>" class="bg-[#faf8f5] border border-[#eadfd4] rounded-3xl p-6 shadow-sm hover:shadow-md transition no-underline">
                    <h3 class="font-display text-lg font-semibold text-[#201913]">Riwayat Reservasi</h3>
                    <p class="text-xs text-[#66574b] mt-1">Cek status semua reservasi Anda.</p>
                </a>
                <a href="<?= route('galeri') ?>" class="bg-[#faf8f5] border border-[#eadfd4] rounded-3xl p-6 shadow-sm hover:shadow-md transition no-underline">
                    <h3 class="font-display text-lg font-semibold text-[#201913]">Galeri & Suasana</h3>
                    <p class="text-xs text-[#66574b] mt-1">Intip kenyamanan ruangan restoran.</p>
                </a>
            </div>
        </section>
    </main>

</body>
</html>
