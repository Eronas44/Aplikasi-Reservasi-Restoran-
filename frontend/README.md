# 🍽️ FRONTEND - APLIKASI RESERVASI RESTORAN KAFIBER

Frontend aplikasi reservasi restoran dengan sistem router terpusat dan struktur yang terorganisir dengan baik.

---

## 🚀 Quick Start

### Prerequisite
- Node.js & npm installed
- Docker (opsional)
- Web server (Apache/Nginx) atau gunakan Docker

### Installation

```bash
# 1. Install npm dependencies
npm install

# 2. Start development server
npm run dev

# OR dengan Docker
docker-compose up -d

# 3. Akses aplikasi
http://localhost:8000/index.php
```

---

## 📖 Dokumentasi

### 📚 Router System (PENTING!)
- **[ROUTING.md](./ROUTING.md)** - Dokumentasi lengkap sistem routing
- **[SETUP_ROUTER.md](./SETUP_ROUTER.md)** - Panduan setup & implementasi
- **[ROUTER_SUMMARY.md](./ROUTER_SUMMARY.md)** - Quick reference routing

### 📁 Struktur Folder
- **[FRONTEND_STRUCTURE.md](./FRONTEND_STRUCTURE.md)** - Dokumentasi struktur lengkap
- **[FILES_TO_UPDATE.md](./FILES_TO_UPDATE.md)** - Checklist files yang perlu diupdate

### Individual Folder Documentation
- `src/views/README.md` - View files
- `src/layouts/README.md` - Layout templates
- `src/components/README.md` - Reusable components
- `src/styles/README.md` - CSS organization
- `src/js/README.md` - JavaScript modules
- `src/config/README.md` - Configuration
- `src/utils/README.md` - Utility functions
- `src/assets/README.md` - Static assets
- `img/README.md` - Images

---

## 📁 Struktur Project

```
frontend/
├── 📄 index.php                    ← ROUTER UTAMA (Entry Point)
├── 📄 package.json                 ← Dependencies
├── 📄 style.css                    ← Global styles
├── 📄 README.md                    ← File ini
│
├── 📚 DOKUMENTASI
│   ├── ROUTING.md
│   ├── SETUP_ROUTER.md
│   ├── ROUTER_SUMMARY.md
│   ├── FRONTEND_STRUCTURE.md
│   └── FILES_TO_UPDATE.md
│
├── 📂 src/
│   ├── 📂 views/                   ← Halaman aplikasi
│   │   ├── home.php
│   │   ├── login.php
│   │   ├── register.php
│   │   ├── dashboard_user.php
│   │   ├── galeri.php
│   │   ├── menu.php
│   │   ├── story.php
│   │   └── README.md
│   │
│   ├── 📂 layouts/                 ← Header & Footer
│   │   ├── header.php
│   │   ├── footer.php
│   │   └── README.md
│   │
│   ├── 📂 components/              ← Komponen reusable
│   │   ├── card.php
│   │   ├── button.php
│   │   ├── modal.php
│   │   └── README.md
│   │
│   ├── 📂 styles/                  ← CSS styles
│   │   ├── main.css
│   │   ├── components.css
│   │   ├── layout.css
│   │   ├── utilities.css
│   │   └── README.md
│   │
│   ├── 📂 js/                      ← JavaScript
│   │   ├── app.js
│   │   ├── carousel.js
│   │   ├── form-handler.js
│   │   ├── api-client.js
│   │   └── README.md
│   │
│   ├── 📂 assets/                  ← Static files
│   │   ├── icons/
│   │   ├── fonts/
│   │   └── README.md
│   │
│   ├── 📂 config/                  ← Konfigurasi
│   │   ├── app.config.php
│   │   ├── api.config.php
│   │   └── README.md
│   │
│   └── 📂 utils/                   ← Helper functions
│       ├── helper.php
│       ├── validator.php
│       ├── formatter.php
│       └── README.md
│
├── 📂 public/                      ← Generated files
│   ├── css/
│   ├── js/
│   └── images/
│
├── 📂 img/                         ← Image assets
│   ├── slide_makanan/
│   ├── galeri_suasana/
│   ├── ruangan_utama/
│   ├── area_privat/
│   ├── suasana_malam/
│   └── README.md
│
├── 📂 docker/                      ← Docker config
│   ├── nginx.conf
│   └── php.ini
│
├── 📄 Dockerfile                   ← Docker image
├── 📄 docker-compose.yml           ← Docker compose
└── 📄 .gitignore
```

Lihat **[FRONTEND_STRUCTURE.md](./FRONTEND_STRUCTURE.md)** untuk detail lengkap.

---

## 🛣️ Available Routes

| Route | URL | File |
|-------|-----|------|
| Home | `index.php?page=home` | `src/views/home.php` |
| Login | `index.php?page=login` | `src/views/login.php` |
| Register | `index.php?page=register` | `src/views/register.php` |
| Dashboard | `index.php?page=dashboard` | `src/views/dashboard_user.php` |
| Gallery | `index.php?page=galeri` | `src/views/galeri.php` |
| Menu | `index.php?page=menu` | `src/views/menu.php` |
| Story | `index.php?page=story` | `src/views/story.php` |

Lihat **[ROUTING.md](./ROUTING.md)** untuk detail lengkap.

---

## 🔗 Cara Membuat Link

```php
<?php
// Gunakan helper function route()
<a href="<?= route('login') ?>">Login</a>
<a href="<?= route('dashboard') ?>">Dashboard</a>
<a href="<?= route('home') ?>">Beranda</a>

// Generated URL:
// http://localhost:8000/index.php?page=login
// http://localhost:8000/index.php?page=dashboard
// http://localhost:8000/index.php?page=home
?>
```

---

## ✨ Fitur Utama

### ✅ Router Terpusat
- Single entry point (`index.php`)
- Semua request divalidasi
- Security: Input sanitization
- 404 error handling

### ✅ Terstruktur dengan Baik
- Views terorganisir (`src/views/`)
- Layouts reusable (`src/layouts/`)
- Components modular (`src/components/`)
- Styles terpisah (`src/styles/`)
- JavaScript modular (`src/js/`)

### ✅ Keamanan
- Input sanitization
- Route validation
- File existence check
- Protection dari directory traversal

### ✅ Easy to Maintain
- One place to manage routes (index.php)
- Consistent link format
- Helper functions
- Comprehensive documentation
- Organized folder structure

### ✅ Developer Friendly
- Clear documentation
- File update checklist
- Examples & templates
- Quick reference guides

---

## 🐳 Docker

### Start dengan Docker Compose
```bash
# Build & Start
docker-compose up -d

# Access
http://localhost:8000

# View Logs
docker-compose logs -f

# Stop
docker-compose down
```

### Manual Docker
```bash
# Build image
docker build -t kafiber-frontend .

# Run container
docker run -d -p 8000:80 kafiber-frontend

# Stop container
docker stop <container_id>
```

---

## 📦 Dependencies

### NPM Packages
```json
{
  "tailwindcss": "latest",
  "postcss": "latest",
  "autoprefixer": "latest"
}
```

Install dengan: `npm install`

### PHP Requirements
- PHP 7.4 or higher
- Web server (Apache/Nginx)
- Session support enabled

### Browser Support
- Chrome/Edge 90+
- Firefox 88+
- Safari 14+
- Mobile browsers

---

## 🛠️ Development

### Available Scripts

```bash
# Install dependencies
npm install

# Start dev server with file watching
npm run dev

# Build CSS
npm run build

# Watch for changes
npm run watch

# Production build
npm run prod
```

### File Organization

**Don't:**
- ❌ Mix PHP & JavaScript in same file
- ❌ Hardcode links (use route())
- ❌ Store CSS inline
- ❌ Duplicate components
- ❌ Hardcode config values

**Do:**
- ✅ Keep views in `src/views/`
- ✅ Keep layouts in `src/layouts/`
- ✅ Keep components in `src/components/`
- ✅ Keep styles in `src/styles/`
- ✅ Keep JS in `src/js/`
- ✅ Use helper functions
- ✅ Use configuration files

---

## 🧪 Testing Checklist

### Manual Testing
- [ ] Home page loads correctly
- [ ] Login page accessible
- [ ] Register page works
- [ ] Dashboard displays (when logged in)
- [ ] Gallery page works
- [ ] Menu page displays
- [ ] Story page accessible
- [ ] All navigation links work
- [ ] All buttons navigate correctly
- [ ] Forms submit properly
- [ ] 404 page on invalid route
- [ ] No console errors
- [ ] Responsive on mobile

### Browser Console
```javascript
// Check for errors
console.log('No errors should appear');

// Check Network tab
// All resources should load (green status)

// Check Performance
// Page should load in < 2 seconds
```

---

## 🚨 Troubleshooting

### Issue: Links Not Working
**Solution**: 
- Use `<?= route('page') ?>` in all links
- Check route exists in `index.php` $routes array
- Verify page file exists in `src/views/`

### Issue: Page Returns 404
**Solution**:
- Check URL query string: `?page=xxx`
- Verify route is registered in index.php
- Check file exists in src/views/

### Issue: CSS Not Loading
**Solution**:
- Verify path to style.css
- Check if Tailwind CSS is built (`npm run build`)
- Hard refresh browser (Ctrl+Shift+R)

### Issue: Images Not Displaying
**Solution**:
- Check image path is relative to frontend folder
- Verify image exists in `img/` folder
- Check image file extension

### Issue: PHP Errors
**Solution**:
- Check PHP error log
- Verify PHP version >= 7.4
- Enable error reporting in index.php

---

## 📝 Next Steps

### 1. Update Remaining Files
Follow **[FILES_TO_UPDATE.md](./FILES_TO_UPDATE.md)** to update:
- login.php
- register.php
- dashboard_user.php
- galeri.php
- menu.php
- story.php

Replace all `href="xxx.php"` with `href="<?= route('xxx') ?>"`

### 2. Test All Routes
```
□ http://localhost:8000/index.php
□ http://localhost:8000/index.php?page=home
□ http://localhost:8000/index.php?page=login
□ http://localhost:8000/index.php?page=register
□ http://localhost:8000/index.php?page=dashboard
□ http://localhost:8000/index.php?page=galeri
□ http://localhost:8000/index.php?page=menu
□ http://localhost:8000/index.php?page=story
```

### 3. Add New Pages
When adding new pages:
1. Create file in `src/views/`
2. Add route in `index.php`
3. Use `route()` in links
4. Test page loads

### 4. Connect Backend
- Check `src/config/api.config.php`
- Update API endpoints
- Test API calls in `src/js/api-client.js`

---

## 💡 Pro Tips

### 1. Use DevTools
```javascript
// Check current page
const page = new URLSearchParams(window.location.search).get('page');
console.log('Current page:', page);
```

### 2. Add Active State to Links
```php
<?php if (isset($_GET['page']) && $_GET['page'] === 'home'): ?>
    <a href="<?= route('home') ?>" class="active">Home</a>
<?php endif; ?>
```

### 3. Reuse Components
```php
<?php
// Include component multiple times with different data
foreach ($items as $item) {
    include 'src/components/card.php';
}
?>
```

### 4. Debug Routes
```php
<?php
// In index.php, temporarily add:
echo '<pre>';
var_dump($_GET, $routes);
echo '</pre>';
// Remove after debugging
?>
```

---

## 📚 Additional Resources

- [Tailwind CSS Docs](https://tailwindcss.com/docs)
- [PHP Documentation](https://www.php.net/docs.php)
- [MDN Web Docs](https://developer.mozilla.org)
- [Bootstrap Docs](https://getbootstrap.com/docs)

---

## 📞 Support & Help

**Having Issues?**

1. **Read Documentation**
   - [ROUTING.md](./ROUTING.md) - Router system
   - [FRONTEND_STRUCTURE.md](./FRONTEND_STRUCTURE.md) - Folder structure
   - [FILES_TO_UPDATE.md](./FILES_TO_UPDATE.md) - Update checklist

2. **Check Error Messages**
   - Browser console (F12)
   - PHP error logs
   - Network tab for 404 errors

3. **Verify Configuration**
   - index.php routes
   - View file paths
   - CSS/JS paths

4. **Check Examples**
   - Review ROUTING.md for link examples
   - Review FRONTEND_STRUCTURE.md for organization
   - Review FILES_TO_UPDATE.md for update patterns

---

## ✅ Checklist

Before deploying:

- [ ] Read all documentation files
- [ ] Verify folder structure is correct
- [ ] Update all view files
- [ ] Test all routes work
- [ ] Test all links work
- [ ] Check no console errors
- [ ] Verify images load
- [ ] Test responsive design
- [ ] Connect backend API
- [ ] Test forms submission

---

## 🎉 You're All Set!

Frontend aplikasi reservasi restoran sudah siap digunakan dengan:
- ✅ Router system yang aman & terorganisir
- ✅ Struktur folder yang rapi
- ✅ Dokumentasi lengkap
- ✅ Helper functions
- ✅ Security features
- ✅ Docker support

Happy coding! 🚀

---

## 📝 Version Info

**Version**: 1.0.0  
**Last Updated**: August 6, 2026  
**Status**: ✅ Production Ready  
**Framework**: PHP + Tailwind CSS  
**Router**: Centralized Router System

---

## 📄 License

Bagian dari Aplikasi Reservasi Restoran Kafiber

---

**Questions?** Check the documentation files in the root folder! 📖
