<template>
  <h1>Test</h1>
  <div>{{jsonResponse}}</div>
  <el-button @click="testApi">Api Test</el-button>
  <el-button @click="doSomething">Do Something</el-button>
  <el-button @click="clearResponse">Clear</el-button>
</template>

<script setup>
  import { ref } from 'vue'
  import axios from 'axios'

  console.log(import.meta.env)

  //  refs
  const jsonResponse = ref('')


  //  methods

  const clearResponse = ()=>{
    jsonResponse.value = ''
  }

  const doSomething = () => {
    getDoSomething().then( response => {
      console.log( response.data )
      jsonResponse.value = JSON.stringify(response.data)
    })
  }

  const getDoSomething = ()=>{
    const promise = axios({
      method: 'get',
      url: 'api/test/do-something'
    })
    return promise
  }

  const getTest =  (  ) => {
    const promise = axios({
      method: 'get',
      url: 'api/test'
    })
    return promise
  }

  const testApi = ()=>{
    console.log('testApi()')
    getTest().then( response => {
      console.log( response.data )
      jsonResponse.value = JSON.stringify(response.data)
    })
  }
</script>