<template>
  <div v-if="dataLoaded" class="main">
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
          <div class="flex-grow"></div>
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

  //  this is the magic that makes the dark/light toggle possible
  import { useDark, useToggle } from '@vueuse/core'
  const isDark = useDark()
  //  the ui experience triggers this
  //  we add class="dark" to index.html to make this work
  const toggleDark = useToggle(isDark)

  import { ref } from 'vue'

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
