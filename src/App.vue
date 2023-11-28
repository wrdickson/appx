<template>
  <div v-if="optionsLoaded && authCompleted && dataLoaded" class="main">
    <el-container>
      <el-header>
        <el-menu
          mode="horizontal"
          :ellipsis="false"
          :router="true"
        >

          <el-menu-item index="/"><font-awesome-icon icon="fa-solid fa-house" size="2x" /></el-menu-item>
          <el-menu-item index="/dashboard"><font-awesome-icon icon="fa-solid fa-gauge" size="2x" /></el-menu-item>
          <el-menu-item index="/workbench"><font-awesome-icon icon="fa-solid fa-calendar-days" size="2x" /></el-menu-item>
          <el-menu-item index=""><font-awesome-icon icon="fa-solid fa-cash-register" size="2x"/></el-menu-item>
          <!--
          <el-sub-menu index="1">
            <template #title><font-awesome-icon icon="fa-solid fa-calendar-days" size="2x" /></template>
            <el-menu-item index="/create-reservation">Create Reservation</el-menu-item>
            <el-menu-item index="/res-view">ResView</el-menu-item>
            <el-menu-item index="/workbench">Workbench</el-menu-item>
          </el-sub-menu>
          -->

          <!--
          <el-sub-menu index="2">
            <template #title>Debug</template>
            <el-menu-item index="/test">Test</el-menu-item>
            <el-menu-item index="/ref-reactive">Ref-Reactive</el-menu-item>
            <el-menu-item index="props-play">Props</el-menu-item>
          </el-sub-menu>
          -->

          <div class="flex-grow"></div>
          <el-menu-item v-if="account.id < 1" index="/login">Login</el-menu-item>
          <el-menu-item v-if="account.id > 0" index="/logoff">Logoff</el-menu-item>
          <localeSwitch/>
          <el-button size="small" @click="toggleDark()" style="margin-top: 6px;">
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
  import { computed, ref } from 'vue'
  //  auth
  import { authStore } from '@/stores/authStore.js'
  import { authData } from '@/data/authData.js'
  //  options
  import { optionsStore } from '@/stores/optionsStore.js'
  import { optionsData } from '@/data/optionsData.js'
  //  locale
  import { localeStore } from '@/stores/localeStore.js'
  //  root spaces
  import { rootSpacesData } from '@/data/rootSpaces.js'
  import { rootSpacesStore } from '@/stores/rootSpacesStore.js'
  //  space types
  import { spaceTypesData } from '@/data/spaceTypes.js'
  import { spaceTypeStore } from '@/stores/spaceTypeStore.js'

  //  PROPS
  const props = defineProps(['autoloadOptions'])

  //  REFS
  const authCompleted = ref(false)
  const optionsLoaded = ref(true)

  const rootSpacesLoaded = ref(false)
  const spaceTypesLoaded = ref(false)

  //  COMPUTED
  const dataLoaded = computed( () => {
    if( rootSpacesLoaded && spaceTypesLoaded ) {
      return true
    }  else {
      return false
    }
  })

  //  HANDLE DARK/LIGHT TOGGLE
  //  this is the magic that makes the dark/light toggle possible
  import { useDark, useToggle } from '@vueuse/core'
  const isDark = useDark()
  //  the ui experience triggers this
  //  we add class="dark" to index.html to make this work
  const toggleDark = useToggle(isDark)

  //  LOAD INITIAL DATA
  const loadInitialData = () => {
    //  rootSpaces
    rootSpacesData.getRootSpaces( ).then( response => {
      rootSpacesStore().setRootSpaces( response.data.root_spaces_children_parents )
      rootSpacesLoaded.value = true
    })
    //  spaceTypes
    spaceTypesData.getSpaceTypes().then( response => {
      spaceTypeStore().setSpaceTypes( response.data.space_types)
      spaceTypesLoaded.value = true
    })
  }

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
      loadInitialData()
    }).catch( err => {
      authStore().setAccountToGuest()
      //router.push('/Login')
      authCompleted.value = true
      //loadInitialData()
    })
  } else {
    authStore().setAccountToGuest()
    //router.push('/Login')
    authCompleted.value = true
    //loadInitialData()
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

</script>

<style>
  body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 15px;
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
