import { defineStore } from 'pinia'

export const spaceTypeStore = defineStore({
  id: 'spaceTypesStore',
  state: () => ({
    spaceTypes: null
  }),
  actions: {
    setSpaceTypes ( spaceTypes ) {
      this.spaceTypes = spaceTypes
    }
  }

})
