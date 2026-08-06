# Public

Folder ini berisi static files yang di-serve langsung oleh server (assets yang tidak perlu di-process).

## File Structure

```
public/
├── images/               # Static images (moved from /img)
│   ├── logo.png
│   ├── favicon.ico
│   └── ...
├── favicon.ico           # Website favicon
└── robots.txt            # SEO robots file
```

## Static Images

Semua image yang digunakan di views harus di-serve dari folder ini.

## Path Reference dalam HTML

```html
<!-- Referensi dari views -->
<img src="<?php echo $basePath; ?>/public/images/logo.png" alt="Logo">

<!-- Atau dari root -->
<img src="/public/images/logo.png" alt="Logo">
```
