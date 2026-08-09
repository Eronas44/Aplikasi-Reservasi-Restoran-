<?php
// header.php — Kafiber Restoran
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
$userName = $_SESSION['user_name'] ?? 'Akun Saya';
$currentPage = isset($_GET['page']) ? sanitize($_GET['page']) : 'home';

// Helper function untuk cek halaman aktif
if (!function_exists('isActive')) {
    function isActive($pageName) {
        global $currentPage;
        return $currentPage === $pageName ? 'active-nav' : '';
    }
}

// Helper function untuk generate route URL
if (!function_exists('route')) {
    function route($page = '', $params = []) {
        $query = [];
        if (!empty($page)) {
            $query['page'] = $page;
        }
        if (is_array($params) && count($params) > 0) {
            foreach ($params as $key => $value) {
                if ($value !== null && $value !== '') {
                    $query[$key] = $value;
                }
            }
        }
        if (count($query) === 0) {
            return 'index.php';
        }
        return 'index.php?' . http_build_query($query);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kafiber — Restoran Reservasi</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,500&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="icon" type="image/png" href="img/kafiber.png">
<link rel="stylesheet" href="src/styles/style.css">
<style>
  .nav-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 9999px;
    border: 1px solid rgba(255,255,255,0.3);
    background: rgba(255,255,255,0.1);
    padding: 0.55rem 1.1rem;
    font-size: 0.92rem;
    font-weight: 600;
    color: #fff;
    text-decoration: none;
    transition: background 180ms ease, border-color 180ms ease;
  }
  .nav-pill:hover {
    background: rgba(255,255,255,0.2);
    border-color: rgba(255,255,255,0.5);
  }
</style>
</head>
<body class="bg-[#f4ece1] font-sans antialiased">

  <!-- NAVBAR -->
  <header style="background: linear-gradient(135deg, #3b1f14 0%, #5e392e 40%, #8a5d49 100%); padding: 1rem 1.5rem;">
    <div style="max-width: 1180px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">

      <!-- Logo Brand -->
      <a href="<?= route('home') ?>" style="display: inline-flex; align-items: center; gap: 0.65rem; text-decoration: none; color: #fff; padding: 0.5rem 0;">
        <img src="img/kafiber.png" alt="Logo Kafiber" style="width: 2.8rem; height: 2.8rem; object-fit: contain; border-radius: 9999px; background: #fff;">
        <span class="font-display" style="font-size: 1.15rem; font-style: italic; color: #fff;">Kafiber</span>
      </a>

      <!-- MENU NAVIGASI TENGAH -->
      <?php if ($currentPage === 'home'): ?>
      <nav style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
        <a href="<?= route('home') ?>#story" class="nav-pill">Story/About</a>
        <a href="<?= route('home') ?>#menu-kuliner" class="nav-pill">Menu Kuliner</a>
        
        <!-- Galeri: Langsung bisa diakses karena sudah login -->
        <a href="<?= route('galeri') ?>" class="nav-pill">
           Galeri / Suasana
        </a>
      </nav>
      <?php else: ?>
      <div></div>
      <?php endif; ?>

      <!-- Tombol Aksi Kanan Atas -->
      <div style="display: flex; align-items: center; gap: 0.8rem; flex-wrap: wrap;">
        <?php if ($isLoggedIn): ?>
          
          <!-- Tombol "Pesan" hanya muncul di halaman home -->
          <?php if ($currentPage === 'home'): ?>
          <a href="<?= route('dashboard') ?>" style="display: inline-flex; align-items: center; justify-content: center; border-radius: 9999px; background: #fff; padding: 0.7rem 1.4rem; font-size: 0.95rem; font-weight: 700; color: #5e392e; text-decoration: none; box-shadow: 0 4px 16px rgba(0,0,0,0.18);">
            Pesan
          </a>
          <?php endif; ?>
          
          <!-- Info Profil User -->
          <div style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.6rem; font-size: 0.92rem; font-weight: 600; color: #fff;">
            <svg style="width: 1.25rem; height: 1.25rem; color: rgba(255,255,255,0.85);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span><?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?></span>
          </div>

          <!-- Tombol Keluar -->
          <a href="<?= route('logout') ?>" style="display: inline-flex; align-items: center; justify-content: center; border-radius: 9999px; border: 1px solid rgba(255,255,255,0.35); background: rgba(255,255,255,0.05); padding: 0.7rem 1rem; font-size: 0.85rem; font-weight: 600; color: #fff; text-decoration: none;">
            Keluar
          </a>

        <?php else: ?>
          
          <!-- Tombol Masuk / Daftar -->
          <a href="<?= route('login') ?>" style="display: inline-flex; align-items: center; justify-content: center; border-radius: 9999px; background: #fff; padding: 0.7rem 1.4rem; font-size: 0.95rem; font-weight: 700; color: #5e392e; text-decoration: none; box-shadow: 0 4px 16px rgba(0,0,0,0.18);">
            Masuk / Daftar
          </a>

        <?php endif; ?>
      </div>

    </div>
  </header>