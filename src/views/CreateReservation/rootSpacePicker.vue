<template>
  <div>
    <el-form
      label-width="120px"
      size="small"
      class="cust-form"
    >
      <el-form-item :label="labelSpace">
        <el-config-provider :locale="locale">
          <el-select
            v-if="rootSpaces" v-model="selectedRootSpace" :placeholder="placeholder"
          >
            <template v-for="aSpace in availableSpaces">
              <el-option :label="aSpace.title" :value="aSpace.id"></el-option>
            </template>
          </el-select>
        </el-config-provider>
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
//import api from '/src/api/api.js'
import { authStore } from '@/stores/authStore.js'
import { rootSpacesStore } from '@/stores/rootSpacesStore.js'
import _ from 'lodash'
export default {
  name: 'RootSpacePicker',
  props: [ 'availableSpaceIds', 'componentKey'],
  emits: [ 'rootSpacePicker:spaceSelected' ],
  data () {
    return {
      selectedRootSpace: null
    }
  },
  computed: {
    availableSpaces () {
      //  create an array of available spaces, based on
      //  the avalableSpaceIds prop
      if(this.rootSpaces && this.availableSpaceIds){
        let arr = []
        _.each(this.rootSpaces, (rootSpace) => {
          if( _.includes(this.availableSpaceIds, rootSpace.id) )
          arr.push(rootSpace)
        })
        return arr
      } else {
        return []
      }
    },
    initialSpaceIdSelected () {
      if(this.selectedSpaceId){
        return true
      } else {
        return false
      }

    },
    labelSpace () {
      return this.$t('message.spaceLabel')
    },
    locale () {
      return this.$i18n
    },
    placeholder () {
      return this.$t('message.select')
    },
    rootSpaces () {
      return rootSpacesStore().rootSpaces
    },
    token () {
      return accountStore().token
    }
  },
  created () {
    /*
    api.rootSpaces.getRootSpaces( this.token ).then (( response ) => {
      this.rootSpaces = response.data.root_spaces_children_parents
    })
    */
  },
  watch: {
    // component key changes when parent wants to clear the form
    componentKey ( newVal ) {
      //console.log('cKey on rSP')
      this.selectedRootSpace = null
    },
    selectedRootSpace ( oldVal, newVal ) {
      this.$emit('rootSpacePicker:spaceSelected', this.selectedRootSpace )
    },
    availableSpaceIds (oldval, newval) {
      //  clear the selection when available ids changes,
      //  ie when the dates have changed
      this.selectedRootSpace = null
    }
  }
}
</script>