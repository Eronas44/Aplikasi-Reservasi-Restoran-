<?php
// menu.php — Menu Kuliner Kafiber (data diambil dari backend API)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;

$categories = [];
$menus = [];
$apiError = '';

if ($isLoggedIn) {
    require_once dirname(__DIR__) . '/config/api.config.php';
    require_once dirname(__DIR__) . '/utils/api.php';

    $catResult = api_get(API_CATEGORIES . '?per_page=50');
    if ($catResult['ok']) {
        $categories = $catResult['data']['data']['data'] ?? $catResult['data']['data'] ?? [];
    }

    $menuResult = api_get(API_MENUS . '?per_page=50');
    if ($menuResult['ok']) {
        $menus = $menuResult['data']['data']['data'] ?? $menuResult['data']['data'] ?? [];
    } else {
        $apiError = api_error_message($menuResult, '');
    }
}

include LAYOUTS_PATH . '/header.php';
?>

<section class="mx-auto max-w-7xl px-6 py-12 lg:px-10">
  <!-- Judul Halaman -->
  <div class="text-center max-w-2xl mx-auto mb-12">
    <span class="eyebrow">Menu Kuliner</span>
    <h1 class="mt-3 font-display text-4xl text-[#201913] md:text-5xl tracking-tight">
      Pilihan Hidangan Kami
    </h1>
    <p class="mt-4 text-sm md:text-base text-[#66574b] leading-relaxed">
      Menu disajikan langsung dari sistem restoran. Pilih hidangan favorit Anda saat reservasi.
    </p>
  </div>

  <?php if (!$isLoggedIn): ?>
    <div class="bg-[#faf8f5] border border-[#eadfd4] rounded-3xl p-10 text-center shadow-sm max-w-xl mx-auto">
      <p class="text-sm text-[#66574b] mb-5">Silakan masuk terlebih dahulu untuk melihat menu kuliner.</p>
      <a href="<?= route('login') ?>" class="inline-block bg-[#8a5d49] hover:bg-[#734d3d] text-white text-xs font-bold px-6 py-3 rounded-full shadow transition">
        Masuk ke Sistem
      </a>
    </div>
  <?php elseif (!empty($apiError)): ?>
    <div class="max-w-xl mx-auto p-4 rounded-2xl bg-red-50 border border-red-200 text-red-600 text-sm font-medium text-center">
      <?= htmlspecialchars($apiError, ENT_QUOTES, 'UTF-8') ?>
    </div>
  <?php elseif (empty($menus)): ?>
    <div class="bg-[#faf8f5] border border-[#eadfd4] rounded-3xl p-10 text-center shadow-sm max-w-xl mx-auto">
      <p class="text-sm text-[#66574b]">Belum ada menu tersedia.</p>
    </div>
  <?php else: ?>
    <?php foreach ($categories as $category): ?>
      <?php
      $catMenus = array_filter($menus, function ($m) use ($category) {
          return (int) ($m['category_id'] ?? 0) === (int) ($category['category_id'] ?? 0);
      });
      if (count($catMenus) === 0) {
          continue;
      }
      ?>
      <div class="mb-12">
        <div class="flex items-end justify-between gap-6 mb-6">
          <h2 class="font-display text-2xl md:text-3xl text-[#201913]">
            <?= htmlspecialchars($category['category_name'] ?? 'Menu', ENT_QUOTES, 'UTF-8') ?>
          </h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <?php foreach ($catMenus as $menu): ?>
            <div class="bg-[#faf8f5] border border-[#eadfd4] rounded-3xl p-6 shadow-sm hover:shadow-md transition flex flex-col">
              <div class="flex items-start justify-between gap-4">
                <h3 class="font-display text-lg font-semibold text-[#201913]">
                  <?= htmlspecialchars($menu['item_name'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                </h3>
                <?php if (!empty($menu['is_available'])): ?>
                  <span class="shrink-0 text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-green-100 text-green-700">Tersedia</span>
                <?php else: ?>
                  <span class="shrink-0 text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-gray-100 text-gray-500">Habis</span>
                <?php endif; ?>
              </div>
              <?php if (!empty($menu['description'])): ?>
                <p class="text-xs text-[#66574b] mt-2 leading-relaxed flex-1"><?= htmlspecialchars($menu['description'], ENT_QUOTES, 'UTF-8') ?></p>
              <?php endif; ?>
              <p class="mt-4 font-bold text-[#8a5d49]">Rp <?= number_format((float) ($menu['price'] ?? 0), 0, ',', '.') ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</section>

<?php include LAYOUTS_PATH . '/footer.php'; ?>
