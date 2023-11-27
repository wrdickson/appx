import axios from 'axios'
import { authStore } from '@/stores/authStore.js'

export const accountData = {

  getAccountsPagination: ( offset, limit ) => {
    const request = axios({
      method: 'post',
      data: {
        offset: offset,
        limit: limit
      },
      headers: { 
        jwt: authStore().token
      },
      url: 'api/accounts-pagination/'
    })
    return request
  }

}
