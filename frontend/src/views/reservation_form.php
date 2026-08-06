<?php
// reservation_form.php — Form Buat Reservasi Kafiber (via backend API)
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

$tables = [];
$apiError = '';
$formError = '';
$old = [
    'table_id' => $_POST['table_id'] ?? ($_GET['table_id'] ?? ''),
    'reservation_date' => $_POST['reservation_date'] ?? date('Y-m-d'),
    'reservation_time' => $_POST['reservation_time'] ?? '19:00',
    'number_of_guest' => $_POST['number_of_guest'] ?? '2',
    'special_request' => $_POST['special_request'] ?? '',
];

// Ambil daftar meja dari backend
$tableResult = api_get(API_TABLES . '?per_page=50');
if ($tableResult['ok']) {
    $tables = $tableResult['data']['data']['data'] ?? $tableResult['data']['data'] ?? [];
} else {
    $apiError = api_error_message($tableResult, '');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tableId = (int) ($_POST['table_id'] ?? 0);
    $date = trim($_POST['reservation_date'] ?? '');
    $time = trim($_POST['reservation_time'] ?? '');
    $guests = (int) ($_POST['number_of_guest'] ?? 0);
    $specialRequest = trim($_POST['special_request'] ?? '');

    if ($tableId <= 0 || $date === '' || $time === '' || $guests < 1) {
        $formError = 'Mohon lengkapi semua kolom yang wajib diisi.';
    } else {
        $bookingCode = 'KBR-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));

        $result = api_post(API_RESERVATIONS, [
            'user_id' => $userId,
            'table_id' => $tableId,
            'booking_code' => $bookingCode,
            'reservation_date' => $date,
            'reservation_time' => date('H:i:s', strtotime($time)),
            'number_of_guest' => $guests,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'special_request' => $specialRequest !== '' ? $specialRequest : null,
        ]);

        if ($result['ok']) {
            $_SESSION['flash_success'] = 'Reservasi berhasil dibuat! Kode booking Anda: ' . $bookingCode;
            header('Location: ' . route('reservations'));
            exit;
        }

        $formError = api_error_message($result, 'Reservasi gagal dibuat. Silakan coba lagi.');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Buat Reservasi — Kafiber Restoran</title>
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
                <a href="<?= route('reservations') ?>" class="flex items-center px-6 py-3.5 rounded-full bg-white border border-[#eadfd4] text-[#201913] text-sm font-semibold shadow-sm hover:border-[#8a5d49] transition">Reservasi Restoran</a>
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
        <header class="mb-8">
            <a href="<?= route('reservations') ?>" class="text-sm font-semibold text-[#8a5d49] hover:underline">← Kembali ke Reservasi Saya</a>
            <h1 class="font-display text-3xl font-semibold text-[#201913] mt-2">Buat Reservasi</h1>
            <p class="text-sm text-[#66574b] mt-1">Pilih meja dan tentukan jadwal kedatangan Anda.</p>
        </header>

        <?php if (!empty($apiError)): ?>
            <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-600 text-sm font-medium"><?= htmlspecialchars($apiError, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <?php if (!empty($formError)): ?>
            <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-600 text-sm font-medium"><?= htmlspecialchars($formError, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= route('reservation-form') ?>" class="max-w-2xl bg-[#faf8f5] border border-[#eadfd4] rounded-3xl p-8 shadow-sm space-y-6">
            <div>
                <label for="table_id" class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Pilih Meja</label>
                <select id="table_id" name="table_id" required
                        class="w-full px-4 py-3 rounded-2xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
                    <option value="">— Pilih Meja —</option>
                    <?php foreach ($tables as $table): ?>
                        <?php $selected = (int) $old['table_id'] === (int) $table['table_id'] ? ' selected' : ''; ?>
                        <option value="<?= (int) $table['table_id'] ?>"<?= $selected ?>>
                            Meja <?= htmlspecialchars($table['table_number'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                            (Kapasitas <?= (int) ($table['capacity'] ?? 0) ?> • <?= htmlspecialchars($table['location_area'] ?? '-', ENT_QUOTES, 'UTF-8') ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="reservation_date" class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Tanggal</label>
                    <input type="date" id="reservation_date" name="reservation_date" required value="<?= htmlspecialchars($old['reservation_date'], ENT_QUOTES, 'UTF-8') ?>"
                           class="w-full px-4 py-3 rounded-2xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
                </div>
                <div>
                    <label for="reservation_time" class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Jam</label>
                    <input type="time" id="reservation_time" name="reservation_time" required value="<?= htmlspecialchars($old['reservation_time'], ENT_QUOTES, 'UTF-8') ?>"
                           class="w-full px-4 py-3 rounded-2xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
                </div>
            </div>

            <div>
                <label for="number_of_guest" class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Jumlah Tamu</label>
                <input type="number" id="number_of_guest" name="number_of_guest" min="1" required value="<?= htmlspecialchars($old['number_of_guest'], ENT_QUOTES, 'UTF-8') ?>"
                       class="w-full px-4 py-3 rounded-2xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
            </div>

            <div>
                <label for="special_request" class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Permintaan Khusus (opsional)</label>
                <textarea id="special_request" name="special_request" rows="3" placeholder="Contoh: meja dekat jendela, ulang tahun, dsb."
                          class="w-full px-4 py-3 rounded-2xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition"><?= htmlspecialchars($old['special_request'], ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <button type="submit"
                    class="w-full bg-[#8a5d49] hover:bg-[#734d3d] text-white font-bold text-sm py-3.5 rounded-2xl shadow-md transition">
                Buat Reservasi
            </button>
        </form>
    </main>

</body>
</html>
