<?php
// pages/kelola_restoran.php — Kelola Restoran (Admin)
// Terhubung ke backend: GET/POST/DELETE /restaurants

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

// Hapus restoran via ?delete=...
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $result = api_request('DELETE', API_RESTAURANTS . '/' . (int) $_GET['delete']);
    if ($result['ok']) {
        $_SESSION['flash_message'] = 'Restoran berhasil dihapus.';
        $_SESSION['flash_type'] = 'green';
    } else {
        $_SESSION['flash_message'] = api_error_message($result, 'Gagal menghapus restoran.');
        $_SESSION['flash_type'] = 'red';
    }
    header("Location: " . route('kelola_restoran'));
    exit;
}

// Tambah restoran baru
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['name'] ?? '');
    $slug = strtolower(trim((string) preg_replace('/[^a-zA-Z0-9]+/', '-', $nama), '-'));
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $rating = ($_POST['rating'] ?? '') !== '' ? (float) $_POST['rating'] : null;
    $isActive = isset($_POST['is_active']) ? filter_var($_POST['is_active'], FILTER_VALIDATE_BOOLEAN) : true;

    if ($nama === '' || $slug === '') {
        $message = 'Nama restoran wajib diisi.';
        $messageType = 'red';
    } else {
        $payload = [
            'name' => $nama,
            'slug' => $slug,
            'address' => $address !== '' ? $address : null,
            'phone' => $phone !== '' ? $phone : null,
            'email' => $email !== '' ? $email : null,
            'rating' => $rating,
            'is_active' => $isActive,
        ];

        // Kumpulkan SEMUA file gambar yang diupload (bisa lebih dari satu).
        $uploadedFiles = [];
        if (!empty($_FILES['images']['name']) && is_array($_FILES['images']['name'])) {
            foreach ($_FILES['images']['name'] as $i => $name) {
                $tmp = $_FILES['images']['tmp_name'][$i] ?? '';
                if (($_FILES['images']['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK && is_uploaded_file($tmp)) {
                    $uploadedFiles[] = [
                        'tmp_name' => $tmp,
                        'name'     => $name,
                        'type'     => $_FILES['images']['type'][$i] ?? 'application/octet-stream',
                    ];
                }
            }
        }

        $result = !empty($uploadedFiles)
            ? api_upload(API_RESTAURANTS, $payload, ['images' => $uploadedFiles], 'POST')
            : api_request('POST', API_RESTAURANTS, $payload);

        if ($result['ok']) {
            $_SESSION['flash_message'] = "Restoran '$nama' berhasil ditambahkan.";
            $_SESSION['flash_type'] = 'green';
            header("Location: " . route('kelola_restoran'));
            exit;
        } else {
            $message = api_error_message($result, 'Gagal menambahkan restoran.');
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

// Ambil daftar restoran dari backend (termasuk yang nonaktif)
$restoList = [];
$restoResult = api_get(API_RESTAURANTS . '?all=1');
if ($restoResult['ok']) {
    $restoRaw = $restoResult['data']['data'] ?? [];
    $restoList = $restoRaw['data'] ?? (is_array($restoRaw) && isset($restoRaw[0]) ? $restoRaw : []);
}
?>

<div class="mx-auto max-w-7xl px-6 py-8 lg:px-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        <?php $sidebarRole = 'admin'; $sidebarActive = 'kelola_restoran'; include __DIR__ . '/../components/dashboard-sidebar.php'; ?>

        <div class="lg:col-span-3 space-y-6">
            <div class="bg-white/90 border border-[#eadfd4] rounded-3xl p-8 md:p-10 shadow-sm space-y-6">

                <div class="border-b border-[#eadfd4] pb-6">
                    <span class="text-xs uppercase tracking-widest text-[#8a5d49] font-bold">Manajemen Outlet</span>
                    <h1 class="font-display text-3xl font-bold text-[#201913] mt-1">Kelola Restoran</h1>
                    <p class="text-sm text-[#66574b] mt-1">Tambah restoran baru beserta foto, alamat, dan status aktif.</p>
                </div>

                <?php if ($message !== ''): ?>
                    <div class="p-4 rounded-2xl border text-sm <?= $messageType === 'green' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-700' ?>"><?= e($message) ?></div>
                <?php endif; ?>

                <!-- Form Tambah Restoran -->
                <form action="<?= route('kelola_restoran') ?>" method="POST" enctype="multipart/form-data" class="bg-[#fcfaf7] border border-[#eadfd4] rounded-2xl p-6 space-y-4">
                    <h2 class="font-display text-lg font-bold text-[#201913]">+ Tambah Restoran Baru</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Nama Restoran</label>
                            <input type="text" name="name" required placeholder="Contoh Kafiber Malang"
                                   class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Rating (0–5)</label>
                            <input type="number" name="rating" min="0" max="5" step="0.1" placeholder="4.8"
                                   class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Alamat</label>
                            <input type="text" name="address" placeholder="Jalan ... No ..."
                                   class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Telepon</label>
                            <input type="text" name="phone" placeholder="0812-3456-7890"
                                   class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Email (opsional)</label>
                            <input type="email" name="email" placeholder="outlet@restoran.com"
                                   class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Foto Restoran (bisa upload banyak gambar untuk slideshow, masing-masing max 20MB)</label>
                            <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp,image/gif"
                                   id="restoImagesInput"
                                   class="w-full px-4 py-3 rounded-xl border border-[#eadfd4] bg-white text-sm outline-none focus:border-[#8a5d49] transition file:mr-3 file:rounded-lg file:border-0 file:bg-[#5e392e] file:px-4 file:py-2 file:text-xs file:font-bold file:text-white hover:file:bg-[#4a2c24]">
                            <p class="text-[11px] text-[#8a5d49] mt-1.5">💡 Bisa pilih banyak gambar sekaligus (tahan <kbd class="px-1.5 py-0.5 rounded bg-[#efebe4] border border-[#eadfd4]">Ctrl</kbd>/<kbd class="px-1.5 py-0.5 rounded bg-[#efebe4] border border-[#eadfd4]">Shift</kbd>) <b>atau</b> pilih lagi nanti — gambar baru <b>menambah</b>, tidak mengganti. Restoran akan tampil sebagai <b>slideshow</b> di Dashboard &amp; Preview Restoran.</p>
                            <div id="restoImagesPreview" class="hidden mt-3 flex flex-wrap gap-3"></div>
                        </div>
                        <div class="md:col-span-3 flex items-center">
                            <label class="inline-flex items-center gap-2 text-sm font-bold text-[#5e392e]">
                                <input type="checkbox" name="is_active" value="1" checked
                                       class="w-4 h-4 rounded border-[#eadfd4] text-[#5e392e] focus:ring-[#8a5d49]">
                                Aktif
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="bg-[#5e392e] hover:bg-[#4a2c24] text-white text-xs font-bold py-2.5 px-6 rounded-xl transition shadow-sm">
                        Simpan Restoran
                    </button>
                </form>

                <!-- Daftar Restoran -->
                <div class="overflow-x-auto">
                    <table data-paginate class="w-full text-sm text-left text-[#4f4338]">
                        <thead>
                            <tr class="border-b border-[#eadfd4] text-[#8a5d49] text-xs uppercase tracking-wider">
                                <th class="py-3 pr-4">Foto</th>
                                <th class="py-3 pr-4">Nama</th>
                                <th class="py-3 pr-4">Alamat</th>
                                <th class="py-3 pr-4">Meja</th>
                                <th class="py-3 pr-4">Menu</th>
                                <th class="py-3 pr-4">Status</th>
                                <th class="py-3 pr-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($restoList as $r): ?>
                                <tr class="border-b border-[#eadfd4]">
                                    <td class="py-3 pr-4">
                                        <?php
                                        $imgUrl = api_resto_image($r['image_url'] ?? '', (int) ($r['restaurant_id'] ?? 0));
                                        $imgCount = count(array_filter((array) ($r['image_urls'] ?? [])));
                                        ?>
                                        <div class="flex items-center gap-2">
                                            <img src="<?= e($imgUrl) ?>" alt="<?= e($r['name'] ?? '') ?>" class="w-14 h-14 object-cover rounded-xl border border-[#eadfd4]" loading="lazy">
                                            <?php if ($imgCount > 1): ?>
                                                <span class="text-[10px] font-bold text-[#5e392e] bg-[#efebe4] rounded-full px-2 py-0.5" title="Gambar slideshow"><?= $imgCount ?> foto</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="py-3 pr-4 font-bold text-[#201913]">
                                        <?= e($r['name'] ?? '') ?>
                                        <?php if (!empty($r['rating'])): ?>
                                            <span class="block text-[11px] font-bold text-[#8a5d49]">★ <?= number_format((float) $r['rating'], 1, ',', '.') ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 pr-4"><?= e($r['address'] ?? '-') ?></td>
                                    <td class="py-3 pr-4"><?= (int) ($r['tables_count'] ?? 0) ?></td>
                                    <td class="py-3 pr-4"><?= (int) ($r['menus_count'] ?? 0) ?></td>
                                    <td class="py-3 pr-4">
                                        <span class="status-badge <?= ($r['is_active'] ?? true) ? 'status-completed' : 'status-cancelled' ?>">
                                            <?= ($r['is_active'] ?? true) ? 'Aktif' : 'Nonaktif' ?>
                                        </span>
                                    </td>
                                    <td class="py-3 pr-4">
                                        <?php if (($r['restaurant_id'] ?? 0) > 0): ?>
                                            <a href="<?= route('kelola_restoran', ['delete' => $r['restaurant_id']]) ?>" class="text-[11px] font-bold text-red-600 hover:underline" onclick="return confirm('Hapus restoran ini beserta seluruh meja & menunya?')">Hapus</a>
                                        <?php else: ?>
                                            <span class="text-[11px] text-[#a39a8f]">Demo</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var input = document.getElementById('restoImagesInput');
    var preview = document.getElementById('restoImagesPreview');
    if (!input || !preview) { return; }

    // Semua file terpilih (terakumulasi dari beberapa sesi pemilihan).
    // Tiap kali user memilih lagi, file BARU di-append, bukan mengganti.
    var allFiles = [];

    function sameFile(a, b) {
        return a.name === b.name && a.size === b.size && a.lastModified === b.lastModified;
    }

    function syncInput() {
        var dt = new DataTransfer();
        allFiles.forEach(function (f) { dt.items.add(f); });
        input.files = dt.files;
    }

    function renderPreviews() {
        preview.innerHTML = '';
        allFiles.forEach(function (file, idx) {
            var wrap = document.createElement('div');
            wrap.className = 'relative group';
            wrap.innerHTML = '<img alt="Preview ' + (idx + 1) + '" class="w-20 h-20 object-cover rounded-xl border border-[#eadfd4] bg-white shadow-sm">'
                + '<button type="button" aria-label="Hapus foto" class="absolute -top-1.5 -right-1.5 w-5 h-5 flex items-center justify-center rounded-full bg-red-600 text-white text-[10px] font-bold shadow hover:bg-red-700 transition">&times;</button>';
            var img = wrap.querySelector('img');
            var del = wrap.querySelector('button');
            var reader = new FileReader();
            reader.onload = function (e) { img.src = e.target.result; };
            reader.readAsDataURL(file);
            del.addEventListener('click', function () {
                allFiles.splice(idx, 1);
                syncInput();
                renderPreviews();
            });
            preview.appendChild(wrap);
        });
        preview.classList.toggle('hidden', allFiles.length === 0);
        preview.classList.toggle('flex', allFiles.length > 0);
    }

    // Pilih file baru -> APPEND ke pilihan lama (dedupe by nama+ukuran+waktu).
    input.addEventListener('change', function () {
        Array.prototype.forEach.call(input.files || [], function (f) {
            var dup = allFiles.some(function (m) { return sameFile(m, f); });
            if (!dup) { allFiles.push(f); }
        });
        syncInput();
        renderPreviews();
    });
})();
</script>