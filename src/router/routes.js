export const routes = [
  //  AUTH
  {
    path: '/login',
    component: () => import('@/views/Login.vue') 
  },
  {
    path: '/logoff',
    component: () => import('@/views/Logoff.vue')
  },

  //  


]