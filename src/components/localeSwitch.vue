<template>
  <div>
    <el-select style="margin-top: 6px; margin-right: 4px;" @change="handleLocaleChange" size="small" class="menuLangSelect" v-model="$i18n.locale">
      <el-option
        v-for="(lang, i) in langs"
        :key="`lang-${i}`"
        :value="lang.code"
      >
        {{ lang.title }}
      </el-option>
    </el-select>
  </div>
</template>

<script>

import { localeStore } from '@/stores/localeStore.js'

export default {
  name: 'LocaleSwitch',
  data() {
    return { 
      langs: [
        {
          code: 'en',
          title: 'English'
        },
        {
          code: 'es',
          title: 'Español'
        }
      ]
    }
  },

  computed: {
    locale () {
      return localeStore().selectedLocale
    },
    i18locale () {
      return this.$i18n.locale
    }
  },

  methods: {
    handleLocaleChange ( locale ) {
      // we are sending the string 'en' or 'es' to the store
      //  the store will assign the element object to the store
      localeStore().setComponentLocale( locale )
    }
  }
  
}
</script>

<style>
.menuLangSelect {
  width: 80px;
  margin-top: 0px;
}
</style>
