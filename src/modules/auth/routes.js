export const authRoutes = 
  [
    {
      path: '/login',
      component: () => import('./Login.vue') 
    },
    {
      path: '/logoff',
      component: () => import('./Logoff.vue')
    }
  ]