# Config

Folder ini berisi file konfigurasi aplikasi.

## File Structure

```
config/
├── routes.js             # Definisi routes aplikasi
├── env.js                # Konfigurasi environment
├── api.js                # API endpoints
└── constants.js          # Konstanta aplikasi
```

## routes.js

Definisikan semua routes aplikasi di sini:

```javascript
const Routes = {
    HOME: '/',
    LOGIN: '/login',
    REGISTER: '/register',
    DASHBOARD: '/dashboard',
    GALLERY: '/gallery',
    PROFILE: '/profile',
    ADMIN: '/admin'
};

export default Routes;
```

## api.js

Definisikan semua API endpoints:

```javascript
const API = {
    BASE_URL: process.env.API_BASE_URL || 'http://localhost:8000/api',
    ENDPOINTS: {
        AUTH: {
            LOGIN: '/auth/login',
            REGISTER: '/auth/register',
            LOGOUT: '/auth/logout'
        },
        USER: {
            PROFILE: '/user/profile',
            UPDATE: '/user/update'
        },
        RESERVATION: {
            LIST: '/reservations',
            CREATE: '/reservations',
            UPDATE: '/reservations/:id',
            DELETE: '/reservations/:id'
        }
    }
};

export default API;
```
