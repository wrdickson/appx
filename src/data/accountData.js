import axios from 'axios'
import { authStore } from '@/stores/authStore.js'

export const accountData = {

  updatePassword: ( id, pwd ) => {
    const promise = axios({
      method: 'post',
      data: {
        id: id,
        pwd: pwd
      },
      headers: { jwt: authStore().token },
      url: 'api/accounts-update-pwd/'

    })
    return promise
  },

  createAccount: ( obj ) => {
    const promise = axios({
      method: 'post',
      data: obj,
      headers: { 
        jwt: authStore().token
      },
      url: 'api/accounts-create/'
    })
    return promise
  },

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
  },

  updateAccount: ( obj ) => {
    const request = axios({
      method: 'post',
      data: obj,
      headers: { 
        jwt: authStore().token
      },
      url: 'api/accounts-update/'
    })
    return request
  }

}
