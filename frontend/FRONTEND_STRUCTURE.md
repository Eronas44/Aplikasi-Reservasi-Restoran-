# 🏗️ FRONTEND STRUCTURE - DOKUMENTASI LENGKAP

Struktur folder frontend yang terorganisir dengan baik

---

## 📁 TREE VIEW

```
frontend/
├── 📄 index.php                    ← ROUTER UTAMA (Entry Point)
├── 📄 package.json                 ← Dependencies
├── 📄 tailwind.config.js           ← Tailwind CSS config
├── 📄 style.css                    ← Global styles
├── 📄 README.md                    ← Project README
│
├── 📚 DOKUMENTASI (Routing System)
│   ├── ROUTING.md                  ← Full routing documentation
│   ├── SETUP_ROUTER.md             ← Setup & implementation guide
│   ├── ROUTER_SUMMARY.md           ← Quick reference
│   └── FILES_TO_UPDATE.md          ← Files checklist
│
├── 📂 src/                         ← Source code folder
│   │
│   ├── 📂 views/                   ← Halaman-halaman aplikasi
│   │   ├── home.php                ← Beranda
│   │   ├── login.php               ← Halaman login
│   │   ├── register.php            ← Halaman registrasi
│   │   ├── dashboard_user.php      ← Dashboard user
│   │   ├── galeri.php              ← Galeri restoran
│   │   ├── menu.php                ← Menu makanan
│   │   ├── story.php               ← Tentang restoran
│   │   └── README.md               ← Views documentation
│   │
│   ├── 📂 layouts/                 ← Template layouts
│   │   ├── header.php              ← Header navbar
│   │   ├── footer.php              ← Footer
│   │   └── README.md               ← Layouts documentation
│   │
│   ├── 📂 components/              ← Reusable components
│   │   ├── card.php                ← Card component
│   │   ├── button.php              ← Button component
│   │   ├── modal.php               ← Modal component
│   │   └── README.md               ← Components documentation
│   │
│   ├── 📂 styles/                  ← CSS styles
│   │   ├── main.css                ← Main styles
│   │   ├── components.css          ← Component styles
│   │   ├── layout.css              ← Layout styles
│   │   ├── utilities.css           ← Utility classes
│   │   └── README.md               ← Styles documentation
│   │
│   ├── 📂 js/                      ← JavaScript files
│   │   ├── app.js                  ← Main app logic
│   │   ├── carousel.js             ← Carousel functionality
│   │   ├── form-handler.js         ← Form handling
│   │   ├── api-client.js           ← API communication
│   │   └── README.md               ← JS documentation
│   │
│   ├── 📂 assets/                  ← Static assets
│   │   ├── icons/                  ← SVG icons
│   │   ├── fonts/                  ← Custom fonts
│   │   └── README.md               ← Assets documentation
│   │
│   ├── 📂 config/                  ← Configuration files
│   │   ├── app.config.php          ← App configuration
│   │   ├── api.config.php          ← API endpoints
│   │   └── README.md               ← Config documentation
│   │
│   └── 📂 utils/                   ← Utility functions
│       ├── helper.php              ← Helper functions
│       ├── validator.php           ← Form validation
│       ├── formatter.php           ← Data formatting
│       └── README.md               ← Utils documentation
│
├── 📂 public/                      ← Public accessible files
│   ├── 📂 css/                     ← Compiled CSS
│   ├── 📂 js/                      ← Compiled JS
│   └── 📂 images/                  ← Images folder
│
├── 📂 img/                         ← Image assets
│   ├── 📂 slide_makanan/           ← Food carousel images
│   ├── 📂 galeri_suasana/          ← Gallery images
│   ├── 📂 ruangan_utama/           ← Main room photos
│   ├── 📂 area_privat/             ← Private area photos
│   ├── 📂 suasana_malam/           ← Night ambiance photos
│   ├── kafiber.png                 ← Logo
│   └── README.md                   ← Images documentation
│
├── 📂 docker/                      ← Docker configuration
│   ├── nginx.conf                  ← Nginx config
│   └── php.ini                     ← PHP config
│
├── 📄 Dockerfile                   ← Docker image config
├── 📄 docker-compose.yml           ← Docker compose config
├── 📄 .dockerignore                ← Docker ignore
├── 📄 .gitignore                   ← Git ignore
└── 📄 node_modules/                ← Dependencies (npm)

```

---

## 📋 FOLDER DESCRIPTIONS

### 🎯 Root Level
| File | Purpose |
|------|---------|
| `index.php` | Router utama - entry point semua request |
| `package.json` | NPM dependencies (Tailwind, etc) |
| `style.css` | Global CSS styles |
| `Dockerfile` | Docker image configuration |

### 👁️ src/views/
**Halaman-halaman aplikasi yang ditampilkan ke user**

| File | Purpose |
|------|---------|
| `home.php` | Beranda dengan hero section & features |
| `login.php` | Form login user |
| `register.php` | Form registrasi user baru |
| `dashboard_user.php` | Dashboard user yang sudah login |
| `galeri.php` | Galeri foto restoran |
| `menu.php` | Menu makanan & minuman |
| `story.php` | Tentang restoran (story/history) |

**Cara Akses:**
```
http://localhost:8000/index.php?page=home
http://localhost:8000/index.php?page=login
http://localhost:8000/index.php?page=register
dll...
```

### 🎨 src/layouts/
**Template yang digunakan di semua halaman**

| File | Purpose |
|------|---------|
| `header.php` | Navigation bar (navbar) |
| `footer.php` | Footer dengan info kontak |

**Cara Kerja:**
```php
// Di setiap view, header & footer auto-included
include 'src/layouts/header.php';  // Navbar
include 'views/home.php';          // Content
include 'src/layouts/footer.php';  // Footer
```

### 🧩 src/components/
**Komponen reusable yang bisa digunakan di berbagai halaman**

| File | Purpose |
|------|---------|
| `card.php` | Card component (untuk menu, gallery, etc) |
| `button.php` | Button component |
| `modal.php` | Modal/dialog component |

**Contoh Penggunaan:**
```php
<?php include 'src/components/card.php'; ?>
<button><?php include 'src/components/button.php'; ?></button>
```

### 🎨 src/styles/
**CSS styles terorganisir**

| File | Purpose |
|------|---------|
| `main.css` | Main styles untuk page |
| `components.css` | Styles untuk components |
| `layout.css` | Layout styles (grid, flex, etc) |
| `utilities.css` | Utility classes |

**Ditambah Tailwind CSS yang di-generate dari tailwind.config.js**

### 📜 src/js/
**JavaScript untuk interaktivitas**

| File | Purpose |
|------|---------|
| `app.js` | Main app logic |
| `carousel.js` | Carousel/slider functionality |
| `form-handler.js` | Form validation & submission |
| `api-client.js` | Communicate dengan backend API |

### 📦 src/assets/
**Static assets (fonts, icons, dll)**

| Folder | Purpose |
|--------|---------|
| `icons/` | SVG icons |
| `fonts/` | Custom fonts |

### ⚙️ src/config/
**Configuration files**

| File | Purpose |
|------|---------|
| `app.config.php` | App settings (timezone, locale, etc) |
| `api.config.php` | Backend API endpoints |

**Contoh:**
```php
// api.config.php
return [
    'backend_url' => 'http://localhost:8080',
    'api_version' => 'v1',
];
```

### 🛠️ src/utils/
**Utility functions**

| File | Purpose |
|------|---------|
| `helper.php` | Helper functions (route(), etc) |
| `validator.php` | Form validation functions |
| `formatter.php` | Data formatting functions |

### 🎨 public/
**Public accessible files (CSS, JS compiled)**

| Folder | Purpose |
|--------|---------|
| `css/` | Compiled CSS files |
| `js/` | Compiled/bundled JS files |
| `images/` | Optimized images |

### 📸 img/
**Image assets organized by category**

| Folder | Purpose |
|--------|---------|
| `slide_makanan/` | Food carousel images |
| `galeri_suasana/` | Gallery/ambiance photos |
| `ruangan_utama/` | Main dining room |
| `area_privat/` | Private dining areas |
| `suasana_malam/` | Night ambiance shots |

---

## 🔄 DATA FLOW

### Request Flow
```
┌─────────────────────┐
│  User Browser       │
│  Klik Link/Button   │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────────────────────┐
│  Browser Request                    │
│  http://localhost:8000/index.php?page=login
└──────────┬──────────────────────────┘
           │
           ▼
┌─────────────────────────────────────┐
│  index.php (Router)                 │
│  1. Get $_GET['page']               │
│  2. Sanitize input                  │
│  3. Check route exists              │
└──────────┬──────────────────────────┘
           │
           ▼
┌─────────────────────────────────────┐
│  Include Files (in order)           │
│  1. src/layouts/header.php          │
│  2. src/views/login.php             │
│  3. src/layouts/footer.php          │
└──────────┬──────────────────────────┘
           │
           ▼
┌─────────────────────────────────────┐
│  Browser Renders HTML               │
│  Load CSS from public/css/          │
│  Load JS from public/js/            │
│  Load Images from img/              │
└─────────────────────────────────────┘
```

---

## 📝 FILE ORGANIZATION RULES

### ✓ Rules

1. **Views di src/views/** - Semua halaman aplikasi
2. **Layouts di src/layouts/** - Header, footer, base templates
3. **Components di src/components/** - Reusable UI components
4. **Styles di src/styles/** - CSS terpisah per kategori
5. **JS di src/js/** - JavaScript logic terpisah
6. **Config di src/config/** - Configuration files
7. **Utils di src/utils/** - Helper functions
8. **Assets di src/assets/** - Static files (fonts, icons)
9. **Images di img/** - Organized by category
10. **Public di public/** - Generated/compiled files

### ✗ Don'ts

❌ Jangan mix PHP & JS  
❌ Jangan simpan CSS inline  
❌ Jangan duplicate components  
❌ Jangan hardcode config values  
❌ Jangan simpan images di src/  

---

## 🚀 WORKFLOW

### Adding New Page

```
1. Create: src/views/new-page.php
2. Add route to index.php:
   'newpage' => 'new-page.php'
3. Use in links:
   <a href="<?= route('newpage') ?>">Link</a>
4. Restart server if needed
```

### Adding New Component

```
1. Create: src/components/new-component.php
2. Use in views:
   <?php include 'src/components/new-component.php'; ?>
3. Add styles in: src/styles/components.css
```

### Adding New JS Module

```
1. Create: src/js/new-module.js
2. Include in view/layout:
   <script src="src/js/new-module.js"></script>
3. Use functions in other JS files
```

---

## 📊 SIZE & PERFORMANCE

### Current Structure
- **Views**: 7 files
- **Components**: 3+ reusable components
- **Styles**: ~4 CSS files + Tailwind
- **JS**: ~4 JavaScript modules
- **Images**: Organized in 5 folders
- **Total**: ~20+ organized files

### Best Practices
- ✓ CSS organized by type
- ✓ JS modular and separated
- ✓ Images categorized
- ✓ Config centralized
- ✓ Utils reusable
- ✓ Components DRY

---

## 🔍 QUICK REFERENCE

### Find Files
```bash
# View file
src/views/home.php

# Layout file
src/layouts/header.php

# Component file
src/components/card.php

# Style file
src/styles/main.css

# JS file
src/js/carousel.js

# Image file
img/slide_makanan/

# Config file
src/config/app.config.php
```

### Include Files
```php
// View
<?php include 'src/views/home.php'; ?>

// Component
<?php include 'src/components/card.php'; ?>

// Include script
<script src="src/js/app.js"></script>

// Include style
<link rel="stylesheet" href="src/styles/main.css">
```

---

## 📚 Related Documentation

- `ROUTING.md` - Router system documentation
- `SETUP_ROUTER.md` - Router setup guide
- `ROUTER_SUMMARY.md` - Quick router reference
- `FILES_TO_UPDATE.md` - Files update checklist
- Individual `README.md` in each folder

---

## 🎯 FOLDER GUIDELINES

### Views Folder
- ✓ Contains all page templates
- ✓ Auto-included with header & footer
- ✓ Accessible via router
- ✓ Use route() for links

### Layouts Folder
- ✓ Reusable page sections
- ✓ Header (navbar)
- ✓ Footer (info)
- ✓ Optional: sidebar, navigation

### Components Folder
- ✓ Small reusable UI pieces
- ✓ Card, button, modal, etc
- ✓ Should be generic & flexible
- ✓ Styled consistently

### Styles Folder
- ✓ Organized by type
- ✓ main.css - main page styles
- ✓ components.css - component styles
- ✓ layout.css - layout utilities
- ✓ utilities.css - helper classes

### JS Folder
- ✓ Organized by functionality
- ✓ app.js - main logic
- ✓ carousel.js - carousel
- ✓ form-handler.js - forms
- ✓ api-client.js - backend communication

---

## ✅ BEST PRACTICES

### ✓ DO

```php
// Use route() for links
<a href="<?= route('login') ?>">Login</a>

// Include components properly
<?php include 'src/components/card.php'; ?>

// Organize styles by type
// main.css, components.css, layout.css

// Modular JS
// app.js, carousel.js, form-handler.js

// Config centralized
// src/config/app.config.php
```

### ✗ DON'T

```php
// Don't hardcode links
<a href="login.php">Login</a>  // WRONG

// Don't duplicate components
// Create once, reuse many times

// Don't mix concerns
// Keep views, styles, JS separated

// Don't hardcode config
// Use src/config/ files

// Don't organize by feature (YET)
// Keep current structure until it grows
```

---

## 📈 FUTURE SCALING

When project grows:

```
src/
├── features/           ← Feature-based (if 50+ pages)
│   ├── auth/
│   │   ├── views/
│   │   ├── components/
│   │   └── js/
│   ├── dashboard/
│   │   ├── views/
│   │   ├── components/
│   │   └── js/
│   └── menu/
│
├── common/             ← Shared across features
│   ├── components/
│   ├── layouts/
│   └── styles/
```

But for now, stick with current structure! It's perfect.

---

**Version**: 1.0  
**Last Updated**: August 6, 2026  
**Status**: ✅ Ready to Use

Happy building! 🎉
