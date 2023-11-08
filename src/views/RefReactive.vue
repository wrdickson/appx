<template>
  <div>
    <h2>Ref and Reactive with collections:</h2>
    <h3>Ref:</h3>
    <div>
      <el-button @click="handleRefItemClick(refItem.id)" v-for="refItem in refCollection">{{refItem.name}}</el-button>
      <el-button @click="parseRefItem">Parse Ref Item</el-button>
    </div>
    <h3>Reactive:</h3>
    <div>
    
      <el-button @click="handleReactiveItemClick(reactiveItem.id)" v-for="reactiveItem in reactiveCollection">{{reactiveItem.name}}</el-button>
      <el-button @click="parseReactiveItem">Parse Reactive Item</el-button>
    </div>
    <hr/>
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
    
  </div>
</template>


<script setup>
  import { ref, reactive } from 'vue'
  import _ from 'lodash'

  let someVar = 'this is someVar'

  //  ref

  const refObj = ref({
    fruit: "orange",
    vegetable: "celery"
  })

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

  //  reactive

  const reactiveObj = reactive({
    tire: "BF Goodrich",
    oil: "Penzoil"
  })

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

  //  methods

  function doSomething ( arg ) {
    console.log('something', arg)
    someVar = arg
  }

  function getSomeVar () {
    console.log('getSomeVar() fires')
    console.log('someVar', someVar)
  }

  const handleReactiveItemClick = ( arg ) => {
    refObj.value.fruit = 'reactive bananna'
    console.log(arg)
  }

  const handleRefItemClick = ( e ) => {
    refObj.value.fruit = 'ref bananna'
    console.log(e)
  }

  const mutateReactive = () => {
    //  with reactive we don't have to use .value
    reactiveObj.tire = "Hankook"
  }

  const mutateRef = () => {
    //  with ref we have to use .value
    refObj.value.fruit = "apple"
  }

  //  this works fine as function parseReactiveItem() { etc
  //  OR as const parseReactiveItem = () => { etc
  //  WHAT'S THE DIFFERENCE?
  function parseReactiveItem(){
    //  this is where we DONT need (or want) .value
    //  if we DO go reactiveCollection.value . . . nothing happens
    //  probaby because reactiveCollection.value is undefined
    console.log('rcv:',reactiveCollection.value) //  undefined 
    _.each(reactiveCollection, refItem => {
      console.log('wha', refItem)
      //  BUT we DONT need .value here . . .
      refItem.name = 'Giagantico'
      //  and it goes to the template 
    })
  }

  const parseRefItem = () => {
    //  see how we DO need .value here
    _.each(refCollection.value, refItem => {
      console.log('wha', refItem)
      //  BUT we DONT need .value here . . .
      refItem.name = 'Baloney'
      //  and it goes to the template 
    })
  }


  const refItemClick = ( e ) => {
    console.log('e', e)
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