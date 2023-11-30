<template>
  <h2>{{selectedAccount.username}}</h2>

  <el-tabs type="border-card" @tab-change="tabChange">
    <el-tab-pane label="Edit">
      <div class="form-wrapper">
        <el-form
          v-if="accountCopy"
          :model="accountCopy"
          size="small" 
          label-width="120px">
          <el-form-item label="username">
            <el-input disabled v-model="accountCopy.username"/>
          </el-form-item>
          <el-form-item label="email">
            <el-input v-model="accountCopy.email"/>
          </el-form-item>
          <el-form-item label="permission">
            <el-select v-model="accountCopy.permission">
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
              <div v-for="(role, index) in accountCopy.roles" class="list-group-item">{{role}}<el-button @click="removeRole(role, index)" size="small" type="danger" plain class="list-group-button">X</el-button></div>
            </el-scrollbar>
          </el-form-item>
          <el-form-item label="add role">
            <el-input v-model="newRole"/>
            <el-button @click="addRole">add</el-button>
          </el-form-item>
          <el-form-item label="is active">
            <el-switch
              v-model="accountCopy.is_active"
              active-value="1"
              inactive-value="0"
              active-text="true"
              inactive-text="false"
            >
            </el-switch>
          </el-form-item>
          <el-button @click="cancel">Cancel</el-button>
          <el-button @click="revert">Revert</el-button>
          <el-button @click="updateAccount">Save</el-button>
        </el-form>
      </div>
    </el-tab-pane>
    <el-tab-pane label="Password">
      <passwordUpdate
        :resetTrigger="passwordChangeReset"
        @reset-password="resetPassword"
      />
    </el-tab-pane>

  </el-tabs>

</template>

<script setup>
  import { onMounted, ref, watch } from 'vue'
  import passwordUpdate from '@/views/AccountEdit/passwordUpdate.vue'
  import _ from 'lodash'

  const props = defineProps(['selectedAccount'])
  const emit = defineEmits(['editor-cancel', 'editor-update-account', 'reset-password'])

  const accountCopy = ref(_.cloneDeep(props.selectedAccount))

  const newRole = ref('')

  const passwordChangeReset = ref(1)

  const permissionOptions = ref([1,2,3,4,5,6,7,8,9,10])

  const addRole = () => {
    accountCopy.value.roles.push(newRole.value)
    newRole.value = ''
  }

  const cancel = () => {
    emit('editor-cancel')
  }

  const removeRole = (role, index) => {
    accountCopy.value.roles.splice( index, 1)
  }

  const resetPassword = ( pwd ) => {
    emit('reset-password', pwd )
  }

  const revert = () => {
    newRole.value = ''
    accountCopy.value = _.cloneDeep(props.selectedAccount)
    //  we have to burrow in and remove reactivity 
    //  on objects/ arrays within objects/arrays
    accountCopy.value.roles = _.cloneDeep(props.selectedAccount.roles)
    //  also, we have to tweak is_active to be a string!!!
    if(props.selectedAccount.is_active){
      accountCopy.value.is_active = "1"
    } else {
      accountCopy.value.is_active = "0"
    }
  }

  const tabChange = ( e ) => {
    console.log('tab change', e)
    //  clear change password inputs
    passwordChangeReset.value += 1

  }

  const updateAccount = () => {
    let c = _.cloneDeep(accountCopy.value)
    // make is_active an int
    c.is_active = parseInt(c.is_active)
    emit('editor-update-account', c)
  }

  onMounted( () => {
    //  IMPORTANT!!! convert is_active to string
    //  so el-switch can bind to it
    if(props.selectedAccount.is_active){
      accountCopy.value.is_active = "1"
    } else {
      accountCopy.value.is_active = "0"
    }
  })

</script>

<style>
.form-wrapper {
  max-width: 450px;
}

.list-group-button {
  margin-left: auto;
  margin-right: 5px;
  }

.list-group-item {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 26px;
  margin-right: 10px;
  text-align: left;
  border: 1px solid #4c4d4f;
  border-radius: 4px;
  padding-left: 10px;
  min-width: 300px;
  max-width: 300px;
  margin-bottom: 2px;
}

</style>