<template>
  <div>
    <el-config-provider :locale="locale">
      <el-form
        label-width="120px"
        size="small"
        >
        <el-form-item :label="labelPeople">
          <el-select v-model="selectedPeopleQty" :placeholder="placeholder">
            <template v-for="item in arr">
              <el-option :label="item" :value="item"/>
            </template>
          </el-select>
        </el-form-item>
      </el-form>
    </el-config-provider>
  </div>
</template>

<script>
import { localeStore } from '@/stores/localeStore.js'
export default {
  name: 'ResPeoplePicker',
  props: [ 'componentKey', 'initialPeopleQty' ],
  emits: [ 'resPeoplePicker:peopleQtyChosen' ],
  data () {
    return {
      arr: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20],
      selectedPeopleQty: null
    }
  },
  computed: {
    labelPeople () {
      return this.$t('message.people')
    },
    locale () {
      return localeStore().selectedLocale
    },
    placeholder () {
      return this.$t('message.select')
    }
  },
  mounted () {
    if( this.initialPeopleQty ) {
      this.selectedPeopleQty = this.initialPeopleQty
    }
  },
  watch: {
    componentKey ( oldVal, newVal ) {
      //console.log('componentKey change on resPoeplePic', newVal)
      this.selectedPeopleQty = null
    },
    selectedPeopleQty ( oldVal, newVal ) {
      //console.log(this.$i18n)
      this.$emit( 'resPeoplePicker:peopleQtyChosen', this.selectedPeopleQty )
    }
  }
}
</script>