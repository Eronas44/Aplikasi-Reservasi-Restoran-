<?php
// pages/menu.php — Langkah 3: Pre-order Makanan & Minuman (dari database + keranjang)

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: ' . route('login'));
    exit;
}

require_once __DIR__ . '/../src/config/api.config.php';
require_once __DIR__ . '/../src/utils/api.php';

$restoId = (int) ($_GET['resto'] ?? $_SESSION['current_reservation']['resto'] ?? 0);
if ($restoId <= 0) {
    $list = api_get(API_RESTAURANTS . '?limit=1');
    $raw = $list['data']['data'] ?? [];
    $first = ($raw['data'] ?? $raw)[0] ?? null;
    $restoId = (int) ($first['restaurant_id'] ?? 1);
}

$restoNama = 'Restoran';
$detail = api_get(API_RESTAURANTS . '/' . $restoId);
if ($detail['ok']) {
    $restoNama = $detail['data']['data']['name'] ?? $restoNama;
}

// ---- Keranjang (session) ----
$cart = $_SESSION['cart'] ?? [];

// Keranjang pre-order hanya berlaku untuk satu restoran. Jika restoran
// berbeda dari yang ada di keranjang, kosongkan keranjang agar item dari
// restoran lain tidak terbawa ke transaksi.
$cartResto = (int) ($_SESSION['cart_resto'] ?? 0);
if ($cartResto !== 0 && $cartResto !== $restoId) {
    $_SESSION['cart'] = [];
    $_SESSION['cart_resto'] = $restoId;
    $cart = [];
} elseif ($cartResto === 0) {
    $_SESSION['cart_resto'] = $restoId;
}

if (isset($_GET['action']) && $_GET['action'] === 'clear') {
    $_SESSION['cart'] = [];
    header('Location: ' . route('menu', ['resto' => $restoId]));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $menuId  = (int) ($_POST['menu_id'] ?? 0);
    $action  = $_POST['action'] ?? 'add';
    $qty     = max((int) ($_POST['qty'] ?? 1), 1);
    $payload = $_POST['payload'] ?? null;

    if ($menuId > 0 && is_array($payload) && isset($payload['name'])) {
        if ($action === 'remove') {
            unset($cart[$menuId]);
        } elseif ($action === 'set') {
            if ($qty <= 0) {
                unset($cart[$menuId]);
            } else {
                $cart[$menuId] = [
                    'qty'   => $qty,
                    'name'  => (string) $payload['name'],
                    'price' => (float) $payload['price'],
                ];
            }
        } else { // add
            $cart[$menuId] = [
                'qty'   => ($cart[$menuId]['qty'] ?? 0) + $qty,
                'name'  => (string) $payload['name'],
                'price' => (float) $payload['price'],
            ];
        }
        $_SESSION['cart'] = $cart;
        $_SESSION['cart_resto'] = $restoId;
    }

    // Redirect agar tidak double-submit saat refresh
    header('Location: ' . route('menu', ['resto' => $restoId]));
    exit;
}

// ---- Ambil menu dari database ----
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

$totalCart = 0;
foreach ($cart as $c) {
    $totalCart += (float) $c['price'] * (int) $c['qty'];
}
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-10">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white/90 border border-[#eadfd4] rounded-3xl p-8 md:p-10 shadow-sm space-y-8">

            <?php $step = 3; include __DIR__ . '/../components/reservation-stepper.php'; ?>

                <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-[#eadfd4] pb-6 gap-4">
                    <div>
                        <span class="text-xs uppercase tracking-widest text-[#8a5d49] font-bold">Langkah 3 dari 4</span>
                        <h1 class="font-display text-3xl font-bold text-[#201913] mt-1">Daftar Menu <?= e($restoNama) ?></h1>
                        <p class="text-sm text-[#66574b] mt-1">Pilih makanan &amp; minuman untuk pre-order. Keranjang tersimpan selama Anda melanjutkan reservasi.</p>
                    </div>
                    <a href="<?= route('pilih_meja') ?>" class="text-xs font-bold text-[#8a5d49] hover:underline">← Ubah Meja</a>
                </div>

                <!-- Restoran Terpilih (dipilih di tahap reservasi) -->
                <div class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-4 flex items-center gap-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#8a5d49] shrink-0">Restoran</span>
                    <span class="text-sm font-bold text-[#201913]"><?= e($restoNama) ?></span>
                </div>

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
                                    $inCart = isset($cart[$mid]['qty']) ? (int) $cart[$mid]['qty'] : 0;
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

                                            <form action="<?= route('menu', ['resto' => $restoId]) ?>" method="POST" class="flex items-center gap-2 mt-3 pt-2 border-t border-[#eadfd4]">
                                                <input type="hidden" name="menu_id" value="<?= $mid ?>">
                                                <input type="hidden" name="qty" value="1">
                                                <input type="hidden" name="payload[name]" value="<?= e($name) ?>">
                                                <input type="hidden" name="payload[price]" value="<?= $price ?>">
                                                <button type="submit" name="action" value="add" class="flex-1 bg-[#5e392e] hover:bg-[#4a2c24] text-white text-[11px] font-bold py-2 px-2 rounded-lg transition text-center shadow-sm">
                                                    + Tambah
                                                </button>
                                                <?php if ($inCart > 0): ?>
                                                    <a href="#" onclick="event.preventDefault();" class="text-[11px] font-bold text-[#8a5d49] bg-[#efebe4] px-2.5 py-2 rounded-lg"><?= $inCart ?>x</a>
                                                <?php endif; ?>
                                            </form>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>

        <!-- Ringkasan Keranjang -->
        <div class="lg:col-span-1">
            <div class="bg-white/90 border border-[#eadfd4] rounded-3xl p-5 shadow-sm space-y-4 sticky top-6">
                <div>
                    <span class="text-[10px] uppercase tracking-widest font-bold text-[#8a5d49]">Keranjang Pre-order</span>
                    <h2 class="font-display text-xl font-bold text-[#201913] mt-1">Ringkasan</h2>
                </div>

                <?php if (empty($cart)): ?>
                    <p class="text-xs text-[#66574b]">Belum ada menu di keranjang. Anda tetap bisa melanjutkan tanpa pre-order.</p>
                <?php else: ?>
                    <div class="space-y-2 max-h-72 overflow-y-auto pr-1">
                        <?php foreach ($cart as $cid => $c): ?>
                            <div class="flex items-center justify-between gap-2 bg-[#fcfaf7] border border-[#eadfd4] rounded-xl px-3 py-2">
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-[#201913] truncate"><?= e($c['name']) ?></p>
                                    <p class="text-[11px] text-[#8a5d49]"><?= (int) $c['qty'] ?> × Rp <?= number_format((float) $c['price'], 0, ',', '.') ?></p>
                                </div>
                                <form action="<?= route('menu', ['resto' => $restoId]) ?>" method="POST" class="shrink-0">
                                    <input type="hidden" name="menu_id" value="<?= $cid ?>">
                                    <input type="hidden" name="payload[name]" value="<?= e($c['name']) ?>">
                                    <input type="hidden" name="payload[price]" value="<?= (float) $c['price'] ?>">
                                    <button type="submit" name="action" value="remove" class="text-[#b3453c] hover:text-red-700 font-bold text-sm" title="Hapus">×</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="border-t border-[#eadfd4] pt-3 flex justify-between text-sm font-bold text-[#201913]">
                    <span>Subtotal</span>
                    <span>Rp <?= number_format($totalCart, 0, ',', '.') ?></span>
                </div>

                <div class="space-y-2 pt-2">
                    <a href="<?= route('pembayaran') ?>" class="block text-center bg-[#5e392e] hover:bg-[#4a2c24] text-white text-xs font-bold py-2.5 px-4 rounded-xl transition shadow-sm">
                        Lanjut ke Pembayaran →
                    </a>
                    <?php if (!empty($cart)): ?>
                        <a href="<?= route('menu', ['resto' => $restoId, 'action' => 'clear']) ?>" class="block text-center px-4 py-2 rounded-xl border border-[#eadfd4] text-stone-600 hover:bg-stone-50 text-[11px] font-bold transition">
                            Kosongkan Keranjang
                        </a>
                    <?php endif; ?>
                </div>
                </div>
            </div>
        </div>
</div>