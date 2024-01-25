
<template>
  <el-form>
    <el-form-item label="Search Term">
      <el-input v-model="searchTerm"/>
      <el-button @click="search">Search</el-button>
    </el-form-item>
  </el-form>
  <el-table
    :data="productsF"
    size="small"
    @row-click="selectProduct"
  >
    <el-table-column prop="product_title" label="Title" width="160"/>
    <el-table-column prop="product_code" label="Code"/>
    <el-table-column prop="priceFormat" label="Price"/>
    <el-table-column prop="group_title" label="Group"/>
    <el-table-column prop="subgroup_title" label="Subgroup"/>
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
    background layout="total, sizes, prev, pager, next" 
    :total="rowCount" 
    :page-sizes="[5,10,15,20,30]"
    size="small"
  />
</template>

<script setup>
  import { computed, ref, watch } from 'vue'
  import { productData } from '@/data/productData.js'
  import { dinero } from 'dinero.js'
  import _ from 'lodash'
  import  { optionsStore } from '@/stores/optionsStore.js'

  const emit = defineEmits(['selectProduct'])

  const options = optionsStore()
  const currencyCode = options.autoloadOptions.currency_code
  const currencyMinorUnits = options.autoloadOptions.currency_fraction_digits

  const searchTerm = ref('')
  const products = ref([])

  const currentPage = ref(1)
  const pageSize = ref(5)
  const rowCount = ref(0)
  const offset = computed( () => {
    return (currentPage.value -1) * pageSize.value
  })

  const productsF = computed( () => {
    let a = []
    _.each(products.value, product => {
      const p = Dinero({
        amount: parseInt(product.price),
        precision: parseInt(currencyMinorUnits)
      })
      let obj = {
        id: product.id,
        product_title: product.product_title,
        product_code: product.product_code,
        price: product.price,
        priceFormat: parseFloat(p.toUnit()).toFixed(currencyMinorUnits),
        group_title: product.group_title,
        subgroup_title: product.subgroup_title,
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
      search_term: searchTerm.value, 
      offset: offset.value,
      pageSize: pageSize.value
     }
    productData.searchProductsByCode( args ).then( response => {
      rowCount.value = response.data.rowCount
      products.value = response.data.products
    }).catch( error => {
      //  bring in the composable error handler here
      console.log(error.message)
    })
  }

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