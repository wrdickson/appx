<template>
  <h1>Logoff</h1>
  <p>Logged in as: <b>{{ user }}</b></p>
  <el-button @click="logoff">Logoff</el-button>
</template>

<script>
import { authStore } from '@/stores/authStore.js'
export default {
  name: 'Logoff',
  computed: {
    user () {
      return authStore().account.username
    }
  },
  methods: {
    logoff () {
      authStore().setAccountToGuest()
      this.$router.push('/')
    }
  }
}
</script>