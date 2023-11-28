<template>
  <div style="margin-top: 5px"></div>
  <el-config-provider :locale="locale">
    <el-table
      :data="accounts"
      v-loading="loading"
      size="small"
      style="width: 100%"
      height="400px"
      @row-click="selectAccount"
      cell-class-name="bonzo"
    >
      <el-table-column
        prop="id"
        label="id"
        width="25"
      />
      <el-table-column
        prop="username"
        label="username"
        width="150"
      />
      <el-table-column
        prop="is_active"
        label="is_active"
        width="150"
      />
      <el-table-column
        prop="email"
        label="email"
        width="200"
      />
      <el-table-column
        prop="permission"
        label="perm"
        width="50"
      />
      <el-table-column
        prop="roles"
        label="roles"
      />
    </el-table>
    <el-pagination
      v-model:current-page="currentPage"
      v-model:page-size="pageSize"
      background layout="sizes, prev, pager, next" 
      :total="rowCount" 
      :page-sizes="[5,10,20,30,40,50]"
    />
  </el-config-provider>
</template>

<script setup>
  import { computed, ref, onMounted, watch } from 'vue'
  import { useI18n } from 'vue-i18n'
  import { accountData } from '@/data/accountData.js'
  import { localeStore } from '@/stores/localeStore.js'
  import _ from 'lodash'

  const emit = defineEmits(['accountsview-select'])

  const { t } = useI18n()

  const accounts = ref([])
  //  these are for pagination
  const currentPage = ref(1)
  const pageSize = ref(10)
  const rowCount = ref(0)

  const loading = ref(true)

  const locale = computed( () => {
    return localeStore().selectedLocale
  })

  const offset = computed( () => {
    return (currentPage.value -1) * pageSize.value
  })

  const getAccounts = () => {
    loading.value = true
    accountData.getAccountsPagination( offset.value, pageSize.value ).then( response => {
      rowCount.value = response.data.row_count
      accounts.value = response.data.accounts
      loading.value = false
    })
  }

  const selectAccount = ( e ) => {
    emit('accountsview-select', _.cloneDeep(e))
  }

  onMounted( () => {
    getAccounts()
  })

  watch( currentPage, newVal => {
    getAccounts()
  })

  watch( offset, newVal => {
    getAccounts()
  })

  watch( pageSize, newVal => {
    getAccounts()
  })
</script>

<style>
  .cell {
    white-space: nowrap !important;
  }
</style>