// ============================================
// FRONTEND ROUTE MANIFEST - APLIKASI RESERVASI RESTORAN
// ============================================
// Single source of truth untuk semua route frontend.
// Nama route di sini harus konsisten dengan router PHP (index.php)
// dan helper route() di frontend.
// ============================================

export const frontendRoutes = [
  // ---------- Guest only (tidak bisa diakses saat sudah login) ----------
  { path: '/login', name: 'login', guestOnly: true, roles: [] },
  { path: '/register', name: 'register', guestOnly: true, roles: [] },

  // ---------- Publik ----------
  { path: '/', name: 'home', roles: [] },
  { path: '/menu', name: 'menu', requiresAuth: true, roles: ['customer', 'staff', 'admin'] },
  { path: '/galeri', name: 'galeri', requiresAuth: true, roles: ['customer', 'staff', 'admin'] },
  { path: '/story', name: 'story', roles: [] },

  // ---------- Wajib login ----------
  { path: '/dashboard', name: 'dashboard', requiresAuth: true, roles: ['customer', 'staff', 'admin'] },
  { path: '/reservations', name: 'reservations', requiresAuth: true, roles: ['customer', 'staff', 'admin'] },
  { path: '/reservation-form', name: 'reservation-form', requiresAuth: true, roles: ['customer', 'staff', 'admin'] },
  { path: '/logout', name: 'logout', requiresAuth: true, roles: ['customer', 'staff', 'admin'] },

  // ---------- Staff / Admin ----------
  { path: '/staff/check-in', name: 'staff.checkIn', requiresAuth: true, roles: ['staff', 'admin'] },
  { path: '/staff/reservation-monitor', name: 'staff.reservationMonitor', requiresAuth: true, roles: ['staff', 'admin'] },

  // ---------- Admin ----------
  { path: '/admin/users', name: 'admin.users', requiresAuth: true, roles: ['admin'] },
  { path: '/admin/tables', name: 'admin.tables', requiresAuth: true, roles: ['admin'] },
  { path: '/admin/menu-management', name: 'admin.menuManagement', requiresAuth: true, roles: ['admin'] },
  { path: '/admin/reports', name: 'admin.reports', requiresAuth: true, roles: ['admin'] },
];

export function getRoute(name) {
  return frontendRoutes.find((route) => route.name === name) || null;
}

export function canAccessRoute(route, userRole, isAuthenticated) {
  if (!route) {
    return false;
  }

  if (route.guestOnly) {
    return !isAuthenticated;
  }

  if (route.requiresAuth && !isAuthenticated) {
    return false;
  }

  if (!route.roles || route.roles.length === 0) {
    return true;
  }

  return route.roles.includes(userRole);
}

// Bangun URL frontend untuk route name, contoh:
//   frontendUrl('dashboard')            -> 'index.php?page=dashboard'
//   frontendUrl('reservation-form', ...) -> 'index.php?page=reservation-form&...'
export function frontendUrl(name, params = {}) {
  const query = new URLSearchParams({ page: name, ...params });
  return 'index.php?' + query.toString();
}
