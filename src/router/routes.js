const routes = [
  {
    path: '/',
    redirect: '/login',
  },
  {
    path: '/',
    component: () => import('layouts/AuthLayout.vue'),
    children: [
      {
        path: 'login',
        name: 'login',
        component: () => import('pages/LoginPage.vue'),
        meta: { guestOnly: true },
      },
      {
        path: 'register',
        name: 'register',
        component: () => import('pages/RegisterPage.vue'),
        meta: { guestOnly: true },
      },
    ],
  },
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      {
        path: 'dashboard',
        name: 'dashboard',
        component: () => import('pages/DashboardPage.vue'),
        meta: { requiresAuth: true },
      },
      {
        path: 'chofer/dashboard',
        name: 'driver-dashboard',
        component: () => import('pages/DriverDashboardPage.vue'),
        meta: { requiresAuth: true },
      },
      {
        path: 'viajes',
        name: 'viajes',
        component: () => import('pages/ViajesPage.vue'),
        meta: { requiresAuth: true },
      },
      {
        path: 'camiones',
        name: 'camiones',
        component: () => import('pages/CamionesPage.vue'),
        meta: { requiresAuth: true },
      },
      {
        path: 'conductores',
        name: 'conductores',
        component: () => import('pages/ConductoresPage.vue'),
        meta: { requiresAuth: true },
      },
      {
        path: 'clientes',
        name: 'clientes',
        component: () => import('pages/ClientesPage.vue'),
        meta: { requiresAuth: true },
      },
      {
        path: 'cargas',
        name: 'cargas',
        component: () => import('pages/CargasPage.vue'),
        meta: { requiresAuth: true },
      },
      {
        path: 'combustible',
        name: 'combustible',
        component: () => import('pages/CombustiblePage.vue'),
        meta: { requiresAuth: true },
      },
      {
        path: 'mantenimiento',
        name: 'mantenimiento',
        component: () => import('pages/MantenimientoPage.vue'),
        meta: { requiresAuth: true },
      },
      {
        path: 'gps',
        name: 'gps',
        component: () => import('pages/GpsPage.vue'),
        meta: { requiresAuth: true },
      },
      {
        path: 'reportes',
        name: 'reportes',
        component: () => import('pages/ReportesPage.vue'),
        meta: { requiresAuth: true },
      },
      {
        path: 'usuarios',
        name: 'usuarios',
        component: () => import('pages/UsuariosPage.vue'),
        meta: { requiresAuth: true, adminOnly: true },
      },
    ],
  },
  {
    path: '/:catchAll(.*)*',
    redirect: '/login',
  },
]

export default routes
