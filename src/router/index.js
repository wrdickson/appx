import { createRouter, createWebHashHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const router = createRouter({
  history: createWebHashHistory(import.meta.env.BASE_URL),
  routes: [
    //  HOME
    {
      path: '/',
      name: 'home',
      component: HomeView
    },
    //  RESERVATIONS
    {
      path: '/create-reservation',
      name: 'create-reservation',
      component: () => import('@/views/CreateReservation/CreateReservation.vue')
    },
    //  DEBUG
    {
      path: '/test',
      name: 'test',
      component: () => import('@/views/Debug/Test.vue')
    },
    {
      path: '/ref-reactive',
      name: 'refreactive',
      component: () => import('@/views/Debug/RefReactive.vue')
    },
    {
      path: '/props-play',
      name: 'props-play',
      component: () => import('@/views/Debug/PropsPlay.vue')
    }
  ]
})

export default router
