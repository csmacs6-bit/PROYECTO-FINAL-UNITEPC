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
      {
        path: 'dashboard',
        name: 'dashboard',
        component: () => import('pages/DashboardPage.vue'),
        meta: { requiresAuth: true },
      },
    ],
  },
  {
    path: '/:catchAll(.*)*',
    redirect: '/login',
  },
]

export default routes
