<template>

  <div style="margin-top: 20px;"></div>  
  <SearchProductCode/>

  <pre>{{customer}}</pre>
  <pre>{{folio}}</pre>
</template>

<script setup>
  import { onMounted, ref } from 'vue'
  import { useRoute } from 'vue-router'
  import { folioData } from '@/data/folioData.js'
  import _ from 'lodash'
  import { ElMessage } from 'element-plus'
  import SearchProductCode from '@/views/Folio/searchProductCode.vue'

  const customer = ref({})
  const folio = ref({})

  const route = useRoute()

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