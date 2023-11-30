<template>
  <div style="margin-top: 8px"></div>
  <el-button  v-if="!selectedAccount && !createAccountShow" plain @click="showCreate">Create</el-button>
  <AccountsView
    v-if="!selectedAccount && !createAccountShow"
    @accountsview-select="accountSelected"
  />
  <div v-loading="loadingUpdate" element-loading-text="updating the account . . .">
    <editAccount
      v-if="selectedAccount"
      :selectedAccount="selectedAccount"
      @editor-cancel="editorCancel"
      @editor-update-account="updateAccount"
      @reset-password="resetPassword"
    />
  </div>

  <div v-loading="loadingCreate" element-loading-text="creating the account . . .">
    <createAccount
      :clearTrigger="createAccountClearTrigger"
      v-if="createAccountShow"
      @close-create-view="closeCreateView"
      @create-account="createNewAccount"
    />
  </div>
</template>

<script setup>
  import { ref } from 'vue'
  import { accountData } from '@/data/accountData.js'
  import AccountsView from '@/views/AccountEdit/AccountsView.vue'
  import editAccount from '@/views/AccountEdit/editAccount.vue'
  import createAccount from '@/views/AccountEdit/createAccount.vue'
  import { ElMessage } from 'element-plus'


  const createAccountShow = ref(false)
  const createAccountClearTrigger = ref(1)
  const loadingCreate = ref(false)
  const loadingUpdate = ref(false)

  const selectedAccount = ref(null)

  const accountSelected = account => {
    selectedAccount.value = account
  }

  const closeCreateView = () => {
    createAccountShow.value = false
  }

  const createNewAccount= ( str ) => {
    //  trigger loader
    loadingCreate.value = true
    //  nice clean nonreactive object
    let newAccount = JSON.parse(str)
    //  type permission and is_active to int
    newAccount.permission = parseInt(newAccount.permission)
    newAccount.is_active = parseInt(newAccount.is_active)
    newAccount.password = newAccount.password1
    //  clean out unneeded properties
    delete newAccount.password1
    delete newAccount.password2
    delete newAccount.newRole
    //  go
    accountData.createAccount(newAccount).then( response => {
      loadingCreate.value = false
      if(response.data.new_account_id){
        const username = response.data.new_account.username
        ElMessage({
          message: username + ' has been created.',
          showClose: true,
          type: 'success',
        })
        //  trigger child component to clear inputs
        createAccountClearTrigger.value += 1
      } else {
        ElMessage({
        message: 'Oops . . . something went wrong.  Account not created.',
        showClose: true,
        type: 'error',
      })
      }
    }).catch( error => {
      loadingCreate.value = false
      ElMessage({
        dangerouslyUseHTMLString: true,
        message: error.request.response,
        showClose: true,
        type: 'error',
      })
    })
  }

  const editorCancel = () => {
    console.log('ec')
    selectedAccount.value = null
  }

  const resetPassword = ( e ) => {
    console.log('resetPassword on AccountEdit.vue', e)
    console.log(selectedAccount.value.id)
    loadingUpdate.value = true
    accountData.updatePassword( selectedAccount.value.id, e ).then( response => {
      loadingUpdate.value = false
      console.log(response.data)
      ElMessage({
        message: 'Account was updated.',
        type: 'success',
      })
    }).catch( error => {
      loadingUpdate.value = false
      ElMessage({
        dangerouslyUseHTMLString: true,
        message: error.request.response,
        showClose: true,
        type: 'error',
      })
    })
  }

  const showCreate = () => {
    createAccountShow.value = true
    selectedAccount.value = null
  }

  const updateAccount = ( obj ) => {
    loadingUpdate.value = true
    accountData.updateAccount(obj).then( response => {
      loadingUpdate.value = false
      console.log(response.data)
      ElMessage({
        message: 'Account was updated.',
        type: 'success',
      })
      selectedAccount.value = null
    }).catch ( error => {
      loadingUpdate.value = false
      ElMessage({
        dangerouslyUseHTMLString: true,
        message: error.request.response,
        showClose: true,
        type: 'error',
      })
    })
  }



</script>