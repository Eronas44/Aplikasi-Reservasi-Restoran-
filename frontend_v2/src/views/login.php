<?php
// login.php — Kafiber Restoran (tampil sebagai popup di atas Home, dengan background blur)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error_message = 'Email dan Kata Sandi wajib diisi.';
    } else {
        require_once dirname(__DIR__) . '/config/api.config.php';
        require_once dirname(__DIR__) . '/utils/api.php';

        $result = api_login($email, $password);

        if ($result['ok'] && isset($result['data']['data']) && is_array($result['data']['data'])) {
            set_frontend_session_from_user($result['data']['data']);
            header('Location: ' . route('home')); // <--- SUDAH DIGANTI KE HOME
            exit;
        }

        $error_message = api_error_message($result, 'Email atau Kata Sandi salah.');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Masuk — Kafiber Restoran</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,500&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="src/styles/style.css">
<style>
    body { font-family: 'Inter', sans-serif; }
    .font-display { font-family: 'Fraunces', serif; }

    /* Background Home jadi blur saat modal login aktif */
    #home-bg {
        filter: blur(6px);
        transform: scale(1.03); /* biar tepi blur tidak keliatan putih */
        transition: filter 0.3s ease;
    }

    /* Animasi muncul modal */
    @keyframes modalPop {
        from { opacity: 0; transform: translateY(16px) scale(0.97); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }
    #login-modal-card {
        animation: modalPop 0.25s ease-out;
    }
</style>
</head>
<body class="bg-[#f4ece1] antialiased">

<!-- ============ BACKGROUND: TAMPILAN HOME (BLUR) ============ -->
<div id="home-bg" class="min-h-screen pointer-events-none select-none">

    <!-- Navbar -->
    <header class="flex items-center justify-between px-16 py-5 bg-[#5e392e]">
        <div class="flex items-center gap-2">
            <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center">
                <span class="text-white font-display italic font-bold">K</span>
            </div>
            <span class="font-display text-xl font-semibold text-white">Kafiber</span>
        </div>

        <nav class="hidden md:flex items-center gap-10 text-sm font-medium text-white/90">
            <span>Story/About</span>
            <span>Menu Kuliner</span>
            <span>Galeri / Suasana</span>
        </nav>

        <div class="bg-white text-[#5e392e] text-sm font-bold px-6 py-2.5 rounded-full">
            Masuk / Daftar
        </div>
    </header>

    <!-- Hero -->
    <section class="grid md:grid-cols-2 gap-10 items-center px-16 py-20 bg-[#f4ece1]">
        <div>
            <span class="inline-block text-xs font-bold tracking-widest text-[#8a5d49] border border-[#8a5d49]/40 rounded-full px-4 py-1.5 mb-6">
                SISTEM RESERVASI RESTORAN
            </span>
            <h1 class="font-display text-5xl font-medium text-[#201913] leading-tight mb-2">
                Kemewahan Rasa di Setiap Sajian.
            </h1>
            <h1 class="font-display text-5xl italic font-medium text-[#201913] leading-tight mb-6">
                Good Food, Good Mood.
            </h1>
            <p class="text-[#66574b] text-base leading-relaxed mb-8 max-w-md">
                Rasa & Cerita Dari meja reservasi hingga pesanan siap saji, menyatukan alur kerja restoran dalam satu genggaman. Tampil berkelas, layani lebih cepat.
            </p>
            <div class="flex gap-4">
                <div class="bg-[#5e392e] text-white text-sm font-bold px-7 py-3.5 rounded-2xl">Buat Akun</div>
                <div class="border border-[#5e392e] text-[#5e392e] text-sm font-bold px-7 py-3.5 rounded-2xl">Masuk ke Sistem</div>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] p-4 shadow-xl">
            <div class="rounded-[1.5rem] overflow-hidden aspect-[4/3]">
                <img src="https://images.unsplash.com/photo-1621996346565-e3dbc353d2e5?q=80&w=1200&auto=format&fit=crop"
                     alt="" class="w-full h-full object-cover">
            </div>
        </div>
    </section>
</div>

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
                <?= htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8') ?>
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

</body>
</html>