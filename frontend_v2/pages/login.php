<?php
// pages/login.php — Kafiber Restoran Modal/Form Login
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error_message = 'Email dan Kata Sandi wajib diisi.';
    } else {
        if (file_exists(__DIR__ . '/../src/config/api.config.php')) {
            require_once __DIR__ . '/../src/config/api.config.php';
        }
        if (file_exists(__DIR__ . '/../src/utils/api.php')) {
            require_once __DIR__ . '/../src/utils/api.php';
        }

        if (function_exists('api_login')) {
            $result = api_login($email, $password);

            if ($result['ok'] && isset($result['data']['data']) && is_array($result['data']['data'])) {
                set_frontend_session_from_user($result['data']['data']);
                header('Location: ' . route('home'));
                exit;
            }

            $error_message = api_error_message($result, 'Email atau Kata Sandi salah.');
        } else {
            // Mock login fallback untuk testing jika backend tidak aktif
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_name'] = explode('@', $email)[0];
            header('Location: ' . route('home'));
            exit;
        }
    }
}
?>

<div class="min-h-[80vh] flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white rounded-3xl p-8 md:p-10 shadow-2xl border border-[#eadfd4] relative">

        <a href="<?= route('home') ?>" class="absolute top-6 right-6 text-[#8a5d49] hover:text-[#5e392e] transition" title="Tutup / Kembali ke Beranda">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </a>

        <div class="text-center mb-8">
            <span class="text-xs uppercase tracking-widest text-[#8a5d49] font-bold">Selamat Datang Kembali</span>
            <h1 class="font-display text-3xl font-bold text-[#201913] mt-1">Masuk Akun</h1>
            <p class="text-xs text-[#66574b] mt-2">Akses reservasi dan layanan eksklusif Kafiber Restoran</p>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-700 text-xs font-medium">
                <?= e($error_message) ?>
            </div>
        <?php endif; ?>

        <div class="mb-5 p-3 rounded-xl bg-[#efe0d5] border border-[#decbbd] text-[#5e392e] text-xs text-center">
            Demo: <strong>admin@reservasi.local</strong> / kata sandi <strong>password</strong>
        </div>

        <form action="<?= route('login') ?>" method="POST" class="space-y-5">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Email</label>
                <input type="email" name="email" required placeholder="nama@email.com"
                       class="w-full px-4 py-3 rounded-2xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-2">Kata Sandi</label>
                <input type="password" name="password" required placeholder="••••••••"
                       class="w-full px-4 py-3 rounded-2xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
            </div>

            <button type="submit"
                    class="w-full bg-[#8a5d49] hover:bg-[#734d3d] text-white font-bold text-sm py-3.5 rounded-2xl shadow-md transition">
                Masuk Sekarang
            </button>
        </form>

        <p class="text-center text-xs text-[#66574b] mt-7">
            Belum punya akun Kafiber? <a href="<?= route('register') ?>" class="font-bold text-[#8a5d49] hover:underline">Buat Akun</a>
        </p>
    </div>
</div>
