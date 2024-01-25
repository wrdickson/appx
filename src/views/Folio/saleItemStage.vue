<template>
  <div>Sale Item Stage</div>
  <div>{{product.product_title}}</div>
  <el-table>

  </el-table>
</template>

<script setup>
  import { ref, watch } from 'vue'
  import _ from 'lodash'
  const props = defineProps(['stagedItem'])

  const product = ref(props.stagedItem)

  watch( () => props.stagedItem.id, ( newValue, oldValue ) => {
    console.log('prop change')
    console.log(product.value = _.cloneDeep(props.stagedItem))
  })

</script>