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
  },

  postFolioSale: ( items, payments, folio ) => {
    const request = axios({
      method: 'post',
      data: {
        items: items,
        payments: payments,
        folio: folio
      },
      headers: {
        jwt: authStore().token
      },
      url: 'api/folio-post-sale'
    })
    return request
  }
}