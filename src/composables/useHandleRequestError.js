import { ElNotification } from 'element-plus'
import  router  from '/src/router/index.js'
import { authStore } from '@/stores/authStore.js'

export default () => {

  const directToHome = () => {
    authStore().setAccountToGuest()
    router.push( { path: '/Home' } )
  }

  const directToLogin = () => {
    authStore().setAccountToGuest()
    router.push( { path: '/Login' } )
  }

  const handleRequestError = (error) => {
    if( error ) {
      if(error.response) {
        if(error.response.status) {
          switch (error.response.status) {
            case 400:  //  bad request
              ElNotification({
                title: 'Error 400 - Bad Request',
                duration: 2000,
                message: 'The request was malformed.  Request was not processed',
                type: 'error'
              })
            break
            case 401:  //  unauthorized
              ElNotification({
                title: 'Error 401 - Unauthorized',
                duration: 2000,
                message: 'Authorization failed.  Request was not processed.',
                type: 'error',
                onClose: directToLogin
              })
            break
            case 403:  //  forbidden
              ElNotification({
                title: 'Error 403 - Forbidden',
                duration: 0,
                message: 'You are not allowed to perform this task.  Request was not processed',
                type: 'error',
                onClose: directToLogin
              })
            break;
            case 404:  //  not found
              ElNotification({
                title: 'Error 404 - Not found',
                duration: 2000,
                message: 'The location of this this request was not found',
                type: 'error'
              })
            case 500:  //  server error
              ElNotification({
                title: 'Server Error',
                duration: 2000,
                message: 'There was an error on the server.  Request was not processed',
                type: 'error'
              })
            break
            default:
              ElNotification({
                title: 'Error',
                duration: 2000,
                message: 'There was an error.  Request was not processed',
                type: 'error'
              })
          }
        } else {
          ElNotification({
            title: 'Error',
            duration: 4500,
            message: 'There was an error.  Request was not processed',
            type: 'error'
          })
        }
      } else {
        ElNotification({
          title: 'Error',
          duration: 0,
          message: 'There was an error 1.  Request was not processed',
          type: 'error'
        })
      }
    } else {
      ElNotification({
        title: 'Error',
        duration: 0,
        message: 'There was an error 2.  Request was not processed',
        type: 'error'
      })
    }
  }
  return { handleRequestError }
}
