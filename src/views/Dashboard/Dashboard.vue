<template>
  <el-container style="height: 600px;">
    <el-aside width="200px">
      <el-menu class="el-menu-dashboard">
        <el-menu-item @click="hideAllViews(), showSpaceEdit=true" index="1-1">Space Edit</el-menu-item>
        <el-menu-item @click="hideAllViews(), showSpaceTypes=true" index="1-2">Space Type Edit</el-menu-item>
      </el-menu>
    </el-aside>
    <el-main>
      <SpaceEdit
        v-if="showSpaceEdit"
      >
      </SpaceEdit>

      <SpaceTypes
        v-if="showSpaceTypes"
      >
      </SpaceTypes>
    </el-main>
  </el-container>
</template>

<script>
import SpaceEdit from '@/views/SpaceEdit/SpaceEdit.vue'
import SpaceTypes from '@/views/SpaceTypeEdit/SpaceTypes.vue'

export default {
  name: 'Dashboard',
  components: {
    SpaceEdit,
    SpaceTypes
},
  data () {
    return {
      showSpaceEdit: false,
      showTaxTypes: false,
      showSaleTypeGroups: false,
      showSpaceTypes: false,
      showSaleTypes: false,
      showAccounts: false,
      showPaymentTypes: false
    }
  },
  methods: {
    hideAllViews () {
      this.showSpaceEdit = false
      this.showTaxTypes = false
      this.showSaleTypeGroups = false
      this.showSpaceTypes = false
      this.showSaleTypes = false
      this.showAccounts = false
      this.showPaymentTypes = false
    }
  }
}
</script>

<style>

</style>