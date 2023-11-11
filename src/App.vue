<template>
  <div v-if="dataLoaded && optionsLoaded && authCompleted" class="main">
    <el-container>
      <el-header>
        <el-menu
          mode="horizontal"
          :ellipsis="false"
          :router="true"
        >
          <el-menu-item index="/">Home</el-menu-item>
          <el-menu-item index="/test">Test</el-menu-item>
          <el-menu-item index="/ref-reactive">Ref-Reactive</el-menu-item>
          <el-menu-item index="props-play">Props</el-menu-item>
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
  import localeSwitch from '@/modules/i18n/localeSwitch.vue'
  import { computed, ref } from 'vue'
  //  modules/auth
  import { authStore } from '@/modules/auth/store.js'
  import { authData } from '@/modules/auth/data.js'
  //  modules/options
  import { optionsStore } from '@/modules/options/store.js'
  import { optionsData } from '@/modules/options/data.js'
  //  modules/locale
  import { localeStore } from '@/modules/i18n/store.js'

  //  REFS
  const authCompleted = ref(false)
  const optionsLoaded = ref(false)

  //  COMPUTED
  const defaultLocaleString = computed( () => {
    return optionsData().autoloadOptions.default_locale
  })

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

  //  HANDLE OPTIONS DATA
  optionsData.getAutoloadOptions().then( response => {
    console.log('OPTIONS',response)
    console.log( optionsStore() )
    optionsStore().setAutoloadOptions(response.data.options)
    optionsLoaded.value = true
  })

  
  //  use this place to load initial data and hydrate stores
  const dataLoaded = ref(true)

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
</style>
