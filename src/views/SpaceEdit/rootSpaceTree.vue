<template>
  <el-tree 
    :data="tData"
    :props="defaultProps"
    node-key="id"
    :default-expanded-keys="allKeys"
    @node-click="handleNodeClick"
    />
</template>

<script >
import _ from 'lodash'
export default {
  name: 'RootspaceTree',
  props: [ 'rootSpaces' ],
  emits: [ 'node-selected' ],
  data() {
    return {
      defaultProps: {
        children: 'children',
        label: 'label',
      },
      selectedNode: null
    }
  },
  computed: {
    allKeys () {
      let arr = []
        _.forEach(this.rootSpaces, (rootSpace) => {
          arr.push(rootSpace.id)
        })
      return arr
    },
    tData () {
      //  recursive
      let getRootSpaceChildren = (rootSpaceId) => {
        let arr = _.filter(this.rootSpaces, (o) => {
          return o.childOf == rootSpaceId
        })
        let returnArr = []
        _.forEach(arr, (rootSpace) => {
            let obj = {
              id: rootSpace.id,
              label: rootSpace.title + ' (' + rootSpace.displayOrder + ' | ' + rootSpace.showChildren + ' )',
              children: getRootSpaceChildren(rootSpace.id)
            }
            returnArr.push(obj)
        }) 
        return returnArr
      }

      //  top level parents
      let tArr = []
      _.forEach( this.rootSpaces, (rootSpace) => {
        if(rootSpace.childOf == 0) {
          let iObj = {
            id: rootSpace.id,
            label: rootSpace.title + ' (' + rootSpace.displayOrder + ' | ' + rootSpace.showChildren + ' )',
            children: getRootSpaceChildren(rootSpace.id)
          }
          tArr.push(iObj)
        }
      })
      return tArr
    }
  },
  methods: {
    handleNodeClick (a,b,c,d) {
      let selectedRootSpace = _.find(this.rootSpaces, (o) => {
        return o.id == a.id
      })
      this.selectedNode = selectedRootSpace
      this.$emit('node-selected', this.selectedNode)
    }
  }
}

</script>