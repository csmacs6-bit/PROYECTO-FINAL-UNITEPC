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

  if (to.meta.guestOnly && session) {
    return '/dashboard'
  }

  return true
})

export default router
