<?php
// pages/pilih_meja.php — Langkah 2: Pilih Meja yang Tersedia (insert booking)

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: ' . route('login'));
    exit;
}

require_once __DIR__ . '/../src/config/api.config.php';
require_once __DIR__ . '/../src/utils/api.php';

$draft = $_SESSION['current_reservation'] ?? null;
if (!$draft || empty($draft['tanggal']) || empty($draft['waktu'])) {
    header('Location: ' . route('reservasi', ['resto' => (int) ($_GET['resto'] ?? 1)]));
    exit;
}

$restoId = (int) ($draft['resto'] ?? 0);
$jumlahTamu = (int) ($draft['jumlah_tamu'] ?? 1);

// Nama restoran dari database
$restoNama = $draft['resto_nama'] ?? 'Restoran';
$detail = api_get(API_RESTAURANTS . '/' . $restoId);
if ($detail['ok']) {
    $restoNama = $detail['data']['data']['name'] ?? $restoNama;
}

// Ambil lagi daftar meja yang tersedia (sinkron dgn backend)
$q = http_build_query([
    'restaurant_id' => $restoId,
    'date'          => $draft['tanggal'],
    'time'          => $draft['waktu'],
    'guests'        => $jumlahTamu,
    'area'          => $draft['area'],
]);
$avail = api_get(API_TABLES . '/available?' . $q);
$tables = [];
if ($avail['ok']) {
    $raw = $avail['data']['data'] ?? [];
    $tables = $raw['data'] ?? $raw;
}

$selectedTableId = (int) ($_SESSION['selected_table_id'] ?? 0);

// Proses pemilihan meja
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tableId = (int) ($_POST['table_id'] ?? 0);
    $goMenu = isset($_POST['go_menu']);

    if ($tableId > 0) {
        $_SESSION['selected_table_id'] = $tableId;
        $_SESSION['current_reservation']['meja'] = [
            'table_id'   => $tableId,
            'table_number' => $_POST['table_number'] ?? '',
            'capacity'   => (int) ($_POST['capacity'] ?? 0),
            'area'       => $_POST['area'] ?? ($draft['area'] ?? 'indoor'),
        ];

        if ($goMenu) {
            header('Location: ' . route('menu', ['resto' => $restoId]));
        } else {
            header('Location: ' . route('pembayaran'));
        }
        exit;
    }

    $error = 'Silakan pilih meja terlebih dahulu.';
}

$areaLabels = [
    'indoor'  => 'Indoor AC',
    'outdoor' => 'Outdoor',
    'vip'     => 'VIP',
    'smoking' => 'Smoking',
];
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-10">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white/90 border border-[#eadfd4] rounded-3xl p-8 md:p-10 shadow-sm space-y-8">

            <?php $step = 2; include __DIR__ . '/../components/reservation-stepper.php'; ?>

            <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-[#eadfd4] pb-6 gap-4">
                    <div>
                        <span class="text-xs uppercase tracking-widest text-[#8a5d49] font-bold">Langkah 2 dari 4</span>
                        <h1 class="font-display text-3xl font-bold text-[#201913] mt-1">Pilih Meja Tersedia</h1>
                        <p class="text-sm text-[#66574b] mt-1">
                            <strong class="text-[#201913]"><?= e($restoNama) ?></strong> &middot;
                            <?= e($draft['tanggal']) ?> &middot; <?= e($draft['waktu']) ?> WIB &middot;
                            <?= $jumlahTamu ?> orang &middot; Area <?= e($areaLabels[$draft['area']] ?? $draft['area']) ?>
                        </p>
                    </div>
                    <a href="<?= route('reservasi', ['resto' => $restoId]) ?>" class="text-xs font-bold text-[#8a5d49] hover:underline">← Ubah Tanggal & Waktu</a>
                </div>

                <?php if (empty($tables)): ?>
                    <!-- Kondisi: Meja Tidak Tersedia -->
                    <div class="text-center py-12 space-y-4">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-orange-100 text-orange-700">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-display text-2xl font-bold text-[#201913]">Meja Tidak Tersedia</h2>
                            <p class="text-sm text-[#66574b] mt-2">
                                Tidak ada meja yang cocok dengan kriteria Anda pada slot tersebut.<br>
                                Silakan coba tanggal, jam, atau area lain.
                            </p>
                        </div>
                        <a href="<?= route('reservasi', ['resto' => $restoId]) ?>" class="inline-block bg-[#5e392e] hover:bg-[#4a2c24] text-white text-xs font-bold py-2.5 px-6 rounded-xl transition shadow-sm">
                            Kembali ke Pilih Tanggal & Waktu
                        </a>
                    </div>
                <?php else: ?>
                    <?php if (isset($error)): ?>
                        <div class="p-4 rounded-2xl bg-orange-50 border border-orange-200 text-orange-800 text-sm"><?= e($error) ?></div>
                    <?php endif; ?>

                    <form action="<?= route('pilih_meja') ?>" method="POST" class="space-y-6" id="form-pilih-meja">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php foreach ($tables as $t): ?>
                                <?php
                                $tid = (int) ($t['table_id'] ?? 0);
                                $isSelected = $selectedTableId === $tid;
                                ?>
                                <label data-table-card data-table-id="<?= $tid ?>" class="table-card relative flex flex-col p-4 rounded-2xl border border-[#eadfd4] bg-white cursor-pointer transition hover:border-[#8a5d49] <?= $isSelected ? 'table-card-selected' : '' ?>">
                                    <input type="radio" name="table_id" value="<?= $tid ?>" <?= $isSelected ? 'checked' : '' ?> required class="sr-only">
                                    <input type="hidden" name="table_number" value="<?= e($t['table_number'] ?? '') ?>">
                                    <input type="hidden" name="capacity" value="<?= (int) ($t['capacity'] ?? 0) ?>">
                                    <input type="hidden" name="area" value="<?= e($t['location_area'] ?? '') ?>">

                                    <div class="flex items-center justify-between mb-3">
                                        <span class="w-11 h-11 rounded-full bg-[#efebe4] text-[#5e392e] font-display font-bold flex items-center justify-center text-sm">
                                            <?= e($t['table_number'] ?? ('M' . $tid)) ?>
                                        </span>
                                        <span class="inline-flex items-center gap-2 text-xs font-bold text-[#8a5d49]">
                                            <span class="table-check-wrap hidden">
                                                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-green-600">
                                                    <svg class="table-check w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 13l4 4L19 7"/></svg>
                                                </span>
                                            </span>
                                            <?= e(strtoupper($t['location_area'] ?? 'indoor')) ?>
                                        </span>
                                    </div>

                                    <div class="mt-auto space-y-1">
                                        <p class="text-sm font-bold text-[#201913]">Meja <?= e($t['table_number'] ?? $tid) ?></p>
                                        <p class="text-xs text-[#66574b]">Kapasitas: <?= (int) ($t['capacity'] ?? 0) ?> orang</p>
                                    </div>

                                    <span class="table-badge absolute top-2 right-2 hidden text-[10px] font-bold text-white bg-green-600 px-2 py-0.5 rounded-full">TERPILIH</span>
                                </label>
                            <?php endforeach; ?>
                        </div>

                        <div class="pt-4 flex flex-wrap items-center justify-between gap-3 border-t border-[#eadfd4]">
                            <a href="<?= route('reservasi', ['resto' => $restoId]) ?>" class="px-5 py-2.5 rounded-xl border border-[#eadfd4] text-stone-600 hover:bg-stone-50 text-xs font-bold transition">
                                ← Kembali
                            </a>
                            <div class="flex flex-wrap gap-3">
                                <button type="submit" name="go_menu" value="1" class="bg-[#5e392e] hover:bg-[#4a2c24] text-white text-xs font-bold py-2.5 px-6 rounded-xl transition shadow-sm">
                                    Lanjut: Pilih Menu →
                                </button>
                                <button type="submit" class="px-5 py-2.5 rounded-xl border border-[#8a5d49] text-[#8a5d49] hover:bg-[#8a5d49] hover:text-white text-xs font-bold transition">
                                    Lewati Pre-order
                                </button>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>

            </div>
        </div>
</div>

<script>
(function () {
    const cards = document.querySelectorAll('[data-table-card]');
    function applySelection(card) {
        cards.forEach(c => {
            const selected = c === card;
            c.classList.toggle('table-card-selected', selected);
            c.classList.toggle('border-[#5e392e]', selected);
            c.classList.toggle('ring-2', selected);
            c.classList.toggle('ring-[#8a5d49]/40', selected);
            c.querySelector('.table-check-wrap')?.classList.toggle('hidden', !selected);
            c.querySelector('.table-badge')?.classList.toggle('hidden', !selected);
        });
    }
    cards.forEach(card => {
        card.addEventListener('click', function () {
            const radio = this.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
            applySelection(this);
        });
    });
})();
</script>