<template>
  <el-container>
    <el-aside width="150px" class="hidden-xs-only">
      <el-scrollbar>
        <el-menu class="el-menu-dashboard">
          <el-menu-item @click="hideAllViews(), showSpaceEdit=true" index="1-1">Space</el-menu-item>
          <el-menu-item @click="hideAllViews(), showSpaceTypes=true" index="1-2">Space Type</el-menu-item>
          <el-menu-item @click="hideAllViews(), showAccounts=true" index="1-3">Account</el-menu-item>
        </el-menu>
      </el-scrollbar>
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
      <AccountEdit 
        v-if="showAccounts">
      </AccountEdit>
    </el-main>
  </el-container>
</template>

<script>
import SpaceEdit from '@/views/SpaceEdit/SpaceEdit.vue'
import SpaceTypes from '@/views/SpaceTypeEdit/SpaceTypes.vue'
import AccountEdit from '@/views/AccountEdit/AccountEdit.vue'

export default {
  name: 'Dashboard',
  components: {
    SpaceEdit,
    SpaceTypes,
    AccountEdit
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