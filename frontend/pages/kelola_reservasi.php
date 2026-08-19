<?php
// pages/kelola_reservasi.php — Kelola Semua Reservasi (Admin)
// Terhubung ke backend: GET /reservations, PUT /reservations/{id} (status)

$isLoggedIn = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
$role = $_SESSION['role'] ?? 'customer';
if (!$isLoggedIn || $role !== 'admin') {
    header('Location: ' . route('login'));
    exit;
}

require_once __DIR__ . '/../src/config/api.config.php';
require_once __DIR__ . '/../src/utils/api.php';

$message = '';
$messageType = 'green';

// Ubah status reservasi via ?id=...&status=...
if (isset($_GET['id']) && is_numeric($_GET['id']) && isset($_GET['status'])) {
    $result = api_request('PUT', API_RESERVATIONS . '/' . (int) $_GET['id'], ['status' => $_GET['status']]);
    if ($result['ok']) {
        $_SESSION['flash_message'] = 'Status reservasi diperbarui.';
        $_SESSION['flash_type'] = 'green';
    } else {
        $_SESSION['flash_message'] = api_error_message($result, 'Gagal memperbarui status.');
        $_SESSION['flash_type'] = 'red';
    }
    header("Location: " . route('kelola_reservasi'));
    exit;
}

// Ambil flash message jika ada
if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    $messageType = $_SESSION['flash_type'] ?? 'green';
    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
}

$reservations = [];
$resResult = api_get(API_RESERVATIONS . '?limit=200');
if ($resResult['ok']) {
    $raw = $resResult['data']['data'] ?? [];
    $reservations = $raw['data'] ?? $raw;
}

$statusBadges = [
    'pending' => 'pending',
    'confirmed' => 'confirmed',
    'completed' => 'completed',
    'cancelled' => 'cancelled',
    'no_show' => 'cancelled',
];
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        <?php $sidebarRole = 'admin'; $sidebarActive = 'kelola_reservasi'; include __DIR__ . '/../components/dashboard-sidebar.php'; ?>

        <div class="lg:col-span-3">
            <div class="bg-white/90 border border-[#eadfd4] rounded-3xl p-8 md:p-10 shadow-sm space-y-6">

                <div class="border-b border-[#eadfd4] pb-6">
                    <span class="text-xs uppercase tracking-widest text-[#8a5d49] font-bold">Manajemen Data</span>
                    <h1 class="font-display text-3xl font-bold text-[#201913] mt-1">Kelola Reservasi</h1>
                    <p class="text-sm text-[#66574b] mt-1">Lihat, ubah status, dan batalkan seluruh reservasi.</p>
                </div>

                <?php if ($message !== ''): ?>
                    <div class="p-4 rounded-2xl border text-sm <?= $messageType === 'green' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-700' ?>"><?= e($message) ?></div>
                <?php endif; ?>

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                    <h2 class="font-display text-lg font-bold text-[#201913]">Daftar Reservasi</h2>
                    <div class="relative md:w-80">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[#a39a8f]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" id="search-reservasi" placeholder="Cari kode, nama, restoran, status..."
                               class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-[#eadfd4] bg-white text-sm outline-none focus:border-[#8a5d49] transition">
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table data-paginate data-paginate-search="search-reservasi" class="w-full text-sm text-left text-[#4f4338]">
                        <thead>
                            <tr class="border-b border-[#eadfd4] text-[#8a5d49] text-xs uppercase tracking-wider">
                                <th class="py-3 pr-4">Kode</th>
                                <th class="py-3 pr-4">Nama</th>
                                <th class="py-3 pr-4">Tanggal</th>
                                <th class="py-3 pr-4">Waktu</th>
                                <th class="py-3 pr-4">Tamu</th>
                                <th class="py-3 pr-4">Restoran</th>
                                <th class="py-3 pr-4">Meja</th>
                                <th class="py-3 pr-4">Status</th>
                                <th class="py-3 pr-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-reservasi">
                            <?php foreach ($reservations as $r): ?>
                                <?php $rid = (int) ($r['reservation_id'] ?? 0); ?>
                                <tr class="border-b border-[#eadfd4]" data-search="<?= e(strtolower(($r['booking_code'] ?? '') . ' ' . ($r['user']['name'] ?? '') . ' ' . ($r['table']['restaurant']['name'] ?? '') . ' ' . ($r['table']['table_number'] ?? '') . ' ' . ($r['status'] ?? ''))) ?>">
                                    <td class="py-3 pr-4 font-mono text-xs font-bold text-[#201913]"><?= e($r['booking_code'] ?? '-') ?></td>
                                    <td class="py-3 pr-4"><?= e($r['user']['name'] ?? 'Tamu') ?></td>
                                    <td class="py-3 pr-4"><?= e(substr((string) ($r['reservation_date'] ?? ''), 0, 10)) ?></td>
                                    <td class="py-3 pr-4"><?= e(substr((string) ($r['reservation_time'] ?? ''), 0, 5)) ?></td>
                                    <td class="py-3 pr-4"><?= (int) ($r['number_of_guest'] ?? 0) ?></td>
                                    <td class="py-3 pr-4 font-bold text-[#8a5d49]"><?= e($r['table']['restaurant']['name'] ?? '-') ?></td>
                                    <td class="py-3 pr-4 font-bold text-[#8a5d49]"><?= e($r['table']['table_number'] ?? '-') ?></td>
                                    <td class="py-3 pr-4"><span class="status-badge status-<?= e($statusBadges[$r['status'] ?? ''] ?? 'pending') ?>"><?= e(ucfirst(str_replace('_', ' ', $r['status'] ?? 'pending'))) ?></span></td>
                                    <td class="py-3 pr-4">
                                        <?php if ($rid > 0): ?>
                                            <select onchange="window.location='<?= route('kelola_reservasi') ?>?id=<?= $rid ?>&status='+this.value" class="px-2 py-1.5 rounded-lg border border-[#eadfd4] bg-white text-xs outline-none focus:border-[#8a5d49]">
                                                <?php foreach (['pending', 'confirmed', 'completed', 'cancelled', 'no_show'] as $st): ?>
                                                    <option value="<?= $st ?>" <?= ($r['status'] ?? '') === $st ? 'selected' : '' ?>><?= ucfirst(str_replace('_', ' ', $st)) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php else: ?>
                                            <span class="text-[11px] text-[#a39a8f]">Demo</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var input = document.getElementById('search-reservasi');
                    if (!input) return;
                    input.addEventListener('input', function () {
                        var q = this.value.toLowerCase().trim();
                        document.querySelectorAll('#tbody-reservasi tr').forEach(function (row) {
                            row.style.display = row.dataset.search.indexOf(q) !== -1 ? '' : 'none';
                        });
                    });
                });
                </script>

            </div>
        </div>
    </div>
</div>
