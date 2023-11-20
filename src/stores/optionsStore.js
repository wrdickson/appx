import { defineStore } from 'pinia'
import _ from 'lodash'
export const optionsStore = defineStore({
  id: 'optionsStore',
  state: () => ({
    autoloadOptions : {}
  }),
  actions: {
    setAutoloadOptions ( obj ) {
      _.each(obj, option => {
        this.autoloadOptions[option.option_name] = option.option_value 
      })
    }
  }
})