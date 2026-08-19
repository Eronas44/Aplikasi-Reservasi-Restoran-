<?php
// pages/walkin.php — Terima Pesanan Walk-in / Waiting List
// Terhubung ke backend: GET/POST /waiting-list

$isLoggedIn = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
$role = $_SESSION['role'] ?? 'customer';
if (!$isLoggedIn) {
    header('Location: ' . route('login'));
    exit;
}
if (!in_array($role, ['staff', 'admin'], true)) {
    header('Location: ' . route('dashboard'));
    exit;
}

require_once __DIR__ . '/../src/config/api.config.php';
require_once __DIR__ . '/../src/utils/api.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama    = trim($_POST['nama'] ?? '');
    $jumlah  = (int) ($_POST['jumlah_tamu'] ?? 0);
    $area    = $_POST['area'] ?? 'indoor';
    $telp    = trim($_POST['telepon'] ?? '');

    if ($nama === '' || $jumlah < 1) {
        $message = 'Nama dan jumlah tamu wajib diisi dengan benar.';
        $messageType = 'error';
    } else {
        $payload = [
            'restaurant_id' => (int) ($_SESSION['restaurant_id'] ?? 1),
            'name' => $nama,
            'phone' => $telp !== '' ? $telp : null,
            'number_of_guest' => $jumlah,
            'area' => $area,
            'status' => 'waiting',
        ];
        $result = api_request('POST', API_WAITING_LIST, $payload);

        if ($result['ok']) {
            $_SESSION['flash_message'] = "Tamu '$nama' ditambahkan ke Waiting List. Anda akan diberi tahu bila meja tersedia.";
            $_SESSION['flash_type'] = 'success';
            header("Location: " . route('walkin'));
            exit;
        } else {
            $message = api_error_message($result, 'Gagal menambahkan tamu ke Waiting List.');
            $messageType = 'error';
        }
    }
}

// Ambil flash message jika ada
if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    $messageType = $_SESSION['flash_type'] ?? 'success';
    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
}

$waitingList = [];
$waitResult = api_get(API_WAITING_LIST);
if ($waitResult['ok']) {
    $raw = $waitResult['data']['data'] ?? [];
    $waitingList = is_array($raw) && array_key_exists('data', $raw) ? ($raw['data'] ?? []) : $raw;
}
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        <?php $sidebarRole = 'staff'; $sidebarActive = 'walkin'; include __DIR__ . '/../components/dashboard-sidebar.php'; ?>

        <div class="lg:col-span-3 space-y-6">
            <div class="bg-white/90 border border-[#eadfd4] rounded-3xl p-8 md:p-10 shadow-sm space-y-8">

                <div class="border-b border-[#eadfd4] pb-6">
                    <span class="text-xs uppercase tracking-widest text-[#8a5d49] font-bold">Walk-in Guest</span>
                    <h1 class="font-display text-3xl font-bold text-[#201913] mt-1">Terima Tamu Walk-in</h1>
                    <p class="text-sm text-[#66574b] mt-1">Catat tamu tanpa reservasi dan alokasikan meja bila tersedia.</p>
                </div>

                <?php if ($message !== ''): ?>
                    <div class="p-5 rounded-2xl border text-sm <?= $messageType === 'success' ? 'bg-green-50 border-green-200 text-green-800' : ($messageType === 'info' ? 'bg-amber-50 border-amber-200 text-amber-800' : 'bg-red-50 border-red-200 text-red-700') ?>">
                        <?= e($message) ?>
                    </div>
                <?php endif; ?>

                <form action="<?= route('walkin') ?>" method="POST" class="space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Nama Tamu</label>
                            <input type="text" name="nama" required placeholder="Nama pemesan walk-in"
                                   class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Jumlah Tamu</label>
                            <input type="number" name="jumlah_tamu" required min="1" max="20" placeholder="Contoh: 4"
                                   class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">No. Telp (opsional)</label>
                            <input type="text" name="telepon" placeholder="08xx-xxxx-xxxx"
                                   class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Pilihan Area</label>
                            <select name="area" class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
                                <option value="indoor">Indoor AC</option>
                                <option value="outdoor">Outdoor / Semi-Outdoor</option>
                                <option value="vip">VIP</option>
                                <option value="smoking">Smoking Area</option>
                            </select>
                        </div>
                    </div>
                    <div class="pt-4 flex items-center justify-end gap-3 border-t border-[#eadfd4]">
                        <button type="submit" class="bg-[#5e392e] hover:bg-[#4a2c24] text-white text-xs font-bold py-2.5 px-6 rounded-xl transition shadow-sm">
                            Proses Tamu Walk-in
                        </button>
                    </div>
                </form>

            </div>

            <!-- Waiting List -->
            <div class="bg-white/90 border border-[#eadfd4] rounded-3xl p-8 md:p-10 shadow-sm space-y-4">
                <h2 class="font-display text-2xl font-bold text-[#201913]">Waiting List Saat Ini</h2>
                <div class="overflow-x-auto">
                    <table data-paginate class="w-full text-sm text-left text-[#4f4338]">
                        <thead>
                            <tr class="border-b border-[#eadfd4] text-[#8a5d49] text-xs uppercase tracking-wider">
                                <th class="py-3 pr-4">No</th>
                                <th class="py-3 pr-4">Nama</th>
                                <th class="py-3 pr-4">Tamu</th>
                                <th class="py-3 pr-4">Area</th>
                                <th class="py-3 pr-4">Waktu</th>
                                <th class="py-3 pr-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; foreach ($waitingList as $w): ?>
                                <tr class="border-b border-[#eadfd4]">
                                    <td class="py-3 pr-4 font-bold text-[#201913]"><?= $i++ ?></td>
                                    <td class="py-3 pr-4"><?= e($w['name'] ?? '-') ?></td>
                                    <td class="py-3 pr-4"><?= (int) ($w['number_of_guest'] ?? 0) ?> org</td>
                                    <td class="py-3 pr-4 uppercase text-xs font-bold text-[#8a5d49]"><?= e($w['area'] ?? '-') ?></td>
                                    <td class="py-3 pr-4"><?= e(date('H:i', strtotime((string) ($w['created_at'] ?? 'now')))) ?></td>
                                    <td class="py-3 pr-4"><span class="status-badge status-pending"><?= e(ucfirst($w['status'] ?? 'waiting')) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
