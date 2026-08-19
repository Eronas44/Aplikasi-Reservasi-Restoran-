<?php
// pages/kelola_staf.php — Kelola Akun Staf (Admin)
// Terhubung ke backend: GET/POST /users (role:admin)

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
    $nama = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $roleStaff = trim($_POST['staff_role'] ?? 'staff');
    $password = $_POST['password'] ?? '';
    $phone = trim($_POST['phone_number'] ?? '');

    if ($nama === '' || $email === '' || $password === '') {
        $message = 'Nama, email, dan password wajib diisi.';
        $messageType = 'red';
    } elseif (strlen($password) < 8) {
        $message = 'Password minimal 8 karakter.';
        $messageType = 'red';
    } else {
        $payload = [
            'name' => $nama,
            'email' => $email,
            'password' => $password,
            'role' => $roleStaff,
            'phone_number' => $phone !== '' ? $phone : null,
        ];
        $result = api_request('POST', API_USERS, $payload);
        if ($result['ok']) {
            $_SESSION['flash_message'] = "Akun staf '$nama' ($email, role: $roleStaff) berhasil dibuat.";
            $_SESSION['flash_type'] = 'green';
            header("Location: " . route('kelola_staf'));
            exit;
        } else {
            $message = api_error_message($result, 'Gagal membuat akun staf.');
            $messageType = 'red';
        }
    }
}

// Ambil flash message jika ada
if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    $messageType = $_SESSION['flash_type'] ?? 'green';
    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
}

// Nonaktifkan role staf -> ubah role akun jadi 'customer' (soft-deactivate tanpa hapus data reservasi)
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $target = api_get(API_USERS . '/' . (int) $_GET['toggle']);
    if ($target['ok'] && isset($target['data']['data'])) {
        $u = $target['data']['data'];
        $newRole = ($u['role'] ?? 'staff') === 'staff' ? 'customer' : 'staff';
        $result = api_request('PUT', API_USERS . '/' . (int) $_GET['toggle'], ['role' => $newRole]);
        if ($result['ok']) {
            $_SESSION['flash_message'] = 'Status akun staf diperbarui.';
            $_SESSION['flash_type'] = 'green';
        } else {
            $_SESSION['flash_message'] = api_error_message($result, 'Gagal memperbarui akun.');
            $_SESSION['flash_type'] = 'red';
        }
    }
    header("Location: " . route('kelola_staf'));
    exit;
}

// Ambil daftar user dari backend, filter hanya role staff & admin
$stafList = [];
$usersResult = api_get(API_USERS . '?limit=200');
if ($usersResult['ok']) {
    // Response paginated: {data: {data: [...], current_page: ..., ...}}
    $outer = $usersResult['data']['data'] ?? [];
    $rows  = $outer['data'] ?? $outer; // ambil array data aktual
    if (is_array($rows)) {
        foreach ($rows as $u) {
            $rowRole = $u['role'] ?? 'customer';
            if ($rowRole === 'staff' || $rowRole === 'admin') {
                $stafList[] = $u;
            }
        }
    }
}
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        <?php $sidebarRole = 'admin'; $sidebarActive = 'kelola_staf'; include __DIR__ . '/../components/dashboard-sidebar.php'; ?>

        <div class="lg:col-span-3 space-y-6">
            <div class="bg-white/90 border border-[#eadfd4] rounded-3xl p-8 md:p-10 shadow-sm space-y-6">

                <div class="border-b border-[#eadfd4] pb-6">
                    <span class="text-xs uppercase tracking-widest text-[#8a5d49] font-bold">Manajemen Tim</span>
                    <h1 class="font-display text-3xl font-bold text-[#201913] mt-1">Kelola Akun Staf</h1>
                    <p class="text-sm text-[#66574b] mt-1">Tambah atau nonaktifkan akun staf.</p>
                </div>

                <?php if ($message !== ''): ?>
                    <div class="p-4 rounded-2xl border text-sm <?= $messageType === 'green' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-700' ?>"><?= e($message) ?></div>
                <?php endif; ?>

                <form action="<?= route('kelola_staf') ?>" method="POST" class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-6 space-y-4">
                    <h2 class="font-display text-lg font-bold text-[#201913]">+ Tambah Akun Staf</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Nama Lengkap</label>
                            <input type="text" name="name" required placeholder="Nama staf"
                                   class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm outline-none focus:border-[#8a5d49] transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Email</label>
                            <input type="email" name="email" required placeholder="nama@resto.local"
                                   class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm outline-none focus:border-[#8a5d49] transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Password (min. 8 karakter)</label>
                            <input type="password" name="password" required placeholder="••••••••"
                                   class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm outline-none focus:border-[#8a5d49] transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">No. HP (opsional)</label>
                            <input type="text" name="phone_number" placeholder="08xx-xxxx-xxxx"
                                   class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm outline-none focus:border-[#8a5d49] transition">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Role</label>
                            <select name="staff_role" class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm outline-none focus:border-[#8a5d49] transition">
                                <option value="staff">Staff</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="bg-[#5e392e] hover:bg-[#4a2c24] text-white text-xs font-bold py-2.5 px-6 rounded-xl transition shadow-sm">
                        Simpan Akun Staf
                    </button>
                </form>

                <!-- Daftar Staf -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                    <h2 class="font-display text-lg font-bold text-[#201913]">Daftar Akun Staf</h2>
                    <div class="relative md:w-80">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[#a39a8f]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" id="search-staf" placeholder="Cari nama, email, role..."
                               class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-[#eadfd4] bg-white text-sm outline-none focus:border-[#8a5d49] transition">
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table data-paginate data-paginate-search="search-staf" class="w-full text-sm text-left text-[#4f4338]">
                        <thead>
                            <tr class="border-b border-[#eadfd4] text-[#8a5d49] text-xs uppercase tracking-wider">
                                <th class="py-3 pr-4">ID</th>
                                <th class="py-3 pr-4">Nama</th>
                                <th class="py-3 pr-4">Email</th>
                                <th class="py-3 pr-4">Role</th>
                                <th class="py-3 pr-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-staf">
                            <?php foreach ($stafList as $s): ?>
                                <tr class="border-b border-[#eadfd4]" data-search="<?= e(strtolower(($s['name'] ?? '') . ' ' . ($s['email'] ?? '') . ' ' . ($s['role'] ?? ''))) ?>">
                                    <td class="py-3 pr-4 font-bold text-[#201913]"><?= e($s['user_id']) ?></td>
                                    <td class="py-3 pr-4"><?= e($s['name']) ?></td>
                                    <td class="py-3 pr-4 font-mono text-xs"><?= e($s['email']) ?></td>
                                    <td class="py-3 pr-4 uppercase text-xs font-bold text-[#8a5d49]"><?= e($s['role']) ?></td>
                                    <td class="py-3 pr-4">
                                        <?php if (($s['user_id'] ?? 0) > 0): ?>
                                            <a href="<?= route('kelola_staf', ['toggle' => $s['user_id']]) ?>" class="text-[11px] font-bold text-[#8a5d49] hover:underline">
                                                Nonaktifkan
                                            </a>
                                        <?php else: ?>
                                            <span class="text-[11px] text-[#a39a8f]">Demo</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var input = document.getElementById('search-staf');
                    if (!input) return;
                    input.addEventListener('input', function () {
                        var q = this.value.toLowerCase().trim();
                        document.querySelectorAll('#tbody-staf tr').forEach(function (row) {
                            row.style.display = row.dataset.search.indexOf(q) !== -1 ? '' : 'none';
                        });
                    });
                });
                </script>

            </div>
        </div>
    </div>
</div>
