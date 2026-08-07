<?php
// index.php — Hardcode Mapping Paksa ke reservasi.php
session_start();

$page = isset($_GET['page']) ? trim($_GET['page']) : 'home';

if ($page === 'logout') {
    session_destroy();
    header('Location: index.php?page=home');
    exit;
}

// Petakan semua varian (baik dengan 's' maupun tanpa 's') agar mutlak lari ke reservasi.php
switch ($page) {
    case 'home':
    case 'dashboard':
        $file = 'src/views/home.php';
        break;
    case 'detail_restoran':
        $file = 'src/views/detail_restoran.php';
        break;
    case 'reservasi':
    case 'reservations': // Kalaupun ada yang manggil pakai 's', tetap kita arahkan ke file tanpa 's'
        $file = 'src/views/reservasi.php';
        break;
    case 'galeri':
        $file = 'src/views/galeri.php';
        break;
    case 'story':
        $file = 'src/views/story.php';
        break;
    case 'menu':
        $file = 'src/views/menu.php';
        break;
    case 'login':
        $file = 'src/views/login.php';
        break;
    default:
        $file = 'src/views/404.php';
        break;
}

// Eksekusi pemanggilan file fisik
if (file_exists($file)) {
    include $file;
} else {
    // Tampilkan informasi jelas jika filenya benar-benar tidak ketemu di disk
    echo "<div style='padding: 30px; font-family: sans-serif; text-align: center;'>";
    echo "<h2 style='color: #a00;'>File Fisik Tidak Ditemukan!</h2>";
    echo "<p>PHP mencari file pada path: <b>" . htmlspecialchars($file) . "</b></p>";
    echo "</div>";
}
?>