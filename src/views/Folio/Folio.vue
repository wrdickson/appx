<template>
  <el-row :gutter="10">
    <el-col :xs="24" :sm="14">
      <h1>Post Sale:</h1>
      <SearchProductCode
        @selectProduct = productSelected
        @empty-search = clearStagedItem
        @applicable-tax-types = setApplicableTaxTypes
      />
      <SaleItemStage
      v-if="stagedItem.id"
        :stagedItem = stagedItem
        @clear-staged-item = "clearStagedItem"
        @add-item-to-sale = "addItemToSale"
      />
      <SaleItemList
        :stagedSaleItems="stagedSaleItems"
        @sale-items:remove-at-index="removeStagedItemAtIndex"
      />
      <CompleteSale
        v-if="stagedItemsExist"
        :stagedSaleItems="stagedSaleItems"
        :activePaymentTypes="activePaymentTypes"
        @completeSale = completeSale
        />
    </el-col>
    <el-col :xs="24" :sm="10">
      <h1>Folio: {{customer.first_name}}&nbsp{{customer.last_name}}</h1>
      <div v-for="sale in folio.sales">
        <div>Items:</div>
        <el-table
          size="small"
          :data="sale.items"
          style="width: 100%"
        >
          <el-table-column prop="product" label="Product"/>
          <el-table-column prop="quantity" label="Qty" width="40"/>
          <el-table-column prop="unit_price" label="Price"/>
          <el-table-column prop="subtotal" label="Subtotal"/>
          <el-table-column prop="tax" label="Tax"/>
          <el-table-column prop="total" label="Total"/>
        </el-table>
        <div>Payments:</div>
        <el-table
          size="small"
          :data="sale.payments"
          style="width: 100%"
          >
          <el-table-column prop="date" label="Date"/>
          <el-table-column prop="type" label="Type"/>
          <el-table-column prop="amount" label="Amount"/>
        </el-table>
        <hr/>
      </div>
    </el-col>
  </el-row>
</template>

<script setup>
  import { computed, onMounted, ref } from 'vue'
  import { useRoute } from 'vue-router'
  import { folioData } from '@/data/folioData.js'
  import _ from 'lodash'
  import { ElMessage } from 'element-plus'
  import SearchProductCode from '@/views/Folio/searchProductCode.vue'
  import SaleItemList from '@/views/Folio/saleItemList.vue'
  import SaleItemStage from '@/views/Folio/saleItemStage.vue'
  import { optionsStore } from '@/stores/optionsStore.js'
  import Decimal from 'decimal.js-light'
  import CompleteSale from '@/views/Folio/completeSale.vue'

  const route = useRoute()


  // Adjust the global configuration for Decimal
  Decimal.set({
    precision: 20,
    //  rounding mode - critical
    rounding: Decimal.ROUND_HALF_EVEN,
    toExpNeg: -7,
    toExpPos: 21
  });

  //  refs

  const activePaymentTypes = ref([])
  const applicableTaxTypes = ref({})
  const customer = ref({})
  const folio = ref({})
  const stagedItem = ref({})
  const stagedSaleItems = ref([])
  const triggerClearStagedItem = ref(1)

  const halfRoundTest = ref()

  //  computed

  const currencyMinorUnits = computed( () => {
    return parseInt(optionsStore().autoloadOptions.currency_fraction_digits)
  })

  const stagedItemsExist = computed( () => {
    if( stagedSaleItems.value.length > 0 ) {
      return true
    } else {
      return false
    }
  })

  //  methods

  const addItemToSale = stItem => {
    //  calculate tax, subtotal, total.  format
    let listItem = {}
    listItem.product = stItem.id
    listItem.product_title = stItem.product_title
    listItem.unit_price = toMinorUnits(stItem.finalPrice)
    listItem.unit_price_f = stItem.finalPrice.toFixed(currencyMinorUnits.value)
    listItem.quantity = stItem.quantity
    listItem.sku = stItem.sku
    listItem.subtotal = listItem.unit_price * listItem.quantity
    listItem.subtotal_f = toFormatString(listItem.subtotal)
    listItem.tax_types = stItem.tax_types
    //  tax
    listItem.tax_spread = []
    let totalTax = 0
    _.each(listItem.tax_types, taxType => {
      const taxObj = _.cloneDeep(applicableTaxTypes.value[taxType])
      let taxSpread = {
        i: taxObj.id,
        r: taxObj.rate,
        t: parseInt(Decimal(listItem.subtotal).times(taxObj.rate).toFixed(0))
      }
      listItem.tax_spread.push(taxSpread)
      totalTax += parseInt(Decimal(listItem.subtotal).times(parseFloat(taxObj.rate)).toFixed(0))
    })
    listItem.tax = totalTax
    listItem.tax_f = toFormatString(totalTax)
    listItem.total = listItem.subtotal + listItem.tax
    listItem.total_f = toFormatString(listItem.total)
    console.log('li', listItem)
    //  add to stagedSaleItems
    stagedSaleItems.value.push(listItem)

    // clear staged item
    stagedItem.value = {}
    
  }

  const clearStagedItem = () => {
    stagedItem.value = {}
  }

  const completeSale = ( payments ) => {
    console.log(completeSale)
  }

  const productSelected = ( product ) => {
    stagedItem.value = product
  }

  const removeStagedItemAtIndex = ( index ) => {
    console.log('index', index)
    stagedSaleItems.value.splice(index, 1)
  }

  const setApplicableTaxTypes = t_types=> {
    applicableTaxTypes.value = t_types
  }

  const toFormatString = val => {
    let n
    switch( currencyMinorUnits.value){
      case 0:
        n = Decimal(val).toFixed(0)
        return n
      break;
      case 2:
        n = Decimal(val).div(100).toFixed(2)
        return n
      break;
      case 3:
        n = Decimal(val).div(1000).toFixed(3)
        return n
      break;
    }
  }

  const toMinorUnits = val => {
    let n
    switch( currencyMinorUnits.value){
      case 0:
        n = Decimal(val).toFixed(0)
        return parseInt(n)
      break;
      case 2:
        n = Decimal(val).times(100).toFixed(0)
        return parseInt(n)
      break;
      case 3:
        n = Decimal(val).times(1000).toFixed(0)
        return parseInt(n)
      break;
    }
  }

  onMounted( () => {
    folioData.getFolio(route.params.id).then(response => {
      customer.value = response.data.customer
      folio.value = response.data.folio
      activePaymentTypes.value = response.data.active_payment_types
    }).catch( error => {
      loadingCreate.value = false
      ElMessage({
        dangerouslyUseHTMLString: true,
        message: error.request.response,
        showClose: true,
        type: 'error',
      })
    })
  })
</script>