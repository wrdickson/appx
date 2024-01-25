import axios from 'axios'
import { authStore } from '@/stores/authStore.js'

export const productData = {

  /*
  *  args object: searchTerm, offset, pageSize
  */
  searchProductsByCode: ( args ) => {
    const request = axios({
      method: 'post',
      data: args,
      headers: { 
        'jwt': authStore().token
      },
      url: 'api/product/search'
    })
    return request
  }
}