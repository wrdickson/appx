import { defineStore } from 'pinia'
import es from 'element-plus/dist/locale/es'
import en from 'element-plus/dist/locale/en'

export const localeStore = defineStore({
  id: 'localeStore',
  state: () => ({
    /*
    **  <el-config-provider> components that wrap element components
    **  will bind to this value and change locale on the fly
    **  if the value is changed . . . NICE!
    */
    //  note en is an object, not the selected locale string
    selectedLocale: en,
    someVar: {
      foo: 'bazz'
    }
   
}),
  actions: {
    setComponentLocale (localeCode) {
      switch(localeCode) {
        case 'en': 
          this.selectedLocale = en
          break;
        case 'es':
          this.selectedLocale = es
          break;
        default:
          this.selectedLocale = en
      }
    }
  }

})