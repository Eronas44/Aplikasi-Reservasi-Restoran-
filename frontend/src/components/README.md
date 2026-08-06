# Components

Folder ini berisi reusable UI components yang dapat digunakan di berbagai halaman.

## Struktur Folder

```
components/
├── Button/
│   ├── Button.php
│   └── Button.css
├── Card/
│   ├── Card.php
│   └── Card.css
├── Modal/
│   ├── Modal.php
│   └── Modal.css
├── Input/
│   ├── InputField.php
│   └── InputField.css
├── Navbar/
│   ├── Navbar.php
│   └── Navbar.css
└── ...
```

## Penamaan Convention

- Gunakan PascalCase untuk nama file component
- Satu component = satu folder dengan file PHP dan CSS terpisah
- Contoh: `Button/Button.php`, `Card/Card.php`

## Cara Penggunaan

```php
<?php include '../components/Button/Button.php'; ?>

<?php Component::Button(['text' => 'Click Me', 'class' => 'btn-primary']); ?>
```
