import axios from 'axios'

export const authData = {

  authorizeToken: (token) => {
    const request = axios({
      method: 'post',
      headers: { 
        'Jwt': token
      },
      url: 'api/authorize-token/'
    })
    return request
  },
  
  login: (username, password) => {
    const request = axios({
      method: 'post',
      data: {
        username: username,
        password: password
      },
      url: 'api/login/'
    })
    return request
  }
}
