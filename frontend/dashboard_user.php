<?php
// dashboard_user.php — Dashboard Reservasi Pelanggan Kafiber
session_start();

// Proteksi halaman: Pastikan sudah login dan rolenya benar-benar 'user' (bukan admin)
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || (isset($_SESSION['role']) && $_SESSION['role'] === 'admin')) {
    header('Location: login.php');
    exit;
}

$userName = $_SESSION['user_name'] ?? 'Pelanggan';

// Data dummy restoran sesuai mockup
$restaurants = [
    ["name" => "Restoran A", "rating" => "4,1", "image" => "img/kafiber.png"],
    ["name" => "Restoran B", "rating" => "4,1", "image" => "img/kafiber.png"],
    ["name" => "Restoran C", "rating" => "4,1", "image" => "img/kafiber.png"],
    ["name" => "Restoran D", "rating" => "4,1", "image" => "img/kafiber.png"],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard User — Kafiber Restoran</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="style.css">
</head>
<body class="bg-[#f4ece1] font-sans antialiased min-h-screen flex">

    <!-- SIDEBAR KIRI -->
    <aside class="w-72 bg-[#faf8f5] border-r border-[#eadfd4] flex flex-col justify-between p-6 shadow-sm">
        <div>
            <!-- Logo & Brand -->
            <div class="flex items-center gap-3 mb-10">
                <div class="w-10 h-10 rounded-full bg-[#5e392e] text-white flex items-center justify-center font-display italic font-bold text-lg shadow">
                    K
                </div>
                <span class="font-display text-lg font-semibold italic text-[#201913]">Kafiber Restoran</span>
            </div>

            <!-- Tombol Navigasi Mockup -->
            <nav class="space-y-4">
                <a href="#preview" class="flex items-center px-6 py-3.5 rounded-full bg-white border border-[#eadfd4] text-[#201913] text-sm font-semibold shadow-sm hover:border-[#8a5d49] transition">
                    Preview Restoran
                </a>
                <a href="#reservasi" class="flex items-center px-6 py-3.5 rounded-full bg-white border border-[#eadfd4] text-[#201913] text-sm font-semibold shadow-sm hover:border-[#8a5d49] transition">
                    Reservasi Restoran
                </a>
                <a href="#menu" class="flex items-center px-6 py-3.5 rounded-full bg-white border border-[#eadfd4] text-[#201913] text-sm font-semibold shadow-sm hover:border-[#8a5d49] transition">
                    Menu
                </a>
            </nav>
        </div>

        <!-- Info User & Tombol Keluar -->
        <div class="pt-6 border-t border-[#eadfd4]">
            <p class="text-xs text-[#66574b] mb-1">Masuk sebagai User:</p>
            <p class="text-sm font-bold text-[#201913] truncate mb-4"><?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?></p>
            <a href="logout.php" class="block w-full text-center bg-red-50 hover:bg-red-100 text-red-600 text-xs font-bold py-3 rounded-2xl transition">
                Keluar Sistem
            </a>
        </div>
    </aside>

    <!-- KONTEN UTAMA KANAN -->
    <main class="flex-1 p-10 overflow-y-auto">
        <header class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="font-display text-3xl font-semibold text-[#201913]">Pilih Restoran & Reservasi</h1>
                <p class="text-sm text-[#66574b] mt-1">Silakan pilih restoran tujuan untuk melakukan pemesanan tempat.</p>
            </div>
        </header>

        <!-- Grid Daftar Restoran Sesuai Mockup -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl">
            <?php foreach ($restaurants as $resto): ?>
                <div class="bg-[#faf8f5] border border-[#eadfd4] rounded-3xl p-6 shadow-sm hover:shadow-md transition flex flex-col items-center">
                    
                    <!-- Kotak Gambar -->
                    <div class="w-full h-52 bg-white border border-[#eadfd4] rounded-2xl flex items-center justify-center text-[#66574b] font-bold text-lg mb-4 shadow-inner">
                        GAMBAR
                    </div>

                    <!-- Informasi Restoran & Tombol Pesan -->
                    <div class="w-full flex items-center justify-between mt-2">
                        <div>
                            <h2 class="font-display text-xl font-semibold text-[#201913]"><?= $resto['name'] ?></h2>
                            <p class="text-xs text-[#66574b] mt-0.5">Rating <?= $resto['rating'] ?></p>
                        </div>
                        <a href="reservasi_form.php?resto=<?= urlencode($resto['name']) ?>" class="bg-[#8a5d49] hover:bg-[#734d3d] text-white text-xs font-bold px-5 py-3 rounded-full shadow transition">
                            Pesan Sekarang
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

</body>
</html>