// Frontend route manifest only (no UI/view layer).
export const frontendRoutes = [
  { path: '/login', name: 'login', guestOnly: true, roles: [] },
  { path: '/register', name: 'register', guestOnly: true, roles: [] },

  { path: '/dashboard', name: 'dashboard', requiresAuth: true, roles: ['customer', 'staff', 'admin'] },
  { path: '/reservations', name: 'reservations', requiresAuth: true, roles: ['customer', 'staff', 'admin'] },
  { path: '/menu', name: 'menu', requiresAuth: true, roles: ['customer', 'staff', 'admin'] },

  { path: '/staff/check-in', name: 'staff.checkIn', requiresAuth: true, roles: ['staff', 'admin'] },
  { path: '/staff/reservation-monitor', name: 'staff.reservationMonitor', requiresAuth: true, roles: ['staff', 'admin'] },

  { path: '/admin/users', name: 'admin.users', requiresAuth: true, roles: ['admin'] },
  { path: '/admin/tables', name: 'admin.tables', requiresAuth: true, roles: ['admin'] },
  { path: '/admin/menu-management', name: 'admin.menuManagement', requiresAuth: true, roles: ['admin'] },
  { path: '/admin/reports', name: 'admin.reports', requiresAuth: true, roles: ['admin'] },
];

export function canAccessRoute(route, userRole, isAuthenticated) {
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
