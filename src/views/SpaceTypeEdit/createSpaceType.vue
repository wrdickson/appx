<template>
  <div>{{ i18nCreate }}&nbsp{{ i18nSpaceType }}</div>
  <el-form
    :model="cSpaceType"
    label-width="120px"
    :rules="rules"
    ref="createSpaceTypeRef"
  >
    <el-form-item :label="i18nBlah" prop="title">
      <el-input v-model="cSpaceType.title"/>
    </el-form-item>
    <el-form-item :label="i18nIsActive">
      <el-select v-model="cSpaceType.is_active" placeholder="Select">
          <el-option :label="i18nTrue" value="1"/>
          <el-option :label="i18nFalse" value="0"/>
        </el-select>
    </el-form-item>
    <el-form-item :label="i18nDisplayOrder" prop="display_order">
      <el-input v-model="cSpaceType.display_order"/>
    </el-form-item>
    <el-form-item>
      <el-button type="danger" @click="close">{{ i18nClose }}</el-button>
      <el-button type="success" @click="createSpaceType(createSpaceTypeRef)">{{i18nCreate}}&nbsp{{i18nSpaceType}}</el-button>
    </el-form-item>
  </el-form>
</template>

<script setup>
  import { computed, ref, reactive } from 'vue'
  import { useI18n } from 'vue-i18n'

  const { t } = useI18n()

  const emit = defineEmits( [ 'createSpaceType:create', 'createSpaceType:close' ] )
  
  const cSpaceType = ref({
    title: '',
    is_active: '1',
    display_order: '0'
  })

  const createSpaceTypeRef = ref()

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

  const rules = reactive( {
    title: [
      { required: true, message: 'Title is required', trigger: 'change'},
      { min: 4, max: 24, message: '4 to 24 characters', trigger: 'change' }
    ],
    display_order: [
      { required: true, message: 'Display order title is required', trigger: 'change'},
      { validator: isInteger, message: 'Display order must be a number', trigger: 'change' },
      { validator: isGreaterThan1, message: 'Display order must be at least 1', trigger: 'change' }
    ]
  })


  const i18nClose = computed( () => { return t('message.close') })
  const i18nCreate = computed( () => { return t('message.create') })
  const i18nDisplayOrder = computed( () => { return t('message.displayOrder') })
  const i18nFalse = computed( () => { return t('message.false') })
  const i18nIsActive = computed( () => { return t('message.isActive') })
  const i18nBlah = computed( () => { return t('message.stTitle') })
  const i18nSpaceType = computed( () => { return t('message.spaceType') })
  const i18nTrue = computed( () => { return t('message.true') })


  const createSpaceType = ( createSpaceTypeRef ) => {
    createSpaceTypeRef.validate( valid => {
      if( valid ) {
        emit('createSpaceType:create', {...cSpaceType.value} )
      } else {
        console.log('not valid')
      }
    })
  }

  const close = () => {
    cSpaceType.value.title = ''
    cSpaceType.value.is_active = '1',
    cSpaceType.value.display_order = '0'
    emit('createSpaceType:close')
  }

</script>