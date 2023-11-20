import { defineStore } from 'pinia'

export const rootSpacesStore = defineStore({
  id: 'rootSpacesStore',
  state: () => ({
    rootSpaces: null
  }),
  actions: {
    setRootSpaces ( rootSpaces ) {
      this.rootSpaces = rootSpaces
    }
  }

})