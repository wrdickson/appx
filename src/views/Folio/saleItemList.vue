<template>
  <div>
    <h3>Sale Items</h3>
    <el-table :data="saleItems" size="small" style="width:100%;">
      <el-table-column prop="id" label="id" width="150"/>
      <el-table-column prop="productTitle" label="Title"/>
      <el-table-column prop="sku" label="sku"/>
      <el-table-column prop="price" label="Price"/>
      <el-table-column width="50" fixed="right">
        <template #default="scope">
          <el-button @click="handleRemoveItem(scope)" type="danger" size="small">X</el-button>
        </template>
      </el-table-column>
    </el-table>
    <div v-if="debug">
      <table class="console-table">
        <!--
        <tr><td>Subtotal</td><td>{{subtotal}}</td></tr>
        <tr><td>SubtotalFormat</td><td>{{subtotalFormat}}</td></tr>
        <tr><td>TaxTotal</td><td>{{taxTotal}}</td></tr>
        <tr><td>TaxTotalFormat</td><td>{{taxTotalFormat}}</td></tr>
        <tr><td>Total</td><td>{{total}}</td></tr>
        -->
        <tr><td>Total</td><td>{{totalFormat}}</td></tr>
      </table>
    </div>

  </div>
</template>

<script setup>
  import _ from 'lodash'
  import{ computed, ref } from 'vue'
  import currency from 'currency.js'
  import { optionsStore } from '@/stores/optionsStore.js'
  const props = defineProps(['saleItems','paymentTypes'])
  const emit = defineEmits(['sale-items:remove-at-index', 'post-sale'])

  //  TODO --- refactor this
  const currencyMinorUnits = parseInt(optionsStore().autoloadOptions.currency_fraction_digits)
  const roundingMode = optionsStore().autoloadOptions.default_rounding_mode

  const debug = ref(true)

  const handleRemoveItem = ( scope => {
    //console.log('scope', scope)
    //console.log('scope.$index', scope.$index)
    emit('sale-items:remove-at-index', scope.$index )
  })

  const subtotal = computed( () => {
    let subtotal = 0
    _.each(props.saleItems, saleItem => {
      subtotal += parseInt(saleItem.subtotal)
    })
    return subtotal
  })

  const subtotalFormat = computed( () => {
    /*
    const subtotalF = Dinero({ amount: parseInt(subtotal.value), precision: currencyMinorUnits })
    switch( currencyMinorUnits ){
      case 3:
        return subtotalF.toFormat('0.000')
      break;
      case 2:
        return subtotalF.toFormat('0.00')
      break;
      case 0:
        return subtotalF.toFormat('0')
      break;
    }
    */
    return false
    
  })

  const taxTotal = computed( () => {
    let taxTotal = 0
    _.each(props.saleItems, saleItem => {
      taxTotal += parseInt(saleItem.tax)
    })
    return taxTotal
  })

  const taxTotalFormat = computed( () => {
    /*
    const taxTotalF = Dinero({ amount: parseInt(taxTotal.value), precision: currencyMinorUnits })
    switch( currencyMinorUnits ){
      case 3:
        return taxTotalF.toFormat('0.000')
      break;
      case 2:
        return taxTotalF.toFormat('0.00')
      break;
      case 0:
        return taxTotalF.toFormat('0')
      break;
    }
    */
    return false
  })

  const total = computed( () => {
    let total = 0
    _.each(props.saleItems, saleItem => {
      total += parseInt(saleItem.total)
    })
    return total
  })

  const totalFormat = computed( () => {
    /*
    const totalF = Dinero({ amount: parseInt(total.value), precision: currencyMinorUnits })
    switch( currencyMinorUnits ){
      case 3:
        return totalF.toFormat('0.000')
      break;
      case 2:
        return totalF.toFormat('0.00')
      break;
      case 0:
        return totalF.toFormat('0')
      break;
    }
    */
    return false
  })

  //  methods

  //  receive an emit from Payments, then emit it up to Sales
  const postSale = ( payments ) => {
    emit('post-sale', payments)
  }

</script>

<style scoped>
  .console-table {
    border-collapse: collapse;
    background-color: #1c1b22;
    margin-top: 4px;
  }

  .console-table td {
    padding: 4px;
    border: 1px solid #363637;
    font-size: 14px;
  }
</style>