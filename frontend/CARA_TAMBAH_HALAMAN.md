# 📖 Panduan Menambah Halaman Baru (`frontend_v2`)

Dokumen ini berisi panduan singkat untuk tim pengembang tentang cara menambah halaman baru pada struktur **`frontend_v2`** tanpa merusak sistem router tunggal.

---

## 🚀 3 Langkah Mudah Menambah Halaman Baru

Misalkan Anda ingin membuat halaman **"Kontak"** (`index.php?page=kontak`).

### 1️⃣ Langkah 1: Buat File Konten Baru di Folder `pages/`
Buat file PHP baru bernama `kontak.php` di dalam direktori `pages/`.

**Path:** `pages/kontak.php`
```php
<?php
// pages/kontak.php — Halaman Kontak Kami
// Catatan: TIDAK PERLU menambahkan <html>, <head>, <body>, navbar, atau footer di sini!
?>

<div class="mx-auto max-w-7xl px-6 py-12">
    <div class="bg-white/90 rounded-3xl p-8 shadow-sm border border-[#eadfd4]">
        <h1 class="font-display text-3xl font-bold text-[#201913] mb-4">Hubungi Kami</h1>
        <p class="text-[#66574b]">Silakan hubungi tim kami untuk informasi lebih lanjut.</p>
    </div>
</div>
```

---

### 2️⃣ Langkah 2: Daftarkan Route di `index.php` (Opsional jika nama file sama)
Buka file `index.php` di root `frontend_v2`, lalu daftarkan route di `$routeMap` dan atur hak akses jika diperlukan:

```php
// index.php

// 1. Daftarkan nama page ke nama file (jika beda nama / alias)
$routeMap = [
    'home'   => 'home.php',
    'kontak' => 'kontak.php', // <-- Daftarkan di sini
];

// 2. Jika halaman ini WAJIB LOGIN, tambahkan ke $authRequiredPages:
$authRequiredPages = ['dashboard', 'reservasi', 'kontak'];
```

> 💡 **Catatan:** Jika nama parameter query (`?page=kontak`) sama persis dengan nama file (`pages/kontak.php`) dan bersifat publik, router akan **otomatis menemukan file tersebut** tanpa harus mendaftarkannya di `$routeMap`.

---

### 3️⃣ Langkah 3: Buat Link Navigasi ke Halaman Baru
Di dalam `components/navbar.php` atau file page mana pun, buat link menggunakan helper `route()`:

```php
<a href="<?= route('kontak') ?>" class="nav-pill">
    Kontak Kami
</a>
```

---

## 🔒 Fitur Keamanan Router

- **Sanitasi `basename()`**: Parameter `$_GET['page']` selalu disaring menggunakan `basename()` untuk mencegah celah *Directory Traversal* (`../../etc/passwd`).
- **Fallback Automatic 404**: Jika file halaman tidak ditemukan di folder `pages/`, router secara otomatis akan menampilkan `pages/404.php`.
