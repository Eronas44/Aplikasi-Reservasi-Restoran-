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
        $restoId = (int) ($_POST['restaurant_id'] ?? 0);

        if ($nama === '' || $harga < 1 || $catId < 1) {
            $message = 'Nama menu, harga, dan kategori wajib diisi dengan benar.';
            $messageType = 'red';
        } else {
            $payload = [
                'category_id' => $catId,
                'restaurant_id' => $restoId > 0 ? $restoId : null,
                'item_name' => $nama,
                'price' => $harga,
                'description' => $desc !== '' ? $desc : null,
                'is_available' => true,
            ];

            $hasImage = isset($_FILES['image'])
                && ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK
                && is_uploaded_file($_FILES['image']['tmp_name'] ?? '');

            $result = $hasImage
                ? api_upload(API_MENUS, $payload, ['image' => $_FILES['image']], 'POST')
                : api_request('POST', API_MENUS, $payload);

            if ($result['ok']) {
                $_SESSION['flash_message'] = "Menu '$nama' (Rp " . number_format($harga, 0, ',', '.') . ") berhasil ditambahkan.";
                $_SESSION['flash_type'] = 'green';
                header("Location: " . route('kelola_menu'));
                exit;
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
                $_SESSION['flash_message'] = "Kategori '$catName' berhasil ditambahkan.";
                $_SESSION['flash_type'] = 'green';
                header("Location: " . route('kelola_menu'));
                exit;
            } else {
                $message = api_error_message($result, 'Gagal menambahkan kategori.');
                $messageType = 'red';
            }
        }
    } elseif ($action === 'update') {
        $menuId = (int) ($_POST['menu_id'] ?? 0);
        $nama   = trim($_POST['item_name'] ?? '');
        $harga  = (int) ($_POST['price'] ?? 0);
        $catId  = (int) ($_POST['category_id'] ?? 0);
        $desc   = trim($_POST['description'] ?? '');
        $restoId = (int) ($_POST['restaurant_id'] ?? 0);
        $available = isset($_POST['is_available'])
            && in_array($_POST['is_available'], ['1', 'true', 'on'], true);

        if ($menuId < 1 || $nama === '' || $harga < 1 || $catId < 1) {
            $message = 'Nama menu, harga, dan kategori wajib diisi dengan benar.';
            $messageType = 'red';
        } else {
            $payload = [
                'category_id' => $catId,
                'restaurant_id' => $restoId > 0 ? $restoId : null,
                'item_name' => $nama,
                'price' => $harga,
                'description' => $desc !== '' ? $desc : null,
                'is_available' => $available,
            ];

            $hasImage = isset($_FILES['image'])
                && ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK
                && is_uploaded_file($_FILES['image']['tmp_name'] ?? '');

            $result = $hasImage
                ? api_upload(API_MENUS . '/' . $menuId, $payload, ['image' => $_FILES['image']], 'PUT')
                : api_request('PUT', API_MENUS . '/' . $menuId, $payload);

            if ($result['ok']) {
                $_SESSION['flash_message'] = "Menu '$nama' berhasil diperbarui.";
                $_SESSION['flash_type'] = 'green';
                header("Location: " . route('kelola_menu'));
                exit;
            } else {
                $message = api_error_message($result, 'Gagal memperbarui menu.');
                $messageType = 'red';
            }
        }
    } elseif ($action === 'update_category') {
        $catId = (int) ($_POST['category_id'] ?? 0);
        $catName = trim($_POST['category_name'] ?? '');
        if ($catId < 1 || $catName === '') {
            $message = 'Nama kategori wajib diisi.';
            $messageType = 'red';
        } else {
            $result = api_request('PUT', API_CATEGORIES . '/' . $catId, ['category_name' => $catName]);
            if ($result['ok']) {
                $_SESSION['flash_message'] = "Kategori '$catName' berhasil diperbarui.";
                $_SESSION['flash_type'] = 'green';
                header("Location: " . route('kelola_menu'));
                exit;
            } else {
                $message = api_error_message($result, 'Gagal memperbarui kategori.');
                $messageType = 'red';
            }
        }
    }
}

// Ambil flash message jika ada
if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    $messageType = $_SESSION['flash_type'] ?? 'green';
    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
}

// Toggle ketersediaan menu
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $menuId = (int) $_GET['toggle'];
    $target = api_get(API_MENUS . '/' . $menuId);
    if ($target['ok'] && isset($target['data']['data'])) {
        $m = $target['data']['data'];
        $result = api_request('PUT', API_MENUS . '/' . $menuId, ['is_available' => !($m['is_available'] ?? true)]);
        if ($result['ok']) {
            $_SESSION['flash_message'] = 'Ketersediaan menu diperbarui.';
            $_SESSION['flash_type'] = 'green';
        } else {
            $_SESSION['flash_message'] = api_error_message($result, 'Gagal memperbarui menu.');
            $_SESSION['flash_type'] = 'red';
        }
    }
    header("Location: " . route('kelola_menu'));
    exit;
}

// Mode edit: muat data menu / kategori dari backend untuk mengisi form
$editMenu = null;
if (isset($_GET['edit_menu']) && is_numeric($_GET['edit_menu'])) {
    $target = api_get(API_MENUS . '/' . (int) $_GET['edit_menu']);
    if ($target['ok'] && isset($target['data']['data'])) {
        $editMenu = $target['data']['data'];
    }
}

$editCategory = null;
if (isset($_GET['edit_category']) && is_numeric($_GET['edit_category'])) {
    $target = api_get(API_CATEGORIES . '/' . (int) $_GET['edit_category']);
    if ($target['ok'] && isset($target['data']['data'])) {
        $editCategory = $target['data']['data'];
    }
}

// Ambil kategori dari backend
$kategoriList = [];
$catResult = api_get(API_CATEGORIES . '?limit=100');
if ($catResult['ok']) {
    // Response paginated: { data: { data: [...], ... } }
    $outer = $catResult['data']['data'] ?? [];
    $kategoriList = $outer['data'] ?? (is_array($outer) && !isset($outer['data']) ? $outer : []);
}

// Ambil daftar menu dari backend
$menuList = [];
$menuResult = api_get(API_MENUS . '?limit=200');
if ($menuResult['ok']) {
    // Response paginated: { data: { data: [...], ... } }
    $outer = $menuResult['data']['data'] ?? [];
    $menuList = $outer['data'] ?? (is_array($outer) && !isset($outer['data']) ? $outer : []);
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

                <form action="<?= route('kelola_menu') ?>" method="POST" enctype="multipart/form-data" class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-6 space-y-4">
                    <input type="hidden" name="action" value="<?= $editMenu ? 'update' : 'create' ?>">
                    <?php if ($editMenu): ?>
                        <input type="hidden" name="menu_id" value="<?= (int) ($editMenu['menu_id'] ?? 0) ?>">
                    <?php endif; ?>
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-2">
                        <h2 class="font-display text-lg font-bold text-[#201913]">
                            <?= $editMenu ? '✏️ Ubah Menu (ID ' . (int) ($editMenu['menu_id'] ?? 0) . ')' : '+ Tambah Menu Baru' ?>
                        </h2>
                        <?php if ($editMenu): ?>
                            <a href="<?= route('kelola_menu') ?>" class="text-[11px] font-bold text-[#8a5d49] hover:underline">← Batal, kembali ke daftar</a>
                        <?php endif; ?>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Nama Menu</label>
                            <input type="text" name="item_name" required placeholder="Nama hidangan"
                                   value="<?= e($editMenu['item_name'] ?? '') ?>"
                                   class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm outline-none focus:border-[#8a5d49] transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Harga (Rp)</label>
                            <input type="number" name="price" required min="1000" placeholder="75000"
                                   value="<?= (int) ($editMenu['price'] ?? 0) ?>"
                                   class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm outline-none focus:border-[#8a5d49] transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Kategori</label>
                            <select name="category_id" class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm outline-none focus:border-[#8a5d49] transition">
                                <?php
                                $curCat = (int) ($editMenu['category']['category_id'] ?? ($editMenu['category_id'] ?? 0));
                                foreach ($kategoriList as $k): ?>
                                    <option value="<?= (int) ($k['category_id'] ?? 0) ?>" <?= (int) ($k['category_id'] ?? 0) === $curCat ? 'selected' : '' ?>>
                                        <?= e($k['category_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Restoran</label>
                            <select name="restaurant_id" class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm outline-none focus:border-[#8a5d49] transition">
                                <option value="">Semua restoran</option>
                                <?php
                                $curResto = (int) ($editMenu['restaurant']['restaurant_id'] ?? ($editMenu['restaurant_id'] ?? 0));
                                foreach ($restoList as $r): ?>
                                    <option value="<?= (int) ($r['restaurant_id'] ?? 0) ?>" <?= (int) ($r['restaurant_id'] ?? 0) === $curResto ? 'selected' : '' ?>>
                                        <?= e($r['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Deskripsi (opsional)</label>
                            <input type="text" name="description" placeholder="Bahan utama, porsi, dsb."
                                   value="<?= e($editMenu['description'] ?? '') ?>"
                                   class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm outline-none focus:border-[#8a5d49] transition">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Gambar Makanan (opsional, max 2MB)</label>
                            <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif"
                                   class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm outline-none focus:border-[#8a5d49] transition file:mr-3 file:rounded-lg file:border-0 file:bg-[#5e392e] file:px-4 file:py-2 file:text-xs file:font-bold file:text-white hover:file:bg-[#4a2c24]">
                            <?php if ($editMenu): ?>
                                <p class="text-[11px] text-[#8a5d49] mt-1">Kosongkan untuk mempertahankan gambar yang ada.</p>
                            <?php endif; ?>
                        </div>
                        <div class="flex items-end pb-1">
                            <label class="flex items-center gap-2 text-sm font-bold text-[#201913] cursor-pointer">
                                <input type="checkbox" name="is_available" value="1" <?= ($editMenu['is_available'] ?? true) ? 'checked' : '' ?>
                                       class="w-4 h-4 text-[#8a5d49] focus:ring-[#8a5d49]">
                                Tersedia
                            </label>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="submit" class="bg-[#5e392e] hover:bg-[#4a2c24] text-white text-xs font-bold py-2.5 px-6 rounded-xl transition shadow-sm">
                            <?= $editMenu ? 'Simpan Perubahan' : 'Simpan Menu' ?>
                        </button>
                        <?php if ($editMenu): ?>
                            <a href="<?= route('kelola_menu') ?>" class="text-xs font-bold text-[#8a5d49] hover:underline">Batal</a>
                        <?php endif; ?>
                    </div>
                </form>

                <form action="<?= route('kelola_menu') ?>" method="POST" class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-5 flex flex-col md:flex-row md:items-end gap-3">
                    <input type="hidden" name="action" value="<?= $editCategory ? 'update_category' : 'create_category' ?>">
                    <?php if ($editCategory): ?>
                        <input type="hidden" name="category_id" value="<?= (int) ($editCategory['category_id'] ?? 0) ?>">
                    <?php endif; ?>
                    <div class="flex-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">
                            <?= $editCategory ? 'Ubah Kategori (ID ' . (int) ($editCategory['category_id'] ?? 0) . ')' : 'Tambah Kategori Baru' ?>
                        </label>
                        <input type="text" name="category_name" required placeholder="Nama kategori (mis. Minuman Panas)"
                               value="<?= e($editCategory['category_name'] ?? '') ?>"
                               class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm outline-none focus:border-[#8a5d49] transition">
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="submit" class="bg-[#5e392e] hover:bg-[#4a2c24] text-white text-xs font-bold py-3 px-6 rounded-xl transition shadow-sm">
                            <?= $editCategory ? 'Simpan Perubahan' : 'Tambah Kategori' ?>
                        </button>
                        <?php if ($editCategory): ?>
                            <a href="<?= route('kelola_menu') ?>" class="text-xs font-bold text-[#8a5d49] hover:underline">Batal</a>
                        <?php endif; ?>
                    </div>
                </form>

                <!-- Daftar Kategori -->
                <div class="overflow-x-auto">
                    <table data-paginate class="w-full text-sm text-left text-[#4f4338]">
                        <thead>
                            <tr class="border-b border-[#eadfd4] text-[#8a5d49] text-xs uppercase tracking-wider">
                                <th class="py-3 pr-4">ID</th>
                                <th class="py-3 pr-4">Nama Kategori</th>
                                <th class="py-3 pr-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($kategoriList as $k): ?>
                                <tr class="border-b border-[#eadfd4]">
                                    <td class="py-3 pr-4 text-[#a39a8f]"><?= (int) ($k['category_id'] ?? 0) ?></td>
                                    <td class="py-3 pr-4 font-bold text-[#201913]"><?= e($k['category_name'] ?? '') ?></td>
                                    <td class="py-3 pr-4">
                                        <a href="<?= route('kelola_menu', ['edit_category' => $k['category_id']]) ?>" class="text-[11px] font-bold text-[#8a5d49] hover:underline">
                                            Edit
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Daftar Menu -->
                <div class="overflow-x-auto">
                    <table data-paginate class="w-full text-sm text-left text-[#4f4338]">
                        <thead>
                            <tr class="border-b border-[#eadfd4] text-[#8a5d49] text-xs uppercase tracking-wider">
                                <th class="py-3 pr-4">Gambar</th>
                                <th class="py-3 pr-4">Nama</th>
                                <th class="py-3 pr-4">Restoran</th>
                                <th class="py-3 pr-4">Kategori</th>
                                <th class="py-3 pr-4">Harga</th>
                                <th class="py-3 pr-4">Tersedia</th>
                                <th class="py-3 pr-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($menuList as $m): ?>
                                <tr class="border-b border-[#eadfd4]">
                                    <td class="py-3 pr-4">
                                        <?php $imgUrl = api_menu_image($m['image_url'] ?? '', (int) ($m['menu_id'] ?? 0)); ?>
                                        <img src="<?= e($imgUrl) ?>" alt="<?= e($m['item_name'] ?? '') ?>" class="w-14 h-14 object-cover rounded-xl border border-[#eadfd4]" loading="lazy">
                                    </td>
                                    <td class="py-3 pr-4 font-bold text-[#201913]"><?= e($m['item_name'] ?? '') ?></td>
                                    <td class="py-3 pr-4"><?= e($m['restaurant']['name'] ?? 'Semua restoran') ?></td>
                                    <td class="py-3 pr-4"><?= e($m['category']['category_name'] ?? 'Tanpa kategori') ?></td>
                                    <td class="py-3 pr-4">Rp <?= number_format((float) ($m['price'] ?? 0), 0, ',', '.') ?></td>
                                    <td class="py-3 pr-4">
                                        <span class="status-badge <?= ($m['is_available'] ?? true) ? 'status-completed' : 'status-cancelled' ?>">
                                            <?= ($m['is_available'] ?? true) ? 'Aktif' : 'Nonaktif' ?>
                                        </span>
                                    </td>
                                    <td class="py-3 pr-4">
                                        <?php if (($m['menu_id'] ?? 0) > 0): ?>
                                            <div class="flex items-center gap-3">
                                                <a href="<?= route('kelola_menu', ['edit_menu' => $m['menu_id']]) ?>" class="text-[11px] font-bold text-[#8a5d49] hover:underline">
                                                    Edit
                                                </a>
                                                <a href="<?= route('kelola_menu', ['toggle' => $m['menu_id']]) ?>" class="text-[11px] font-bold text-[#8a5d49] hover:underline">
                                                    <?= ($m['is_available'] ?? true) ? 'Nonaktifkan' : 'Aktifkan' ?>
                                                </a>
                                            </div>
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
