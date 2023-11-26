<template v-if="rootSpaces">
  <div style="padding-left: 8px;">
    <span>
      <el-button-group>
        <el-button type="primary" plain @click="viewBack7">-7</el-button>
        <el-button type="primary" plain  @click="viewBack1">-1</el-button>
      </el-button-group>
      <singleDatePicker
        :overrideDate="overRideSingleDatePicker"
        @singleDatePicker:dateSelected="singleDateSelected"
      />
      <el-button-group>
        <el-button type="primary" plain @click="viewForward1">+1</el-button>
        <el-button type="primary" plain @click="viewForward7">+7</el-button>
      </el-button-group>&nbsp
    </span>
    <ResViewTable
      v-if = "rootSpaces"
      @resBlockClick="reservationSelected"
      @resview-toggle-show-children="toggleShowChildren"
      @emptyCellClick="emptyCellClick"
      @unassigned-res-selected="unassignedResSelected"
      :tDateArray="tDateArray"
      :tableData="resTableData"
      :trigger="trigger"
      :resSpaceCopy="spaceRecords"
      :tableHeight="tableHeight"
    />
  </div>
</template>

<script>
import singleDatePicker from '@/views/ResView/singleDatePicker.vue'
import ResViewTable from '@/views/ResView/resViewTable.vue'
import dayjs from 'dayjs'
import 'dayjs/locale/es'
import isSameOrBefore from 'dayjs'
import isSameOrAfter from 'dayjs'
import _ from 'lodash'
import { localeStore } from '@/stores/localeStore.js'
import { resViewStore } from '@/stores/resViewStore.js'
import { rootSpacesStore } from '@/stores/rootSpacesStore.js'
import useHandleRequestError from '@/composables/useHandleRequestError.js'
import { ElLoading } from 'element-plus'
import { reservationData } from '@/data/reservationData'


export default {
  setup () {
      //  import composable function for request error handling
      const { handleRequestError } = useHandleRequestError()
      return { handleRequestError }
  },
  name: 'ResView',
  props: ['reloadTrigger'],
  components: {
    singleDatePicker,
    ResViewTable
  },
  emits: [ 'reservation-selected' ],
  data () {
    return {
      leftPaneSize: 30,
      overRideSingleDatePicker: '',
      reservationDialog: false,
      reservations: [],
      rootSpaces: null,
      selectedReservation: null,
      showCreateReservation: true,
      showReservationView: false,
      trigger: 1,
      windowHeight: 0,
      windowWidth: 0,
      componentTrigger: 0
    }
  },
  computed: {
    locale () {
      return localeStore().selectedLocale
    },
    rDrawerWidth () {
      if(this.windowWidth < 768){
        return '100%'
      }
      if(this.windowWidth > 768 && this.windowWidth < 1200 ) {
        return '500px'
      }
      if(this.windowWidth >= 1200 ) {
        return '35%'
      }
    },
    resViewEndDate () {
      return dayjs(this.resViewStartDate).add(31, 'days').format('YYYY-MM-DD')
    },
    resViewStartDate () {
      return resViewStore().startDate
    },
    resTableData () {

      let presentedResStart
      let pResStart
      let iSpan
      let ciInRange
      let coInRange
      let iKey
      let sKey

      let spaceRecords

      //  SUPER IMPORTANT FOR CORRECT PRESENTATION
      //  we have to clear out all the properties on the root spaces
      //  that represent a res block for presentation.
      //  These property keys all start with "D"
      //  for example 'D20220827isfirst'
      _.each(this.rootSpaces, ( rootSpace ) => {
        const keys =  Object.keys(rootSpace)
        _.each(keys, (key) => {
          if ( key.substring(0,1) == "D" ) {
            delete rootSpace[key]
          }
        })
      })

      /**
       * this is where we generate a properly formatted piece of data
       * to hand to resViewTable, which will
       * hand it to the el-table component as THE KEY PROP
       */

       // we are manipulating rootSpaces here
      spaceRecords = this.rootSpaces
      //  iterate through the reservations and add data to the appropriate array item
      _.each(this.reservations, (iReservation) => {
        //  ignore reservations where checkout == resViewStartDate
        if( iReservation.checkout == this.resViewStartDate){
          //do nothing
        } else {
          let iRecord = _.find(spaceRecords, (o) => {
            //get the reservation
            return o.id == iReservation.space_id
          })
          //  don't present if iReservation.is_assigned == 0 (ie is unassigned)
          if(iRecord && iReservation.is_assigned){ 
            console.log('iRecord HERE:', iRecord)
            console.log('iRes HERE', iReservation)
            //  first present the reservation

            if( dayjs(iReservation.checkout).isAfter(dayjs(this.resViewEndDate)) ){
              coInRange = false
            } else {
              coInRange = true
            }

            //  check in is before start date, checkout is after start date
            //  presentedResStart is the first day that should be represented
            if ( dayjs(iReservation.checkin).isBefore(dayjs(this.resViewStartDate)) ){
                ciInRange = false
                presentedResStart = dayjs(this.resViewStartDate).format('YYYYMMDD')
                pResStart = dayjs(this.resViewStartDate).format('YYYY-MM-DD')
                iSpan = iSpan = dayjs(iReservation.checkout).diff(dayjs(this.resViewStartDate), 'd')
              } else {
                ciInRange = true
                presentedResStart = dayjs(iReservation.checkin).format('YYYYMMDD')
                pResStart = dayjs(iReservation.checkin).format('YYYY-MM-DD')
                iSpan = dayjs(iReservation.checkout).diff(dayjs(iReservation.checkin), 'd')
              }

            //  calculate the difference between presentedResStart and checkout
            let iDiff = dayjs(iReservation.checkout).diff(dayjs(pResStart), 'd')
            for( let i = 0; i < iDiff; i++){
                  //generate the day
                  let iDate = dayjs(presentedResStart).add(i, 'd').format('YYYYMMDD')
                  //  determine if this is the first day of the presentation
                  if( i == 0 ) {
                    iKey = 'D' + iDate + 'isfirst'
                    iRecord[iKey] = true
                  }

                  //  determine if this is the last day of the presentation
                  if( dayjs(this.resViewEndDate).isSame(iDate) ) {
                    iKey = 'D' + iDate + 'islast'
                    iRecord[iKey] = true
                  }

                  //  is block indicator
                  iKey = 'D' + iDate + 'blocked'
                  iRecord[iKey] = false
                  //  isStartTruncated flag
                  iKey = 'D' + iDate + 'starttruncated'
                  iRecord[iKey] = !ciInRange
                  //  isEndTruncated flat
                  iKey = 'D' + iDate + 'endtruncated'
                  iRecord[iKey] = !coInRange
                  //  resid
                  iKey = 'D' + iDate + 'resid'
                  iRecord[iKey] = iReservation.id
                  //  start
                  iKey = 'D' + iDate + 'start'
                  iRecord[iKey] = iReservation.checkin
                  //  end
                  iKey = 'D' + iDate + 'end'
                  iRecord[iKey] = iReservation.checkout
                  //  customer
                  iKey = 'D' +  iDate + 'customer'
                  iRecord[iKey] = iReservation.customer_last
                  //  status
                  iKey = 'D' + iDate + 'status'
                  iRecord[iKey] = iReservation.status
            }
          }

          //  now create 'empty block' records for children of reservations
          //  if there is no iRecord, it means the reservation is unassigned
          //  ie spaceId is 0, so we do not proceed with this reservation (yet)
          if(iRecord && iRecord.children.length > 0){
            console.log('iRecord at POINT 2', iRecord)
            _.forEach(iRecord.children, (childId) => {
              let qRecord = _.find(spaceRecords, (o) => {
                return o.id == childId
              })
              if(qRecord) {
                //  calculate the difference between presentedResStart and checkout
                let iDiff = dayjs(iReservation.checkout).diff(dayjs(pResStart), 'd')
                //  iterate through the needed number of days
                for( let i = 0; i < iDiff; i++){
                  // console.log('i', i)
                  //generate the day
                  let iDate = dayjs(presentedResStart).add(i, 'd').format('YYYYMMDD')
                  // console.log( iDate )
                  sKey = 'D' + iDate + 'blocked'
                  //  also check for a reservation
                  let iiKey = 'D' + iDate + 'resid'
                  // console.log('sKey', sKey)
                  if( !qRecord[sKey]){
                    // console.log('no block for this. sKey: ', qRecord)
                    
                    let sKey = 'D' + iDate + 'blocked'
                    qRecord[sKey] = true
                    //  span
                    sKey = 'D' +  iDate + 'span'
                    qRecord[sKey] = 1
                    //  resIdRef
                    sKey = 'D' +  iDate + 'resIdRef'
                    qRecord[sKey] = iReservation.id
                  }
                }
              }
            })
          }

          //  now create 'empty block' records for parents of reservations
          //  if there is no iRecord, it means the reservation is unassigned
          //  ie spaceId is 0, so we do not proceed with this reservation (yet)
          if(iRecord && iRecord.parents.length > 0){
            _.forEach(iRecord.parents, (parentId) => {
              let qRecord = _.find(spaceRecords, (o) => {
                return o.id == parentId
              })
              if(qRecord) {
                //  iterate through the 'days'
                //  calculate the difference between presentedResStart and checkout
                let iDiff = dayjs(iReservation.checkout).diff(dayjs(pResStart), 'd')
                //console.log('iDiff', iDiff)
                //iterate through the 'days'
                for( let i = 0; i < iDiff; i++){
                  // console.log('i', i)
                  //generate the day
                  let iDate = dayjs(presentedResStart).add(i, 'd').format('YYYYMMDD')
                  // console.log( iDate )
                  let sKey = 'D' + iDate + 'blocked'
                  //console.log('sKey', sKey)
                  if( !qRecord[sKey] ){
                    // console.log('no block for this')
                    let sKey = 'D' + iDate + 'blocked'
                    qRecord[sKey] = true
                    //  span
                    sKey = 'D' +  iDate + 'span'
                    qRecord[sKey] = 1
                    //  resIdRef
                    sKey = 'D' +  iDate + 'resIdRef'
                    qRecord[sKey] = iReservation.id
                  }
                }
              }
            })
          }
          //  NOW, we handle unassigned reservations
          if(iReservation.is_assigned == 0) {
            console.log('iReservation @ handle unassigned')
            console.table(iReservation)
            let iDate = dayjs(iReservation.checkin).format('YYYYMMDD')
            let sKey = 'D' + iDate + 'unassigned'
            let qRecord = _.find(spaceRecords, (o) => {
                //  this is the id of the root space for 'unassigned'
                //  THERE HAD BETTER THE FUCK BE ONLY ONE OF THESE
                return o.isUnassigned == 1
            })
            if(qRecord) {
              console.log('qRecord', qRecord)
              if(!qRecord[sKey]){
                
              qRecord[sKey] = [{...iReservation}]
              } else {
                qRecord[sKey].push(iReservation)
              }
            }
          }
        }
      })
      return spaceRecords
    },
    tableHeight () {
      return this.windowHeight - 108
    },
    tDateArray () {
      //  set a reactive locale to day.js for table header formatting
      dayjs.locale(this.locale.name)
      //  this constructs the column elements for the table
      //  we want 31 columns, starting from start date
      const dArr = []
      for( let i = 0; i < 32; i++ ) {
        let iObj = {
          //  formatted without dashes
          dayString: 'D' +  dayjs(this.resViewStartDate).add(i, 'day').format('YYYYMMDD'),
          //  formatted with dashes
          dayLabel: dayjs(this.resViewStartDate).add(i, 'day').format('ddd MMM D')
        }
        dArr.push(iObj)
      }
      return dArr
    }
  },
  methods: {
    childrenHide ( k ) {
      k.showChildren = 0
      _.each(k.children, (childId) => {
          console.log('childId', childId)
          let m = _.find(this.rootSpaces, (o) => {
            return o.id == childId
          })
          console.log('m', m)
          if(m){
            this.childrenHide(m)
          }
      })
    },
    childrenShow ( k ) {
      k.showChildren = 1
      _.each(k.children, (childId) => {
          let m = _.find(this.rootSpaces, (o) => {
                return o.id == childId
          })
          if(m){
            //  activate this if we want show to recursively down the tree
            //this.childrenShow(m)
          }
      })
    },
    clearReservations () {
    },
    closeReservationView () {
      this.showCreateReservation = true
      this.showReservationView = false
    },
    emptyCellClick ( obj ) {
      console.log('empty cell selected', obj)
    },
    getReservations () {
      //  trigger full-screen loading
      const loading = ElLoading.service({
        lock: true,
        text: 'Loading',
        background: 'rgba(0, 0, 0, 0.7)',
      })
      reservationData.getReservationsByRange( this.resViewStartDate, this.resViewEndDate )
      .then( (response) => {
        loading.close()
        console.log('res fetch from rv3 getR()')
        this.reservations = response.data.reservations
        this.componentTrigger += 1
      }).catch( error => {
          loading.close()
          this.handleRequestError(error)
          this.reservations = []
      })
    },
    leftPane100 () {
      this.leftPaneSize = 100
    },
    leftPane30 () {
      this.leftPaneSize = 30
    },
    leftPane50 () {
      this.leftPaneSize = 50
    },
    leftPane70 () {
      this.leftPaneSize = 70
    },
    leftPane10 () {
      this.leftPaneSize = 5
    },
    leftResize ( e ) {
      console.log(e)
    },
    reservationSelected ( resId ) {
      const reactiveSelectedReservation = _.find(this.reservations, function(o){
        return o.id == resId
      })
      this.selectedReservation = {...reactiveSelectedReservation}
      this.$emit('reservation-selected', _.cloneDeep(reactiveSelectedReservation) )
    },
    rowClassName ( obj) {
    },
    showCreateReservationFtn () {
      this.showCreateReservation = true
      this.showReservationView = false
    },
    singleDateSelected ( nDate ) {
      //  format it
      let fDateSelected = dayjs(nDate).format('YYYY-MM-DD')
      //  send it to the store
      //  this will react locally
      resViewStore().startDate = fDateSelected
      //  get reservations from db
      //  this will update the view
      this.getReservations()
    },
    toggleShowChildren( spaceId ) {
      //  note that this IS going to change the store
      //  we're breaking one way data flow in this situation
      //  that's how we get persistence on show/hide children
      let k = _.find(this.rootSpaces, (o) => {
        return o.id == spaceId
      })
      //console.log('k @find')
      if(!k.showChildren) {
        this.childrenShow(k)
      } else {
        this.childrenHide(k)
      }

      resViewStore().setShowHideRootSpaceCopy(this.rootSpaces)
    },
    toggleCreateReservationDrawer () {
      this.showCreateReservationDrawer = !this.showCreateReservationDrawer
    },
    unassignedResSelected ( resId ) {
      //console.log('resId @ resview3', resId)
      const reactiveSelectedReservation = _.find(this.reservations, function(o){
        return o.id == resId
      })
      console.log('selRES @ res', _.cloneDeep(reactiveSelectedReservation))
      this.selectedReservation = {...reactiveSelectedReservation}
      this.$emit('reservation-selected', _.cloneDeep(reactiveSelectedReservation) )
      this.showCreateReservation = false
      this.showReservationView = true
    },
    updateSelectedReservation ( newRes ) {
      this.selectedReservation = newRes
    },
    viewBack1 () {
      const newDate = dayjs(resViewStore().startDate).subtract( 1, 'days').format('YYYY-MM-DD')
      this.overRideSingleDatePicker = newDate
    },
    viewBack7 () {
      const newDate = dayjs(resViewStore().startDate).subtract( 7, 'days').format('YYYY-MM-DD')
      this.overRideSingleDatePicker = newDate
    },
    viewForward1 () {
      const newDate = dayjs(resViewStore().startDate).add( 1, 'days').format('YYYY-MM-DD')
      this.overRideSingleDatePicker = newDate
    },
    viewForward7 () {
      const newDate = dayjs(resViewStore().startDate).add( 7, 'days').format('YYYY-MM-DD')
      this.overRideSingleDatePicker = newDate
    }
  },
  created () {
    dayjs.extend(isSameOrBefore)
    dayjs.extend(isSameOrAfter)
    this.spaceRecords =  {}
  },
  mounted () {
    /**
     *  Get window width and height and handle changes
     */
    this.windowWidth = window.innerWidth
    this.windowHeight = window.innerHeight
    window.addEventListener('resize', (event) => {
      this.windowWidth = window.innerWidth
      this.windowHeight = window.innerHeight
    }, true)

    //  SUPER IMPORTANT that we are referencing the hide/show iteration of
    //  space records if they exist.  these hold the show/hide children in
    //  current state, so the user has a continuous experience of show/hide children
    //  do we have a showHideRootSpaceCopy in store?
    //  ALSO NOTE: this is jankey in that it mutates the store value . . .  
    //  BUT, we are soooo tightly coupled here that it's probably okay
    if( resViewStore().showHideRootSpaceCopy ) {
      this.rootSpaces = resViewStore().showHideRootSpaceCopy
    } else { 
      this.rootSpaces = rootSpacesStore().rootSpaces
    }
    // and . . .   LOAD THE RESERVATIONS
    this.getReservations()
  },
  watch: {
    reloadTrigger ( newVal ) {
      this.getReservations()
    }

  }
}
</script>

<style>
  .row-hidden {
    display: none !important;
  }

  .splitpanes--vertical > .splitpanes__splitter {
  min-width: 10px;
  background:  #3375b9
}

.splitpanes--horizontal > .splitpanes__splitter {
  min-height: 10px;
  background: rgb(10, 66, 18);
}
</style>