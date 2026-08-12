<?php
// components/reservation-stepper.php - Indikator langkah reservasi.
// Set variabel $step (1-4) sebelum include.
$step = (int) ($step ?? 1);
$steps = [
    1 => ['Pilih Waktu', 'Tanggal & jam'],
    2 => ['Pilih Meja', 'Tersedia'],
    3 => ['Pilih Menu', 'Pre-order'],
    4 => ['Bayar Deposit', 'Konfirmasi'],
];
?>
<div class="flex items-center gap-2 md:gap-3 flex-wrap">
    <?php foreach ($steps as $i => [$label, $sub]): ?>
        <div class="flex items-center gap-2 md:gap-3">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold shrink-0 <?= $i < $step ? 'bg-green-600 text-white' : ($i === $step ? 'bg-[#5e392e] text-white ring-2 ring-[#8a5d49]/40' : 'bg-[#efebe4] text-[#a39a8f]') ?>">
                    <?= $i < $step ? '✓' : $i ?>
                </span>
                <span class="hidden sm:block">
                    <span class="block text-[11px] font-bold leading-tight <?= $i === $step ? 'text-[#201913]' : ($i < $step ? 'text-[#201913]' : 'text-[#a39a8f]') ?>"><?= e($label) ?></span>
                    <span class="block text-[10px] leading-tight <?= $i === $step ? 'text-[#8a5d49]' : 'text-[#a39a8f]' ?>"><?= e($sub) ?></span>
                </span>
            </div>
            <?php if ($i < 4): ?>
                <span class="w-6 md:w-10 h-px bg-[#eadfd4] shrink-0"></span>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
