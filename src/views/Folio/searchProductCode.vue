<template>
  <el-form>
    <el-form-item label="SKU">
      <el-input v-model="searchTerm"/>
      <!--
      <el-button @click="search">Search</el-button>
      -->
    </el-form-item>
  </el-form>
  <el-table
    v-loading="loading"
    :data="productsF"
    size="small"
    @row-click="selectProduct"
  >
    <el-table-column prop="product_title" label="Title" width="160"/>
    <el-table-column prop="sku" label="sku"/>
    <el-table-column prop="price" label="Price"/>
    <!--
    <el-table-column prop="tax_types" label="Tax Types"/>
    -->
  </el-table>
  <!--
  <div>currentPage:{{currentPage}}</div>
  <div>pageSize:{{pageSize}}</div>
  <div>rowCount{{rowCount}}</div>
  <div>offset{{offset}}</div>
  -->
  <el-pagination
    small
    v-model:current-page="currentPage"
    v-model:page-size="pageSize"
    background layout="sizes, prev, pager, next" 
    :total="rowCount" 
    :page-sizes="[5,10,15,20,30]"
    size="small"
  />
</template>

<script setup>
  import { computed, onMounted, ref, watch } from 'vue'
  import { productData } from '@/data/productData.js'
  import _ from 'lodash'
  import  { optionsStore } from '@/stores/optionsStore.js'

  const emit = defineEmits(['selectProduct', 'empty-search', 'applicable-tax-types'])

  const options = optionsStore()
  const currencyCode = options.autoloadOptions.currency_code
  const currencyMinorUnits = options.autoloadOptions.currency_fraction_digits

  const searchTerm = ref('')
  const products = ref([])

  const currentPage = ref(1)
  const pageSize = ref(5)
  const rowCount = ref(0)
  const loading = ref(false)
  const offset = computed( () => {
    return (currentPage.value -1) * pageSize.value
  })

  const productsF = computed( () => {
    let a = []
    _.each(products.value, product => {
      let obj = {
        id: product.id,
        product_title: product.product_title,
        sku: product.sku,
        price: product.price,
        tax_group: product.tax_group,
        tax_types: product.tax_types
      }
      a.push(obj)
    })
    return a
  })

  const selectProduct = ( e ) => {
    emit('selectProduct', _.cloneDeep(e))
  }

  const search = () => {
    const args = { 
      searchTerm: searchTerm.value, 
      offset: offset.value,
      pageSize: pageSize.value
     }
    loading.value = true
    productData.searchProductsByCode( args ).then( response => {
      loading.value = false
      rowCount.value = response.data.rowCount
      products.value = response.data.products
      //  send applicable tax types to parent
      emit('applicable-tax-types', response.data.tax_types)
      if( response.data.products.length == 0 ) {
        emit('empty-search')
      }
    }).catch( error => {
      loading.value = false
      //  bring in the composable error handler here
      console.log(error.message)
    })
  }

  onMounted( () => {
    search()
  })

  watch( offset, newVal => {
    search()
  })

  watch( pageSize, newVal => {
    search()
  })

  watch( searchTerm, () => {
    search()
  })
</script>
