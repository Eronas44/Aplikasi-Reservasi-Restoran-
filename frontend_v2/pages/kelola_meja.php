<?php
// pages/kelola_meja.php — Kelola Meja & Layout (Admin)
// Terhubung ke backend: GET/POST /tables, PATCH /tables/{id}, DELETE /tables/{id}

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

// Aksi: Hapus meja
if (isset($_GET['delete'])) {
    $result = api_request('DELETE', API_TABLES . '/' . (int) $_GET['delete']);
    if ($result['ok']) {
        $message = 'Meja berhasil dihapus.';
    } else {
        $message = api_error_message($result, 'Gagal menghapus meja.');
        $messageType = 'red';
    }
    header("Location: " . route('kelola_meja'));
    exit;
}

// Aksi: Ubah status meja
if (isset($_GET['status']) && isset($_GET['id'])) {
    $result = api_request('PATCH', API_TABLES . '/' . (int) $_GET['id'], [
        'status' => $_GET['status'],
    ]);
    if ($result['ok']) {
        $message = 'Status meja diperbarui.';
    } else {
        $message = api_error_message($result, 'Gagal memperbarui status meja.');
        $messageType = 'red';
    }
    header("Location: " . route('kelola_meja'));
    exit;
}

// Aksi: Simpan meja baru / update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomor      = trim($_POST['table_number'] ?? '');
    $kapasitas  = (int) ($_POST['capacity'] ?? 0);
    $zona       = $_POST['location_area'] ?? 'indoor';
    $tableId    = (int) ($_POST['table_id'] ?? 0);

    if ($nomor === '' || $kapasitas < 1) {
        $message = 'Nomor meja dan kapasitas wajib diisi dengan benar.';
        $messageType = 'red';
    } else {
        $payload = [
            'table_number'  => $nomor,
            'capacity'      => $kapasitas,
            'location_area' => $zona,
            'status'        => $_POST['status'] ?? 'available',
        ];

        if ($tableId > 0) {
            $result = api_request('PUT', API_TABLES . '/' . $tableId, $payload);
        } else {
            $result = api_request('POST', API_TABLES, $payload);
        }

        if ($result['ok']) {
            $message = $tableId > 0
                ? "Meja $nomor berhasil diperbarui."
                : "Meja $nomor (kapasitas $kapasitas, zona $zona) berhasil ditambahkan.";
        } else {
            $message = api_error_message($result, 'Gagal menyimpan meja.');
            $messageType = 'red';
        }
    }
}

// Ambil daftar meja dari backend
$tables = [];
$tablesResult = api_get(API_TABLES);
if ($tablesResult['ok'] && isset($tablesResult['data']['data'])) {
    $raw = $tablesResult['data']['data'];
    $items = $raw['data'] ?? $raw;
    foreach ($items as $t) {
        $tables[] = [
            'table_id'     => $t['table_id'],
            'table_number' => $t['table_number'],
            'capacity'     => $t['capacity'],
            'location_area'=> $t['location_area'],
            'status'       => $t['status'],
        ];
    }
}

// Fallback demo bila backend tidak tersedia
if (empty($tables)) {
    $tables = [
        ['table_id' => 1, 'table_number' => 'T01', 'capacity' => 2, 'location_area' => 'indoor',  'status' => 'available'],
        ['table_id' => 2, 'table_number' => 'T02', 'capacity' => 2, 'location_area' => 'indoor',  'status' => 'occupied'],
        ['table_id' => 3, 'table_number' => 'T03', 'capacity' => 4, 'location_area' => 'indoor',  'status' => 'reserved'],
        ['table_id' => 4, 'table_number' => 'T04', 'capacity' => 4, 'location_area' => 'indoor',  'status' => 'available'],
        ['table_id' => 5, 'table_number' => 'T05', 'capacity' => 6, 'location_area' => 'indoor',  'status' => 'available'],
        ['table_id' => 6, 'table_number' => 'T06', 'capacity' => 6, 'location_area' => 'outdoor', 'status' => 'occupied'],
        ['table_id' => 7, 'table_number' => 'T07', 'capacity' => 4, 'location_area' => 'outdoor', 'status' => 'available'],
        ['table_id' => 8, 'table_number' => 'T08', 'capacity' => 8, 'location_area' => 'vip',     'status' => 'reserved'],
        ['table_id' => 9, 'table_number' => 'T09', 'capacity' => 8, 'location_area' => 'vip',     'status' => 'available'],
        ['table_id' => 10, 'table_number' => 'T10', 'capacity' => 4, 'location_area' => 'smoking', 'status' => 'maintenance'],
    ];
}
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        <?php $sidebarRole = 'admin'; $sidebarActive = 'kelola_meja'; include __DIR__ . '/../components/dashboard-sidebar.php'; ?>

        <div class="lg:col-span-3 space-y-6">
            <div class="bg-white/90 border border-[#eadfd4] rounded-3xl p-8 md:p-10 shadow-sm space-y-6">

                <div class="border-b border-[#eadfd4] pb-6">
                    <span class="text-xs uppercase tracking-widest text-[#8a5d49] font-bold">Konfigurasi Denah</span>
                    <h1 class="font-display text-3xl font-bold text-[#201913] mt-1">Kelola Meja & Layout</h1>
                    <p class="text-sm text-[#66574b] mt-1">Tambah, hapus, dan ubah kapasitas serta zona meja (FR-011).</p>
                </div>

                <?php if ($message !== ''): ?>
                    <div class="p-4 rounded-2xl border text-sm <?= $messageType === 'green' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-700' ?>"><?= e($message) ?></div>
                <?php endif; ?>

                <!-- Form Tambah Meja -->
                <form action="<?= route('kelola_meja') ?>" method="POST" class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-6 space-y-4">
                    <h2 class="font-display text-lg font-bold text-[#201913]">+ Tambah Meja Baru</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Nomor Meja</label>
                            <input type="text" name="table_number" required placeholder="T17"
                                   class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Kapasitas</label>
                            <input type="number" name="capacity" required min="1" max="20" placeholder="4"
                                   class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Zona</label>
                            <select name="location_area" class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
                                <option value="indoor">Indoor</option>
                                <option value="outdoor">Outdoor</option>
                                <option value="vip">VIP</option>
                                <option value="smoking">Smoking</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="bg-[#5e392e] hover:bg-[#4a2c24] text-white text-xs font-bold py-2.5 px-6 rounded-xl transition shadow-sm">
                        Simpan Meja
                    </button>
                </form>

                <!-- Daftar Meja -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-[#4f4338]">
                        <thead>
                            <tr class="border-b border-[#eadfd4] text-[#8a5d49] text-xs uppercase tracking-wider">
                                <th class="py-3 pr-4">No</th>
                                <th class="py-3 pr-4">Kapasitas</th>
                                <th class="py-3 pr-4">Zona</th>
                                <th class="py-3 pr-4">Status</th>
                                <th class="py-3 pr-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tables as $t): ?>
                                <tr class="border-b border-[#eadfd4]">
                                    <td class="py-3 pr-4 font-bold text-[#201913]"><?= e($t['table_number']) ?></td>
                                    <td class="py-3 pr-4"><?= (int) $t['capacity'] ?> org</td>
                                    <td class="py-3 pr-4 uppercase text-xs font-bold text-[#8a5d49]"><?= e($t['location_area']) ?></td>
                                    <td class="py-3 pr-4">
                                        <span class="status-badge status-<?= e($t['status']) ?>"><?= e(ucfirst($t['status'])) ?></span>
                                    </td>
                                    <td class="py-3 pr-4 space-x-2">
                                        <?php if ($t['status'] === 'available'): ?>
                                            <a href="<?= route('kelola_meja', ['id' => $t['table_id'], 'status' => 'maintenance']) ?>" class="text-[11px] font-bold text-amber-600 hover:underline" onclick="return confirm('Tandai meja ini sebagai maintenance?')">Maintenance</a>
                                        <?php elseif ($t['status'] === 'maintenance'): ?>
                                            <a href="<?= route('kelola_meja', ['id' => $t['table_id'], 'status' => 'available']) ?>" class="text-[11px] font-bold text-green-600 hover:underline">Siap Pakai</a>
                                        <?php endif; ?>
                                        <a href="<?= route('kelola_meja', ['delete' => $t['table_id']]) ?>" class="text-[11px] font-bold text-red-600 hover:underline" onclick="return confirm('Hapus meja ini?')">Hapus</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
