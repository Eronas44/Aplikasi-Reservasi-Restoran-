# Assets

Folder ini berisi static assets seperti images, fonts, dan icons.

## File Structure

```
assets/
├── images/               # Gambar (logo, icons, dll)
│   ├── logo.png
│   ├── placeholder.jpg
│   └── icons/
├── fonts/                # Custom fonts
│   └── ...
├── videos/               # Video files (opsional)
└── sounds/               # Audio files (opsional)
```

## Penamaan Convention

- Gunakan kebab-case untuk nama file: `user-avatar.png`, `restaurant-logo.svg`
- Kelompokkan by type (images, fonts, dll)
- Gunakan format yang optimal (WebP untuk images modern)

## Optimization

- Compress images sebelum commit
- Gunakan SVG untuk icons dan logos
- Lazy load images di runtime jika perlu
