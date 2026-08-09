<?php
// pages/kebijakan.php — Kebijakan Deposit & Refund (Admin)
// Terhubung ke backend: GET/POST /policies

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

// Ambil kebijakan aktif dari backend
$policy = null;
$policyResult = api_get(API_POLICIES);
if ($policyResult['ok'] && isset($policyResult['data']['data'])) {
    $raw = $policyResult['data']['data'];
    $items = $raw['data'] ?? $raw;
    foreach ($items as $p) {
        if ($policy === null || ($p['is_active'] && !($policy['is_active'] ?? false))) {
            $policy = $p;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payload = [
        'restaurant_id' => (int) ($_SESSION['restaurant_id'] ?? 1),
        'deposit_percent' => (float) ($_POST['deposit_percent'] ?? 20),
        'deposit_min_amount' => (float) ($_POST['deposit_fixed'] ?? 0),
        'refund_full_hours' => (int) ($_POST['refund_full_hours'] ?? 24),
        'refund_partial_hours' => (int) ($_POST['refund_partial_hours'] ?? 6),
        'refund_partial_percent' => (float) ($_POST['refund_partial_percent'] ?? 50),
        'is_active' => true,
    ];

    if ($policy !== null) {
        $result = api_request('PUT', API_POLICIES . '/' . $policy['policy_id'], $payload);
    } else {
        $result = api_request('POST', API_POLICIES, $payload);
    }

    if ($result['ok']) {
        $message = 'Kebijakan deposit & refund berhasil disimpan (FR-007, FR-014).';
        $policy = array_merge($payload, ['policy_id' => $policy['policy_id'] ?? null]);
    } else {
        $message = api_error_message($result, 'Gagal menyimpan kebijakan.');
        $messageType = 'red';
    }
}

// Nilai default bila backend tidak tersedia
$depositPercent   = $policy['deposit_percent'] ?? 20;
$depositFixed     = $policy['deposit_min_amount'] ?? 50000;
$refundFullHours  = $policy['refund_full_hours'] ?? 24;
$refundPartialHours = $policy['refund_partial_hours'] ?? 6;
$refundPartialPercent = $policy['refund_partial_percent'] ?? 50;

$refundRules = [
    [">= $refundFullHours jam sebelum", '100%', 'green'],
    ["$refundPartialHours - $refundFullHours jam sebelum", "$refundPartialPercent%", 'amber'],
    ["< $refundPartialHours jam sebelum / no-show", '0%', 'red'],
];
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        <?php $sidebarRole = 'admin'; $sidebarActive = 'kebijakan'; include __DIR__ . '/../components/dashboard-sidebar.php'; ?>

        <div class="lg:col-span-3">
            <div class="bg-white/90 border border-[#eadfd4] rounded-3xl p-8 md:p-10 shadow-sm space-y-8">

                <div class="border-b border-[#eadfd4] pb-6">
                    <span class="text-xs uppercase tracking-widest text-[#8a5d49] font-bold">Kebijakan Restoran</span>
                    <h1 class="font-display text-3xl font-bold text-[#201913] mt-1">Deposit & Refund</h1>
                    <p class="text-sm text-[#66574b] mt-1">Atur nominal/persentase minimum deposit dan ambang batas refund berjenjang.</p>
                </div>

                <?php if ($message !== ''): ?>
                    <div class="p-4 rounded-2xl border text-sm <?= $messageType === 'green' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-700' ?>"><?= e($message) ?></div>
                <?php endif; ?>

                <form action="<?= route('kebijakan') ?>" method="POST" class="space-y-6">

                    <div class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-6 space-y-4">
                        <h2 class="font-display text-lg font-bold text-[#201913]">Minimum Deposit (FR-007)</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Nominal Tetap (Rp)</label>
                                <input type="number" name="deposit_fixed" value="<?= (int) $depositFixed ?>" min="0"
                                       class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm outline-none focus:border-[#8a5d49] transition">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Persentase (%)</label>
                                <input type="number" name="deposit_percent" value="<?= (float) $depositPercent ?>" min="0" max="100" step="0.01"
                                       class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm outline-none focus:border-[#8a5d49] transition">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Refund Penuh (jam sebelum)</label>
                                <input type="number" name="refund_full_hours" value="<?= (int) $refundFullHours ?>" min="0"
                                       class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm outline-none focus:border-[#8a5d49] transition">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Refund Parsial (jam sebelum)</label>
                                <input type="number" name="refund_partial_hours" value="<?= (int) $refundPartialHours ?>" min="0"
                                       class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm outline-none focus:border-[#8a5d49] transition">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Persentase Refund Parsial (%)</label>
                            <input type="number" name="refund_partial_percent" value="<?= (float) $refundPartialPercent ?>" min="0" max="100" step="0.01"
                                   class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm outline-none focus:border-[#8a5d49] transition">
                        </div>
                    </div>

                    <div class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-6 space-y-4">
                        <h2 class="font-display text-lg font-bold text-[#201913]">Ambang Batas Refund Berjenjang (FR-014)</h2>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-[#4f4338]">
                                <thead>
                                    <tr class="border-b border-[#eadfd4] text-[#8a5d49] text-xs uppercase tracking-wider">
                                        <th class="py-3 pr-4">Batas Waktu Pembatalan</th>
                                        <th class="py-3 pr-4">Persentase Refund</th>
                                        <th class="py-3 pr-4">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($refundRules as $r): ?>
                                        <tr class="border-b border-[#eadfd4]">
                                            <td class="py-3 pr-4 font-bold text-[#201913]"><?= e($r[0]) ?></td>
                                            <td class="py-3 pr-4 font-bold text-[#8a5d49]"><?= e($r[1]) ?></td>
                                            <td class="py-3 pr-4">
                                                <span class="status-badge status-<?= $r[2] === 'green' ? 'completed' : ($r[2] === 'amber' ? 'pending' : 'cancelled') ?>">
                                                    <?= $r[2] === 'green' ? 'Full Refund' : ($r[2] === 'amber' ? 'Partial' : 'No Refund') ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-3 border-t border-[#eadfd4]">
                        <button type="submit" class="bg-[#5e392e] hover:bg-[#4a2c24] text-white text-xs font-bold py-2.5 px-6 rounded-xl transition shadow-sm">
                            Simpan Kebijakan
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
