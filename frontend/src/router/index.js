import { createRouter, createWebHistory } from 'vue-router'
import { authStore } from '@/stores/auth'


const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    { path: '/:pathMatch(.*)*', redirect: '/login', },
    { path: '/login', name: 'login', component: () => import('../views/Authentication/Login.vue') },
    { path: '/forgot', name: 'forgot', component: () => import('../views/Authentication/Forgot.vue') },
    { path: '/reset/:token', name: 'reset', component: () => import('@/views/Authentication/Reset.vue') },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: () => import('../views/Dashboard/index.vue'),
      meta: { title: 'Dashboard' }
    },
    {
      path: '/settings',
      name: 'settings',
      component: () => import('../views/Settings/index.vue'),
      meta: { title: 'Settings' }
    },
    {
      path: '/roles',
      name: 'roles',
      component: () => import('../views/Roles/index.vue'),
      meta: { title: 'Roles' }
    },
    {
      path: '/users',
      name: 'users',
      component: () => import('../views/Users/index.vue'),
      meta: { title: 'Usuarios' }
    },
  ],
})

const protected_routes = [
  'dashboard',
  'profile',
  'settings',
]

router.beforeEach((to, from, next) => {
  const auth_store = authStore()
  if (to.name !== 'login' && !auth_store.authenticated && protected_routes.includes(to.name)) next({name: 'login'})
  else next()
})

export default router
