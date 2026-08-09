# Panduan Menambahkan Route Baru (`index.php`)

Dokumen ini menjelaskan panduan langkah demi langkah untuk menambahkan route / halaman baru pada frontend aplikasi secara benar, aman, dan tersinkronisasi.

---

## 📌 Alur Singkat (Quick Checklist)

- [ ] **Langkah 1**: Buat file *view* baru di folder `src/views/`
- [ ] **Langkah 2**: Daftarkan *route* di `$routes` pada file `index.php`
- [ ] **Langkah 3**: Atur hak akses (proteksi login/guest) di `index.php`
- [ ] **Langkah 4**: Daftarkan di manifest JS `src/config/routes.js`
- [ ] **Langkah 5**: Gunakan helper `route()` untuk membuat link/navigasi

---

## 🛠️ Langkah-Langkah Detail

### 1. Buat File View Baru
Buat file PHP baru di dalam folder `src/views/`. Contoh: `src/views/detail_restoran.php`.

```php
<?php
// src/views/detail_restoran.php

// Ambil parameter jika ada (opsional)
$restoId = isset($_GET['resto']) ? $_GET['resto'] : 'A';

// Dynamic header
include LAYOUTS_PATH . '/header.php';
?>

<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold">Detail Restoran <?= e($restoId) ?></h1>
    <!-- Konten Halaman -->
</div>

<?php 
// Dynamic footer
include LAYOUTS_PATH . '/footer.php'; 
?>
```

---

### 2. Daftarkan Route di `frontend/index.php`
Buka file `frontend/index.php`, lalu tambahkan pasangan `'nama_page' => 'nama_file.php'` pada array `$routes`:

```php
// frontend/index.php (sekitar baris 96)

$routes = [
    'home'             => 'home.php',
    'login'            => 'login.php',
    'register'         => 'register.php',
    'dashboard'        => 'dashboard_user.php',
    'galeri'           => 'galeri.php',
    'menu'             => 'menu.php',
    'story'            => 'story.php',
    'reservations'     => 'reservations.php',
    'reservation-form' => 'reservation_form.php',
    'detail_restoran'  => 'detail_restoran.php', // <-- Tambahkan di sini
];
```

---

### 3. Konfigurasi Proteksi Akses (Hak Akses)
Tentukan apakah halaman tersebut memerlukan autentikasi login atau hanya untuk pengguna yang belum login (*guest*):

```php
// frontend/index.php (sekitar baris 108)

// Halaman yang WAJIB LOGIN (User akan di-redirect ke halaman login jika belum login)
$authRequiredPages = [
    'dashboard', 
    'galeri', 
    'reservations', 
    'reservation-form',
    'detail_restoran' // <-- Tambahkan jika butuh login
];

// Halaman khusus GUEST (User yang sudah login akan di-redirect ke dashboard)
$guestOnlyPages = [
    'login', 
    'register'
];
```

*Catatan: Jika halaman dapat diakses publik oleh siapa saja (tanpa login maupun setelah login), tidak perlu dimasukkan ke `$authRequiredPages` maupun `$guestOnlyPages`.*

---

### 4. Update Manifest JS (`src/config/routes.js`)
Agar router PHP dan JavaScript tetap konsisten sebagai *Single Source of Truth*, tambahkan route baru di file `src/config/routes.js`:

```javascript
// src/config/routes.js

export const frontendRoutes = [
  // ...
  // Halaman yang wajib login
  { path: '/detail-restoran', name: 'detail_restoran', requiresAuth: true, roles: ['customer', 'staff', 'admin'] },
];
```

---

### 5. Cara Memanggil/Membuat Link ke Route Baru

Gunakan helper `route()` dalam kode PHP untuk membuat URL secara dinamis:

#### Tanpa Query Parameter:
```php
<a href="<?= route('detail_restoran') ?>">Lihat Restoran</a>
<!-- Output: index.php?page=detail_restoran -->
```

#### Dengan Query Parameter:
```php
<a href="<?= route('detail_restoran', ['resto' => 'A']) ?>">Restoran A</a>
<!-- Output: index.php?page=detail_restoran&resto=A -->
```

#### Di JavaScript Frontend:
```javascript
import { frontendUrl } from './config/routes.js';

const url = frontendUrl('detail_restoran', { resto: 'A' });
// Output: index.php?page=detail_restoran&resto=A
```

---

## ❓ Troubleshoot Error Umum

| Error | Penyebab | Solusi |
|---|---|---|
| **404 Halaman Tidak Ditemukan** | Route belum dimasukkan ke array `$routes` di `index.php`. | Tambahkan `'nama_page' => 'file.php'` ke array `$routes`. |
| **File halaman tidak ditemukan** | Key route sudah ada di `$routes`, tapi file `.php` di folder `src/views/` tidak ada. | Pastikan nama file di `src/views/` sesuai dengan nama file di `$routes`. |
| **Selalu Ter-redirect ke Login** | Route dimasukkan ke `$authRequiredPages`, tetapi pengguna belum mempunyai sesi login. | Hapus dari `$authRequiredPages` jika ingin halaman bisa diakses publik. |
