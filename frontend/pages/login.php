<?php
// pages/login.php — Kafiber Restoran Modal/Form Login (style frontend lama / v1)
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
                header('Location: ' . dashboard_route());
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

<style>
    /* Animasi muncul modal */
    @keyframes modalPop {
        from { opacity: 0; transform: translateY(16px) scale(0.97); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }
    #login-modal-card {
        animation: modalPop 0.25s ease-out;
    }
</style>

<?php include __DIR__ . '/../components/auth-background.php'; ?>

<!-- ============ OVERLAY GELAP ============ -->
<div class="fixed inset-0 bg-[#201913]/55 z-40"></div>

<!-- ============ MODAL LOGIN (POP-UP) ============ -->
<div class="fixed inset-0 z-50 flex items-center justify-center px-4 py-10">

    <div id="login-modal-card" class="relative w-full max-w-md bg-[#faf8f5] border border-[#eadfd4] rounded-3xl shadow-2xl px-8 py-10">

        <!-- Tombol close -> kembali ke Home (via router) -->
        <a href="<?= route('home') ?>" aria-label="Tutup"
           class="absolute top-5 right-5 w-9 h-9 rounded-full bg-[#f4ece1] hover:bg-[#eadfd4] flex items-center justify-center text-[#5e392e] transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </a>

        <div class="w-14 h-14 rounded-full bg-[#5e392e] text-white flex items-center justify-center font-display italic font-bold text-xl mx-auto mb-5 shadow-md">
            K
        </div>

        <h1 class="font-display text-3xl font-semibold text-center text-[#201913] mb-1">
            Masuk ke Akun
        </h1>
        <p class="text-center text-sm text-[#66574b] mb-8">
            Kelola reservasi dan nikmati kemudahan di Kafiber
        </p>

        <?php if (!empty($error_message)): ?>
            <div class="mb-5 p-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-xs text-center font-medium">
                <?= e($error_message) ?>
            </div>
        <?php endif; ?>

        <div class="mb-5 p-3 rounded-xl bg-[#efe0d5] border border-[#decbbd] text-[#5e392e] text-xs text-center">
            Demo: <strong>admin@reservasi.local</strong> / kata sandi <strong>password</strong><br>
            Demo: <strong>staff@reservasi.local</strong> / kata sandi <strong>password</strong><br>
            Demo: <strong>budi.santoso@example.com</strong> / kata sandi <strong>password</strong>
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
