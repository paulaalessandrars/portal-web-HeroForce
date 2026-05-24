import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [
  { path: '/', redirect: '/dashboard' },
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/views/LoginView.vue'),
    meta: { guest: true }
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('@/views/RegisterView.vue'),
    meta: { guest: true }
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: () => import('@/views/DashboardView.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/projects/new',
    name: 'ProjectCreate',
    component: () => import('@/views/ProjectFormView.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/projects/:id/edit',
    name: 'ProjectEdit',
    component: () => import('@/views/ProjectFormView.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const auth = useAuthStore()

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return next('/login')
  }
  if (to.meta.guest && auth.isAuthenticated) {
    return next('/dashboard')
  }
  if (to.meta.requiresAdmin && !auth.isAdmin) {
    return next('/dashboard')
  }
  next()
})

export default router
