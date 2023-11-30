<template>
  <h4>create account</h4>
  <div class="form-wrapper">
    <el-form
      :model="cAccount"
      ref="createAccountRef"
      :rules="rules"
      size="small" 
      label-width="120px">
      <el-form-item label="username" prop="username">
        <el-input v-model="cAccount.username"/>
      </el-form-item>
      <el-form-item label="email" prop="email">
        <el-input v-model="cAccount.email"/>
      </el-form-item>
      <el-form-item label="password" prop="password1">
        <el-input v-model="cAccount.password1" type="password"/>
      </el-form-item>
      <el-form-item label="password again" prop="password2">
        <el-input v-model="cAccount.password2" type="password" />
      </el-form-item>
      <el-form-item label="permission">
        <el-select v-model="cAccount.permission">
          <el-option 
            v-for="opt in permissionOptions"
            :index="opt"
            :label="opt"
            :value="opt"
          />
        </el-select>
      </el-form-item>
      <el-form-item label="roles">
        <el-scrollbar max-height="200px">
          <div v-for="(role, index) in cAccount.roles" class="list-group-item">{{role}}<el-button @click="removeRole(role, index)" size="small" type="danger" plain class="list-group-button">X</el-button></div>
        </el-scrollbar>
      </el-form-item>
      <el-form-item label="add role" prop="newRole">
        <el-input v-model="cAccount.newRole"/>
        <el-button v-if="cAccount.newRole" @click="addRole">add</el-button>
      </el-form-item>
      <el-form-item label="is active">
        <el-switch
          v-model="cAccount.is_active"
          active-value="1"
          inactive-value="0"
          active-text="true"
          inactive-text="false"
        >
        </el-switch>
      </el-form-item>
      <el-button @click="close">Cancel</el-button>
      <el-button @click="createAccount(createAccountRef)">Save</el-button>
    </el-form>
  </div>
</template>

<script setup>
  import { ref, reactive, watch } from 'vue'
  import _ from 'lodash'

  const emit = defineEmits(['close-create-view', 'create-account'])
  const props = defineProps(['clearTrigger'])

  //  refs
  const cAccount = ref({
    username: '',
    email: '',
    password1: '',
    password2: '',
    permission: '1',
    roles: [],
    is_active: "1",
    newRole: ''
  })
  const createAccountRef = ref()
  const newRole = ref('')
  const permissionOptions = ref([1,2,3,4,5,6,7,8,9,10])

  //  validator methods must come before rules
  const isAlphanumColonDash = ( rule, value, callback ) => {
    //  first, it's okay to be empty
    if( value.length == 0 ) {
      callback()
    }
    const pattern = /^[A-Za-z0-9:-]+$/
    if( pattern.test(value) == false ) {
      callback( new Error('must be alphanum colon dash') )
    } else {
      callback()
    }
  }

  const isEmail = ( rule, value, callback ) => {
    const pattern = /(?:[a-z0-9+!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])/i
    if( pattern.test(value) == false ) {
      callback( new Error('must be a valid email') )
    } else {
      callback()
    }
  }

  const validatePass1 = ( rule, value, callback ) => {
    if( value !== cAccount.value.password2 ) {
      callback( new Error('passwords must match') )
    } else {
      callback()
    }
  }

  const validatePass2 = ( rule, value, callback ) => {
    if( value !== cAccount.value.password1 ) {
      callback( new Error('passwords must match') )
    } else {
      callback()
    }
  }

  const rules = reactive({
    username: [
      { required: true, message: 'Please input username', trigger: 'blur' },
      { min: 4, max: 24, message: 'Length should be 4 to 24', trigger: 'blur' },
    ],
    email: [
      { required: true, message: 'Please enter a valid email', trigger: 'blur'},
      { min: 4, max: 36, message: 'Length shoule be 4 to 36', trigger: 'blur'},
      { validator: isEmail, message: 'must be a valid email', trigger: 'blur' }
    ],
    newRole: [
      { validator: isAlphanumColonDash, message: 'fix it bitch!', trigger: 'change'}
    ],
    password1: [
      { required: true, message: 'please enter a password', trigger: 'blur' },
      { min: 6, max: 36, message: 'Length shoule be 6 to 36', trigger: 'blur'},
      /*{ validator: validatePass1, message: 'passwords must match', trigger: 'blur'}*/
    ],
    password2: [
      { required: true, message: 'please enter a password', trigger: 'blur' },
      { min: 6, max: 36, message: 'Length shoule be 6 to 36', trigger: 'blur'},
      { validator: validatePass2, message: 'passwords must match', trigger: 'blur'}
    ]
  })

  //  methods
  const addRole = () => {
    cAccount.value.roles.push( _.cloneDeep(cAccount.value.newRole) )
    cAccount.value.newRole = ''
  }

  const clearInputs = () => {
    cAccount.value = {
      username: '',
      email: '',
      password1: '',
      password2: '',
      permission: '1',
      roles: [],
      is_active: "1",
      newRole: ''
    }
  }

  const close = () => {
    emit('close-create-view')
  }

  const createAccount = ( createAccountRef ) => {
    createAccountRef.validate( valid => {
      if( valid ) {
        emit('create-account', JSON.stringify(cAccount.value) )
      } else {
        console.log('not valid')
      }
    })
  }

  const removeRole = (role, index) => {
    cAccount.value.roles.splice( index, 1)
  }

  //  watch
  //  kinda different how we have to watch a prop
  watch( () => props.clearTrigger, (val) => {
    console.log('clear trigger')
    clearInputs()
  })

</script>