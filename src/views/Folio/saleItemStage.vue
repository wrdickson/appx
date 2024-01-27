<template>
  <el-form
    ref="stageRef"
    label-width="80"
    :model="product"
    :rules="rules"
    size="small"
  >
    <el-form-item label="product" prop="product_title">
      <el-input v-model="product.product_title" disabled/>
    </el-form-item>
    <el-row>
      <el-col :span="12">
        <el-form-item label="sku" prop="sku">
          <el-input v-model="product.sku" disabled/>
        </el-form-item>
      </el-col>
      <el-col :span="12">
        <el-form-item label="price" prop="price">
          <el-input v-model="product.price" disabled/>
        </el-form-item>
      </el-col>
    </el-row>
    <el-row>
      <el-col :span="12">
        <el-form-item label="quantity" prop="quantity">
          <el-input v-model="product.quantity"/>
        </el-form-item>
      </el-col>
      <el-col :span="12">
        <el-form-item label="override" prop="finalPrice">
          <el-input v-model="product.finalPrice"/>
        </el-form-item>
      </el-col>
    </el-row>
    <el-form-item>
      <el-button type="primary" @click="addToSale(stageRef)">Add to sale</el-button>
      <el-button @click="clearStagedItem">Clear</el-button>
      <el-button @click="revert">Revert</el-button>
    </el-form-item>
  </el-form>
</template>

<script setup>
  import { computed, reactive, ref, watch } from 'vue'
  import { optionsStore } from '@/stores/optionsStore.js'
  import _ from 'lodash'

  const props = defineProps(['stagedItem'])
  const emit = defineEmits(['clear-staged-item', 'add-item-to-sale'])

  //  computed

  const currencyMinorUnits = computed( () => {
    return parseInt( optionsStore().autoloadOptions.currency_fraction_digits )
  })

  //  refs 

  const product = ref({
    id: 0,
    product_title: '',
    sku: '',
    price: '',
    quantity: 1,
    finalPrice: 0
  })

  const productRevertToProp = ref({
    id: 0,
    product_title: '',
    sku: '',
    price: '',
    quantity: 1,
    finalPrice: 0
  })

  const stageRef = ref()

  //  methods
  //  ** rules follow validator methods **

  const addToSale = ( stageRef ) => {
    stageRef.validate((valid, fields) => {
      if (valid) {
        console.log('submit!', _.cloneDeep(product.value))
        //  clean up types (several strings from text inputs)
        let typedProduct = _.cloneDeep(product.value)
        typedProduct.quantity = parseFloat(typedProduct.quantity)
        typedProduct.finalPrice = parseFloat(typedProduct.finalPrice)
        typedProduct.price = parseFloat(typedProduct.price)
        emit( 'add-item-to-sale',  typedProduct )
      } else {
        console.log('error submit!', fields)
      }
    })
  }

  const afterDecimal = ( rule, value, callback ) => {
    const digits = value.split('.')
    if( digits[1] ) {
      const length = digits[1].length
      if(length > currencyMinorUnits.value || length < currencyMinorUnits.value ) {
        callback( new Error('wrong digits after decimal') )
      } else {
        callback()
      }
    } else {
      callback()
    }
  }

  const clearStagedItem = () => {
    emit('clear-staged-item')
  }

  const hasPeriod = ( rule, value, callback ) => {
    if( value.match(/\./) ) {
      callback( new Error( 'no decimals') )
    } else {
      callback()
    }
  }

  const priceValid = ( rule, value, callback ) => {
    if( !value.match(/^[+-]?\d+(\.\d+)?$/) ) {
      callback( new Error( 'must be correct number') )
    } else {
      callback()
    }
  }

  const qtyValid = ( rule, value, callback ) => {
    const number = Number(value)
    const isInteger = Number.isInteger(number);
    const isPositive = number > 0;
    //  not zero
    if( value === '0' ){
      callback( new Error( 'must be greater than 0' ))
    } else if ( !isInteger ) {
      callback( new Error( 'must be a number' ))
    } else if ( !isPositive ) {
      callback( new Error( 'must be positive' ))
    } else {
      callback()
    }
  }

  const revert = () => {
    product.value = _.cloneDeep(productRevertToProp.value)
  }

  //  rules has to come after validator methods

  const rules = reactive({
    finalPrice: [
      { required: true, message: 'price is required', trigger: 'change' },
      { validator: priceValid, trigger: 'change' },
      { validator: afterDecimal, trigger: 'change' }
    ],
    quantity: [
      { required: true, message: 'quantity is required', trigger: 'change' },
      { validator: qtyValid, trigger: 'change' },
      { validator: hasPeriod, trigger: 'change' }
    ]
  })

  //  watch props

  watch( () => props.stagedItem, ( newValue, oldValue ) => {
    let x = _.cloneDeep(newValue)
    //  quantity needs to be a string 
    x.quantity = '1'
    x.finalPrice = x.price
    product.value = x

    let y = _.cloneDeep(newValue)
    y.quantity = '1'
    y.finalPrice = y.price
    productRevertToProp.value = y
  }, { immediate: true } )

</script>
