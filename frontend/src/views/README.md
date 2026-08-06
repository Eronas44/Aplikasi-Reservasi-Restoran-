# Views

Folder ini berisi semua halaman (page-level components) aplikasi.

## Struktur File

```
views/
├── Home.php              # Homepage
├── Login.php             # Halaman login
├── Register.php          # Halaman register
├── Dashboard.php         # Dashboard user
├── Gallery.php           # Galeri
├── Profile.php           # Profil user
├── Reservations.php      # Daftar reservasi
└── NotFound.php          # Halaman 404
```

## Penamaan Convention

- Gunakan PascalCase untuk nama file
- Satu file = satu halaman

## Template

```php
<?php
// Gunakan layout
include '../layouts/Header.php';
?>

<main class="container mx-auto">
    <!-- Konten halaman -->
</main>

<?php
include '../layouts/Footer.php';
?>
```
