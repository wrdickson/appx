<template>
  <el-row :gutter="10">
    <el-col :xs="24" :sm="12">
      <h1>Post Sale:</h1>
      <SearchProductCode
        @selectProduct = productSelected
      />
      <SaleItemStage
        :stagedItem=stagedItem
      />
    </el-col>
    <el-col :xs="24" :sm="12">
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
      <h1>Sale items:</h1>

      <SaleItemList/>
    </el-col>
  </el-row>

  <pre>{{folio}}</pre>
</template>

<script setup>
  import { onMounted, ref } from 'vue'
  import { useRoute } from 'vue-router'
  import { folioData } from '@/data/folioData.js'
  import _ from 'lodash'
  import { ElMessage } from 'element-plus'
  import SearchProductCode from '@/views/Folio/searchProductCode.vue'
  import SaleItemList from '@/views/Folio/saleItemList.vue'
  import SaleItemStage from '@/views/Folio/saleItemStage.vue'
  const route = useRoute()

  const customer = ref({})
  const folio = ref({})
  const stagedItem = ref({})

  

  //  methods
  const productSelected = ( product ) => {
    console.log(product)
    stagedItem.value = product
  }

  onMounted( () => {
    console.log('onMounted()')
    folioData.getFolio(route.params.id).then(response => {
      customer.value = response.data.customer
      folio.value = response.data.folio
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