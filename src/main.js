import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { createI18n } from 'vue-i18n'

import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'
import 'element-plus/theme-chalk/dark/css-vars.css'
import 'element-plus/theme-chalk/display.css'
import _ from 'lodash'
import { messages } from '@/i18n/messages.js'

import App from '@/App.vue'

//  router
import router from '@/router'
import { routes } from '@/router/routes.js'
_.each(routes, route => { router.addRoute(route) })

//  MODULR LOADER: OPTIONS
import { optionsData } from '@/data/optionsData.js'

//  GET AND HANDLE OPTIONS DATA
optionsData.getAutoloadOptions().then( response => {
  //  create an object to pass to App as props
  const optionsObj = {
    autoloadOptions:  response.data.options
  }
  //  find the default_locale option
  const optionLocale = _.find(response.data.options, o => {
    return o.option_name == 'default_locale'
  })
  const defaultLocale = optionLocale.option_value

  //  then, assign default_locale as we run createI81n . . .
  const useI18n = createI18n({
    legacy: false, // you must set `false`, to use Composition API
    locale: defaultLocale, // set locale to option value
    fallbackLocale: 'en', // set fallback locale
    messages // set locale messages
  })
  
  //  INSTANTIATE APP AND PASS OPTIONS PROPS
  const app = createApp(App, optionsObj)
  app.use(useI18n)
  app.use(createPinia())
  app.use(router)
  app.use(ElementPlus)
  app.mount('#app')
})


