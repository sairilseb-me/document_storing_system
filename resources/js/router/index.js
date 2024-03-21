import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
   {
    path: '/',
    name: 'dashboard',
    component: () => import('@/pages/dashboard.vue'),
    meta: {
      layout: 'default'
    }
   },
   {
    path: '/login',
    name: 'login',
    component: () => import('@/pages/login.vue'),
    meta: {
      layout: 'blank'
    }
   },
   {
    path: '/roles',
    name: 'roles',
    component: () => import('@/views/pages/users/roles.vue'),
    meta: {
      layout: 'default'
    }
   },
   {
    path: '/users',
    name: 'users',
    component: () => import('@/views/pages/users/users.vue'),
    meta: {
      layout: 'default'
    }
   },
   {
    path: '/file-management',
    name: 'file-management',
    component: () => import('@/views/pages/file-management/file-management.vue'),
    meta: {
      layout: 'default'
    }
   }
  ],
  
})

export default router
