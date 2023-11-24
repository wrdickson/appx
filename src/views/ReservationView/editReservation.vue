<template>
  <!--
  <div>c values:</div>
  <div>this.checkin: {{ checkin }}</div>
  <div>resId: {{ cResId }}</div>
  <div>checkin: {{ cCheckinF }}</div>
  <div>checkout: {{ cCheckoutF }}</div>
  <div>spaceId: {{ cSpaceId }}</div>
  <div>people: {{ cPeople }}</div>
  <div>beds: {{ cBeds }}</div>
  <div>customer: {{cCustomer}}</div>
  <div>customerFirst: {{ cCustomerFirst }}</div>
  <div>customerLast: {{ cCustomerLast }}</div>
  <div>aSI: {{ availableSpaceIds }}</div>
  <div>SELECTED SPACE {{ selectedSpace }}</div>
  -->
  
  <div v-if="rootSpaces">
  
    <!--  CUSTOMER SELECT AND CREATE 
    <hr/>
    <div>
      Customer: {{ cCustomerFirst}}&nbsp{{cCustomerLast}}
    </div>
    <div>
      <el-button @click="showSearchCustomers = true, showCreateCustomer = false" size="small">{{ $t('message.searchCustomers') }}</el-button>
      <el-button v-if="showSearchCustomers" @click="showSearchCustomers = false" size="small">cancel</el-button>
    </div>
    <div>
      <el-button @click="showCreateCustomer = true, createCustomer = false" size="small">{{ $t('message.createCustomer') }}</el-button>
      <el-button v-if="showCreateCustomer" @click="showCreateCustomer = false" size="small">cancel</el-button>
    </div>
    <SearchCustomers
      v-if="showSearchCustomers"
      :componentKey="componentKey2"
      @searchCustomers:customerSelected="searchCustomerSelected">
    </SearchCustomers>
    <CreateCustomer
      v-if="showCreateCustomer"
      :componentKey="componentKey2"
      @createCustomer:customerCreated="createCustomerSelected">
    </CreateCustomer>
    <hr/>
    -->
    <el-config-provider :locale="locale">
      <el-form
        label-width="70px"
        size="small"
        >

          <el-form-item :label="peoplePickerLabel">
            <el-select v-model="cPeople" :placeholder="placeholder">
              <template v-for="item in peopleSelectArr">
                <el-option :label="item" :value="item"/>
              </template>
            </el-select>
          </el-form-item>

          <el-form-item :label="bedsPickerLabel">
            <el-select v-model="cBeds" :placeholder="placeholder">
              <template v-for="item in bedsSelectArr">
                <el-option :label="item" :value="item"/>
              </template>
            </el-select>
          </el-form-item>

          <el-form-item :label="rangePickerLabelDates">
            <el-date-picker
              v-model="cDateRange"
              type="daterange"
              size="small"
              format="D-MMM-YYYY"
              :clearable=false
              :range-separator="rangePickerSeperator"
              :start-placeholder="rangePickerStartPlaceholder"
              :end-placeholder="rangePickerEndPlaceholder"
            />
          </el-form-item>

          <el-form-item :label="spacePickerLabel"
            v-if="cIsAssigned"
          >
            <el-select
              v-if="this.rootSpaces && this.availableSpaceIds" v-model="this.selectedSpace" :placeholder="placeholder"
            >
              <template v-for="aSpace in this.availableSpaces">
                <el-option :label="aSpace.title" :value="aSpace.id"></el-option>
              </template>
            </el-select>
          </el-form-item>

          <el-form-item :label="labelAssigned">
            <el-select v-model="cIsAssigned">
              <el-option :label="labelTrue" :value="1"></el-option>
              <el-option :label="labelFalse" :value="0"></el-option>
            </el-select>
          </el-form-item>

          <el-form-item v-if="!cIsAssigned" :label="typeLabel">
            <el-select
              v-model="cSpaceTypePref"
            >
              <template v-for="spaceType in spaceTypes">
                <el-option :label="spaceType.title" :value="spaceType.id"></el-option>
              </template>
            </el-select>
          </el-form-item>
        </el-form>
    </el-config-provider>

    <div>
      <el-button @click="updateReservation" plain type="success" v-if="editReady">Save</el-button>
      <el-button @click="revertToProps" plain type="primary">revert</el-button>
    </div>
  </div>

</template>

<script>
import { reservationData } from '@/data/reservationData.js'
import { localeStore } from '/src/stores/localeStore.js'
import { rootSpacesStore } from '/src/stores/rootSpacesStore.js'
import { spaceTypeStore } from '/src/stores/spaceTypeStore.js'
import SearchCustomers from '@/views/ReservationView/searchCustomers.vue'
import CreateCustomer from '@/views/ReservationView/createCustomer.vue'
import dayjs from 'dayjs'
import _ from 'lodash'

export default {
  name: 'EditReservation',
  props: ['componentKey', 'resId', 'checkin', 'checkout', 'people', 'beds', 'isAssigned', 'spaceTypePref', 'spaceId', 'customer', 'customerFirst', 'customerLast' ],
  emits: [ 'edit-reservation:modify-reservation-1' ],
  components: {
    SearchCustomers,
    CreateCustomer
  },
  data () {
    return {
      //  we create these values with the initial props
      //  because we don't mutate props, we mutate a copy of the props.
      //  when the props change, these values should reload
      //  that's why we have watchers on them below
      cResId: this.resId,
      cCheckin: this.checkin,
      cCheckout: this.checkout,
      cPeople: this.people,
      cBeds: this.beds,
      cIsAssigned: this.isAssigned,
      cSpaceTypePref: this.spaceTypePref,
      cSpaceId: this.spaceId,
      cCustomer: this.customer,
      cCustomerFirst: this.customerFirst,
      cCustomerLast: this.customerLast,

      // this component has 'componentKey' as a prop,
      //  so we make another to pass to child componenets
      componentKey2: 0,

      availableSpaceIds: [],

      availableSpaces: [],
      
      showCreateCustomer: false,
      showSearchCustomers: false,

      selectedSpace: '',


      bedsSelectArr: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20],
      peopleSelectArr: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20],
    }

  },
  computed: {
    bedsPickerLabel () {
      return this.$t('message.beds')
    },
    cCheckinF () {
      return dayjs(this.cDateRange[0]).format('YYYY-MM-DD')
    },
    cCheckoutF () {
      return dayjs(this.cDateRange[1]).format('YYYY-MM-DD')
    },
    cDateRange :{
      get () {
        const k = [
          this.cCheckin,
          this.cCheckout
        ]
        return k
      },
      set ( i ) {
        this.cCheckin = dayjs(i[0]).format('YYYY-MM-DD')
        this.cCheckout = dayjs(i[1]).format('YYYY-MM-DD')
      }
    },
    editReady () {
      if( this.selectedSpace ||  (!this.cIsAssigned  && this.cSpaceTypePref)  ){
        return true
      } else {
        return false
      }
    },
    labelAssigned () {
      return this.$t('message.assigned')
    },
    labelFalse () {
      return this.$t('message.false')
    },
    labelTrue () {
      return this.$t('message.true')
    },
    locale () {
      return localeStore().selectedLocale
    },
    notAssignedTxt () {
      return this.$t('message.notAssigned')
    },
    rootSpaces () {
      //  we do NOT want a reactive value here!!!
      return {...rootSpacesStore().rootSpaces}
    },
    spaceTypes () {
      return spaceTypeStore().spaceTypes
    },
    peoplePickerLabel () {
      return this.$t('message.people')
    },
    placeholder () {
      return this.$t('message.select')
    },
    rangePickerEndPlaceholder () {
      return this.$t('message.endDate')
    },
    rangePickerLabelDates () {
      return this.$t('message.dates')
    },
    rangePickerSeperator () {
      return this.$t('message.to')
    },
    rangePickerStartPlaceholder () {
      return this.$t('message.startDate')
    },
    spacePickerLabel () {
      return this.$t('message.spaceLabel')
    },
    typeLabel () {
      return this.$t('message.type')
    }

  },
  methods: {

    createCustomerSelected ( e ) {
      //console.log('cust created', e)
      this.cCustomer = e.id
      this.cCustomerFirst = e.firstName
      this.cCustomerLast = e.lastName
      //  reset the components
      this.componentKey += 1
      this.showCreateCustomer = false
    },

    loadAvailableSpaces () {
      let arr = []
        _.each(this.rootSpaces, (rootSpace) => {
          if( _.includes(this.availableSpaceIds, rootSpace.id) )
          arr.push(rootSpace)
        })
        //console.log('av Sp', arr)
        this.availableSpaces = arr
    },
    loadAvailableSpaceIds () {
      reservationData.checkAvailabilityByDatesIgnoreRes(this.resId, this.cCheckinF, this.cCheckoutF ).then(
        response => {
          //console.log(response)
          this.availableSpaceIds = response.data.availableSpaceIds
          this.loadAvailableSpaces()
          //  reset selected space
          this.selectedSpace = null
      })
    },
    revertToProps () {
      this.cCheckin = this.checkin
      this.cCheckout = this.checkout
      this.cPeople = this.people
      this.cBeds = this.beds
      this.cCustomer = this.customer
      this.cCustomerFirst = this.customerFirst
      this.cCustomerLast = this.customerLast
      this.cSpaceTypePref = this.spaceTypePref
      this.cIsAssigned = this.isAssigned

      this.selectedSpace = null

      this.showSearchCustomers = false
      this.showCreateCustomer = false
    },
    searchCustomerSelected( e ) {
      //console.log(e)
      this.cCustomer = e.id
      this.cCustomerFirst = e.firstName
      this.cCustomerLast = e.lastName
      //  reset the components
      this.componentKey2 += 1
      this.showSearchCustomers = false
    },
    updateReservation () {
      /*
      let updateSelectedSpace = null

      //  handle data based on 'isAssigned'
      if(!this.cIsAssigned){
        updateSelectedSpace = 0
      } else {
        updateSelectedSpace = this.selectedSpace
        //  set spaceTypePref to space selected value
        //const iRootSpace = _.find(this.rootSpaces, o => {return o.id = this.selectedSpace})
        //this.cSpaceTypePref = iRootSpace.spaceType
      }
      */
      console.log('THESE ARE current')
      console.log('customer', this.cCustomer)
      console.log('spaceId', this.selectedSpace )
      console.log('people', this.cPeople )
      console.log('beds', this.cBeds)
      console.log('checkin', this.cCheckinF)
      console.log('checkout', this.cCheckoutF)
      console.log('customerFirst', this.cCustomerFirst)
      console.log('customerLast', this.cCustomerLast)
      console.log('isAssigned', this.cIsAssigned)
      console.log('spaceTypePref', this.cSpaceTypePref)
      
      let modSpaceSelected = null
      if(!this.cIsAssigned){
        modSpaceSelected = 0
      } else {
        modSpaceSelected = this.selectedSpace
      }

      //  if 
      let modSpaceTypePref = null
      if(modSpaceSelected){
        modSpaceTypePref = 0
      } else {
        modSpaceTypePref = this.cSpaceTypePref
      }

      const args = {
        res_id: this.cResId,
        space_id: modSpaceSelected,    //  TODO validate params
        people: this.cPeople,
        beds: this.cBeds,
        is_assigned: this.cIsAssigned,
        space_type_pref: modSpaceTypePref,
        checkin: this.cCheckinF,
        checkout: this.cCheckoutF
      }

      //console.log('this IS ARGS')
      //console.table(args)
      this.$emit('edit-reservation:modify-reservation-1', args)

    }
  },
  mounted () {
    //  this normally fires on watch when dates change
    //  but initially, we need to load them
    reservationData.checkAvailabilityByDatesIgnoreRes(this.resId, this.checkin, this.checkout ).then(
        response => {
          console.log(response)
          this.availableSpaceIds = response.data.availableSpaceIds
          //  get initial rootSpaces data
          //this.rootSpaces = rootSpacesStore().rootSpaces
          this.loadAvailableSpaces()

      })
  },
  watch: {
    //  watch component key and revert form to current props when changed
    //  the component will have the new reservation data ( or SHOULD )
    componentKey ( i ) {
      this.revertToProps()
    },
    //  watch the props and change the copy accordingly
    resId ( i ) {
      this.cResId = i
    },
    checkin ( i ) {
      this.cCheckin = i
      //this.loadAvailableSpaceIds()
    },
    checkout ( i ) {
      this.cCheckout = i
      //this.loadAvailableSpaceIds()
    },
    people ( i ) {
      this.cPeople = i
    },
    beds ( i ) {
      this.cBeds = i
    },
    isAssigned ( i ) {
      this.cIsAssigned = i
    },
    spaceTypePref ( i ) {
      this.cSpaceTypePref = i
    },
    spaceId ( i ) {
      this.cSpaceId = i
    },
    customer ( i ) {
      this.cCustomer = i
    },
    customerFirst ( i ) {
      this.cCustomerFirst = i
    },
    customerLast (i) {
      this.cCustomerLast = i
    },

    //  watch computed cDateRange to reload availableSpadeIds, 
    //  which also reloads available spaces!!!
    cDateRange ( newVal ) {
      //console.log('cDateRange change', newVal)
      this.loadAvailableSpaceIds()
    },
    cIsAssigned ( newVal ) {
      this.loadAvailableSpaceIds
    },
    selectedSpace ( oldVal, newVal ) {
      //console.log('selectedSpace change', oldVal, newVal )
      //console.log('cDateRange @ selectedSpace change:')
      //console.table(this.cDateRange)
    }
  }
}
</script>