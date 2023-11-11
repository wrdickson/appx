import axios from 'axios'

export const optionsData = {

  getAutoloadOptions: () => {
    const request = axios({
      method: 'get',
      url: 'api/autoload-options'
    })
    return request
  }
}
