<?php
// pages/preview_restoran.php — Preview Restoran (daftar restoran dari database)

require_once __DIR__ . '/../src/config/api.config.php';
require_once __DIR__ . '/../src/utils/api.php';

$restaurants = [];
$result = api_get(API_RESTAURANTS . '?limit=100');
if ($result['ok']) {
    $raw = $result['data']['data'] ?? [];
    $restaurants = $raw['data'] ?? $raw;
}
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        <?php $sidebarRole = 'customer'; $sidebarActive = 'preview_restoran'; include __DIR__ . '/../components/dashboard-sidebar.php'; ?>

        <div class="lg:col-span-3 space-y-6">

            <!-- Header -->
            <div class="bg-[#5e392e] rounded-3xl p-8 text-white shadow-sm">
                <span class="text-xs uppercase tracking-widest text-[#e8c39e] font-bold">Menu Preview Restoran</span>
                <h1 class="font-display text-3xl font-bold mt-1">Jelajahi Restoran Kami</h1>
                <p class="text-sm text-[#e8c39e] mt-1">
                    Diambil langsung dari database. Lihat detail restoran, lalu buat reservasi meja Anda.
                </p>
            </div>

            <!-- Daftar Restoran -->
            <?php if (empty($restaurants)): ?>
                <div class="bg-white/80 border border-[#eadfd4] rounded-3xl p-10 text-center text-sm text-[#8a5d49]">
                    Belum ada restoran yang tersedia di database. Silakan coba lagi nanti.
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php foreach ($restaurants as $resto): ?>
                        <?php
                        $restoId    = (int) ($resto['restaurant_id'] ?? 0);
                        $restoName  = $resto['name'] ?? 'Restoran';
                        $restoAddr  = $resto['address'] ?? '';
                        $restoPhone = $resto['phone'] ?? $resto['phone_number'] ?? '';
                        $restoRating = $resto['rating'] ?? '5.0';
                        $restoImg   = api_resto_image($resto['image_url'] ?? '', $restoId);
                        $tableCount = (int) ($resto['tables_count'] ?? 0);
                        ?>
                        <div class="bg-white/80 border border-[#eadfd4] rounded-3xl overflow-hidden shadow-sm flex flex-col hover:border-[#8a5d49] transition group">
                            <div class="w-full h-44 overflow-hidden border-b border-[#eadfd4] bg-[#f4ece1] flex items-center justify-center">
                                <img src="<?= e($restoImg) ?>" alt="<?= e($restoName) ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" loading="lazy">
                            </div>

                            <div class="p-5 flex flex-col flex-1 gap-3">
                                <div>
                                    <div class="flex items-start justify-between gap-2">
                                        <h3 class="font-display text-lg font-bold text-[#201913]"><?= e($restoName) ?></h3>
                                        <span class="text-xs font-bold text-[#8a5d49] bg-[#efebe4] px-2.5 py-1 rounded-full shrink-0">★ <?= e($restoRating) ?></span>
                                    </div>
                                    <?php if ($restoAddr): ?>
                                        <p class="text-xs text-[#8a5d49] mt-1"><?= e($restoAddr) ?></p>
                                    <?php endif; ?>
                                </div>

                                <div class="flex items-center gap-2 text-[11px] font-bold text-[#5e392e]">
                                    <span class="bg-[#f4ece1] px-2.5 py-1 rounded-full"><?= (int) $tableCount ?> Meja</span>
                                    <?php if ($restoPhone): ?>
                                        <span class="bg-[#f4ece1] px-2.5 py-1 rounded-full"><?= e($restoPhone) ?></span>
                                    <?php endif; ?>
                                </div>

                                <div class="flex items-center gap-2 pt-2 border-t border-[#eadfd4] mt-auto">
                                    <a href="<?= route('detail_restoran', ['resto_id' => $restoId]) ?>"
                                       class="w-1/2 bg-[#efebe4] hover:bg-[#e2dcd2] text-[#5e392e] text-[11px] font-bold py-2.5 px-3 rounded-xl transition text-center border border-[#eadfd4]">
                                        Lihat Detail
                                    </a>
                                    <a href="<?= route('reservasi', ['resto' => $restoId]) ?>"
                                       class="w-1/2 bg-[#5e392e] hover:bg-[#4a2c24] text-white text-[11px] font-bold py-2.5 px-3 rounded-xl transition text-center shadow-sm">
                                        Buat Reservasi →
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>