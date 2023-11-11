<template>
  <div>
    <el-row>
      <el-col :span="12">
        <el-card>
          <template #header>
           <div class="card-header">Login</div>
          </template>
          <el-form id="login-form">
            <el-form-item label="username">
              <el-input
                v-model="username1"
                type="text"
              />
           </el-form-item>
           <el-form-item label="password">
              <el-input
                v-model="password1"
                type="password"
              />
           </el-form-item>
           <el-form-item>
              <el-button
                @click="loginCheck"
              >
                Login
              </el-button>
            </el-form-item>
          </el-form>
        </el-card>
      </el-col>
    </el-row>
  </div>
</template>

<script>
import { ElMessage } from 'element-plus'
import authData from './data.js'
import { authStore } from './store.js'

export default {
  name: 'Login',
  components: {
  },
  data: function () {
    return {
      loginResponse: '',
      username1: '',
      password1: ''
    }
  },
  computed: {
    accountId() { return authStore().account.id },
    username() { return authStore().account.username }
  },
  methods: {
    loginCheck () {
      authData.login(this.username1, this.password1).then((response) => {
        console.log(response)
        //  clear the input variables
        this.loginResponse = ''
        this.username1 = ''
        this.password1 = ''
        //  how did login go?
        if (response.data.pass === 1 &&
          Number.isInteger(parseInt(response.data.account.id))) {
          //  show the user if the login succeeded
          this.loginResponse = 'Login Succeeded'
          //  load the user to vuex and localstorage
          authStore().setAccount(response.data.account)
          authStore().setToken(response.data.token)
          ElMessage({
            message: 'Login Succeeded',
            type: 'success',
            center: true
          })
          //  navigate
          this.$router.push('/')
        } else {
          //  show user the login has failed
          this.loginResponse = 'Login Failed'
          //  load Guest dummy user to vuex and localstorage
          authStore().setAccountToGuest()
          authStore().setToken('')
          ElMessage({
            message: 'Login Failed',
            type: 'warning',
            center: true
          })
        }
      }).catch( error => {
        console.log(error)
      })
    }
  }
}
</script>

<style>
  .info1{
    color: rgb(15, 119, 29);
  }
</style>
