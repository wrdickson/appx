<template>
  <h3>Parent Component</h3>
  <div>refCollection[0].name: {{refCollection[0].name}}</div>
  <div>reactiveCollection[0].name: {{reactiveCollection[0].name}}</div>

  <div>refCollection:</div>
    <el-input v-model="refCollection[0].name"/>
    <div>reactiveCollection:</div>
    <el-input v-model="reactiveCollection[0].name"/>
  <hr/>
  <h3>Mutating refValue and reactive value on parent:</h3>
  <div>refValue:</div>
  <el-input v-model="refValue"/>
  <div>reactiveValue</div>
  <el-input v-model="reactiveValue"/>



  <h3>Child Component:</h3>
  <propComp
    :refCollection="refCollection"
    :reactiveCollection="reactiveCollection"
    :refValue="refValue"
    :reactiveValue="reactiveValue"
    />
</template>

<script setup>
  import propComp from './propComp.vue'
  import { ref, reactive } from 'vue'

  const refValue = ref('foo')
  const reactiveValue = ref('bar')

  const refCollection = ref([
    {
      id: '1',
      name: 'Barney'
    },
    {
      id: '2',
      name: 'Betty'
    }
  ])

  const reactiveCollection = reactive([
    {
      id: '100',
      name: 'Georgia'
    },
    {
      id: '101',
      name: 'Geronimo'
    }
  ])

</script>