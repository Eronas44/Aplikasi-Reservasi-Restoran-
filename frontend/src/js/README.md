# JavaScript

Folder ini berisi semua file JavaScript untuk interaktivitas.

## File Structure

```
js/
├── app.js                # Entry point utama
├── auth.js               # Logika authentikasi
├── reservations.js       # Logika reservasi
├── dashboard.js          # Logika dashboard
└── ui.js                 # UI interactions (modals, menus, dll)
```

## Penamaan Convention

- Gunakan camelCase untuk nama file
- Satu file = satu module/feature
- Group related functions dalam object/class

## Contoh Structure

```javascript
// auth.js
const Auth = {
    login: async (email, password) => {
        // Login logic
    },
    register: async (data) => {
        // Register logic
    },
    logout: () => {
        // Logout logic
    }
};

export default Auth;
```

## Importing di HTML

```html
<script type="module">
    import Auth from './js/auth.js';
    import Reservations from './js/reservations.js';
</script>
```
