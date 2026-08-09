<?php
// pages/register.php — Kafiber Restoran Modal/Form Registrasi
$error_message = '';
if (!empty($_GET['error'])) {
    $error_message = $_GET['error'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['telepon'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $konfirmasi = trim($_POST['konfirmasi_password'] ?? '');

    if ($password !== $konfirmasi) {
        $error_message = 'Password dan Konfirmasi Password tidak cocok.';
    } else {
        if (file_exists(__DIR__ . '/../src/config/api.config.php')) {
            require_once __DIR__ . '/../src/config/api.config.php';
        }
        if (file_exists(__DIR__ . '/../src/utils/api.php')) {
            require_once __DIR__ . '/../src/utils/api.php';
        }

        if (function_exists('api_register')) {
            $result = api_register([
                'name' => $name,
                'email' => $email,
                'phone_number' => $phone,
                'password' => $password,
            ]);

            if ($result['ok'] && isset($result['data']['data']) && is_array($result['data']['data'])) {
                set_frontend_session_from_user($result['data']['data']);
                header('Location: ' . route('dashboard'));
                exit;
            }

            $error_message = api_error_message($result, 'Pendaftaran gagal. Silakan coba lagi.');
        } else {
            // Mock register fallback
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_name'] = $name ?: 'Pengguna Baru';
            header('Location: ' . route('dashboard'));
            exit;
        }
    }
}
?>

<div class="min-h-[85vh] flex items-center justify-center p-4">
    <div class="w-full max-w-lg bg-white rounded-3xl p-8 md:p-10 shadow-2xl border border-[#eadfd4] relative">

        <a href="<?= route('home') ?>" class="absolute top-6 right-6 text-[#8a5d49] hover:text-[#5e392e] transition" title="Tutup / Kembali ke Beranda">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </a>

        <div class="text-center mb-6">
            <span class="text-xs uppercase tracking-widest text-[#8a5d49] font-bold">Bergabung Bersama Kami</span>
            <h1 class="font-display text-3xl font-bold text-[#201913] mt-1">Buat Akun Barumu</h1>
            <p class="text-xs text-[#66574b] mt-1">Isi formulir berikut untuk mulai melakukan reservasi</p>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="mb-5 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-700 text-xs font-medium">
                <?= e($error_message) ?>
            </div>
        <?php endif; ?>

        <div id="registerMessage" class="text-center text-xs min-h-[18px] text-[#8a5d49] font-medium mb-3"></div>

        <form id="registerForm" action="<?= route('register') ?>" method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-1">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" required placeholder="Masukkan nama lengkap"
                       class="w-full px-4 py-2.5 rounded-2xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-1">Alamat Email</label>
                <input type="email" id="email" name="email" required placeholder="nama@email.com"
                       class="w-full px-4 py-2.5 rounded-2xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-1">No. Telepon / WhatsApp</label>
                <input type="tel" id="telepon" name="telepon" required placeholder="08123456789"
                       class="w-full px-4 py-2.5 rounded-2xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-1">Kata Sandi</label>
                <input type="password" id="password" name="password" required placeholder="••••••••"
                       class="w-full px-4 py-2.5 rounded-2xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-1">Konfirmasi Kata Sandi</label>
                <input type="password" id="konfirmasi_password" name="konfirmasi_password" required placeholder="••••••••"
                       class="w-full px-4 py-2.5 rounded-2xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
            </div>

            <button
                type="submit"
                class="w-full bg-[#8a5d49] hover:bg-[#734d3d] text-white font-bold text-sm py-3 rounded-2xl shadow-md transition mt-2">
                Daftar Sekarang
            </button>
        </form>

        <p class="text-center text-xs text-[#66574b] mt-5">
            Sudah punya akun Kafiber?
            <a href="<?= route('login') ?>" class="font-bold text-[#8a5d49] hover:underline">Masuk di sini</a>
        </p>
    </div>
</div>

<script>
    document.getElementById('registerForm').addEventListener('submit', function (e) {
        var password   = document.getElementById('password').value;
        var konfirmasi  = document.getElementById('konfirmasi_password').value;
        var messageBox  = document.getElementById('registerMessage');

        if (password !== konfirmasi) {
            e.preventDefault();
            messageBox.textContent = 'Password dan Konfirmasi Password tidak cocok.';
            messageBox.className = 'text-center text-xs min-h-[18px] text-red-600 font-bold';
            return false;
        }

        messageBox.textContent = '';
    });
</script>
