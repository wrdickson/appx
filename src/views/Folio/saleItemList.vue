<template>
  <div>
    <h3>Sale Items</h3>
    <el-table 
      :data="stagedSaleItems"
      size="small"
      show-summary
      style="width:100%;">
      <el-table-column prop="product_title" label="Title"/>
      <el-table-column prop="sku" label="sku" width="80"/>
      <el-table-column prop="quantity" label="qty" width="60"/>
      <el-table-column prop="unit_price_f" label="Price" width="70"/>
      <el-table-column prop="subtotal_f" label="Subtotal"/>
      <el-table-column prop="tax_f" label="tax"/>
      <el-table-column prop="total_f" label="total"/>
      <el-table-column width="50" fixed="right">
        <template #default="scope">
          <el-button @click="handleRemoveItem(scope)" type="danger" size="small">X</el-button>
        </template>
      </el-table-column>
    </el-table>
  </div>
</template>

<script setup>
  import _ from 'lodash'
  import{ computed, ref } from 'vue'
  import { optionsStore } from '@/stores/optionsStore.js'
  const props = defineProps(['stagedSaleItems','paymentTypes'])
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
    _.each(props.stagedSaleItems, stagedSaleItem => {
      subtotal += parseInt(stagedSaleItem.subtotal)
    })
    return subtotal
  })

  const taxTotal = computed( () => {
    let taxTotal = 0
    _.each(props.stagedSaleItems, stagedSaleItem => {
      taxTotal += parseInt(stagedSaleItem.tax)
    })
    return taxTotal
  })

  const total = computed( () => {
    let total = 0
    _.each(props.stagedSaleItems, stagedSaleItem => {
      total += parseInt(stagedSaleItem.total)
    })
    return total
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