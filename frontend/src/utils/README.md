# Utils

Folder ini berisi utility functions dan helper functions yang dapat digunakan di mana saja.

## File Structure

```
utils/
├── ApiClient.js          # Fungsi untuk API calls
├── validators.js         # Fungsi validasi form
├── formatters.js         # Format data (dates, currency, dll)
├── helpers.php           # PHP helper functions
└── constants.js          # Constants dan enums
```

## Contoh Penggunaan

### ApiClient.js
```javascript
const ApiClient = {
    async get(url) {
        const response = await fetch(url);
        return response.json();
    },
    async post(url, data) {
        const response = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        return response.json();
    }
};
```

### Validators.js
```javascript
const Validators = {
    isEmail: (email) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email),
    isPhone: (phone) => /^[0-9]{10,}$/.test(phone),
    isEmpty: (value) => !value || value.trim() === ''
};
```

### Formatters.js
```javascript
const Formatters = {
    formatDate: (date) => new Date(date).toLocaleDateString('id-ID'),
    formatCurrency: (amount) => new Intl.NumberFormat('id-ID', { 
        style: 'currency', 
        currency: 'IDR' 
    }).format(amount)
};
```
