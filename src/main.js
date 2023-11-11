import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { createI18n } from 'vue-i18n'

import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'
import 'element-plus/theme-chalk/dark/css-vars.css'
import 'element-plus/theme-chalk/display.css'
import _ from 'lodash'
import { messages } from '@/modules/i18n/messages.js'

//  router
import router from './router'

//  module loader
//  auth . . .
import { authRoutes } from '@/modules/auth/routes.js'
_.each(authRoutes, route => { router.addRoute(route) })

const useI18n = createI18n({
  legacy: false, // you must set `false`, to use Composition API
  locale: 'en', // set locale
  fallbackLocale: 'en', // set fallback locale
  messages // set locale messages
})

//  app
import App from '@/App.vue'
const app = createApp(App)
app.use(useI18n)
app.use(createPinia())
app.use(router)
app.use(ElementPlus)
app.mount('#app')
