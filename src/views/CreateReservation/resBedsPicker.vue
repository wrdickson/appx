<template>
  <div>
    <el-config-provider>
      <el-form 
        label-width="120px"
        size="small"
        >
          <el-form-item :label="labelNumberOfBeds">
  
            <el-select v-model="selectedBedQty" :placeholder="placeholder">
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
export default {
  name: 'ResBedsPicker',
  props: [ 'componentKey', 'initialBedsQty' ],
  emits: [ 'resBedsPicker:bedQtyChosen' ],
  data () {
    return {
      arr: [1,2,3,4,5,6,7,8,9,10],
      selectedBedQty: null
    }
  },
  computed: {
    labelNumberOfBeds () {
      return this.$t('message.numberOfBeds')
    },
    locale () {
      return this.$i18n
    },
    placeholder () {
      return this.$t('message.select')
    }
  },
  mounted () {
    if( this.initialBedsQty ) {
      this.selectedBedQty = this.initialBedsQty
    }
  },
  watch: {
    componentKey ( newVal ) {
      this.selectedBedQty = null
    },
    selectedBedQty ( oldVal, newVal ) {
      this.$emit( 'resBedsPicker:bedQtyChosen', this.selectedBedQty )
    }
  }
}
</script>