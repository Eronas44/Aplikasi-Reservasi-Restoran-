<?php
// index.php — Perbaikan Routing
session_start();

$page = isset($_GET['page']) ? trim($_GET['page']) : 'home';

if ($page === 'logout') {
    session_destroy();
    header('Location: index.php?page=home');
    exit;
}

// Petakan rute halaman dengan benar ke file fisiknya masing-masing
switch ($page) {
    case 'home':
        $file = 'src/views/home.php';
        break;
    case 'dashboard':
        $file = 'src/views/dashboard_user.php'; // Diarahkan ke dashboard user yang benar
        break;
    case 'detail_restoran':
        $file = 'src/views/detail_restoran.php';
        break;
    case 'reservasi':
    case 'reservations': 
        $file = 'src/views/reservasi.php';
        break;
    // Tambahkan rute untuk proses_reservasi di sini
    case 'proses_reservasi':
        $file = 'src/views/proses_reservasi.php';
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
    echo "<div style='padding: 30px; font-family: sans-serif; text-align: center;'>";
    echo "<h2 style='color: #a00;'>File Fisik Tidak Ditemukan!</h2>";
    echo "<p>PHP mencari file pada path: <b>" . htmlspecialchars($file) . "</b></p>";
    echo "</div>";
}
?>