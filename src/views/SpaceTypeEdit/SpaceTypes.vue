<template>
  <el-row>
    <el-col :xs="24">
      <p style="color: red">This currently lets the user delete and update willy nilly.  Need to make sure it doesn't compromise data.</p>
    </el-col>
  </el-row>
  <el-row>
    <el-col :xs="24">
      <el-button type="primary" plain @click="displayCreateSpaceType = true, selectedSpaceType = null">{{ i18nCreate }} {{ i18nSpaceType }}</el-button>
    </el-col>
  </el-row>
  
  <el-row>
    <el-col :xs="24" :sm="12">
      <TableSpaceTypes
        :reloadTrigger="reloadTrigger"
        @tableSpaceTypes:spaceTypeSelected="spaceTypeSelected"
      />
    </el-col>
    <el-col :xs="24" :sm="12">
      <EditSpaceType
        v-if="selectedSpaceType"
        :selectedSpaceType="selectedSpaceType"
        @editSpaceType-close="closeEditSpaceType"
        @editSpaceType-update="spaceTypeUpdate"
        @editSpaceType:delete="spaceTypeDelete"
      />
      <CreateSpaceType
        v-if="displayCreateSpaceType"
        @createSpaceType:create="createSpaceType"
        @createSpaceType:close="closeCreateSpaceType"
      />
    </el-col>
  </el-row>
</template>

<script setup>
  import TableSpaceTypes from '@/views/SpaceTypeEdit/tableSpaceTypes.vue'
  
  import EditSpaceType from '@/views/SpaceTypeEdit/editSpaceType.vue'
  import CreateSpaceType from '@/views/SpaceTypeEdit/createSpaceType.vue'
  
  import { spaceTypeStore } from '@/stores/spaceTypeStore.js'
  import { spaceTypesData } from '@/data/spaceTypes.js'
  import { ElMessage } from 'element-plus'
  import { computed, ref } from 'vue'
  import { useI18n } from 'vue-i18n'

  const { t } = useI18n() // call `useI18n`, and spread `t` from  `useI18n` returning
  
  //  refs
  const reloadTrigger = ref(1)
  const selectedSpaceType = ref(null)
  const displayCreateSpaceType = ref(false)

  //  computed
  const i18nCreate = computed(() => { return t('message.create') })
  const i18nSpaceType = computed(() => { return t('message.spaceType') })
  const iGroup = computed(()=> { return t('message.spaceType') })
    
  const closeCreateSpaceType = () => {
    displayCreateSpaceType.value = false
    //  reset
  }
  const createSpaceType = ( o ) => {
    //  convert string values
    o.is_active = parseInt(o.is_active)
    o.display_order = parseInt(o.display_order)
    o.title = o.title
    //console.table(o)
    spaceTypesData.createSpaceType( o ).then( response => {
      if( response.data.create == true ) {
        //  update the store
        spaceTypeStore().setSpaceTypes(response.data.all_sale_type_groups)
        //  force Table component to reload
        reloadTrigger.value += 1
        ElMessage({
          type: 'success',
          message: 'Space type was created.'
        })
        //  close the create component
        displayCreateSpaceType.value = false
      } else {
        ElMessage({
          type: 'error',
          message: 'Error! Not created.'
        })
      }
    })
  }

  const closeEditSpaceType = () => {
    selectedSpaceType.value = null
  }

  const showCreateSpaceType = () => {
    displayCreateSpaceType.value = true
    selectedSpaceType.value = null
  }

  const spaceTypeDelete = ( o ) => {
    //console.log('o', o)
    spaceTypesData.deleteSpaceType( o.id ).then( response => {
      if(response.data.delete == true ) {
        //  reset selected
        selectedSpaceType.value = null
        //  update the store
        spaceTypeStore().setSpaceTypes(response.data.all_sale_type_groups)

        //  force spaceTypes component to reload
        reloadTrigger.value += 1
        ElMessage({
          type: 'success',
          message: 'Space type was updated.'
        })
      } else {
        ElMessage({
          type: 'error',
          message: 'Error!  Not deleted.'
        })
      }
    })
  }

  const spaceTypeSelected = ( o ) => {
    selectedSpaceType.value = o
    displayCreateSpaceType.value = false
  }

  const spaceTypeUpdate = ( obj ) => {
    //  get the types correct before sending the request
    //  the el-form components just like strings . . .  
    obj.is_active = parseInt(obj.is_active)
    spaceTypesData.updateSpaceType( obj ).then( ( response ) => {
      if( response.data.update == true ) {
        //  update the store
        spaceTypeStore().setSpaceTypes(response.data.space_types )
        //  force tableSpaceTypes component to reload
        reloadTrigger.value += 1
        ElMessage({
          type: 'success',
          message: 'Space type was updated.'
        })
      } else {
        ElMessage({
          type: 'error',
          message: 'Error!  Not updated.'
        })
      }
      
    })
  }
</script>