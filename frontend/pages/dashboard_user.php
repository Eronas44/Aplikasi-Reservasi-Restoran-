<?php
// pages/dashboard_user.php — Halaman Dashboard User / Pesan
$isLoggedIn = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
$role = $_SESSION['role'] ?? 'customer';

if (!$isLoggedIn) {
    header('Location: ' . route('login'));
    exit;
}

// Keamanan: admin dan staff jangan sampai di halaman customer dashboard
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

// Ambil daftar restoran dari backend
$restaurants = [];
$restoResult = api_get(API_RESTAURANTS);
if ($restoResult['ok']) {
    $raw = $restoResult['data']['data'] ?? [];
    $restaurants = $raw['data'] ?? $raw;
}

// Ambil reservasi aktif milik user saat ini
$myReservations = [];
$resResult = api_get(API_RESERVATIONS . '?limit=100');
if ($resResult['ok']) {
    $raw = $resResult['data']['data'] ?? [];
    $allRes = $raw['data'] ?? $raw;
    // Filter hanya milik user ini, status aktif
    $userId = $_SESSION['user_id'] ?? 0;
    foreach ($allRes as $r) {
        if (($r['user_id'] ?? 0) == $userId || ($r['user']['user_id'] ?? 0) == $userId) {
            $myReservations[] = $r;
        }
    }
    // Sort terbaru dulu
    usort($myReservations, function($a, $b) {
        return strcmp($b['reservation_date'] ?? '', $a['reservation_date'] ?? '');
    });
    $myReservations = array_slice($myReservations, 0, 5);
}

$statusLabels = [
    'pending'   => ['label' => 'Menunggu',   'class' => 'status-pending'],
    'confirmed' => ['label' => 'Dikonfirmasi','class' => 'status-confirmed'],
    'completed' => ['label' => 'Selesai',    'class' => 'status-completed'],
    'cancelled' => ['label' => 'Dibatalkan', 'class' => 'status-cancelled'],
    'no_show'   => ['label' => 'No-Show',    'class' => 'status-cancelled'],
];
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-10">
  <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

    <!-- Sidebar Menu Dashboard -->
    <div class="lg:col-span-1 space-y-3">
      <?php $sidebarRole = 'customer'; $sidebarActive = 'dashboard'; include __DIR__ . '/../components/dashboard-sidebar.php'; ?>

      <!-- Info User -->
      <div class="bg-[#5e392e] rounded-2xl p-5 text-white shadow-sm">
        <p class="text-xs uppercase tracking-widest text-[#e8c39e] font-bold mb-1">Selamat Datang</p>
        <p class="font-display font-bold text-lg leading-tight"><?= e($_SESSION['user_name'] ?? 'Pelanggan') ?></p>
        <p class="text-xs text-[#e8c39e] mt-1"><?= e($_SESSION['user_email'] ?? '') ?></p>
      </div>
    </div>

    <!-- Main Content -->
    <div class="lg:col-span-3 space-y-6">

      <!-- Header -->
      <div class="bg-[#5e392e] rounded-3xl p-8 text-white shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="text-xs uppercase tracking-widest text-[#e8c39e] font-bold">Dashboard Pelanggan</span>
          <h1 class="font-display text-3xl font-bold mt-1">Halo, <?= e($_SESSION['user_name'] ?? 'Pelanggan') ?>!</h1>
          <p class="text-sm text-[#e8c39e] mt-1">Buat reservasi, lihat menu, dan kelola booking Anda.</p>
        </div>
        <a href="<?= route('reservasi') ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white text-[#5e392e] font-bold text-xs shadow-sm transition hover:bg-[#efebe4]">
          + Buat Reservasi
        </a>
      </div>

      <!-- Reservasi Terbaru Saya -->
      <div class="bg-white/90 border border-[#eadfd4] rounded-3xl p-6 md:p-8 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
          <h2 class="font-display text-2xl font-bold text-[#201913]">Reservasi Saya</h2>
          <a href="<?= route('riwayat_reservasi') ?>" class="text-xs font-bold text-[#8a5d49] hover:underline">Lihat Semua →</a>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-sm text-left text-[#4f4338]">
            <thead>
              <tr class="border-b border-[#eadfd4] text-[#8a5d49] text-xs uppercase tracking-wider">
                <th class="py-3 pr-4">Kode</th>
                <th class="py-3 pr-4">Restoran</th>
                <th class="py-3 pr-4">Tanggal</th>
                <th class="py-3 pr-4">Tamu</th>
                <th class="py-3 pr-4">Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($myReservations as $r): ?>
                <tr class="border-b border-[#eadfd4]">
                  <td class="py-3 pr-4 font-mono text-xs font-bold text-[#201913]"><?= e($r['booking_code'] ?? '-') ?></td>
                  <td class="py-3 pr-4"><?= e($r['restaurant']['name'] ?? $r['restaurant']['restaurant_name'] ?? 'Restoran') ?></td>
                  <td class="py-3 pr-4"><?= e(substr((string) ($r['reservation_date'] ?? ''), 0, 10)) ?></td>
                  <td class="py-3 pr-4"><?= (int) ($r['number_of_guest'] ?? 0) ?> org</td>
                  <td class="py-3 pr-4">
                    <?php
                    $st = $r['status'] ?? 'pending';
                    $stInfo = $statusLabels[$st] ?? ['label' => ucfirst($st), 'class' => 'status-pending'];
                    ?>
                    <span class="status-badge <?= $stInfo['class'] ?>"><?= e($stInfo['label']) ?></span>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if (empty($myReservations)): ?>
                <tr>
                  <td colspan="5" class="py-8 text-center text-sm text-[#8a5d49]">
                    Belum ada reservasi. <a href="<?= route('reservasi') ?>" class="font-bold hover:underline">Buat sekarang →</a>
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Daftar Restoran dari Backend -->
      <div class="space-y-4">
        <h2 class="font-display text-2xl font-bold text-[#201913]">Pilih Restoran</h2>
        <?php if (empty($restaurants)): ?>
          <div class="bg-white/80 border border-[#eadfd4] rounded-2xl p-6 text-center text-sm text-[#8a5d49]">
            Belum ada restoran yang tersedia. Silakan coba lagi nanti.
          </div>
        <?php else: ?>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php foreach ($restaurants as $resto): ?>
              <?php
              $restoId   = $resto['restaurant_id'] ?? 0;
              $restoName = $resto['name'] ?? $resto['restaurant_name'] ?? 'Restoran';
              $restoAddr = $resto['address'] ?? '';
              $restoPhone= $resto['phone_number'] ?? '';
              ?>
              <a href="<?= route('detail_restoran', ['resto_id' => $restoId]) ?>"
                 class="bg-white/80 border border-[#eadfd4] rounded-3xl p-5 shadow-sm flex flex-col hover:border-[#8a5d49] transition group">
                <div class="w-full h-40 rounded-2xl overflow-hidden mb-4 border border-[#eadfd4] bg-[#f4ece1] flex items-center justify-center">
                  <?php if (!empty($resto['image_url'])): ?>
                    <img src="<?= e($resto['image_url']) ?>" alt="<?= e($restoName) ?>" class="w-full h-full object-cover">
                  <?php else: ?>
                    <svg class="w-14 h-14 text-[#c9a98a]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3 9.75L12 4l9 5.75V20a1 1 0 01-1 1H4a1 1 0 01-1-1V9.75z"/>
                    </svg>
                  <?php endif; ?>
                </div>
                <div class="flex items-start justify-between mt-auto gap-2">
                  <div>
                    <h3 class="font-display text-lg font-bold text-[#201913] group-hover:text-[#8a5d49] transition"><?= e($restoName) ?></h3>
                    <?php if ($restoAddr): ?>
                      <p class="text-xs text-[#8a5d49] mt-0.5"><?= e($restoAddr) ?></p>
                    <?php endif; ?>
                  </div>
                  <span class="text-xs font-bold text-white bg-[#5e392e] px-3 py-1 rounded-full shrink-0">Buka →</span>
                </div>
              </a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </div>
</div>
