
<template>
  <div v-if="eSpaceType">
    <div>{{ i18nEdit }}&nbsp{{ i18nSpaceType }}</div>
    <el-form
      :model="eSpaceType"
      label-width="120px"
      :rules="rules"
      ref="spaceTypeRef"
    >
      <el-form-item :label="i18nFoo" prop="title">
        <el-input v-model="eSpaceType.title"/>
      </el-form-item>
      <el-form-item :label="i18nIsCurrent">
        <el-select v-model="eSpaceType.is_active" placeholder="Select">
            <el-option :label="i18nTrue" value="1"/>
            <el-option :label="i18nFalse" value="0"/>
          </el-select>
      </el-form-item>
      <el-form-item :label="i18nDisplayOrder" prop="display_order">
        <el-input v-model="eSpaceType.display_order"/>
      </el-form-item>
      <el-form-item>
        <el-button type="primary" @click="close">{{ i18nClose }}</el-button>
        <el-button type="success" @click="updateSpaceType(spaceTypeRef)">{{ i18nUpdate }}</el-button>
      </el-form-item>
    </el-form>
    <hr/>
    <el-popconfirm
      title="Are you sure to delete 
      ?"
      @confirm="deleteSpaceType">
      <template #reference>
        <el-button type="danger">Delete</el-button>
      </template>
    </el-popconfirm>
  </div>
</template>

<script setup>
    import { ref, reactive, computed, onMounted, watch } from 'vue'
    import { useI18n } from 'vue-i18n'

    const { t } = useI18n()

    const props = defineProps( ['selectedSpaceType'] )
    const emit = defineEmits( ['editSpaceType-close', 'editSpaceType-update', 'editSpaceType:delete'] ) 
    
    const spaceTypeRef = ref()
    const eSpaceType = ref(null)

    const isGreaterThan1 = ( rule, value, callback ) => {
    if( parseInt(value) < 1 ) {
        callback( new Error( 'must be greater than 0' ))
      } else {
        callback()
      }
    }
    const isInteger = ( rule, value, callback ) => {
      const pattern = /^[0-9]*$/
      if( pattern.test(value) == false ) {
        callback( new Error('must be numbers') )
      } else {
        callback()
      }
    }

    const rules = reactive({
      title: [
        { required: true, message: 'Group title is required', trigger: 'change' },
        { min: 4, max: 24, message: '4 to 24', trigger: 'change' }
      ],
      display_order: [
        { required: true, message: 'Display order title is required', trigger: 'change'},
        { validator: isInteger, message: 'Display order must be a number', trigger: 'change' },
        { validator: isGreaterThan1, message: 'Display order must be at least 1', trigger: 'change' }
      ]
    })
    const i18nClose = computed(() => { return t('message.close') })
    const i18nDisplayOrder = computed( () =>{ return t('message.displayOrder') })
    const i18nEdit = computed(() => { return t('message.edit') })
    const i18nFalse = computed(() => { return t('message.false') })
    const i18nIsCurrent = computed(() => { return t('message.isCurrent') })
    const i18nTaxRate = computed(() => { return t('message.taxRate') })
    const i18nFoo= computed(() => { return t('message.stTitle') })
    const i18nSpaceType = computed(() => { return t('message.spaceType') })
    const i18nTrue = computed(() =>{ return t('message.true') })
    const i18nUpdate = computed(() => { return t('message.update') })
  
    const close = () => {
      emit('editSpaceType-close')
    }
    const deleteSpaceType = () => {
      console.log('del')
      emit('editSpaceType:delete', {...eSpaceType.value})
    }
    const updateSpaceType = (spaceTypeRef) => {
      console.log('str', spaceTypeRef)
      spaceTypeRef.validate( (valid) => {
        if(valid) {
          emit('editSpaceType-update', {...eSpaceType.value})
        } else {
          console.log('not valid')
        }
      })
    }

    onMounted (() => {
      eSpaceType.value = props.selectedSpaceType
      eSpaceType.value.is_active = props.selectedSpaceType.is_active.toString()
      
    })
    watch( props.selectedSpaceType, ( n ) => {
      eSpaceType.value = n
      eSpaceType.value.is_active = n.is_active.toString()
    })
    
</script>