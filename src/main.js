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

//  FONT AWESOME ICONS
/* import fontawesome core */
import { library } from '@fortawesome/fontawesome-svg-core'
/* import font awesome icon component */
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
/* treeshake specific icons */
import {
  faArrowDown,
  faBars,
  faCircleArrowRight,
  faChevronRight,
  faChevronDown,
  faCircleArrowDown,
  faWindowClose,
  faGauge,
  faCalendarDays,
  faHouse,
  faArrowRightToBracket,
  faArrowRight,
  faRightFromBracket,
  faCashRegister,
  faFireFlameCurved
} from '@fortawesome/free-solid-svg-icons'

/* add icons to the library */
library.add(
  faArrowDown,
  faBars,
  faChevronRight,
  faChevronDown,
  faCircleArrowRight,
  faCircleArrowDown,
  faWindowClose,
  faGauge,
  faCalendarDays,
  faHouse,
  faArrowRightToBracket,
  faArrowRight,
  faRightFromBracket,
  faCashRegister,
  faFireFlameCurved
)



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
  app.component('font-awesome-icon', FontAwesomeIcon)
  app.use(useI18n)
  app.use(createPinia())
  app.use(router)
  app.use(ElementPlus)
  app.mount('#app')
})


