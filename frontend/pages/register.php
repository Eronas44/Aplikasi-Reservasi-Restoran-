<?php
// pages/register.php — Kafiber Restoran Modal/Form Registrasi (style frontend lama / v1)
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
            if (function_exists('api_reset_backend_session')) {
                api_reset_backend_session();
            }

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

<style>
    /* Animasi muncul modal */
    @keyframes modalPop {
        from { opacity: 0; transform: translateY(16px) scale(0.97); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }
    #register-modal-card {
        animation: modalPop 0.25s ease-out;
    }
</style>

<?php include __DIR__ . '/../components/auth-background.php'; ?>

<!-- ============ OVERLAY GELAP ============ -->
<div class="fixed inset-0 bg-[#201913]/55 z-40"></div>

<!-- ============ MODAL REGISTER (POP-UP) ============ -->
<div class="fixed inset-0 z-50 flex items-center justify-center px-4 py-10">

    <div id="register-modal-card" class="relative w-full max-w-md bg-[#faf8f5] border border-[#eadfd4] rounded-3xl shadow-2xl px-8 py-7">

        <!-- Tombol close -> kembali ke Home -->
        <a href="<?= route('home') ?>" aria-label="Tutup"
           class="absolute top-5 right-5 w-9 h-9 rounded-full bg-[#f4ece1] hover:bg-[#eadfd4] flex items-center justify-center text-[#5e392e] transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </a>

        <div class="w-11 h-11 rounded-full bg-[#5e392e] text-white flex items-center justify-center font-display italic font-bold text-lg mx-auto mb-3 shadow-md overflow-hidden">
            <img src="assets/images/kafiber.png" alt="Kafiber" class="w-full h-full object-cover" onerror="this.style.display='none'; this.parentElement.innerText='K';">
        </div>

        <h1 class="font-display text-2xl font-semibold text-center text-[#201913] mb-1">
            Buat Akun Baru
        </h1>
        <p class="text-center text-xs text-[#66574b] mb-5">
            Daftar untuk mulai reservasi &amp; pesan menu favorit Anda di Kafiber
        </p>

        <?php if (!empty($error_message)): ?>
            <div class="mb-4 p-2.5 rounded-xl bg-red-50 border border-red-200 text-red-600 text-xs text-center font-medium">
                <?= e($error_message) ?>
            </div>
        <?php endif; ?>

        <!-- Form register (frontend saja, action diarahkan ke backend saat sudah dibuat) -->
        <form id="registerForm" action="<?= route('register') ?>" method="POST" class="space-y-3.5" novalidate>

            <div>
                <label for="nama" class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-1.5">
                    Nama Lengkap
                </label>
                <input
                    type="text"
                    id="nama"
                    name="nama"
                    placeholder="Nama lengkap Anda"
                    required
                    class="w-full px-4 py-2.5 rounded-2xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
            </div>

            <div class="grid grid-cols-2 gap-3.5">
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-1.5">
                        Email
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="nama@email.com"
                        required
                        class="w-full px-4 py-2.5 rounded-2xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
                </div>

                <div>
                    <label for="telepon" class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-1.5">
                        No. Telepon
                    </label>
                    <input
                        type="tel"
                        id="telepon"
                        name="telepon"
                        placeholder="08xxxxxxxxxx"
                        pattern="[0-9]{9,15}"
                        required
                        class="w-full px-4 py-2.5 rounded-2xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3.5">
                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-1.5">
                        Kata Sandi
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Min. 8 karakter"
                        minlength="8"
                        required
                        class="w-full px-4 py-2.5 rounded-2xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
                </div>

                <div>
                    <label for="konfirmasi_password" class="block text-xs font-bold uppercase tracking-wider text-[#8a5d49] mb-1.5">
                        Konfirmasi
                    </label>
                    <input
                        type="password"
                        id="konfirmasi_password"
                        name="konfirmasi_password"
                        placeholder="Ulangi password"
                        minlength="8"
                        required
                        class="w-full px-4 py-2.5 rounded-2xl border border-[#eadfd4] bg-white text-sm text-[#201913] outline-none focus:border-[#8a5d49] transition">
                </div>
            </div>

            <!-- Pesan validasi client-side (password tidak cocok, dsb) -->
            <div id="registerMessage" class="text-center text-sm min-h-[16px]"></div>

            <button
                type="submit"
                class="w-full bg-[#8a5d49] hover:bg-[#734d3d] text-white font-bold text-sm py-3 rounded-2xl shadow-md transition">
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
    // Validasi frontend: pastikan password & konfirmasi password sama sebelum submit.
    document.getElementById('registerForm').addEventListener('submit', function (e) {
        var password   = document.getElementById('password').value;
        var konfirmasi  = document.getElementById('konfirmasi_password').value;
        var messageBox  = document.getElementById('registerMessage');

        if (password !== konfirmasi) {
            e.preventDefault();
            messageBox.textContent = 'Password dan Konfirmasi Password tidak cocok.';
            messageBox.className = 'text-center text-sm min-h-[18px] text-[#b3413a]';
            return false;
        }

        messageBox.textContent = '';
    });
</script>
