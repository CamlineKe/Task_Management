import { createRouter, createWebHistory } from 'vue-router'
import Dashboard from '@/views/Dashboard.vue'
import Tasks from '@/views/Tasks.vue'
import Report from '@/views/Report.vue'

/**
 * Router Configuration
 * Defines all application routes with metadata
 */
const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'dashboard',
      component: Dashboard,
      meta: {
        title: 'Dashboard',
        icon: 'dashboard',
      },
    },
    {
      path: '/tasks',
      name: 'tasks',
      component: Tasks,
      meta: {
        title: 'Tasks',
        icon: 'tasks',
      },
    },
    {
      path: '/report',
      name: 'report',
      component: Report,
      meta: {
        title: 'Daily Report',
        icon: 'report',
      },
    },
  ],
})

// Update page title on route change
router.beforeEach((to) => {
  document.title = `${to.meta.title} - Task Management`
})

export default router