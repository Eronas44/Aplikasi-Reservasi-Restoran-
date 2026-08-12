<?php
// pages/detail_restoran.php — Detail Restoran dari Database (Preview Restoran)

require_once __DIR__ . '/../src/config/api.config.php';
require_once __DIR__ . '/../src/utils/api.php';

$restoId = (int) ($_GET['resto_id'] ?? $_GET['resto'] ?? 0);

// Fallback: jika tidak ada id dikirim, ambil restoran pertama dari database.
if ($restoId <= 0) {
    $list = api_get(API_RESTAURANTS . '?limit=1');
    $raw = $list['data']['data'] ?? [];
    $first = ($raw['data'] ?? $raw)[0] ?? null;
    $restoId = (int) ($first['restaurant_id'] ?? 1);
}

$currentResto = null;
if ($restoId > 0) {
    $result = api_get(API_RESTAURANTS . '/' . $restoId);
    if ($result['ok']) {
        $currentResto = $result['data']['data'] ?? null;
    }
}

if (!$currentResto) {
    // Restoran tidak ditemukan di database
    ?>
    <div class="mx-auto max-w-3xl px-6 py-16 text-center">
        <div class="bg-white/90 border border-[#eadfd4] rounded-3xl p-10 shadow-sm">
            <h1 class="font-display text-3xl font-bold text-[#201913]">Restoran Tidak Ditemukan</h1>
            <p class="text-sm text-[#66574b] mt-2">Data restoran tidak tersedia di database.</p>
            <a href="<?= route('preview_restoran') ?>" class="inline-block mt-6 bg-[#5e392e] hover:bg-[#4a2c24] text-white text-xs font-bold py-2.5 px-6 rounded-xl transition shadow-sm">
                Kembali ke Preview Restoran
            </a>
        </div>
    </div>
    <?php
    return;
}

$restoName   = $currentResto['name'] ?? 'Restoran';
$restoAddr   = $currentResto['address'] ?? '';
$restoPhone  = $currentResto['phone'] ?? '';
$restoEmail  = $currentResto['email'] ?? '';
$restoRating = $currentResto['rating'] ?? '5.0';
$restoImg    = !empty($currentResto['image_url']) ? api_image_url($currentResto['image_url']) : '';
$tableCount  = isset($currentResto['tables_count']) ? (int) $currentResto['tables_count'] : count($currentResto['tables'] ?? []);
$menuCount   = isset($currentResto['menus_count']) ? (int) $currentResto['menus_count'] : 0;

// Jam operasional dari database (grouping by day)
$openingHours = $currentResto['opening_hours'] ?? [];
$dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$hoursText = [];
foreach ($openingHours as $oh) {
    $dayIdx = (int) ($oh['day_of_week'] ?? 0);
    $dayName = $dayNames[$dayIdx] ?? 'Hari';
    if (!empty($oh['is_closed'])) {
        $hoursText[] = $dayName . ': Tutup';
    } else {
        $hoursText[] = $dayName . ': ' . substr((string) ($oh['open_time'] ?? ''), 0, 5) . ' - ' . substr((string) ($oh['close_time'] ?? ''), 0, 5);
    }
}
$hoursText = array_slice($hoursText, 0, 7);
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-10">

  <div class="mb-6 flex flex-wrap items-center gap-3">
    <a href="<?= route('preview_restoran') ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#efebe4] hover:bg-[#decbbd] text-[#5e392e] text-xs font-bold transition shadow-sm">
      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
      </svg>
      Kembali ke Preview Restoran
    </a>
  </div>

  <div class="bg-white/90 border border-[#eadfd4] rounded-3xl p-8 md:p-10 shadow-sm backdrop-blur space-y-8">

    <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-[#eadfd4] pb-6 gap-4">
      <div>
        <span class="text-xs uppercase tracking-widest text-[#8a5d49] font-bold">Detail Restoran Mitra</span>
        <h1 class="font-display text-3xl md:text-4xl font-bold text-[#201913] mt-1"><?= e($restoName) ?></h1>
        <?php if ($restoAddr): ?>
          <p class="text-sm text-[#66574b] mt-1">📍 <?= e($restoAddr) ?></p>
        <?php endif; ?>
        <?php if ($restoPhone || $restoEmail): ?>
          <p class="text-sm text-[#66574b] mt-0.5">
            <?= e($restoPhone) ?><?= ($restoPhone && $restoEmail) ? ' &middot; ' : '' ?><?= e($restoEmail) ?>
          </p>
        <?php endif; ?>
      </div>

      <div class="flex flex-wrap items-center gap-3">
        <span class="text-sm font-bold text-[#8a5d49] bg-[#efebe4] px-4 py-2 rounded-full border border-[#eadfd4]">
          ★ <?= e($restoRating) ?> / 5.0
        </span>
        <a href="<?= route('reservasi', ['resto' => $restoId]) ?>" class="bg-[#5e392e] hover:bg-[#4a2c24] text-white text-xs font-bold py-2.5 px-6 rounded-xl transition shadow-sm">
          Reservasi Sekarang →
        </a>
      </div>
    </div>

    <!-- Gambar Restoran -->
    <div class="w-full h-64 md:h-[380px] rounded-2xl overflow-hidden border border-[#eadfd4] bg-[#f4ece1] flex items-center justify-center">
      <?php if ($restoImg): ?>
        <img src="<?= e($restoImg) ?>" alt="<?= e($restoName) ?>" class="w-full h-full object-cover">
      <?php else: ?>
        <svg class="w-20 h-20 text-[#c9a98a]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 9.75L12 4l9 5.75V20a1 1 0 01-1 1H4a1 1 0 01-1-1V9.75z"/>
        </svg>
      <?php endif; ?>
    </div>

    <!-- Statistik Singkat -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-5 text-center">
        <span class="block text-2xl font-display font-bold text-[#201913]"><?= $tableCount ?></span>
        <span class="text-xs font-bold uppercase tracking-wider text-[#8a5d49]">Total Meja</span>
      </div>
      <div class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-5 text-center">
        <span class="block text-2xl font-display font-bold text-[#201913]"><?= $menuCount ?></span>
        <span class="text-xs font-bold uppercase tracking-wider text-[#8a5d49]">Total Menu</span>
      </div>
      <div class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-5 text-center">
        <span class="block text-2xl font-display font-bold text-[#201913]">4</span>
        <span class="text-xs font-bold uppercase tracking-wider text-[#8a5d49]">Preferensi Area</span>
      </div>
    </div>

    <!-- Jam Operasional -->
    <div class="space-y-4">
      <h2 class="font-display text-2xl font-bold text-[#201913]">Jam Operasional</h2>
      <?php if ($hoursText): ?>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
          <?php foreach ($hoursText as $ht): ?>
            <div class="bg-[#fcfaf7] border border-[#eadfd4] rounded-xl px-4 py-3 text-sm font-bold text-[#201913]">
              <?= e($ht) ?>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p class="text-sm text-[#66574b]">Jam operasional belum diatur di database.</p>
      <?php endif; ?>
    </div>

    <!-- CTA -->
    <div class="pt-2 flex flex-wrap gap-4">
      <a href="<?= route('reservasi', ['resto' => $restoId]) ?>" class="bg-[#5e392e] hover:bg-[#4a2c24] text-white text-xs font-bold py-3 px-8 rounded-xl transition shadow-sm">
        Pilih Tanggal & Waktu Kunjungan →
      </a>
      <a href="<?= route('menu', ['resto' => $restoId]) ?>" class="px-5 py-3 rounded-xl border border-[#eadfd4] text-stone-600 hover:bg-stone-50 text-xs font-bold transition">
        Lihat Menu
      </a>
    </div>

  </div>
</div>