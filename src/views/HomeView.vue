<template>
  <h1>Home</h1>
  <h2>i18n/ localization tests:</h2>
  <div>{{howdy}}</div>
  <el-config-provider :locale="locale">
    <el-date-picker
        v-model="sel"
        type="date"
        size="small"
        value-format="YYYY-MM-DD"
        format="D MMMM YYYY"
      />
      <div>{{sel}}</div>
  </el-config-provider>
  <hr/>
</template>

<script setup>
  import { useI18n } from 'vue-i18n'
  import { localeStore } from '@/modules/i18n/store.js'
  import { computed, ref } from 'vue'
  import 'dayjs/locale/es'

  const lStore = localeStore()
  const { t } = useI18n()

  const howdy = computed( () => {
    return t('message.hello')
  })

  const sel = ref(null)

  const locale = computed( () => {
    return localeStore().selectedLocale
  })

</script>