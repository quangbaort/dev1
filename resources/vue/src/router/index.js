import { canNavigate } from '@/plugins/acl/routeProtection'
import Vue from 'vue'
import VueRouter from 'vue-router'
import apps from './apps'
import dashboard from './dashboard'
import themeConfig from '../../themeConfig'
import store from '@/store'
import {computed} from "@vue/composition-api";

Vue.use(VueRouter)
const userMenu = localStorage.getItem('userMenu') !== null ?
  JSON.parse(localStorage.getItem('userMenu')) : []
const userAbility = localStorage.getItem('userAbility') !== null ?
  JSON.parse(localStorage.getItem('userAbility')) : []
const organization = localStorage.getItem('Organization') !== null ?
  JSON.parse(localStorage.getItem('Organization')) : null
const getUserMenu = () => {
  if(userMenu.length > 0) {
    let menu = userMenu.map((item) => {
      return item.function
    })
    const menuDashBoard = apps.filter(item =>{
      if(menu.includes(item.function)){
        return item
      }
    })
    return [...menuDashBoard]
  }
  return []
}
const routes = [
  // ? We are redirecting to different pages based on role.
  // NOTE: Role is just for UI purposes. ACL is based on abilities.
  {
    path: '/',
    redirect: () => {
      const userData = JSON.parse(localStorage.getItem('userData'))
      const statusOrganization = organization && organization.hasOwnProperty('name')
      && organization.hasOwnProperty('id') && organization.hasOwnProperty('calendar_enabled')
      && organization.hasOwnProperty('library_enabled') && organization.hasOwnProperty('news_enabled')
      && organization.hasOwnProperty('safety_check_enabled') && organization.hasOwnProperty('storage_avail')
      && organization.hasOwnProperty('storage_limit') && organization.hasOwnProperty('account_limit')
      ? true : false;
      if(getUserMenu().length <= 0) {
        store.dispatch('logout').then(() => {

        })
      }
      let calendar = false
      if(getUserMenu().length > 0){
        getUserMenu().forEach((item) => {
          if(item.name.includes('apps-calendar')){
            calendar = true

          }
        })
      }
      if (userData && getUserMenu().length > 0) return { name: calendar ? 'apps-calendar' : getUserMenu()[0].name }
    },
  },
  {
    path: '/error-404',
    name: 'error-404',
    component: () => import('@/views/Error404.vue'),
    meta: {
      layout: 'blank',
      resource: 'Public',
    },
  },
  {
    path: '/login',
    name: 'auth-login',
    component: () => import('@/views/Login.vue'),
    meta: {
      layout: 'blank',
      resource: 'Public',
      redirectIfLoggedIn: true,
      title :  trans('app.login'),
    },
  },
  {
    path: '/forgot-password',
    name: 'auth-forgot-password',
    component: () => import('@/views/ForgotPassword.vue'),
    meta: {
      layout: 'blank',
      resource: 'Public',
      redirectIfLoggedIn: true,
      title : trans('app.password_reset'),
    },
  },
  {
    path: '/reset-password/:token/:email',
    name: 'auth-reset-password',
    component: () => import('@/views/ResetPassword.vue'),
    meta: {
      layout: 'blank',
      resource: 'Public',
      redirectIfLoggedIn: true,
      title : trans('app.password_reset'),
    },
  },
  ...( localStorage.getItem('userMenu')  ? getUserMenu() : []),
  {
    path: '*',
    redirect: 'error-404',
  },
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes,
  scrollBehavior() {
    return { x: 0, y: 0 }
  },
})

// ? Router Before Each hook: https://router.vuejs.org/guide/advanced/navigation-guards.html
router.beforeEach((to, _, next) => {
  document.title = themeConfig.app.name + '-' + to.meta.title || themeConfig.app.name;
  const userData = localStorage.getItem('userData')

  const isLoggedIn = userData && localStorage.getItem('accessToken') !== null
    && localStorage.getItem('userAbility') !== null && localStorage.getItem('userMenu') !== null

  if (!canNavigate(to)) {
    // Redirect to login if not logged in
    if (!isLoggedIn) return next({ name: 'auth-login' })

  }

  // Redirect if logged in
  if (to.meta.redirectIfLoggedIn && isLoggedIn) {
    next('/')
  }

  return next()
})

export default router
