<template>
  <h4>change password</h4>
  <el-form
    :model="passwords"
    :rules="formRules"
    ref="formRef"
    label-width="120">
    <el-form-item label="password" prop="p1">
      <el-input v-model="passwords.p1" type="password"/>
    </el-form-item>
    <el-form-item label="again" prop="p2">
      <el-input v-model="passwords.p2" type="password"/>
    </el-form-item>
    <el-form-item>
      <el-button @click="handleSubmit(formRef)" simple>
        <font-awesome-icon icon="fa-solid fa-fire-flame-curved" size="2x" color="orange"/>
      </el-button>
    </el-form-item>
  </el-form>
</template>

<script setup>

  import { reactive, ref, watch } from 'vue'

  const props = defineProps(['resetTrigger'])
  const emit = defineEmits(['reset-password'])
  const formRef = ref()

  const p2Matchp1 = ( rule, value, callback ) => {
    if( value !==  passwords.value.p1 ){
      callback( new Error('do not match'))
    } else {
      callback()
    }
  }

  const formRules = reactive({
    p1:[ 
      { required: true, message: 'enter the damn password'},
      { min: 5, max: 36, message: '5 to 36 characters'},
    ],
    p2:[
      { required: true, message: 'enter the damn password'},
      { min: 5, max: 36, message: '5 to 36 characters' },
      { validator: p2Matchp1, message: 'inputs must match'}
    ]
  })

  const passwords = ref({
    p1: '',
    p2: ''
  })

  const handleSubmit = ( formRef ) => {
    formRef.validate( valid => {
      if(valid){
        emit('reset-password', passwords.value.p1)
      } else {
      }
    })
  }

  watch( () => props.resetTrigger, (val) => {
    passwords.value = {
      p1: '',
      p2: ''
    }
  })

</script>