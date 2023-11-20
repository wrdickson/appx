<template>
  <div>
    <h4>refValue prop</h4>
    <div>{{refValue}}</div>
    <h4>reactiveValue prop</h4>
    <div>{{reactiveValue}}</div>
    <hr/>
    propcomp
    <div>{{props.refCollection[0].name}}</div>
    <div>refCollection:</div>
    <el-input v-model="props.refCollection[0].name"/>
    <div>reactiveCollection:</div>
    <el-input v-model="props.reactiveCollection[0].name"/>
    <hr/>
    <div>refValue:{{refValue}}</div>
    <div>reactiveValue:{{reactiveValue}}</div>
    <div>refValue:</div>
    <el-input v-model="props.refValue"/>
  </div>
</template>

<script setup>
  const props = defineProps([
    'refCollection', 
    'reactiveCollection',
    'refValue',
    'reactiveValue'
  ])

  console.log('props.refValue:', props.refValue)
  //  throws a warning: the prop is read only
  props.refValue = 'bang'
  console.log('props.refValue:', props.refValue)
  
  console.log('props.reactiveValue:', props.reactiveValue)
  //  throws a warning: the prop is read only
  props.reactiveValue = 'zing'
  console.log('props.reactiveValue:', props.reactiveValue)


  console.log('ref:')
  console.log(props.refCollection[0].name)

  console.log('reactive:')
  console.log(props.reactiveCollection[0].name)

  //  we CAN mutate the prop
  //  it is also mutated on the parent component
  

</script>