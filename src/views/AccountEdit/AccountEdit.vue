<template>
  <AccountsView
    v-if="!selectedAccount"
    @accountsview-select="accountSelected"
  />
  <editAccount
    v-if="selectedAccount"
    :selectedAccount="selectedAccount"
    @editor-cancel="editorCancel"
  />
</template>

<script setup>
  import { ref } from 'vue'
  import AccountsView from '@/views/AccountEdit/AccountsView.vue'
  import editAccount from '@/views/AccountEdit/editAccount.vue'

  const selectedAccount = ref(null)

  const accountSelected = account => {
    selectedAccount.value = account
  }

  const editorCancel = () => {
    console.log('ec')
    selectedAccount.value = null
  }



</script>