# Layouts

Folder ini berisi layout templates yang membungkus halaman utama.

## File Structure

```
layouts/
├── Header.php            # Header/Navbar (shared)
├── Footer.php            # Footer (shared)
├── MainLayout.php        # Main layout wrapper
├── AuthLayout.php        # Layout untuk halaman login/register
└── AdminLayout.php       # Layout untuk admin pages
```

## Penamaan Convention

- Gunakan PascalCase untuk nama file
- Satu file = satu layout pattern

## Cara Penggunaan

```php
<?php
// Menggunakan main layout
include '../layouts/Header.php';
?>

<div class="main-content">
    <!-- Konten spesifik halaman -->
</div>

<?php
include '../layouts/Footer.php';
?>
```

## Best Practices

- Layout harus minimal (hanya struktur dasar HTML)
- Styling untuk layout ada di `styles/layouts.css`
- Component-specific styling ada di component folder masing-masing
