<template>
  <h4>Edit Account</h4>
  <div class="form-wrapper">
    <el-form
      :model="accountCopy"
      size="small" 
      label-width="120px">
      <el-form-item label="username">
        <el-input v-model="accountCopy.username"/>
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
      <el-button @click="cancel">Cancel</el-button>
      <el-button @click="revert">Revert</el-button>
      <el-button @click="saveAccount">Save</el-button>
    </el-form>
  </div>  
</template>

<script setup>
  import { onMounted, ref, watch } from 'vue'
  import _ from 'lodash'

  const props = defineProps(['selectedAccount'])
  const emit = defineEmits(['editor-cancel'])

  const accountCopy = ref(_.cloneDeep(props.selectedAccount))

  const newRole = ref('')

  const permissionOptions = ref([1,2,3,4,5,6,7,8,9,10])

  const addRole = () => {
    accountCopy.value.roles.push(newRole.value)
    newRole.value = ''
  }

  const cancel = () => {
    emit('editor-cancel')
  }

  const removeRole = (role, index) => {
    console.log(role, index)
    accountCopy.value.roles.splice( index, 1)
  }

  const revert = () => {
    newRole.value = ''
    accountCopy.value = _.cloneDeep(props.selectedAccount)
    //  we have to burrow in and remove reactivity 
    //  on objects/ arrays within objects/arrays
    accountCopy.value.roles = _.cloneDeep(props.selectedAccount.roles)
  }

  const saveAccount = () => {
    console.table(_.cloneDeep(accountCopy.value))
  }

  onMounted( () => {
    console.log('mounted')
  })

  //  we have to watch a property, not the entire object
  watch( () => props.selectedAccount.id, ( newId ) => {
    newRole.value = ''
    accountCopy.value = _.cloneDeep(props.selectedAccount)
    //  we have to burrow in and remove reactivity 
    //  on objects/ arrays within objects/arrays
    accountCopy.value.roles = _.cloneDeep(props.selectedAccount.roles)
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