<template>
  <div>
    <el-config-provider :locale="locale">
      <el-form
        label-width="120px"
        size="small"
        >
          <el-form-item :label="labelDates">
            <el-date-picker
              v-model="dRange"
              type="daterange"
              size="small"
              format="D-MMM-YYYY"
              :clearable=false
              :range-separator="rSeperator"
              :start-placeholder="startPlaceholder"
              :end-placeholder="endPlaceholder"
            />
          </el-form-item>
        </el-form>
    </el-config-provider>
  </div>
</template>

<script>
import { localeStore } from '@/stores/localeStore.js'
import { ElMessage } from 'element-plus'
import dayjs from 'dayjs'
export default {
  name: 'DateRangePicker',
  props: [ 'componentKey', 'initialStart', 'initialEnd' ],
  emits: [ 
    'dateRangePicker:rangeSelected',
    'dateRangePicker:clearDates'
  ],
  data () {
    return {
      dRange: null
    }
  },
  computed: {
    endDate () {
      if(this.dRange) {
        return dayjs(this.dRange[1]).format('YYYY-MM-DD')
      } else { return null }
    },
    endPlaceholder () {
      return this.$t('message.endDate')
    },
    labelDates () {
      return this.$t('message.dates')
    },
    locale () {
      return localeStore().selectedLocale
    },
    rSeperator () {
      return this.$t('message.to')
    },
    startDate () {
      if( this.dRange ) {
        return dayjs(this.dRange[0]).format('YYYY-MM-DD')
      } else { return null }
    },
    startPlaceholder () {
      return this.$t('message.startDate')
    }
  },
  mounted () {
    if( this.initialStart && this.initialEnd ) {
      this.dRange = [
        this.initialStart,
        this.initialEnd
      ]
    }
  },
  watch: {
    componentKey ( newVal ) {
      this.dRange = null
      this.$emit('dateRangePicker:rangeSelected', [null, null])
    },
    dRange ( oldR, newR ) {
      //  handle the case where user chooses the same dates for start and ent
      if( this.startDate != null && this.endDate != null ) {
        if(this.startDate == this.endDate ) {
          ElMessage( {
            type: 'warning',
            message: 'Start and end must be different'
          })
          this.$emit('dateRangePicker:clearDates')
        } else {
          const rVal = [
            dayjs(this.dRange[0]).format('YYYY-MM-DD'),
            dayjs(this.dRange[1]).format('YYYY-MM-DD')
          ]
          this.$emit('dateRangePicker:rangeSelected', rVal)
        }
      }
    }
  }
}
</script>