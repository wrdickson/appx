import axios from 'axios'
import { authStore } from '@/stores/authStore.js'

export const reservationData = {
  checkAvailabilityByDates: (start, end) => {
    const promise = axios({
      method: 'post',
      headers: {
        'Jwt': authStore().token
      },
      data: {
        'startDate': start,
        'endDate': end
      },
      url: 'api/reservations/availability'
    })
    return promise
  },

  checkAvailabilityByDatesIgnoreRes: (resId, start, end, token) => {
    const promise = axios({
      method: 'post',
      headers: {
        'Jwt': token
      },
      data: {
        'res_id': resId,
        'start_date': start,
        'end_date': end
      },
      url: 'api/reservations/range-ignore-res'
    })
    return promise
  },

  checkConflicts ( start, end, spaceId, token ) {
    const promise = axios({
      method: 'post',
      headers: {
        'Jwt': token
      },
      data: {
        'startDate': start,
        'endDate': end,
        'spaceId': spaceId
      },
      url: 'api/reservations/conflicts'
    })
    return promise
  },

  
  /**
    *@param spaceId int
    *@param checkin date/string
    *@param checkout date/string
    *@param customer int
    *@param people int
    *@param beds int
  */
  createReservation ( checkin, checkout, customer, spaceId, people, beds, isAssigned, spaceTypePref ) {
    const promise = axios({
      headers: {
        Jwt: authStore().token
      },
      data: {
        checkin: checkin,
        checkout: checkout,
        customer: customer,
        space_id: spaceId,
        people: people,
        beds: beds,
        is_assigned: isAssigned,
        space_type_pref: spaceTypePref
      },
      method: 'post',
      url: 'api/reservations/'
    })
    return promise
  },

  getReservationsByRange ( startDate, endDate, token ) {
    const request = axios({
      method: 'post',
      headers: {
        'Jwt': token
      },
      data: {
        startDate: startDate,
        endDate: endDate
      },
      url: 'api/reservations/range/'
    })
    return request
  },

  modifyReservation1 ( resObj, token ) {
    const request = axios({
      method: 'post',
      headers: {
        'Jwt': token
      },
      data: {
        res_obj: resObj
      },
      url: 'api/reservations/update1/'
    })
    return request
  },

  reservationCheckin ( resId, token ) {
    const request = axios({
      method: 'post',
      headers: {
        'Jwt': token
      },
      data: {
        res_id: resId
      },
      url: 'api/reservations/checkin/'
    })
    return request
  },

  reservationCheckout ( resId, token ) {
    const request = axios({
      method: 'post',
      headers: {
        'Jwt': token
      },
      data: {
        res_id: resId
      },
      url: 'api/reservations/checkout/'
    })
    return request
  }
}
