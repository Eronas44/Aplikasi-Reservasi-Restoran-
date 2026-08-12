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
    $restoId    = (int) ($_POST['restaurant_id'] ?? 0);

    if ($nomor === '' || $kapasitas < 1) {
        $message = 'Nomor meja dan kapasitas wajib diisi dengan benar.';
        $messageType = 'red';
    } else {
        $payload = [
            'table_number'  => $nomor,
            'capacity'      => $kapasitas,
            'location_area' => $zona,
            'status'        => $_POST['status'] ?? 'available',
            'restaurant_id' => $restoId > 0 ? $restoId : null,
            'layout_row'    => ($_POST['layout_row'] ?? '') !== '' ? (int) $_POST['layout_row'] : null,
            'layout_column' => ($_POST['layout_column'] ?? '') !== '' ? (int) $_POST['layout_column'] : null,
        ];

        if ($tableId > 0) {
            $result = api_request('PUT', API_TABLES . '/' . $tableId, $payload);
        } else {
            $result = api_request('POST', API_TABLES, $payload);
        }

        if ($result['ok']) {
            $_SESSION['flash_message'] = $tableId > 0
                ? "Meja $nomor berhasil diperbarui."
                : "Meja $nomor (kapasitas $kapasitas, zona $zona) berhasil ditambahkan.";
            $_SESSION['flash_type'] = 'green';
            header("Location: " . route('kelola_meja'));
            exit;
        } else {
            $message = api_error_message($result, 'Gagal menyimpan meja.');
            $messageType = 'red';
        }
    }
}

// Ambil flash message jika ada
if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    $messageType = $_SESSION['flash_type'] ?? 'green';
    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
}

// Ambil daftar meja dari backend
$tables = [];
$tablesResult = api_get(API_TABLES . '?limit=200');
if ($tablesResult['ok']) {
    // Response paginated: {data: {data: [...], ...}}
    $outer = $tablesResult['data']['data'] ?? [];
    $items = $outer['data'] ?? (is_array($outer) && isset($outer[0]) ? $outer : []);
    foreach ($items as $t) {
        $tables[] = [
            'table_id'      => $t['table_id'],
            'table_number'  => $t['table_number'],
            'capacity'      => $t['capacity'],
            'location_area' => $t['location_area'],
            'status'        => $t['status'],
            'layout_row'    => $t['layout_row'] ?? null,
            'layout_column' => $t['layout_column'] ?? null,
            'restaurant'    => $t['restaurant'] ?? null,
        ];
    }
}

// Ambil daftar restoran dari backend
$restoList = [];
$restoResult = api_get(API_RESTAURANTS . '?all=1');
if ($restoResult['ok']) {
    $restoRaw = $restoResult['data']['data'] ?? [];
    $restoList = $restoRaw['data'] ?? (is_array($restoRaw) && isset($restoRaw[0]) ? $restoRaw : []);
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
                    <p class="text-sm text-[#66574b] mt-1">Tambah meja sekaligus tentukan posisi yang disimpan pada denah.</p>
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
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Baris Denah (opsional)</label>
                            <input type="number" name="layout_row" min="1" max="100" placeholder="Otomatis"
                                   class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Kolom Denah (1–8)</label>
                            <input type="number" name="layout_column" min="1" max="8" placeholder="Otomatis"
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
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Restoran</label>
                            <select name="restaurant_id" class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
                                <option value="">Pilih restoran</option>
                                <?php foreach ($restoList as $r): ?>
                                    <option value="<?= (int) ($r['restaurant_id'] ?? 0) ?>"><?= e($r['name']) ?></option>
                                <?php endforeach; ?>
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
                                <th class="py-3 pr-4">Restoran</th>
                                <th class="py-3 pr-4">Kapasitas</th>
                                <th class="py-3 pr-4">Zona</th>
                                <th class="py-3 pr-4">Status</th>
                                <th class="py-3 pr-4">Posisi</th>
                                <th class="py-3 pr-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tables as $t): ?>
                                <tr class="border-b border-[#eadfd4]">
                                    <td class="py-3 pr-4 font-bold text-[#201913]"><?= e($t['table_number']) ?></td>
                                    <td class="py-3 pr-4"><?= e($t['restaurant']['name'] ?? '-') ?></td>
                                    <td class="py-3 pr-4"><?= (int) $t['capacity'] ?> org</td>
                                    <td class="py-3 pr-4 uppercase text-xs font-bold text-[#8a5d49]"><?= e($t['location_area']) ?></td>
                                    <td class="py-3 pr-4">
                                        <span class="status-badge status-<?= e($t['status']) ?>"><?= e(ucfirst($t['status'])) ?></span>
                                    </td>
                                    <td class="py-3 pr-4 text-xs">B<?= e($t['layout_row'] ?? '-') ?> / K<?= e($t['layout_column'] ?? '-') ?></td>
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
