<?php
// pages/menu_view.php — Halaman Lihat Menu (read-only, tanpa pre-order/keranjang)
$isLoggedIn = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
$role = $_SESSION['role'] ?? 'customer';

if (!$isLoggedIn) {
    header('Location: ' . route('login'));
    exit;
}
if ($role === 'admin') {
    header('Location: ' . route('dashboard_admin'));
    exit;
}
if ($role === 'staff') {
    header('Location: ' . route('dashboard_staff'));
    exit;
}

require_once __DIR__ . '/../src/config/api.config.php';
require_once __DIR__ . '/../src/utils/api.php';

// Daftar restoran untuk dropdown pilihan
$restoList = [];
$restoListResult = api_get(API_RESTAURANTS . '?limit=100');
if ($restoListResult['ok']) {
    $raw = $restoListResult['data']['data'] ?? [];
    $restoList = $raw['data'] ?? (is_array($raw) && isset($raw[0]) ? $raw : []);
}

$restoId = (int) ($_GET['resto'] ?? 0);
if ($restoId <= 0 && !empty($restoList)) {
    $restoId = (int) ($restoList[0]['restaurant_id'] ?? 1);
}

$restoNama = 'Restoran';
$detail = api_get(API_RESTAURANTS . '/' . $restoId);
if ($detail['ok']) {
    $restoNama = $detail['data']['data']['name'] ?? $restoNama;
}

// ---- Ambil menu dari database (hanya yang tersedia) ----
$menusByCategory = [];
$result = api_get(API_MENUS . '?restaurant_id=' . $restoId . '&limit=500&available=1');
if ($result['ok']) {
    $raw = $result['data']['data'] ?? [];
    $items = isset($raw['data']) ? $raw['data'] : $raw;
    foreach ($items as $m) {
        $cat = $m['category']['category_name'] ?? 'Lainnya';
        $menusByCategory[$cat][] = $m;
    }
}
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        <?php $sidebarRole = 'customer'; $sidebarActive = 'menu_view'; include __DIR__ . '/../components/dashboard-sidebar.php'; ?>

        <div class="lg:col-span-3 space-y-6">
            <div class="bg-white/90 border border-[#eadfd4] rounded-3xl p-8 md:p-10 shadow-sm space-y-8">

                <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-[#eadfd4] pb-6 gap-4">
                    <div>
                        <span class="text-xs uppercase tracking-widest text-[#8a5d49] font-bold">Menu Restoran</span>
                        <h1 class="font-display text-3xl font-bold text-[#201913] mt-1">Daftar Menu <?= e($restoNama) ?></h1>
                        <p class="text-sm text-[#66574b] mt-1">Lihat daftar makanan &amp; minuman. Untuk memesan, lakukan pada proses reservasi.</p>
                    </div>
                    <a href="<?= route('reservasi', ['resto' => $restoId]) ?>" class="bg-[#5e392e] hover:bg-[#4a2c24] text-white text-xs font-bold py-2.5 px-5 rounded-xl transition shadow-sm">
                        Buat Reservasi →
                    </a>
                </div>

                <!-- Pilih Restoran -->
                <form method="GET" action="index.php" onchange="this.submit()" class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-4 flex flex-col md:flex-row md:items-center gap-4">
                    <input type="hidden" name="page" value="menu_view">
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49]">Restoran</label>
                    <select name="resto" class="flex-1 px-4 py-2.5 rounded-xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
                        <?php foreach ($restoList as $r): ?>
                            <option value="<?= (int) ($r['restaurant_id'] ?? 0) ?>" <?= (int) ($r['restaurant_id'] ?? 0) === $restoId ? 'selected' : '' ?>>
                                <?= e($r['name'] ?? 'Restoran') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>

                <?php if (empty($menusByCategory)): ?>
                    <div class="text-center py-12">
                        <p class="text-sm text-[#66574b]">Menu belum tersedia di database untuk restoran ini.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($menusByCategory as $kategori => $items): ?>
                        <div class="space-y-4">
                            <div class="flex items-center gap-3">
                                <span class="w-2 h-2 rounded-full bg-[#8a5d49]"></span>
                                <h2 class="font-display text-xl font-bold text-[#201913] tracking-tight"><?= e($kategori) ?></h2>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <?php foreach ($items as $m): ?>
                                    <?php
                                    $mid   = (int) ($m['menu_id'] ?? 0);
                                    $name  = $m['item_name'] ?? 'Menu';
                                    $price = (float) ($m['price'] ?? 0);
                                    $desc  = $m['description'] ?? '';
                                    $img   = api_menu_image($m['image_url'] ?? '', $mid);
                                    ?>
                                    <div class="bg-white/80 border border-[#eadfd4] rounded-2xl p-4 shadow-sm flex gap-4 hover:border-[#8a5d49] transition">
                                        <div class="w-20 h-20 rounded-xl overflow-hidden bg-[#f4ece1] shrink-0">
                                            <img src="<?= e($img) ?>" alt="<?= e($name) ?>" class="w-full h-full object-cover" loading="lazy">
                                        </div>

                                        <div class="flex flex-col flex-1 min-w-0">
                                            <div class="flex justify-between items-start gap-2">
                                                <h3 class="font-display font-bold text-[#201913] text-sm md:text-base"><?= e($name) ?></h3>
                                                <span class="text-xs font-bold text-[#8a5d49] bg-[#efebe4] px-2.5 py-1 rounded-full shrink-0">Rp <?= number_format($price, 0, ',', '.') ?></span>
                                            </div>
                                            <?php if ($desc): ?>
                                                <p class="text-xs text-[#66574b] mt-1 line-clamp-2"><?= e($desc) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>
