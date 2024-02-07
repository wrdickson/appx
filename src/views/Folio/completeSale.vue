<template>
  <h3>Payments:</h3>
  <el-table
    v-if="stagedPaymentsExist"
    :data="stagedPaymentsF"
    size="small"
    show-summary
    >
    <el-table-column prop="title" label="Payment"/>
    <el-table-column prop="amount" label="Amount"/>
    <el-table-column width="50" fixed="right">
        <template #default="scope">
          <el-button @click="handleRemoveItem(scope)" type="danger" size="small">X</el-button>
        </template>
      </el-table-column>
  </el-table>
  <el-form
    ref="paymentRef"
    size="small"
    label-width="120"
    :model="payment"
    :rules="paymentRules"
  >
    <el-form-item label="payment type" prop="paymentType">
      <el-select
        v-model="payment.paymentType"
      >
        <el-option 
          v-for="paymentType in activePaymentTypes"
          :key="paymentType.id"
          :label="paymentType.payment_title"
          :value="paymentType.id"
        />
      </el-select>
    </el-form-item>
    <el-form-item label="amount" prop="amount">
      <el-input v-model="payment.amount"/>
    </el-form-item>
    <el-form-item>
      <el-button @click="addPayment(paymentRef)">Add Payment</el-button>
    </el-form-item>
  </el-form>
  <el-form>
    <el-row :gutter="10">
      <el-col :span="12">
        <el-form-item label="payment total">
          <el-input disabled v-model="paymentTotal"/>
        </el-form-item>
      </el-col>
      <el-col :span="12">
        <el-form-item>
          <el-button v-if="paymentsEqualSales" @click="submitSale">Post Sale</el-button>
        </el-form-item>
      </el-col>
    </el-row>
  </el-form>
  <div>pit: {{paymentsEqualSales}}</div>
  <div>stagedItemsTotal: {{stagedItemsTotal}}</div>

</template>

<script setup>
  import { computed, onMounted, reactive, ref, watch } from 'vue'
  import _ from 'lodash'
  import { optionsStore } from '@/stores/optionsStore.js'
  import Decimal from 'decimal.js-light'

  const props = defineProps(['activePaymentTypes','stagedSaleItems'])
  const emit = defineEmits(['submit-sale'])

  //  refs

  const payment = ref({
    paymentType: '',
    amount: ''
  })

  //  this is the ref of the form for validation
  const paymentRef = ref()

  const stagedPayments = ref([])

  //  computed

  const currencyMinorUnits = computed( () => {
    return parseInt( optionsStore().autoloadOptions.currency_fraction_digits )
  })

  const paymentTotal = computed( () => {
    let total = new Decimal(0)
    let t = 0
    _.each(stagedPayments.value, sp => {
      console.log('got one:', sp.amount, total)
      total = new Decimal(total.plus(sp.amount))
      console.log('total after add', total)
      
    })
    //  this is a string
    
    return total.toNumber().toFixed(currencyMinorUnits.value)
  })

  const paymentsEqualSales = computed( () => {
    const payments = new Decimal(paymentTotal.value)
    const total = new Decimal(stagedItemsTotal.value)
    return payments.equals(total)
  })

  const paymentTotalType = computed( () => {
    return typeof(paymentTotal.value)
  })

  const stagedPaymentsF = computed( () => {
    //  clean array ->
    console.log(' _.cloneDeep(props.activePaymentTypes)', _.cloneDeep(props.activePaymentTypes))
    
    /*
    * WTF is going on here?????
    * the first yields a proxy, but the second returns undefined ?????!!!!!
    * what the WTF WTF WTF!!!!!
    */
    //  proxy -> 
    console.log('props.activePaymentTypes', props.activePaymentTypes)
    //  undefined ->
    console.log('props.activePaymentTypes.value', props.activePaymentTypes.value)
    let staged = []
    _.each(stagedPayments.value, payment => {
      //  so, to get a clean read on props, make a copy
      let cleanP =  _.cloneDeep(props.activePaymentTypes)
      let aType = _.find(cleanP, o => {
        return o.id == payment.paymentType
      })
      let format = {
        paymentType: payment.paymentType,
        amount:  parseFloat(payment.amount).toFixed(currencyMinorUnits.value),
        title: aType.payment_title
      }
      staged.push(format)
    })
    return staged
  })

  const stagedItemsLength = computed( () => {
    return props.stagedSaleItems.length
  })

  const stagedItemsTotal = computed( () => {
    const si = _.cloneDeep(props.stagedSaleItems)
    let total = Decimal(0)
    _.each( si, item  => {
      console.log('item:', item)
      total = total.plus(parseFloat(item.total_f))
    })
    return total
  })

  const stagedPaymentsExist = computed( () => {
    if( stagedPayments.value.length > 0 ) {
      return true
    } else {
      return false
    }
  })

  //  methods

  const addPayment = ( paymentRef ) => {
    paymentRef.validate (  ( valid, fields ) => {
      if( valid ) {
        console.log('valid')
        stagedPayments.value.push(_.cloneDeep(payment.value))
        //  reset the ref
        payment.value = {
          paymentType: '',
          amount: ''
        }
      } else {
        console.log('invalid')
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

  const handleRemoveItem = scope => {
    stagedPayments.value.splice(scope.index, 1)
  }

  const priceValid = ( rule, value, callback ) => {
    if( !value.match(/^[+-]?\d+(\.\d+)?$/) ) {
      callback( new Error( 'must be correct number') )
    } else {
      callback()
    }
  }

  const submitSale = () => {
    console.log('spv', _.cloneDeep(stagedPayments.value))
    emit( 'submit-sale', _.cloneDeep(props.stagedSaleItems ), _.cloneDeep(stagedPayments.value) )
  }

  //  form validation rulesmust be after validation methods

  const paymentRules = reactive({
    amount: [
      { required: true, message: 'amount is required', trigger: 'blur' },
      { validator: priceValid, trigger: 'blur' },
      { validator: afterDecimal, trigger: 'blur' }
    ],
    paymentType: [
      { required: true, message: 'type is required', trigger: 'blur' }
    ]
  })


</script>