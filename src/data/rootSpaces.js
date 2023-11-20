import axios from 'axios'
import { authStore } from '@/stores/authStore.js'

export const rootSpacesData = {

  createRootSpace: ( token, beds, childOf, displayOrder, people, showChildren, spaceType, title, isActive ) => {
    const promise = axios({
      method: 'post',
      headers: {
        'Jwt': token
      },
      data: {
        beds: beds,
        childOf: childOf,
        displayOrder: displayOrder,
        people: people,
        showChildren: showChildren,
        spaceType: spaceType,
        title: title,
        isActive: isActive
      },
      url: 'api/root-spaces-create'
    })
    return promise
  },

  deleteRootSpace: ( token, rootSpaceId ) => {
    const promise = axios({
      method: 'post',
      headers: {
        Jwt: token
      },
      data: {
        root_space_id: rootSpaceId
      },
      url: 'api/root-spaces-delete'
    })
    return promise
  },

  getRootSpaces: ( ) => {
    const request = axios({
      method: 'post',
      headers: { 
        'Jwt': authStore().token
      },
      url: 'api/root-spaces'
    })
    return request
  },

  updateRootSpace( token, updateSpace ) {
    //  childOf and showChildren are strings, convert to int
    updateSpace.isActive = parseInt(updateSpace.isActive)
    updateSpace.showChildren = parseInt(updateSpace.showChildren)
    const promise = axios({
      method: 'post',
      headers: {
        'Jwt': token
      },
      dataType: 'json',
      data: {
        updateSpace
      },
      url: 'api/root-spaces/update/' + updateSpace.id
    }).catch( error => {
      throw new Error(error)
    })
    return promise
  }
}
