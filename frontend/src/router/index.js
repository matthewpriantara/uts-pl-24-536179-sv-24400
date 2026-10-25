import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../views/LoginView.vue'
import InventoryView from '../views/InventoryView.vue'

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: LoginView,
    meta: { guestOnly: true },
  },
  {
    path: '/',
    name: 'Inventory',
    component: InventoryView,
    meta: { requiresAuth: true },
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')

  if (to.meta.requiresAuth && !token) {
    next({ name: 'Login' })
  } else if (to.meta.guestOnly && token) {
    next({ name: 'Inventory' })
  } else {
    next()
  }
})

export default router
