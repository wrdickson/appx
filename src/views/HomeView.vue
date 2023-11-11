<template>
  <h1>Home</h1>
  <h2>i18n/ localization tests:</h2>
  <div>{{howdy}}</div>
  <hr/>
    <el-config-provider :locale="locale">
      <el-table mb-1 :data="[]" />
      <el-pagination :total="100" />
    </el-config-provider>
</template>

<script setup>
  import { useI18n } from 'vue-i18n'
  import { localeStore } from '@/modules/i18n/store.js'
  import { computed, ref } from 'vue'

  const lStore = localeStore()
  const { t } = useI18n()

  const howdy = computed( () => {
    return t('message.hello')
  })

  const locale = computed( () => {
    return lStore.selectedLocale
  })

</script>