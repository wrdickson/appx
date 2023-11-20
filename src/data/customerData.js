import axios from 'axios'
import { authStore } from '@/stores/authStore.js'

export const customerData = {

  createCustomer: ( lastName, firstName, email, phone ) => {
    const request = axios({
      headers: {
        'jwt': authStore().token
      },
      method: 'post',
      data: {
        lastName: lastName,
        firstName: firstName,
        email: email,
        phone: phone
      },
      url: 'api/customers/'
    })
    return request
  },

  searchCustomers: (lastName, firstName, offset, limit) => {
    const request = axios({
      headers: {
        'jwt': authStore().token
      },
      method: 'post',
      data: {
        lastName: lastName,
        firstName: firstName,
        offset: offset,
        limit: limit
      },
      url: 'api/customers/search'
    })
    return request
  }
}
