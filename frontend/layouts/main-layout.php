<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kafiber — Restoran Reservasi</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,500&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Favicon & Custom CSS -->
    <link rel="icon" type="image/png" href="assets/images/kafiber.png">
    <link rel="stylesheet" href="assets/css/style.css">
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
<body class="bg-[#f4ece1] font-sans antialiased min-h-screen flex flex-col justify-between">

    <!-- KOMPONEN NAVBAR -->
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <!-- KONTEN DINAMIS HALAMAN -->
    <main class="flex-grow">
        <?php 
        if (isset($pageFile) && file_exists($pageFile)) {
            include $pageFile;
        } else {
            include __DIR__ . '/../pages/404.php';
        }
        ?>
    </main>

    <!-- KOMPONEN FOOTER -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

</body>
</html>
