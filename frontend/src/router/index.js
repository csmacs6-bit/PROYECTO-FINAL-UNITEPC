import { createRouter, createWebHistory } from 'vue-router'
import routes from './routes'
import { getSession } from 'src/services/auth'

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to) => {
  const session = getSession()

  if (to.meta.requiresAuth && !session) {
    return '/login'
  }

  if (to.path === '/dashboard' && session?.role === 'Chofer') {
    return '/chofer/dashboard'
  }

  if (
    session?.role === 'Chofer' &&
    to.meta.requiresAuth &&
    to.path !== '/chofer/dashboard'
  ) {
    return '/chofer/dashboard'
  }

  if (to.meta.adminOnly && session?.role !== 'Administrador') {
    return '/dashboard'
  }

  if (to.meta.guestOnly && session) {
    return '/dashboard'
  }

  return true
})

export default router
