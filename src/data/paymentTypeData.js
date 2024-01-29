import axios from 'axios'

export const paymentTypeData = {

  getActivePaymentTypes: (token) => {
    const request = axios({
      method: 'post',
      headers: { 
        'Jwt': token
      },
      url: 'api/get-active-payment-types/'
    })
    return request
  }

}