<?php
// header.php — Kafiber Restoran
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
$userName = $_SESSION['user_name'] ?? 'Akun Saya';
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
<link rel="stylesheet" href="style.css">
</head>
<body class="bg-[#f4ece1] font-sans antialiased">

  <!-- NAVBAR -->
  <header style="background: linear-gradient(135deg, #3b1f14 0%, #5e392e 40%, #8a5d49 100%); padding:1rem 1.5rem;">
    <div style="max-width:1180px; margin:0 auto; display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap;">
      
      <!-- Logo Brand -->
      <a href="home.php" style="display:inline-flex; align-items:center; gap:0.65rem; text-decoration:none; color:#fff; padding:0.5rem 0; border-radius:12px;">
        <img src="img/kafiber.png" alt="Logo Kafiber" style="width:2.8rem; height:2.8rem; object-fit:contain; border-radius:9999px; background:#fff;">
        <span class="font-display" style="font-size:1.15rem; font-style:italic; color:#fff;">Kafiber</span>
      </a>

      <!-- Menu Navigasi -->
      <nav style="display:flex; align-items:center; gap:0.6rem; flex-wrap:wrap;">
        <a href="story.php" style="display:inline-flex; align-items:center; justify-content:center; border-radius:9999px; border:1px solid rgba(255,255,255,0.3); background:rgba(255,255,255,0.1); padding:0.55rem 1.1rem; font-size:0.92rem; font-weight:600; color:#fff; text-decoration:none; transition:background 180ms ease;">Story/About</a>
        <a href="home.php#menu" style="display:inline-flex; align-items:center; justify-content:center; border-radius:9999px; border:1px solid rgba(255,255,255,0.3); background:rgba(255,255,255,0.1); padding:0.55rem 1.1rem; font-size:0.92rem; font-weight:600; color:#fff; text-decoration:none; transition:background 180ms ease;">Menu Kuliner</a>
        
        <!-- Menu Galeri dengan proteksi login -->
        <?php if ($isLoggedIn): ?>
          <a href="galeri.php" style="display:inline-flex; align-items:center; justify-content:center; border-radius:9999px; border:1px solid rgba(255,255,255,0.3); background:rgba(255,255,255,0.1); padding:0.55rem 1.1rem; font-size:0.92rem; font-weight:600; color:#fff; text-decoration:none; transition:background 180ms ease;">Galeri / Suasana</a>
        <?php else: ?>
          <a href="login.php" onclick="alert('Silakan masuk terlebih dahulu untuk melihat Galeri / Suasana.');" style="display:inline-flex; align-items:center; justify-content:center; border-radius:9999px; border:1px solid rgba(255,255,255,0.3); background:rgba(255,255,255,0.1); padding:0.55rem 1.1rem; font-size:0.92rem; font-weight:600; color:#fff; text-decoration:none; transition:background 180ms ease;">Galeri / Suasana</a>
        <?php endif; ?>
      </nav>

      <!-- Tombol Masuk / Akun (Dinamis berdasarkan Status Login) -->
      <?php if ($isLoggedIn): ?>
        <a href="profil.php" style="display:inline-flex; align-items:center; justify-content:center; border-radius:9999px; background:#fff; padding:0.7rem 1.4rem; font-size:0.95rem; font-weight:700; color:#5e392e; text-decoration:none; box-shadow:0 4px 16px rgba(0,0,0,0.18); transition:transform 180ms ease, box-shadow 180ms ease;">
          <?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?>
        </a>
      <?php else: ?>
        <a href="login.php" style="display:inline-flex; align-items:center; justify-content:center; border-radius:9999px; background:#fff; padding:0.7rem 1.4rem; font-size:0.95rem; font-weight:700; color:#5e392e; text-decoration:none; box-shadow:0 4px 16px rgba(0,0,0,0.18); transition:transform 180ms ease, box-shadow 180ms ease;">
          Masuk / Daftar
        </a>
      <?php endif; ?>

    </div>
  </header>