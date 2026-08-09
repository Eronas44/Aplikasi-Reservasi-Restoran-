<?php
// components/navbar.php — Navigation Bar Component
$isLoggedIn = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
$userName = $_SESSION['user_name'] ?? 'Akun Saya';
$currentPage = isset($_GET['page']) ? sanitize(basename($_GET['page'])) : 'home';
?>

<!-- NAVBAR -->
<header style="background: linear-gradient(135deg, #3b1f14 0%, #5e392e 40%, #8a5d49 100%); padding: 1rem 1.5rem;">
  <div style="max-width: 1180px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">

    <!-- Logo Brand -->
    <a href="<?= route('home') ?>" style="display: inline-flex; align-items: center; gap: 0.65rem; text-decoration: none; color: #fff; padding: 0.5rem 0;">
      <img src="assets/images/kafiber.png" alt="Logo Kafiber" style="width: 2.8rem; height: 2.8rem; object-fit: contain; border-radius: 9999px; background: #fff;">
      <span class="font-display" style="font-size: 1.15rem; font-style: italic; color: #fff;">Kafiber</span>
    </a>

    <!-- MENU NAVIGASI TENGAH -->
    <?php if ($currentPage === 'home'): ?>
    <nav style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
      <a href="<?= route('home') ?>#story" class="nav-pill">Story/About</a>
      <a href="<?= route('home') ?>#menu-kuliner" class="nav-pill">Menu Kuliner</a>
      <a href="<?= route('galeri') ?>" class="nav-pill">Galeri / Suasana</a>
    </nav>
    <?php else: ?>
    <nav style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
      <a href="<?= route('home') ?>" class="nav-pill">Beranda</a>
      <a href="<?= route('menu') ?>" class="nav-pill">Menu</a>
      <a href="<?= route('galeri') ?>" class="nav-pill">Galeri</a>
      <a href="<?= route('story') ?>" class="nav-pill">Story</a>
    </nav>
    <?php endif; ?>

    <!-- Tombol Aksi Kanan Atas -->
    <div style="display: flex; align-items: center; gap: 0.8rem; flex-wrap: wrap;">
      <?php if ($isLoggedIn): ?>
        
        <?php if ($currentPage === 'home'): ?>
        <a href="<?= route('dashboard') ?>" style="display: inline-flex; align-items: center; justify-content: center; border-radius: 9999px; background: #fff; padding: 0.7rem 1.4rem; font-size: 0.95rem; font-weight: 700; color: #5e392e; text-decoration: none; box-shadow: 0 4px 16px rgba(0,0,0,0.18);">
          Pesan
        </a>
        <?php endif; ?>
        
        <div style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.6rem; font-size: 0.92rem; font-weight: 600; color: #fff;">
          <svg style="width: 1.25rem; height: 1.25rem; color: rgba(255,255,255,0.85);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
          <span><?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?></span>
        </div>

        <a href="<?= route('logout') ?>" style="display: inline-flex; align-items: center; justify-content: center; border-radius: 9999px; border: 1px solid rgba(255,255,255,0.35); background: rgba(255,255,255,0.05); padding: 0.7rem 1rem; font-size: 0.85rem; font-weight: 600; color: #fff; text-decoration: none;">
          Keluar
        </a>

      <?php else: ?>
        
        <a href="<?= route('login') ?>" style="display: inline-flex; align-items: center; justify-content: center; border-radius: 9999px; background: #fff; padding: 0.7rem 1.4rem; font-size: 0.95rem; font-weight: 700; color: #5e392e; text-decoration: none; box-shadow: 0 4px 16px rgba(0,0,0,0.18);">
          Masuk / Daftar
        </a>

      <?php endif; ?>
    </div>

  </div>
</header>
