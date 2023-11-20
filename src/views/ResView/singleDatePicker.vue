<template>
  <el-config-provider :locale="locale">
    <el-date-picker
      v-model="selectedDate"
      type="date"
      format="D-MMM-YYYY"
      :placeholder="datepickerPlaceholder"
      :size="size"
    />
  </el-config-provider>
</template>

<script>
import dayjs from 'dayjs'
import { localeStore } from '@/stores/localeStore.js'
import { resViewStore } from '@/stores/resViewStore.js'
export default {
  name: 'singelDatePicker',
  props: ['overrideDate'],
  emits: ['singleDatePicker:dateSelected'],
  data () {
    return {
      //selectedDate: dayjs().format('YYYY-MM-DD'),
      selectedDate: resViewStore().startDate,
      size: 'default'
    }
  },
  computed: {
    datepickerPlaceholder () {
      return this.$t('message.datepickerPlaceholder')
    },
    locale () {
      return localeStore().selectedLocale
    }
  },
  watch: {
    overrideDate ( date ) {
      this.selectedDate = date
    },
    selectedDate( newDate, oldDate ) {
      this.$emit('singleDatePicker:dateSelected', this.selectedDate)
    }
  }
}

</script>

<style>
</style>