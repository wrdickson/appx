import axios from 'axios'
import { authStore } from '@/stores/authStore.js'

export const folioData = {

  getFolio: (id) => {
    const request = axios({
      method: 'post',
      data: id,
      headers: {
        jwt: authStore().token
      },
      url: 'api/folio/' + id
    })
    return request
  }
}