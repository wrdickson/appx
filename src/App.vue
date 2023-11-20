<template>
  <div v-if="optionsLoaded && authCompleted" class="main">
    <el-container>
      <el-header>
        <el-menu
          mode="horizontal"
          :ellipsis="false"
          :router="true"
        >
          <el-menu-item index="/">Home</el-menu-item>
          <el-menu-item index="/create-reservation">Create Reservation</el-menu-item>
          <el-sub-menu index="2">
            <template #title>Debug</template>
            <el-menu-item index="/test">Test</el-menu-item>
            <el-menu-item index="/ref-reactive">Ref-Reactive</el-menu-item>
            <el-menu-item index="props-play">Props</el-menu-item>
          </el-sub-menu>

          <div class="flex-grow"></div>
          <el-menu-item v-if="account.id < 1" index="/login">Login</el-menu-item>
          <el-menu-item v-if="account.id > 0" index="/logoff">{{account.username}} - Logoff</el-menu-item>
          <localeSwitch/>
          <el-button size="small" @click="toggleDark()">
            Is Dark: {{ isDark }}
          </el-button>
        </el-menu>
      </el-header>
      <el-main style="margin-top: 0px !important;">
        <router-view>
        </router-view>

      </el-main>
    </el-container>
  </div>
</template>

<script setup>
  import { RouterView } from 'vue-router'
  import localeSwitch from '@/components/localeSwitch.vue'
  import _ from 'lodash'
  import { computed, ref, onMounted } from 'vue'
  //  modules/auth
  import { authStore } from '@/stores/authStore.js'
  import { authData } from '@/data/authData.js'
  //  modules/options
  import { optionsStore } from '@/stores/optionsStore.js'
  import { optionsData } from '@/data/optionsData.js'
  //  modules/locale
  import { localeStore } from '@/stores/localeStore.js'

  //  PROPS
  const props = defineProps(['autoloadOptions'])

  //  REFS
  const authCompleted = ref(false)
  const optionsLoaded = ref(true)

  //  HANDLE DARK/LIGHT TOGGLE
  //  this is the magic that makes the dark/light toggle possible
  import { useDark, useToggle } from '@vueuse/core'
  const isDark = useDark()
  //  the ui experience triggers this
  //  we add class="dark" to index.html to make this work
  const toggleDark = useToggle(isDark)

  //  HANDLE ACCOUNT (USER)   
  const localstorageAccount = JSON.parse(localStorage.getItem('account'))
  const token = localStorage.getItem('token')
  if (localstorageAccount && token ) {
    authData.authorizeToken( token ).then( (response) => {
      if(response.data.decoded.account){
        authStore().setAccount( response.data.decoded.account )
        authStore().setToken( token )
      }
      authCompleted.value = true
    }).catch( err => {
      authStore().setAccountToGuest()
      //router.push('/Login')
      authCompleted.value = true
    })
  } else {
    authStore().setAccountToGuest()
    //router.push('/Login')
    authCompleted.value = true
  }
  const account = computed( () => {
    return authStore().account
  })

  //  HANDLE OPTIONS DATA, WHICH IS A PROP FROM MAIN.JS
  optionsStore().setAutoloadOptions(props.autoloadOptions)
  optionsLoaded.value = true
  const optionLocale = _.find(props.autoloadOptions, o => {
    return o.option_name == 'default_locale'
  })
  const defaultLocale = optionLocale.option_value
  localeStore().setComponentLocale(defaultLocale)

  //  LOAD INITIAL DATA
  

</script>

<style>
  body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 14px;
  }
  .flex-grow {
    flex-grow: 1;
  }
  .el-menu-item {
    height: 40px !important;
  }
  .el-header {
    height: 40px !important;
  }
  .el-main {
    padding-top: 0px !important;
  }
  .el-sub-menu {
    height: 40px !important;
  }
</style>
