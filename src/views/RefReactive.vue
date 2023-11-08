<template>
  <div>
    <h2>Non reactive properties and methods of the script</h2>
    <div>someVar:{{someVar}}</div>
    <el-button @click="doSomething(testRef.fruit)">doSomething</el-button>
    <el-button @click="getSomeVar">getSomeVar</el-button>

    

    <hr/>
    <h2>Ref and Reactive with a simple object:</h2>
    <div>refObj.fruit:{{refObj.fruit}}</div>
    <div>reactiveObj.tire:{{reactiveObj.tire}}</div>
    <h3>Mutating porperties on ref and reactive objects:</h3>
    <el-button @click="testRef">Test Ref</el-button>
    <el-button @click="testReactive">Test Reactive</el-button>
    <el-button @click="mutateRef">Mutate Ref</el-button>
    <el-button @click="mutateReactive">Mutate Reactive</el-button>
    <h3>Data binding to ref and reactive</h3>
    <h4>ref:</h4>
    <el-input v-model="refObj.fruit"/>
    <h4>reactive:</h4>
    <el-input v-model="reactiveObj.tire"/>
    <hr/>
    <h2>Ref and Reactive with collections:</h2>
    
  </div>
</template>


<script setup>
  import { ref, reactive } from 'vue'

  let someVar = 'this is someVar'

  //  ref

  const refObj = ref({
    fruit: "orange",
    vegetable: "celery"
  })

  const refCollection = ref([
    {
      name: 'Barney'
    },
    {
      name: 'Betty'
    }

  ])

  //  reactive

  const reactiveObj = reactive({
    tire: "BF Goodrich",
    oil: "Penzoil"
  })

  const reactiveCollection = reactive([
    {
      name: 'Georgia'
    },
    {
      name: 'Geronimo'
    }

  ])

  //  methods

  function doSomething ( arg ) {
    console.log('something', arg)
    someVar = arg

  }

  function getSomeVar () {
    console.log('getSomeVar() fires')
    console.log('someVar', someVar)
  }


  const mutateReactive = () => {
    //  with reactive we don't have to use .value
    reactiveObj.tire = "Hankook"
  }

  const mutateRef = () => {
    //  with ref we have to use .value
    refObj.value.fruit = "apple"
  }

  const testRef = () => {
    console.log('ref:')
    console.log(refObj.value.fruit)
  }

  const testReactive = () => {
    console.log('reactive')
    console.log(reactiveObj.tire)
  }


</script>