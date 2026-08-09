<?php
// pages/kelola_menu.php — Kelola Menu & Kategori (Admin)
// Terhubung ke backend: GET/POST/PUT/DELETE /menus, GET/POST /categories

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'create';

    if ($action === 'create') {
        $nama  = trim($_POST['item_name'] ?? '');
        $harga = (int) ($_POST['price'] ?? 0);
        $catId = (int) ($_POST['category_id'] ?? 0);
        $desc  = trim($_POST['description'] ?? '');

        if ($nama === '' || $harga < 1 || $catId < 1) {
            $message = 'Nama menu, harga, dan kategori wajib diisi dengan benar.';
            $messageType = 'red';
        } else {
            $payload = [
                'category_id' => $catId,
                'item_name' => $nama,
                'price' => $harga,
                'description' => $desc !== '' ? $desc : null,
                'is_available' => true,
            ];
            $result = api_request('POST', API_MENUS, $payload);
            if ($result['ok']) {
                $message = "Menu '$nama' (Rp " . number_format($harga, 0, ',', '.') . ") berhasil ditambahkan.";
            } else {
                $message = api_error_message($result, 'Gagal menambahkan menu.');
                $messageType = 'red';
            }
        }
    } elseif ($action === 'create_category') {
        $catName = trim($_POST['category_name'] ?? '');
        if ($catName === '') {
            $message = 'Nama kategori wajib diisi.';
            $messageType = 'red';
        } else {
            $result = api_request('POST', API_CATEGORIES, ['category_name' => $catName]);
            if ($result['ok']) {
                $message = "Kategori '$catName' berhasil ditambahkan.";
            } else {
                $message = api_error_message($result, 'Gagal menambahkan kategori.');
                $messageType = 'red';
            }
        }
    }
}

// Toggle ketersediaan menu
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $menuId = (int) $_GET['toggle'];
    $target = api_get(API_MENUS . '/' . $menuId);
    if ($target['ok'] && isset($target['data']['data'])) {
        $m = $target['data']['data'];
        $result = api_request('PUT', API_MENUS . '/' . $menuId, ['is_available' => !($m['is_available'] ?? true)]);
        if ($result['ok']) {
            $message = 'Ketersediaan menu diperbarui.';
        } else {
            $message = api_error_message($result, 'Gagal memperbarui menu.');
            $messageType = 'red';
        }
    }
}

// Ambil kategori dari backend (fallback demo)
$kategoriList = [];
$catResult = api_get(API_CATEGORIES . '?limit=100');
if ($catResult['ok']) {
    $raw = $catResult['data']['data'] ?? [];
    $kategoriList = $raw['data'] ?? $raw;
}
if (empty($kategoriList)) {
    $kategoriList = [
        ['category_id' => 0, 'category_name' => 'Makanan Utama'],
        ['category_id' => 0, 'category_name' => 'Makanan Pembuka'],
        ['category_id' => 0, 'category_name' => 'Makanan Penutup'],
        ['category_id' => 0, 'category_name' => 'Minuman'],
    ];
}

// Ambil daftar menu dari backend (fallback demo)
$menuList = [];
$menuResult = api_get(API_MENUS . '?limit=100');
if ($menuResult['ok']) {
    $raw = $menuResult['data']['data'] ?? [];
    $menuList = $raw['data'] ?? $raw;
}
if (empty($menuList)) {
    $menuList = [
        ['menu_id' => 0, 'item_name' => 'Teppanyaki Wagyu Steak', 'price' => 295000, 'is_available' => true, 'category' => ['category_name' => 'Makanan Utama']],
        ['menu_id' => 0, 'item_name' => 'Caesar Salad', 'price' => 68000, 'is_available' => true, 'category' => ['category_name' => 'Makanan Pembuka']],
        ['menu_id' => 0, 'item_name' => 'Tiramisu', 'price' => 75000, 'is_available' => true, 'category' => ['category_name' => 'Makanan Penutup']],
        ['menu_id' => 0, 'item_name' => 'Espresso', 'price' => 28000, 'is_available' => true, 'category' => ['category_name' => 'Minuman']],
    ];
}
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        <?php $sidebarRole = 'admin'; $sidebarActive = 'kelola_menu'; include __DIR__ . '/../components/dashboard-sidebar.php'; ?>

        <div class="lg:col-span-3 space-y-6">
            <div class="bg-white/90 border border-[#eadfd4] rounded-3xl p-8 md:p-10 shadow-sm space-y-6">

                <div class="border-b border-[#eadfd4] pb-6">
                    <span class="text-xs uppercase tracking-widest text-[#8a5d49] font-bold">Manajemen Kuliner</span>
                    <h1 class="font-display text-3xl font-bold text-[#201913] mt-1">Kelola Menu & Kategori</h1>
                    <p class="text-sm text-[#66574b] mt-1">Tambah, ubah harga, dan kelola ketersediaan menu.</p>
                </div>

                <?php if ($message !== ''): ?>
                    <div class="p-4 rounded-2xl border text-sm <?= $messageType === 'green' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-700' ?>"><?= e($message) ?></div>
                <?php endif; ?>

                <form action="<?= route('kelola_menu') ?>" method="POST" class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-6 space-y-4">
                    <input type="hidden" name="action" value="create">
                    <h2 class="font-display text-lg font-bold text-[#201913]">+ Tambah Menu Baru</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Nama Menu</label>
                            <input type="text" name="item_name" required placeholder="Nama hidangan"
                                   class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm outline-none focus:border-[#8a5d49] transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Harga (Rp)</label>
                            <input type="number" name="price" required min="1000" placeholder="75000"
                                   class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm outline-none focus:border-[#8a5d49] transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Kategori</label>
                            <select name="category_id" class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm outline-none focus:border-[#8a5d49] transition">
                                <?php foreach ($kategoriList as $k): ?>
                                    <option value="<?= (int) ($k['category_id'] ?? 0) ?>"><?= e($k['category_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Deskripsi (opsional)</label>
                            <input type="text" name="description" placeholder="Bahan utama, porsi, dsb."
                                   class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm outline-none focus:border-[#8a5d49] transition">
                        </div>
                    </div>
                    <button type="submit" class="bg-[#5e392e] hover:bg-[#4a2c24] text-white text-xs font-bold py-2.5 px-6 rounded-xl transition shadow-sm">
                        Simpan Menu
                    </button>
                </form>

                <form action="<?= route('kelola_menu') ?>" method="POST" class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-5 flex flex-col md:flex-row md:items-end gap-3">
                    <input type="hidden" name="action" value="create_category">
                    <div class="flex-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Tambah Kategori Baru</label>
                        <input type="text" name="category_name" required placeholder="Nama kategori (mis. Minuman Panas)"
                               class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm outline-none focus:border-[#8a5d49] transition">
                    </div>
                    <button type="submit" class="bg-[#5e392e] hover:bg-[#4a2c24] text-white text-xs font-bold py-3 px-6 rounded-xl transition shadow-sm">
                        Tambah Kategori
                    </button>
                </form>

                <!-- Daftar Menu -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-[#4f4338]">
                        <thead>
                            <tr class="border-b border-[#eadfd4] text-[#8a5d49] text-xs uppercase tracking-wider">
                                <th class="py-3 pr-4">Nama</th>
                                <th class="py-3 pr-4">Kategori</th>
                                <th class="py-3 pr-4">Harga</th>
                                <th class="py-3 pr-4">Tersedia</th>
                                <th class="py-3 pr-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($menuList as $m): ?>
                                <tr class="border-b border-[#eadfd4]">
                                    <td class="py-3 pr-4 font-bold text-[#201913]"><?= e($m['item_name'] ?? '') ?></td>
                                    <td class="py-3 pr-4"><?= e($m['category']['category_name'] ?? 'Tanpa kategori') ?></td>
                                    <td class="py-3 pr-4">Rp <?= number_format((float) ($m['price'] ?? 0), 0, ',', '.') ?></td>
                                    <td class="py-3 pr-4">
                                        <span class="status-badge <?= ($m['is_available'] ?? true) ? 'status-completed' : 'status-cancelled' ?>">
                                            <?= ($m['is_available'] ?? true) ? 'Aktif' : 'Nonaktif' ?>
                                        </span>
                                    </td>
                                    <td class="py-3 pr-4">
                                        <?php if (($m['menu_id'] ?? 0) > 0): ?>
                                            <a href="<?= route('kelola_menu', ['toggle' => $m['menu_id']]) ?>" class="text-[11px] font-bold text-[#8a5d49] hover:underline">
                                                <?= ($m['is_available'] ?? true) ? 'Nonaktifkan' : 'Aktifkan' ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-[11px] text-[#a39a8f]">Demo</span>
                                        <?php endif; ?>
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
