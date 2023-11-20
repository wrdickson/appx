import { defineStore } from 'pinia'
import dayjs from 'dayjs'
import _ from 'lodash'

export const resViewStore = defineStore({
  id: 'resViewStore',
  state: () => ({
    startDate: dayjs().subtract( 1, 'days').format('YYYY-MM-DD'),
    showHideRootSpaceCopy: null,
    scrollLeft: 0,
    scrollTop: 0
  }),
  actions: {
    setShowHideRootSpaceCopy ( rsCopy ) {
      console.log('storing rscopy')
      this.showHideRootSpaceCopy = rsCopy
    },
    setStartDate ( startDate ) {
      this.startDate = startDate
    }
  }
})