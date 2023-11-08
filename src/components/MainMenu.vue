

<template>
  <el-menu @select="menuSelect" :router="true">
    <localeSwitch/>
    <el-menu-item index="/Home">{{ $t('message.homePage') }}</el-menu-item>
    <el-menu-item index="/Dashboard">Dashboard</el-menu-item>
    <el-menu-item
      v-if="account.permission > 0" 
      index="/resView3">
        {{ $t('message.reservationView') }}
    </el-menu-item>
    <el-menu-item
      v-if="account.permission > 0"
      index="/houseAccountView">
      House Account
    </el-menu-item>
    <el-menu-item
      index="/ShiftReport">
      Shift Report
    </el-menu-item>
    <el-menu-item
      v-if="account.permission > 0"
      index="/PeriodSales">
      Period Sales
    </el-menu-item>
    <el-menu-item
      index="/FolioView">
      Folio View
    </el-menu-item>
    <el-menu-item
      index="/XStateDemo">
      XStateDemo
    </el-menu-item>


  </el-menu>
</template>

<script>
  import localeSwitch from '/src/components/localeSwitch.vue'
  import { accountStore } from '/src/stores/account.js'
  export default {
    name: 'MainMenu',
    emits: [ 'mainMenu-select' ],
    components: { localeSwitch },
    computed: {
      account () {
        return accountStore().account
      }
    },
    methods: {
      menuSelect () {
        this.$emit('mainMenu-select')
      }
    }
  }
</script>

<style>
.el-menu-item {
  height: 40px !important;
}
</style>