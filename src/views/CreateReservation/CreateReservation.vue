<template>
  <el-row>
    <el-col>
      <h1>{{ $t('message.createReservation') }}</h1>
      <el-button type="primary" size="small" @click="resetForm">Reset Form</el-button>
      <!--
      <div>CreateReservation State</div>
      <div>selectedPeople: {{ selectedPeople }}</div>
      <div>selectedBeds: {{ selectedBeds }}</div>
      <div>startDate: {{ startDate }}</div>
      <div>endDate: {{ endDate }}</div>

      <div>selectedCustomer: {{ selectedCustomer }}</div>
      -->
      <div>spaceId: {{ selectedSpaceId }}</div>
      <div>spaceTypePref: {{spaceTypePref}}</div>
      <div>isAssigned: {{isAssigned}}</div>
      
      <hr/>
      <div>
        <span style="display: flex;">
          <span v-if="selectedCustomer">{{ $t('message.customer') }}:&nbsp<b>{{ selectedCustomer.first_name }}&nbsp{{selectedCustomer.last_name}}</b></span>
        
          <el-button 
            v-if="selectedCustomer"
            type="warning"
            size="small"
            style="margin-left: auto;"
            @click="selectedCustomer = null; showSearchCustomers = null; showCreateCustomer = null"
            >
              {{ $t('message.reset') }}
          </el-button>
        </span>
      </div>
      <div>
        <span v-if="!selectedCustomer">
          <el-form size="small">
            <el-form-item :label="labelCustomer">
              <el-button
                size="small"
                type="primary"
                @click="showSearchCustomers = true; showCreateCustomer = false">
                {{ $t('message.search') }}
              </el-button>
              <el-button
                size="small"
                type="primary"
                @click="showCreateCustomer = true; showSearchCustomers = false">
                {{ $t('message.create') }}
              </el-button> 
            </el-form-item>
          </el-form>
        </span>
      </div>
      <SearchCustomers
        v-if="showSearchCustomers && !selectedCustomer"
        :componentKey="componentKey"
        @searchCustomers:customerSelected="customerSelected">
      </SearchCustomers>
      <CreateCustomer
        :componentKey="componentKey"
        @createCustomer:customerCreated="customerSelected"
        v-if="showCreateCustomer && !selectedCustomer"
      >
      </CreateCustomer>
      <hr/>
      <ResPeoplePicker
        :componentKey="componentKey"
        @resPeoplePicker:peopleQtyChosen="peopleQtyChosen"
        ></ResPeoplePicker>
      <ResBedsPicker 
        :componentKey="componentKey" 
        @resBedsPicker:bedQtyChosen="bedQtyChosen"></ResBedsPicker>
      <DateRangePicker
        :componentKey="componentKey"
        @dateRangePicker:rangeSelected="dRangeSelected"
        @dateRangePicker:clearDates="dRangeClearDates">
      </DateRangePicker>
      <RootSpacePicker 
        :componentKey="componentKey"
        v-if="availableSpaceIds && isAssigned"
        :availableSpaceIds="availableSpaceIds"
        @rootSpacePicker:spaceSelected="spaceSelected">
      </RootSpacePicker>
      <el-form size="small" label-width="120px">
        <el-form-item :label="labelAssigned">
          <el-select v-model="isAssigned">
            <el-option :label="labelTrue" :value="1"></el-option>
            <el-option :label="labelFalse" :value="0"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item v-if="!isAssigned" :label="typeLabel">
            <el-select
              v-model="spaceTypePref"
            >
              <template v-for="spaceType in spaceTypes">
                <el-option :label="spaceType.title" :value="spaceType.id"></el-option>
              </template>
            </el-select>
          </el-form-item>
      </el-form>
      <el-form-item
        v-if="startDate && endDate && selectedCustomer && selectedPeople && selectedBeds && ( selectedSpaceId || (!isAssigned && spaceTypePref)) "
      >
        <el-button type="primary" size="small" @click="createReservation">{{ $t('message.createReservation') }}</el-button>
        <el-button type="primary" size = "small" @click="checkConflicts">{{ $t('message.availability') }}</el-button>
      </el-form-item>
    </el-col>
  </el-row>
</template>

<script>
  import DateRangePicker from './dateRangePicker.vue'
  import RootSpacePicker from './rootSpacePicker.vue'
  import SearchCustomers from './searchCustomers.vue'
  import CreateCustomer from './createCustomer.vue'
  import ResBedsPicker from './resBedsPicker.vue'
  import ResPeoplePicker from './resPeoplePicker.vue'
  import { ElMessage } from 'element-plus'
  import { ElLoading } from 'element-plus'
  import useHandleRequestError from '@/composables/useHandleRequestError.js'
  import { authStore } from '@/stores/authStore.js'
  import { spaceTypeStore } from '@/stores/spaceTypeStore.js'
  import dayjs from 'dayjs'
  import { reservationData } from '@/data/reservationData.js'

  export default {
    setup () {
      //  import composable function for request error handling
      const { handleRequestError } = useHandleRequestError()
      return { handleRequestError }
    },
    name: 'CreateReservation',
    emits: [],
    components: {
      DateRangePicker,
      RootSpacePicker,
      SearchCustomers,
      CreateCustomer,
      ResBedsPicker,
      ResPeoplePicker
    },
    data() {
      return {
        availableSpaceIds: null,
        isAssigned: 1,
        selectedCustomer: null,
        showCreateCustomer: false,
        showSearchCustomers: false,
        spaceTypePref: 0,
        startDate: null,
        endDate: null,
        selectedSpaceId: null,
        selectedPeople: null,
        selectedBeds: null,

        componentKey: 0
      }
    },
    computed: {
      labelCustomer () {
        return this.$t('message.customer')
      },
      fStartDate () {
        if(this.startDate) {
          return dayjs(this.startDate).format('YYYY-MM-DD')
        } else {
          return null
        }
      },
      fEndDate () {
        if(this.endDate) {
          return dayjs(this.endDate).format('YYYY-MM-DD')
        } else {
          return null
        }
      },
      spaceTypes () {
        return spaceTypeStore().spaceTypes
      },
      token () {
        return authStore().token
      },
      //  i18n
      labelAssigned () {
      return this.$t('message.assigned')
      },
      labelFalse () {
        return this.$t('message.false')
      },
      labelTrue () {
        return this.$t('message.true')
      },
      typeLabel () {
        return this.$t('message.type')
      }
    },
    methods: {
      bedQtyChosen ( beds ) {
        this.selectedBeds = beds
      },
      checkConflicts () {
        api.reservations.checkConflicts( this.fStartDate, this.fEndDate, this.selectedSpaceId, this.token ).then( response => {
          console.log(response)
        }).catch ( error  => {
          handleError(error)
        })
      },
      createReservation () {
        /*
        console.log('checkin: ', this.fStartDate)
        console.log('checkout: ', this.fEndDate )
        console.log('people: ', this.selectedPeople)
        console.log('beds: ', this.selectedBeds )
        console.log('spaceId', this.selectedSpaceId )
        console.log('customer: ', this.selectedCustomer )
        */
      let pSTP
      if( !this.spaceTypePref ) {
        pSTP = 0
      } else { 
        pSTP = this.spaceTypePref
      }
      //  trigger full-screen loading
      const loading = ElLoading.service({
        lock: true,
        text: 'Loading',
        background: 'rgba(0, 0, 0, 0.7)',
      })
       reservationData.createReservation( this.fStartDate, 
                                          this.fEndDate, 
                                          this.selectedCustomer, 
                                          this.selectedSpaceId, 
                                          this.selectedPeople, 
                                          this.selectedBeds,
                                          this.isAssigned,
                                          pSTP ).then( response => {
          loading.close()
          if(response.data.create.execute_res_sql == true){
            ElMessage({
              type: 'success',
              message: 'Reservation was created'
            })
            this.resetForm()
          }
        }).catch( error => {
          loading.close()
          this.handleRequestError(error)
        })

      },
      customerSelected ( e ) {
        //console.log( e )
        this.selectedCustomer = e
      },
      dRangeClearDates () {
        this.startDate = null
        this.endDate = null
        this.availableSpaceIds = null
      },
      dRangeSelected ( range ) {
        //console.log('dRange @ change on createres', range)
        // DO NOT load if range is null . . . this results in all
        // ids as available . . . BAD BAD BAD
        if( range[0] && range[1] ) {
          this.startDate = range[0]
          this.endDate = range[1]
          //  trigger full-screen loading
          const loading = ElLoading.service({
            lock: true,
            text: 'Loading',
            background: 'rgba(0, 0, 0, 0.7)',
          })
          reservationData.checkAvailabilityByDates(this.fStartDate, this.fEndDate, this.token).then( (response) => {
            //console.log(response.data)
            loading.close()
            this.availableSpaceIds = response.data.availability.availableSpaceIds
          }).catch( error => {
            loading.close
            handleRequestError(error)
          })
        } else {
          this.availableSpaceIds = []
          this.startDate = null
          this.endDate = null
        }
      },
      peopleQtyChosen ( people ) {
        //console.log('people chosen: ', people )
        this.selectedPeople = people
      },
      resetForm () {
        this.componentKey += 1
        // also, we clear availableSpaceIds
        this.availableSpaceIds = []
        //  clear customer
        this.selectedCustomer = null
        this.showCreateCustomer = false
        this.showSearchCustomers = false
        this.isAssigned = 1
        this.spaceTypePref = null
        this.selectedSpaceId = null
      },
      spaceSelected ( spaceId ) {
        //console.log(spaceId)
        this.selectedSpaceId = spaceId
      }
    },
    watch: {
      isAssigned ( oldVal, newVal ) {
        this.selectedSpaceId = null
        this.spaceTypePref = 0
      }
    }
  }
</script>

