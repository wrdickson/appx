<template>
 
  <el-row>
    <el-col :span="12">
      <el-button @click="showCreateSpace = true, selectedSpace = null" type="primary" size="small">{{$t('message.createSpace')}}</el-button>
      <el-divider/>
      <rootSpaceTree
        v-if="rootSpaces"
        :rootSpaces="rootSpaces"
        @node-selected="nodeSelected"
      ></rootSpaceTree>
    </el-col>
    <el-col :span="12">
      <p style="color: #ff0000;">This is for setup only.  If we are actually editing/ adding/ removing in a situation where we had reservations in db, we would need to recalculate all reservations to reflect changes.  Probably move some to an 'unassigned' category.  Complicated.</p>
      <editRootSpace
        v-if="rootSpaces && spaceTypes && selectedSpace"
        :rootSpaces="rootSpaces"
        :spaceTypes="spaceTypes"
        :selectedSpace="selectedSpace"
        @update-space="updateSpace"
        @emit-close="unselectSpace"
        @delete-space="deleteSpace"
      >
      </editRootSpace>
      <createRootSpace
        v-if="rootSpaces && spaceTypes && showCreateSpace"
        :rootSpaces="rootSpaces"
        :spaceTypes="spaceTypes"
        @emit-close="showCreateSpace = false"
        @create-space="createSpace"
      >
      </createRootSpace>
    </el-col>
  </el-row>
</template>

<script setup>
  import { rootSpacesStore } from '/src/stores/rootSpacesStore.js'
  import { resViewStore } from '/src/stores/resViewStore.js'
  import _ from 'lodash'
  import { reservationData } from '@/data/reservationData.js'
  import { rootSpacesData } from '@/data/rootSpaces.js'
  import { spaceTypesData } from '@/data/spaceTypes.js'
  import RootSpaceTree from '@/views/SpaceEdit/rootSpaceTree.vue'
  import editRootSpace from '@/views/SpaceEdit/editRootSpace.vue'
  import createRootSpace from '@/views/SpaceEdit/createRootSpace.vue'
  import { ElMessage } from 'element-plus'
  import { ElLoading } from 'element-plus'
  import { computed, ref, onMounted } from 'vue'
  import { useI18n } from 'vue-i18n'

  const { t } = useI18n() // call `useI18n`, and spread `t` from  `useI18n` returning


  const selectedSpace = ref(null)
  const showCreateSpace = ref(false)
  const rootSpaces = ref(null)
  const spaceTypes = ref(null)
  const isLoading = ref(false)

  const $i18ErrorNoAction = computed( () => { return t('message.errorNoAction') })
  const $i18SpaceCreated = computed( () => { return t('message.spaceCreated') })
  const $i18SpaceDeleted = computed( () => { return t('message.spaceDeleted') })
  
  
  const createSpace = ( obj ) => {
    const beds = obj.beds
    const childOf = obj.childOf
    const displayOrder = obj.displayOrder
    const people = obj.people
    const showChildren = obj.showChildren
    const spaceType = obj.spaceType
    const title = obj.title
    const isActive = obj.isActive
    rootSpacesData.createRootSpace( beds, childOf, displayOrder, people, showChildren, spaceType, title, isActive ).then( (response) => {
      if(response.data.create > 0){
      const sorted = _.sortBy(response.data.root_spaces_children_parents, 'displayOrder')
      rootSpaces.value = sorted
      //  update store
      rootSpacesStore().setRootSpaces(sorted)
      //  inform user
      ElMessage({
        type: 'success',
        message: $i18SpaceCreated
      })
      } else {
        //error
        ElMessage({
          type: 'error',
          mesage: $i18ErrorNoAction
        })
      }
    })
  }

  const deleteSpace = ( space ) => {
    console.log('d:', space )
    rootSpacesData.deleteRootSpace ( space.id ).then( (response) => {
      if(response.data.delete.execute_delete == true){
      const sorted = _.sortBy(response.data.root_spaces_children_parents, 'display_order')
      rootSpaces.value = sorted
      //  tell resview that root spaces have changed
      resViewStore().setShowHideRootSpaceCopy(null)
      //  update the store
      rootSpacesStore().setRootSpaces(sorted)
      ElMessage({
        type: 'success',
        message: $i18SpaceDeleted
      })
      } else {
        ElMessage({
          type: 'error',
          message: $i18ErrorNoAction
        })
      }
    })
  }

  const nodeSelected = ( rootSpace ) => {
    selectedSpace.value = rootSpace
  }

  const unselectSpace = () => {
    selectedSpace.value = null
  }
  const updateSpace = ( uSpace ) => {
    //  trigger full-screen loading
    const loading = ElLoading.service({
      lock: true,
      text: 'Loading',
      background: 'rgba(0, 0, 0, 0.7)',
    })
    
    rootSpacesData.updateRootSpace( uSpace ).then( (response) => {
      //  close full-screen loading

      loading.close()

      console.table(response.data)
      const sorted = _.sortBy(response.data.root_spaces_children_parents, 'displayOrder')
      rootSpaces.value = sorted
      //update the store
      //nuke the show/hide copy that is used by resview
      resViewStore().setShowHideRootSpaceCopy(null)
      //  reset the store with updated data
      rootSpacesStore().setRootSpaces(sorted)
      ElMessage({
        type: 'success',
        message: 'space updated'
      })
    }).catch( err => {
      loading.close()
      ElMessage({
        type: 'error',
        message: err
      })
    })
      
  }

  onMounted( () => {
      //  load root spaces
      rootSpacesData.getRootSpaces( ).then( response => {
        rootSpaces.value = response.data.root_spaces_children_parents
      })

      spaceTypesData.getSpaceTypes().then( (response) => {
        console.log('st response', response)
        spaceTypes.value = response.data.space_types
      })
    }
  )
</script>