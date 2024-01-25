export const routes = [
  {
    path: '/folio/:id',
    component: () => import ('@/views/Folio/Folio.vue')
  },
  {
    path: '/login',
    component: () => import('@/views/Login.vue') 
  },
  {
    path: '/logoff',
    component: () => import('@/views/Logoff.vue')
  },
  {
    path: '/workbench',
    component: () => import('@/views/workbench/workbench.vue')
  },
  {
    path: '/dashboard',
    component: () => import('@/views/Dashboard/Dashboard.vue')
  },
  {
    path: '/pos',
    component: () => import('@/views/Pos/PosView.vue')
  }

]