<template>
  <div @click="divClick" class="c1-wrapper">
    <div v-if="hasChildren">
      <el-button class="eButton" type="primary" plain v-if="showChildren == 0" size="small"><font-awesome-icon  icon="fa-chevron-right" size="sm"/></el-button>
      <el-button class="eButton" type="primary" plain v-if="showChildren == 1" size="small"><font-awesome-icon  icon="fa-chevron-down" size="sm"/></el-button>
    </div>
  </div>

</template>

<script lang="js">
  import { ElMessage } from 'element-plus'
  export default {
    name: "c1",
    props:[ 'spaceId', 'showChildren', 'children'],
    emits: [ 'c1-toggle-show-children' ],
    computed: {
      hasChildren () {
        if( this.children.length > 0){
          return true
        } else { return false }
      }
    },
    methods: {
      divClick (e) {
        console.log('divClick', this.spaceId)
        this.$emit( 'c1-toggle-show-children', this.spaceId )
        return false
      }
    }
  }
</script>

<style scoped>
  .c1-wrapper {
    min-width: 60px;
    max-width: 60px;
    max-height: 25px;
  }

  .eButton {
    max-width: 27px !important;
    min-width: 27px !important;
  }

</style>