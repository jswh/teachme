import { RouteConfig } from 'vue-router'
const routes: RouteConfig[] = [
  {
    path: '/login',
    component: () => import('pages/Login.vue')
  },
  {
    path: '/register',
    component: () => import('pages/Register.vue')
  },
  {
    path: '/school/:school_id/invite/:token',
    component: () => import('pages/Invite.vue')
  },
  {
    path: '/',
    component: () => import('layouts/StudentLayout.vue'),
    children: [
      { path: '/home', component: () => import('pages/StudentHome.vue') },
    ]
  },
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: 'home', component: () => import('pages/Index.vue') },
      { path: 'school', component: () => import('pages/School.vue') },
      { path: 'school/:school_id/teacher', component: () => import('pages/Teacher.vue') },
      { path: 'school/:school_id/student', component: () => import('pages/Student.vue') },
    ]
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '*',
    component: () => import('pages/Error404.vue')
  }
]

export default routes
