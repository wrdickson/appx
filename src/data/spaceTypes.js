import axios from 'axios'
import { authStore } from '@/stores/authStore.js'

export const spaceTypesData = {
  createSpaceType: ( token, obj ) => {
    const promise = axios({
      method: 'POST',
      headers: {
        'Jwt': token
      },
      data: obj,
      url: 'api/space-type-create'
    })
    return promise
  },
  getSpaceTypes: ( ) => {
    const promise = axios({
      method: 'post',
      headers: {
        'jwt': authStore().token
      },
      url: 'api/space-types'
    })
    return promise
  },
  updateSpaceType: ( token, obj ) => {
    const promise = axios({
      method: 'post',
      headers: {
        'Jwt': token
      },
        data: {...obj},
      url: 'api/space-type-update/'
    })
    return promise
  },
  deleteSpaceType: ( token, spaceTypeId ) => {
    const promise = axios({
      method: 'post',
      headers: {
        'Jwt': token
      },
      data: {
        space_type_id: spaceTypeId
      },
      url: 'api/space-type-delete/'
    })
    return promise
  }
}
