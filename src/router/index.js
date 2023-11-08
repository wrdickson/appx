import { createRouter, createWebHashHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const router = createRouter({
  history: createWebHashHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView
    },
    {
      path: '/test',
      name: 'test',
      component: () => import('/src/views/Test.vue')
    },
    {
      path: '/ref-reactive',
      name: 'refreactive',
      component: () => import('/src/views/RefReactive.vue')
    },
    {
      path: '/props-play',
      name: 'props-play',
      component: () => import('@/views/PropsPlay.vue')
    }
  ]
})

export default router
