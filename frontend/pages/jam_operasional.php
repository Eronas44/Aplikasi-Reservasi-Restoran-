<?php
// pages/jam_operasional.php — Jam Operasional per Restoran (Admin)
// Terhubung ke backend: GET/POST /opening-hours

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

$dayNames = [
    'Minggu' => 0, 'Senin' => 1, 'Selasa' => 2, 'Rabu' => 3,
    'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6,
];

// Ambil daftar restoran dari backend
$restaurants = [];
$restoResult = api_get(API_RESTAURANTS . '?limit=200');
if ($restoResult['ok']) {
    $raw = $restoResult['data']['data'] ?? [];
    $restaurants = $raw['data'] ?? $raw;
}

// Validasi daftar id restoran yang tersedia
$validRestoIds = [];
foreach ($restaurants as $resto) {
    $rid = (int) ($resto['restaurant_id'] ?? 0);
    if ($rid > 0) {
        $validRestoIds[] = $rid;
    }
}

// Tentukan restoran terpilih: dari POST/GET, fallback ke restoran pertama
$requestedRestoId = (int) ($_POST['restaurant_id'] ?? $_GET['restaurant_id'] ?? 0);
$restaurantId = in_array($requestedRestoId, $validRestoIds, true)
    ? $requestedRestoId
    : ($validRestoIds[0] ?? 0);

// Nama restoran terpilih
$selectedRestoName = 'Restoran';
foreach ($restaurants as $resto) {
    if ((int) ($resto['restaurant_id'] ?? 0) === $restaurantId) {
        $selectedRestoName = $resto['name'] ?? $resto['restaurant_name'] ?? 'Restoran';
        break;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $restaurantId > 0) {
    $saved = true;

    foreach ($dayNames as $dayName => $dayNum) {
        $idx = array_search($dayName, array_keys($dayNames), true);
        $open = substr((string) ($_POST['open'][$idx] ?? '10:00'), 0, 5);
        $close = substr((string) ($_POST['close'][$idx] ?? '22:00'), 0, 5);
        $closed = isset($_POST['closed'][$idx]) ? true : false;

        // Ambil / buat id opening_hour untuk hari ini (per restoran)
        $result = api_get(API_OPENING_HOURS);
        $existingId = null;
        if ($result['ok'] && isset($result['data']['data'])) {
            $raw = $result['data']['data'];
            $items = $raw['data'] ?? $raw;
            foreach ($items as $oh) {
                if ((int) ($oh['restaurant_id'] ?? 0) === $restaurantId && (int) $oh['day_of_week'] === $dayNum) {
                    $existingId = $oh['opening_hour_id'];
                    break;
                }
            }
        }

        $payload = [
            'restaurant_id' => $restaurantId,
            'day_of_week' => $dayNum,
            'open_time' => $closed ? null : $open,
            'close_time' => $closed ? null : $close,
            'is_closed' => $closed,
        ];

        if ($existingId) {
            $r = api_request('PUT', API_OPENING_HOURS . '/' . $existingId, $payload);
        } else {
            $r = api_request('POST', API_OPENING_HOURS, $payload);
        }

        if (!$r['ok']) {
            $saved = false;
            $message = api_error_message($r, "Gagal menyimpan jam operasional hari $dayName.");
            $messageType = 'red';
            break;
        }
    }

    if ($saved) {
        $message = "Jam operasional '$selectedRestoName' berhasil diperbarui. Perubahan langsung diterapkan ke batas pilihan jadwal pelanggan (FR-004, FR-015).";
    }
}

// Ambil jam operasional dari backend untuk restoran terpilih
$hours = [];
$hoursResult = api_get(API_OPENING_HOURS);
if ($hoursResult['ok'] && isset($hoursResult['data']['data'])) {
    $raw = $hoursResult['data']['data'];
    $items = $raw['data'] ?? $raw;
    foreach ($items as $oh) {
        if ((int) ($oh['restaurant_id'] ?? 0) !== $restaurantId) {
            continue;
        }
        $hours[(int) $oh['day_of_week']] = [
            'open_time' => $oh['open_time'],
            'close_time' => $oh['close_time'],
            'is_closed' => (bool) $oh['is_closed'],
        ];
    }
}

$days = [];
foreach ($dayNames as $dayName => $dayNum) {
    $h = $hours[$dayNum] ?? null;
    $days[$dayName] = [
        substr((string) ($h['open_time'] ?? '10:00'), 0, 5),
        substr((string) ($h['close_time'] ?? '22:00'), 0, 5),
        $h['is_closed'] ?? false,
    ];
}
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        <?php $sidebarRole = 'admin'; $sidebarActive = 'jam_operasional'; include __DIR__ . '/../components/dashboard-sidebar.php'; ?>

        <div class="lg:col-span-3">
            <div class="bg-white/90 border border-[#eadfd4] rounded-3xl p-8 md:p-10 shadow-sm space-y-6">

                <div class="border-b border-[#eadfd4] pb-6">
                    <span class="text-xs uppercase tracking-widest text-[#8a5d49] font-bold">Konfigurasi Waktu</span>
                    <h1 class="font-display text-3xl font-bold text-[#201913] mt-1">Jam Operasional Restoran</h1>
                    <p class="text-sm text-[#66574b] mt-1">Atur jam buka/tutup per hari untuk setiap restoran (FR-015).</p>
                </div>

                <?php if ($message !== ''): ?>
                    <div class="p-4 rounded-2xl border text-sm <?= $messageType === 'green' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-700' ?>"><?= e($message) ?></div>
                <?php endif; ?>

                <?php if (empty($restaurants)): ?>
                    <div class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-6 text-center text-sm text-[#8a5d49]">
                        Belum ada restoran terdaftar. Tambahkan restoran terlebih dahulu di halaman Kelola Restoran.
                    </div>
                <?php else: ?>

                    <!-- Pilih Restoran -->
                    <form method="GET" action="<?= route('jam_operasional') ?>" class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-5 flex flex-col md:flex-row md:items-end gap-4">
                        <input type="hidden" name="page" value="jam_operasional">
                        <div class="flex-1">
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Pilih Restoran</label>
                            <select name="restaurant_id" onchange="this.form.submit()"
                                    class="w-full px-4 py-2.5 rounded-xl border border-[#eadfd4] bg-white text-sm outline-none focus:border-[#8a5d49] transition">
                                <?php foreach ($restaurants as $resto): ?>
                                    <?php $rid = (int) ($resto['restaurant_id'] ?? 0); ?>
                                    <option value="<?= $rid ?>" <?= $rid === $restaurantId ? 'selected' : '' ?>>
                                        <?= e($resto['name'] ?? $resto['restaurant_name'] ?? 'Restoran') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="bg-[#5e392e] hover:bg-[#4a2c24] text-white text-xs font-bold py-2.5 px-6 rounded-xl transition shadow-sm">Tampilkan</button>
                    </form>

                    <div class="bg-[#5e392e] rounded-2xl px-6 py-4 text-white flex items-center justify-between gap-4">
                        <div>
                            <span class="text-[10px] uppercase tracking-widest text-[#e8c39e] font-bold">Jam Operasional Sedang Diatur</span>
                            <p class="font-display text-lg font-bold mt-0.5"><?= e($selectedRestoName) ?></p>
                        </div>
                        <span class="text-xs font-semibold text-[#e8c39e]">ID Restoran: #<?= (int) $restaurantId ?></span>
                    </div>

                    <form action="<?= route('jam_operasional') ?>" method="POST" class="space-y-4">
                        <input type="hidden" name="restaurant_id" value="<?= (int) $restaurantId ?>">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-[#4f4338]">
                                <thead>
                                    <tr class="border-b border-[#eadfd4] text-[#8a5d49] text-xs uppercase tracking-wider">
                                        <th class="py-3 pr-4">Hari</th>
                                        <th class="py-3 pr-4">Jam Buka</th>
                                        <th class="py-3 pr-4">Jam Tutup</th>
                                        <th class="py-3 pr-4">Tutup / Libur</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $idx = 0; foreach ($days as $day => [$open, $close, $closed]): ?>
                                        <tr class="border-b border-[#eadfd4]">
                                            <td class="py-3 pr-4 font-bold text-[#201913]"><?= e($day) ?></td>
                                            <td class="py-3 pr-4">
                                                <input type="time" name="open[<?= $idx ?>]" value="<?= e($open) ?>"
                                                       class="px-3 py-2 rounded-xl border border-[#eadfd4] bg-white text-sm outline-none focus:border-[#8a5d49] transition">
                                            </td>
                                            <td class="py-3 pr-4">
                                                <input type="time" name="close[<?= $idx ?>]" value="<?= e($close) ?>"
                                                       class="px-3 py-2 rounded-xl border border-[#eadfd4] bg-white text-sm outline-none focus:border-[#8a5d49] transition">
                                            </td>
                                            <td class="py-3 pr-4">
                                                <input type="checkbox" name="closed[<?= $idx ?>]" <?= $closed ? 'checked' : '' ?>
                                                       class="text-[#8a5d49] focus:ring-[#8a5d49]">
                                            </td>
                                        </tr>
                                    <?php $idx++; endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="pt-4 flex items-center justify-end gap-3 border-t border-[#eadfd4]">
                            <button type="submit" class="bg-[#5e392e] hover:bg-[#4a2c24] text-white text-xs font-bold py-2.5 px-6 rounded-xl transition shadow-sm">
                                Simpan Jam Operasional
                            </button>
                        </div>
                    </form>

                <?php endif; ?>

            </div>
        </div>
    </div>
</div>
